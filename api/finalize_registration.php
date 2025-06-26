<?php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$section_ids = $data['section_ids'] ?? [];

if (empty($section_ids) || !is_array($section_ids)) {
    http_response_code(400); 
    echo json_encode(['error' => 'Invalid or empty section data provided.']);
    exit;
}

$student_id = $_SESSION['user_id'];

$pdo->beginTransaction();

try {
    foreach ($section_ids as $section_id) {
        $stmt_capacity = $pdo->prepare("SELECT enrolled, capacity FROM sections WHERE id = ? FOR UPDATE");
        $stmt_capacity->execute([$section_id]);
        $section = $stmt_capacity->fetch();
        if ($section['enrolled'] >= $section['capacity']) {
            throw new Exception("A selected course (Section ID: $section_id) became full just now. Please try again.");
        }
    }
    
    $stmt_insert = $pdo->prepare("
        INSERT INTO registrations (student_id, section_id) 
        VALUES (?, ?)
    ");
    
    $stmt_update = $pdo->prepare("
        UPDATE sections SET enrolled = enrolled + 1 WHERE id = ?
    ");

    foreach ($section_ids as $section_id) {
        $section_id = (int)$section_id;
        
        $stmt_insert->execute([$student_id, $section_id]);

        $stmt_update->execute([$section_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Registration completed successfully! Redirecting...']);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500); 
    echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
}