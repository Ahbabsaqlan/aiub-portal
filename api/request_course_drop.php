<?php
// api/request_course_drop.php
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
$section_id = $data['section_id'] ?? null;

if (empty($section_id)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Section ID not provided.']);
    exit;
}

$student_id = $_SESSION['user_id'];

try {
    // Fetch course details for the application log
    $stmt_details = $pdo->prepare("
        SELECT c.course_code, c.title, s.section_char FROM sections s
        JOIN courses c ON s.course_id = c.id
        WHERE s.id = ?
    ");
    $stmt_details->execute([$section_id]);
    $course_details = $stmt_details->fetch();

    if (!$course_details) {
        throw new Exception("Invalid section ID.");
    }
    
    // Check if a pending request already exists for this section
    $stmt_check = $pdo->prepare("SELECT id FROM applications WHERE student_id = ? AND details LIKE ? AND status = 'Pending'");
    $details_string = "%Dropping " . $course_details['course_code'] . "%";
    $stmt_check->execute([$student_id, $details_string]);
    if ($stmt_check->fetch()) {
        throw new Exception("A drop request for this course is already pending.");
    }

    // Insert the new application
    $application_details = "Request to drop course: " . $course_details['course_code'] . " - " . $course_details['title'] . " (Section " . $course_details['section_char'] . ")";
    $stmt_insert = $pdo->prepare("
        INSERT INTO applications (student_id, application_type, details, status) 
        VALUES (?, 'Course Drop', ?, 'Pending')
    ");
    $stmt_insert->execute([$student_id, $application_details]);
    
    echo json_encode(['success' => true, 'message' => 'Drop request submitted successfully.']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}