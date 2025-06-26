<?php

header('Content-Type: application/json');

require_once '../includes/auth_check.php';
require_once '../config/database.php';

$course_code = $_GET['course_code'] ?? null;

if (!$course_code) {
    http_response_code(400); 
    echo json_encode(['error' => 'Course code was not provided.']);
    exit;
}

try {
    $stmt_sem = $pdo->query("SELECT id FROM semesters WHERE is_active_registration = 1 LIMIT 1");
    $active_semester_id = $stmt_sem->fetchColumn();

    if (!$active_semester_id) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            s.id, 
            s.section_char, 
            s.schedule_time, 
            s.room, 
            s.capacity, 
            s.enrolled,
            c.course_code,
            c.title,
            c.credits,
            f.name as faculty_name
        FROM sections s
        JOIN courses c ON s.course_id = c.id
        JOIN faculty f ON s.faculty_id = f.id
        WHERE c.course_code = :course_code AND s.semester_id = :semester_id
        ORDER BY s.section_char
    ");

    $stmt->execute([
        ':course_code' => $course_code,
        ':semester_id' => $active_semester_id
    ]);
    
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sections);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("API Error in get_offered_sections.php: " . $e->getMessage());
    echo json_encode(['error' => 'A server error occurred. Please try again later.']);
}