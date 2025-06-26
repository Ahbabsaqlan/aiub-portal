<?php
// pages/exam-schedule.php

// Get IDs of courses the student is registered for in the current semester (ID=1)
$stmt_registered = $pdo->prepare("
    SELECT c.id FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    WHERE rg.student_id = ? AND s.semester_id = 1
");
$stmt_registered->execute([$current_student['id']]);
$registered_course_ids = $stmt_registered->fetchAll(PDO::FETCH_COLUMN);

$quizzes = $midterms = $finals = [];

if (!empty($registered_course_ids)) {
    // Create placeholders for IN clause
    $placeholders = rtrim(str_repeat('?,', count($registered_course_ids)), ',');

    // Fetch all exams for these courses
    $stmt_exams = $pdo->prepare("
        SELECT es.*, c.course_code, c.title 
        FROM exam_schedule es
        JOIN courses c ON es.course_id = c.id
        WHERE es.semester_id = 1 AND es.course_id IN ($placeholders)
        ORDER BY es.exam_datetime ASC
    ");
    $stmt_exams->execute($registered_course_ids);
    $all_exams = $stmt_exams->fetchAll();
    
    // Group exams by type
    foreach ($all_exams as $exam) {
        if (strpos($exam['exam_type'], 'Quiz') !== false) {
            $quizzes[] = $exam;
        } elseif ($exam['exam_type'] == 'Midterm') {
            $midterms[] = $exam;
        } elseif ($exam['exam_type'] == 'Final') {
            $finals[] = $exam;
        }
    }
}
?>

<section id="exam-schedule" class="page-content">
    <h2 class="page-title"><i class="fas fa-calendar-check"></i> Exam Schedule</h2>
    <div class="exam-tabs">
        <button class="exam-tab active" data-exam-type="quiz">Quizzes</button>
        <button class="exam-tab" data-exam-type="midterm">Midterm Exams</button>
        <button class="exam-tab" data-exam-type="final">Final Exams</button>
    </div>

    <!-- Quizzes -->
    <div id="quiz-exams" class="exam-content active">
        <h3>Upcoming Quizzes - Spring 2023-2024</h3>
        <?php if(empty($quizzes)): echo "<p>No upcoming quizzes scheduled.</p>"; else: ?>
            <?php foreach($quizzes as $exam): ?>
            <div class="exam-item">
                <h4><?php echo htmlspecialchars($exam['course_code']); ?> - <?php echo htmlspecialchars($exam['title']); ?> - <?php echo htmlspecialchars($exam['exam_type']); ?></h4>
                <p><i class="fas fa-calendar-day"></i> Date: <?php echo date('F j, Y', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-clock"></i> Time: <?php echo date('h:i A', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> Room: <?php echo htmlspecialchars($exam['room']); ?></p>
                <p><i class="fas fa-book-reader"></i> Syllabus: <?php echo htmlspecialchars($exam['syllabus']); ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Midterms -->
    <div id="midterm-exams" class="exam-content">
        <h3>Midterm Examination Schedule - Spring 2023-2024</h3>
        <?php if(empty($midterms)): echo "<p>Midterm schedule not published yet.</p>"; else: ?>
            <?php foreach($midterms as $exam): ?>
            <div class="exam-item">
                <h4><?php echo htmlspecialchars($exam['course_code']); ?> - <?php echo htmlspecialchars($exam['title']); ?></h4>
                <p><i class="fas fa-calendar-day"></i> Date: <?php echo date('F j, Y', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-clock"></i> Time: <?php echo date('h:i A', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> Room: <?php echo htmlspecialchars($exam['room']); ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Finals -->
    <div id="final-exams" class="exam-content">
        <h3>Final Examination Schedule - Spring 2023-2024</h3>
        <?php if(empty($finals)): echo '<p style="text-align: center; padding: 1rem; background-color: #fff3cd;"><i class="fas fa-info-circle"></i> Final exam schedule not published yet.</p>'; else: ?>
             <?php foreach($finals as $exam): ?>
            <div class="exam-item">
                <h4><?php echo htmlspecialchars($exam['course_code']); ?> - <?php echo htmlspecialchars($exam['title']); ?></h4>
                <p><i class="fas fa-calendar-day"></i> Date: <?php echo date('F j, Y', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-clock"></i> Time: <?php echo date('h:i A', strtotime($exam['exam_datetime'])); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> Room: <?php echo htmlspecialchars($exam['room']); ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>