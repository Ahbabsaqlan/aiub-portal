<?php
// pages/curriculum.php

$stmt = $pdo->prepare("
    SELECT cur.semester_level, c.course_code, c.title, c.credits, c.course_type
    FROM curriculum cur
    JOIN courses c ON cur.course_id = c.id
    WHERE cur.program_name = ?
    ORDER BY cur.semester_level, c.course_code
");
$stmt->execute([$current_student['program']]);
$curriculum_courses = $stmt->fetchAll();

// Group courses by semester level
$grouped_curriculum = [];
foreach ($curriculum_courses as $course) {
    $grouped_curriculum[$course['semester_level']][] = $course;
}

?>
<section id="curriculum" class="page-content">
    <h2 class="page-title"><i class="fas fa-sitemap"></i> Curriculum - <?php echo htmlspecialchars($current_student['program']); ?></h2>
    <?php foreach($grouped_curriculum as $semester_level => $courses): ?>
        <div class="curriculum-semester">
            <h3><i class="fas fa-layer-group"></i> Semester <?php echo $semester_level; ?></h3>
            <ul class="curriculum-courses">
                <?php foreach($courses as $course): ?>
                <li>
                    <div class="course-code-title">
                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['title']); ?>
                    </div> 
                    <span class="course-credits"><?php echo number_format($course['credits'], 1); ?> Credits</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</section>