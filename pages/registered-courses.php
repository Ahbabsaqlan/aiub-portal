<?php
// pages/registered-courses.php
// This file is included by index.php, so $pdo and $current_student are available.

// Determine which semester to show. Default to the latest one.
// In a real app, you'd fetch all available semesters for the student.
$semesters = [
    'spring2024' => 'Spring 2023-2024',
    'fall2023' => 'Fall 2022-2023',
    'summer2023' => 'Summer 2022-2023'
];
$selected_semester_key = $_GET['semester'] ?? 'spring2024';

// Fetch registered courses for the selected semester
$stmt = $pdo->prepare("
    SELECT 
        c.course_code, 
        c.title, 
        c.credits, 
        s.section_char, 
        s.schedule_time, 
        s.room, 
        s.faculty_name
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    JOIN semesters sm ON s.semester_id = sm.id
    WHERE rg.student_id = ? AND sm.semester_key = ?
    ORDER BY c.course_code
");
$stmt->execute([$current_student['id'], $selected_semester_key]);
$registered_courses = $stmt->fetchAll();

// Calculate credit summary
$total_credits = 0;
$theory_credits = 0;
$lab_credits = 0;
foreach ($registered_courses as $course) {
    $total_credits += $course['credits'];
    // A simple heuristic for lab/theory
    if (stripos($course['title'], 'lab') !== false) {
        $lab_credits += $course['credits'];
    } else {
        $theory_credits += $course['credits'];
    }
}
?>

<section id="registered-courses" class="page-content">
    <h2 class="page-title"><i class="fas fa-book"></i> Registered Courses</h2>
    <div class="semester-selector">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="registered-courses">
            <select name="semester" class="semester-select" onchange="this.form.submit()">
                <?php foreach ($semesters as $key => $name): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($selected_semester_key === $key ? 'selected' : ''); ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
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
                            <td><button class="btn btn-sm view-course-details-btn"><i class="fas fa-info-circle"></i> View</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>