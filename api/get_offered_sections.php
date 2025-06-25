<?php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$course_code = $_GET['course_code'] ?? null;
$current_semester_id = 1; // Assuming Spring 2024 is ID 1. Make this dynamic in a real app.

if (!$course_code) {
    echo json_encode(['error' => 'Course code not provided.']);
    http_response_code(400);
    exit;
}

try {
    // Fetch sections for the given course in the current semester
    $stmt = $pdo->prepare("
        SELECT s.id, s.section_char, s.schedule_time, s.room, s.faculty_name, s.capacity, s.enrolled, c.course_code, c.title, c.credits
        FROM sections s
        JOIN courses c ON s.course_id = c.id
        WHERE c.course_code = ? AND s.semester_id = ?
    ");
    $stmt->execute([$course_code, $current_semester_id]);
    $sections = $stmt->fetchAll();

    echo json_encode($sections);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
}