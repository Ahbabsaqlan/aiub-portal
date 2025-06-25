<?php
// api/get_course_details.php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$course_code = $_GET['course_code'] ?? null;
$semester_key = $_GET['semester_key'] ?? 'spring2024'; // Get semester context

if (!$course_code) {
    http_response_code(400);
    echo json_encode(['error' => 'Course code is required.']);
    exit;
}

try {
    // Fetch main course info, section info, and registration info in one go
    $stmt = $pdo->prepare("
        SELECT 
            c.course_code, c.title, c.credits, c.description,
            s.section_char, s.schedule_time, s.room, s.faculty_name,
            rg.grade, rg.grade_point
        FROM courses c
        LEFT JOIN sections s ON c.id = s.course_id
        LEFT JOIN semesters sm ON s.semester_id = sm.id AND sm.semester_key = :semester_key
        LEFT JOIN registrations rg ON s.id = rg.section_id AND rg.student_id = :student_id
        WHERE c.course_code = :course_code AND s.semester_id = sm.id
        LIMIT 1
    ");
    $stmt->execute([
        ':course_code' => $course_code,
        ':semester_key' => $semester_key,
        ':student_id' => $_SESSION['user_id']
    ]);

    $details = $stmt->fetch();

    if (!$details) {
        // Fallback for courses not registered in the selected semester (e.g., from curriculum page)
        $stmt_fallback = $pdo->prepare("SELECT * FROM courses WHERE course_code = ?");
        $stmt_fallback->execute([$course_code]);
        $details = $stmt_fallback->fetch();
    }
    
    if($details) {
        // You can add more data fetching here, e.g., for assignments, notes, etc.
        $details['faculty_info'] = ['title_dept' => 'Assistant Professor, Dept. of CSE', 'email' => strtolower(str_replace(' ','.', $details['faculty_name'] ?? '')) . '@aiub.edu'];
        $details['assessments'] = [
            ['type' => 'Midterm', 'weight' => '30%', 'score' => 'N/A', 'max' => '30'],
            ['type' => 'Final', 'weight' => '50%', 'score' => 'N/A', 'max' => '50'],
            ['type' => 'Quizzes', 'weight' => '20%', 'score' => 'N/A', 'max' => '20']
        ];
        echo json_encode($details);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Course not found.']);
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}