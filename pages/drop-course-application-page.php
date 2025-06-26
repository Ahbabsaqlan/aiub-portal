<?php
// --- Get current semester info ---
$stmt_sem = $pdo->query("SELECT * FROM semesters WHERE is_active_registration = 1 LIMIT 1");
$current_semester = $stmt_sem->fetch();

// --- Get currently registered courses for the active semester ---
$stmt_reg = $pdo->prepare("
    SELECT s.id as section_id, c.course_code, c.title, s.section_char
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    WHERE rg.student_id = ? AND s.semester_id = ?
");
$stmt_reg->execute([$current_student['id'], $current_semester['id']]);
$registered_courses = $stmt_reg->fetchAll();

// --- Get all 'Course Drop' application history ---
$stmt_hist = $pdo->prepare("
    SELECT a.*, s.name as semester_name 
    FROM applications a
    LEFT JOIN semesters s ON a.details LIKE CONCAT('%', s.name, '%')
    WHERE a.student_id = ? AND a.application_type = 'Course Drop'
    ORDER BY a.request_date DESC
");
$stmt_hist->execute([$current_student['id']]);
$drop_history = $stmt_hist->fetchAll();

// --- Check which courses already have a pending drop request ---
$pending_requests = array_filter($drop_history, function($req) {
    return $req['status'] === 'Pending';
});

$pending_course_codes = [];
foreach ($pending_requests as $req) {
    if (preg_match('/course: ([\w\s]+) -/', $req['details'], $matches)) {
        $pending_course_codes[] = trim($matches[1]);
    }
}
?>

<section id="drop-course-application-page" class="page-content">
    <h2 class="page-title"><i class="fas fa-minus-circle"></i> Course Drop Application</h2>
    
    <div id="current-semester-courses-for-drop">
        <h3>Your Registered Courses for <?php echo htmlspecialchars($current_semester['name']); ?>:</h3>
        <div id="drop-course-list" class="table-container" style="margin-top:1rem;">
            <?php if (empty($registered_courses)): ?>
                <p class="no-results">You have no courses registered for the current semester to drop.</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>Course Code</th><th>Title</th><th>Section</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach($registered_courses as $course): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td><?php echo htmlspecialchars($course['section_char']); ?></td>
                                <td>
                                    <!-- ** THE CONDITIONAL BUTTON LOGIC ** -->
                                    <?php if (in_array($course['course_code'], $pending_course_codes)): ?>
                                        <button class="btn btn-sm btn-warning" disabled>Drop Requested</button>
                                    <?php else: ?>
                                        <form class="request-drop-form" method="POST"
                                            data-section-id="<?php echo $course['section_id']; ?>"
                                            data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>"
                                            data-title="<?php echo htmlspecialchars($course['title']); ?>"
                                            data-section-char="<?php echo htmlspecialchars($course['section_char']); ?>"
                                        >
                                            <button type="submit" class="btn btn-sm btn-danger">Request Drop</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <hr style="margin: 2rem 0;">
    
    <div id="drop-request-history">
        <h3><i class="fas fa-history"></i> Drop Request History</h3>
        <div id="drop-history-table-container" class="table-container" style="margin-top:1rem;">
            <?php if (empty($drop_history)): ?>
                <p class="no-results">No drop request history found.</p>
            <?php else: ?>
                <table id="drop-history-table">
                    <thead><tr><th>Request Date</th><th>Semester</th><th>Course Code</th><th>Title</th><th>Section</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach($drop_history as $req): 
                            $course_code = $title = $section = 'N/A';
                            if (preg_match('/course: ([\w\s]+) - ([\w\s\d\(\)]+)\s\(Section\s([\w\d]+)\)/', $req['details'], $matches)) {
                                $course_code = trim($matches[1]);
                                $title = trim($matches[2]);
                                $section = trim($matches[3]);
                            }
                        ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($req['request_date'])); ?></td>
                            <td><?php echo htmlspecialchars($req['semester_name'] ?? $current_semester['name']); ?></td>
                            <td><?php echo htmlspecialchars($course_code); ?></td>
                            <td><?php echo htmlspecialchars($title); ?></td>
                            <td><?php echo htmlspecialchars($section); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($req['status']); ?>">
                                    <?php echo htmlspecialchars($req['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const showToast = typeof window.showToast === 'function' 
        ? window.showToast 
        : (msg, type) => alert(`${type.toUpperCase()}: ${msg}`);

    document.querySelectorAll('.request-drop-form').forEach(form => {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true; 

            const sectionId = this.dataset.sectionId;
            const courseCode = this.dataset.courseCode;
            const sectionChar = this.dataset.sectionChar;
            const title = this.dataset.title;
            const semesterName = '<?php echo htmlspecialchars($current_semester['name']); ?>';
            
            if (!confirm(`Are you sure you want to request dropping ${courseCode}?`)) {
                submitBtn.disabled = false;
                return;
            }

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch('api/request_course_drop.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ section_id: sectionId })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'An unknown error occurred.');
                }

                

                // Show success toast
                showToast(`Drop request for ${courseCode} - Section ${sectionChar} submitted.`, 'success');

                // Change the button's appearance
                submitBtn.innerHTML = 'Drop Requested';
                submitBtn.classList.remove('btn-danger');
                submitBtn.classList.add('btn-warning');

                const historyTable = document.getElementById('drop-history-table');
                const noHistoryP = document.querySelector('#drop-history-table-container .no-results');
                
                // If the "no history" message is there, remove it and create the table
                if (noHistoryP) {
                    noHistoryP.remove();
                    document.getElementById('drop-history-table-container').innerHTML = `
                        <table id="drop-history-table">
                            <thead><tr><th>Request Date</th><th>Semester</th><th>Course Code</th><th>Title</th><th>Section</th><th>Status</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    `;
                }

                const historyTableBody = document.getElementById('drop-history-table').querySelector('tbody');
                const newRow = historyTableBody.insertRow(0); 
                const today = new Date().toISOString().slice(0, 10);

                newRow.innerHTML = `
                    <td>${today}</td>
                    <td>${semesterName}</td>
                    <td>${courseCode}</td>
                    <td>${title}</td>
                    <td>${sectionChar}</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                `;

            } catch (error) {
                showToast(error.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Request Drop';
            }
        });
    });
});
</script>