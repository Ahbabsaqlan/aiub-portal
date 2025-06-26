<?php
// --- Get all semesters the student has results for ---
$semesters_stmt = $pdo->prepare("
    SELECT DISTINCT s.id, s.name, s.semester_key 
    FROM academic_results ar
    JOIN semesters s ON ar.semester_id = s.id
    WHERE ar.student_id = ?
    ORDER BY s.id DESC
");
$semesters_stmt->execute([$current_student['id']]);
$all_semesters = $semesters_stmt->fetchAll();
if (!in_array(1, array_column($all_semesters, 'id'))) {
    array_unshift($all_semesters, ['id' => 1, 'name' => 'Spring 2023-2024', 'semester_key' => 'spring2024']);
}

$selected_semester_id = $_GET['semester_id'] ?? $all_semesters[0]['id'];

// --- Fetch results for the selected semester ---
$results_stmt = $pdo->prepare("
    SELECT ar.*, c.course_code, c.title, c.credits 
    FROM academic_results ar
    JOIN courses c ON ar.course_id = c.id
    WHERE ar.student_id = ? AND ar.semester_id = ?
");
$results_stmt->execute([$current_student['id'], $selected_semester_id]);
$completed_courses = $results_stmt->fetchAll();

$inprogress_courses = [];
if ($selected_semester_id == 1) { 
    $inprogress_stmt = $pdo->prepare("
        SELECT 'IP' as grade, NULL as grade_point, NULL as midterm_score, NULL as final_score, NULL as quiz_score, c.*, s.section_char
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        WHERE rg.student_id = ? AND s.semester_id = ?
    ");
    $inprogress_stmt->execute([$current_student['id'], $selected_semester_id]);
    $inprogress_courses = $inprogress_stmt->fetchAll();
}

$all_courses_for_semester = array_merge($completed_courses, $inprogress_courses);

// --- Calculate all statistics ---
$credits_taken_sem = 0;
$credits_earned_sem = 0;
$total_grade_points_product_sem = 0;
foreach ($all_courses_for_semester as $res) {
    $credits_taken_sem += $res['credits'];
    if (isset($res['grade_point']) && $res['grade_point'] > 0) {
        $credits_earned_sem += $res['credits'];
        $total_grade_points_product_sem += $res['grade_point'] * $res['credits'];
    }
}
$semester_gpa = ($credits_earned_sem > 0) ? ($total_grade_points_product_sem / $credits_earned_sem) : 0.00;

// Calculate Cumulative GPA (CGPA) up to and including the selected semester
$cgpa_stmt = $pdo->prepare("
    SELECT 
        SUM(ar.grade_point * c.credits) / SUM(c.credits) as cgpa
    FROM academic_results ar
    JOIN courses c ON ar.course_id = c.id
    WHERE ar.student_id = ? AND ar.semester_id <= ?
");
$cgpa_stmt->execute([$current_student['id'], $selected_semester_id]);
$cumulative_gpa = $cgpa_stmt->fetchColumn() ?? $semester_gpa;
?>
<section id="academic-results" class="page-content">
    <h2 class="page-title"><i class="fas fa-chart-line"></i> Academic Results</h2>
    <div class="semester-selector">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="academic-results">
            <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                 <?php foreach ($all_semesters as $semester): ?>
                    <option value="<?php echo $semester['id']; ?>" <?php echo ($selected_semester_id == $semester['id'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($semester['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div id="academic-results-content-area">
        <?php if (empty($all_courses_for_semester)): ?>
            <p class="no-results">No results available for the selected semester.</p>
        <?php else: ?>
            <div class="result-card">
                <div class="result-header">
                    <div class="result-title"><?php echo htmlspecialchars(array_column($all_semesters, 'name', 'id')[$selected_semester_id]); ?></div>
                    <div class="result-semester">Semester GPA: <?php echo number_format($semester_gpa, 2); ?></div>
                </div>
                <div class="result-details">
                    <div class="result-item"><span class="result-label">Credits Taken</span><span class="result-value"><?php echo number_format($credits_taken_sem, 1); ?></span></div>
                    <div class="result-item"><span class="result-label">Credits Earned</span><span class="result-value"><?php echo number_format($credits_earned_sem, 1); ?></span></div>
                    <div class="result-item"><span class="result-label">Semester GPA</span><span class="result-value"><?php echo number_format($semester_gpa, 2); ?></span></div>
                    <div class="result-item"><span class="result-label">Cumulative GPA</span><span class="result-value"><?php echo number_format($cumulative_gpa, 2); ?></span></div>
                </div>
                <div class="table-container" style="margin-top: 1.5rem;">
                    <table>
                        <thead>
                            <tr><th>Course Code</th><th>Course Title</th><th>Credit</th><th>Grade</th><th>Grade Point</th><th>Details</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_courses_for_semester as $res): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($res['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($res['title']); ?></td>
                                    <td><?php echo number_format($res['credits'], 1); ?></td>
                                    <td><?php echo htmlspecialchars($res['grade']); ?></td>
                                    <td><?php echo isset($res['grade_point']) ? number_format($res['grade_point'], 2) : 'N/A'; ?></td>
                                    <td>
                                        <?php if(isset($res['midterm_score'])): // Only show button if scores exist in DB ?>
                                            <button class="btn btn-sm view-assessment-btn" data-course-id="<?php echo $res['id']; ?>">View</button>
                                        <?php else: echo 'N/A'; endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Assessment Breakdown Sections (Initially Hidden) -->
            <?php foreach ($completed_courses as $res): ?>
                <div class="assessment-breakdown" id="assessment-<?php echo $res['id']; ?>" style="display: none;">
                    <h4><?php echo htmlspecialchars($res['title']); ?> (<?php echo htmlspecialchars($res['course_code']); ?>) Assessment - <?php echo htmlspecialchars(array_column($all_semesters, 'name', 'id')[$selected_semester_id]); ?></h4>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Assessment Type</th><th>Weight</th><th>Score</th><th>Max Score</th></tr></thead>
                            <tbody>
                                <tr><td>Midterm</td><td>25%</td><td><?php echo $res['midterm_score']; ?></td><td>25</td></tr>
                                <tr><td>Final</td><td>40%</td><td><?php echo $res['final_score']; ?></td><td>40</td></tr>
                                <tr><td>Quizzes/Assignments</td><td>35%</td><td><?php echo $res['quiz_score']; ?></td><td>35</td></tr>
                                <tr style="font-weight: bold;"><td>Total</td><td>100%</td><td><?php echo $res['midterm_score'] + $res['final_score'] + $res['quiz_score']; ?></td><td>100</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>