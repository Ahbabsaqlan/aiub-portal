<?php
// pages/registered-courses.php

// Get all semesters to populate the dropdown
$semesters_stmt = $pdo->query("SELECT * FROM semesters ORDER BY id DESC");
$all_semesters = $semesters_stmt->fetchAll();

// Determine the selected semester (default to the first one if not set)
$selected_semester_id = $_GET['semester_id'] ?? ($all_semesters[2]['id'] ?? null);

$registered_courses = [];
$total_credits = 0;
$theory_credits = 0;
$lab_credits = 0;

if ($selected_semester_id) {
    // Query for registered courses in the selected semester
    $stmt = $pdo->prepare("
        SELECT c.course_code, c.title, c.credits, s.section_char, s.schedule_time, s.room, f.name as faculty_name
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        JOIN faculty f ON s.faculty_id = f.id
        WHERE rg.student_id = ? AND s.semester_id = ?
        ORDER BY c.course_code
    ");
    $stmt->execute([$current_student['id'], $selected_semester_id]);
    $registered_courses = $stmt->fetchAll();

    // Calculate credit summary
    foreach ($registered_courses as $course) {
        $total_credits += $course['credits'];
        // Simple heuristic to differentiate lab/theory credits
        if (stripos($course['title'], 'lab') !== false) {
            $lab_credits += $course['credits'];
        } else {
            $theory_credits += $course['credits'];
        }
    }
}
?>

<section id="registered-courses" class="page-content">
    <h2 class="page-title"><i class="fas fa-book"></i> Registered Courses</h2>
    
    <div class="semester-selector">
        <?php if (!empty($all_semesters)): ?>
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="registered-courses">
            <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                <?php foreach ($all_semesters as $semester): ?>
                    <option value="<?php echo $semester['id']; ?>" <?php echo ($selected_semester_id == $semester['id'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($semester['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>
    </div>

    <div class="credit-summary" id="registered-courses-credit-summary">
        <div class="credit-item">
            <div class="credit-value"><?php echo number_format($total_credits, 1); ?></div>
            <div class="credit-label">Total Credits</div>
        </div>
        <div class="credit-item">
            <div class="credit-value"><?php echo number_format($theory_credits, 1); ?></div>
            <div class="credit-label">Theory Credits</div>
        </div>
        <div class="credit-item">
            <div class="credit-value"><?php echo number_format($lab_credits, 1); ?></div>
            <div class="credit-label">Lab Credits</div>
        </div>
        <div class="credit-item">
            <div class="credit-value"><?php echo count($registered_courses); ?></div>
            <div class="credit-label">Courses</div>
        </div>
    </div>

    <div class="table-container" id="registered-courses-table-container">
        <?php if (empty($registered_courses)): ?>
            <p class="no-results" style="text-align:center; padding:1rem;">No courses registered for this semester.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Title</th>
                        <th>Credit</th>
                        <th>Section</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Faculty</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registered_courses as $course): ?>
                        <tr data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>">
                            <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo number_format($course['credits'], 1); ?></td>
                            <td><?php echo htmlspecialchars($course['section_char']); ?></td>
                            <td><?php echo htmlspecialchars($course['schedule_time']); ?></td>
                            <td><?php echo htmlspecialchars($course['room']); ?></td>
                            <td><?php echo htmlspecialchars($course['faculty_name']); ?></td>
                            <td>
                                <!-- ** THE CORRECTED BUTTON ** -->
                                <button class="btn btn-sm view-course-details-btn">
                                    <i class="fas fa-info-circle"></i> View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>