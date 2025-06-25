<?php
// pages/academic-results.php
$semesters = [
    'spring2024' => 'Spring 2023-2024',
    'fall2023' => 'Fall 2022-2023',
    'summer2023' => 'Summer 2022-2023'
];
$selected_semester_key = $_GET['semester'] ?? 'fall2023'; // Default to a semester with grades

// Fetch results for the selected semester
$stmt = $pdo->prepare("
    SELECT 
        c.course_code, 
        c.title, 
        c.credits, 
        rg.grade,
        rg.grade_point
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    JOIN semesters sm ON s.semester_id = sm.id
    WHERE rg.student_id = ? AND sm.semester_key = ?
    ORDER BY c.course_code
");
$stmt->execute([$current_student['id'], $selected_semester_key]);
$results = $stmt->fetchAll();

// Calculate Semester GPA
$total_credits_taken = 0;
$total_grade_points_product = 0;
foreach ($results as $res) {
    if ($res['grade'] !== 'IP' && $res['grade_point'] !== null) {
        $total_credits_taken += $res['credits'];
        $total_grade_points_product += $res['grade_point'] * $res['credits'];
    }
}
$semester_gpa = ($total_credits_taken > 0) ? ($total_grade_points_product / $total_credits_taken) : 0;
?>
<section id="academic-results" class="page-content">
    <h2 class="page-title"><i class="fas fa-chart-line"></i> Academic Results</h2>
    <div class="semester-selector">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="academic-results">
            <select name="semester" class="semester-select" onchange="this.form.submit()">
                 <?php foreach ($semesters as $key => $name): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($selected_semester_key === $key ? 'selected' : ''); ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div id="academic-results-content-area">
        <?php if (empty($results)): ?>
            <p class="no-results">No results available for the selected semester.</p>
        <?php else: ?>
            <div class="result-card">
                <div class="result-header">
                    <div class="result-title"><?php echo htmlspecialchars($semesters[$selected_semester_key]); ?></div>
                    <div class="result-semester">Semester GPA: <?php echo number_format($semester_gpa, 2); ?></div>
                </div>
                <!-- Other details like CGPA would require a more complex query -->
                <div class="table-container" style="margin-top: 1.5rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit</th>
                                <th>Grade</th>
                                <th>Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $res): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($res['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($res['title']); ?></td>
                                    <td><?php echo number_format($res['credits'], 1); ?></td>
                                    <td><?php echo htmlspecialchars($res['grade'] ?? 'N/A'); ?></td>
                                    <td><?php echo ($res['grade_point'] !== null) ? number_format($res['grade_point'], 2) : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>