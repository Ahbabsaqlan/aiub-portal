<?php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$section_ids = $data['section_ids'] ?? [];

if (empty($section_ids) || !is_array($section_ids)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or empty section data provided.']);
    exit;
}

$student_id = $_SESSION['user_id'];

// Use a transaction to ensure all registrations succeed or none do
$pdo->beginTransaction();

try {
    // Check for conflicts and capacity before inserting
    foreach ($section_ids as $section_id) {
        // You would add more robust checks here (e.g., against already registered courses, time conflicts on the server-side)
        
        // Check capacity
        $stmt = $pdo->prepare("SELECT enrolled, capacity FROM sections WHERE id = ?");
        $stmt->execute([$section_id]);
        $section = $stmt->fetch();
        if ($section['enrolled'] >= $section['capacity']) {
            throw new Exception("Section ID $section_id is already full.");
        }
    }
    
    // If all checks pass, proceed with registration
    $stmt_insert = $pdo->prepare("INSERT INTO registrations (student_id, section_id, grade) VALUES (?, ?, 'IP')");
    $stmt_update = $pdo->prepare("UPDATE sections SET enrolled = enrolled + 1 WHERE id = ?");

    foreach ($section_ids as $section_id) {
        // Sanitize to ensure it's an integer
        $section_id = (int)$section_id;
        
        // Insert into registrations table
        $stmt_insert->execute([$student_id, $section_id]);

        // Increment enrolled count in sections table
        $stmt_update->execute([$section_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Registration completed successfully!']);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
}