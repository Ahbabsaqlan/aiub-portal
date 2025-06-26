<?php
// api/get_course_details.php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$course_code = $_GET['course_code'] ?? null;
$semester_key = $_GET['semester_key'] ?? 'spring2024';

if (!$course_code) {
    http_response_code(400);
    echo json_encode(['error' => 'Course code is required.']);
    exit;
}

try {
    // Step 1: Find the section ID for the student in the given semester
    $stmt_section = $pdo->prepare("
        SELECT s.id as section_id
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        JOIN semesters sm ON s.semester_id = sm.id
        WHERE rg.student_id = :student_id AND c.course_code = :course_code AND sm.semester_key = :semester_key
    ");
    $stmt_section->execute([
        ':student_id' => $_SESSION['user_id'],
        ':course_code' => $course_code,
        ':semester_key' => $semester_key
    ]);
    $section = $stmt_section->fetch();
    $section_id = $section['section_id'] ?? null;

    // Step 2: Fetch all details based on course_code and section_id
    $stmt = $pdo->prepare("
        SELECT 
            c.course_code, c.title, c.credits, c.description,
            s.section_char, s.schedule_time, s.room,
            f.name as faculty_name, f.title_dept, f.email as faculty_email, f.phone as faculty_phone, f.office as faculty_office, f.education as faculty_education
        FROM courses c
        LEFT JOIN sections s ON c.id = s.course_id
        LEFT JOIN faculty f ON s.faculty_id = f.id
        WHERE c.course_code = :course_code " . ($section_id ? "AND s.id = :section_id" : "") . "
        LIMIT 1
    ");
    $params = [':course_code' => $course_code];
    if ($section_id) {
        $params[':section_id'] = $section_id;
    }
    $stmt->execute($params);
    $details = $stmt->fetch();
    
    if ($details) {
        // Step 3: Fetch related data like assignments and notices if section is known
        $assignments = [];
        $notices = [];
        if ($section_id) {
            $stmt_assign = $pdo->prepare("SELECT * FROM assignments WHERE section_id = ? ORDER BY due_date DESC");
            $stmt_assign->execute([$section_id]);
            $assignments = $stmt_assign->fetchAll();

            $stmt_notice = $pdo->prepare("SELECT * FROM notices WHERE section_id = ? ORDER BY publish_date DESC");
            $stmt_notice->execute([$section_id]);
            $notices = $stmt_notice->fetchAll();
        }

        // Step 4: Prepare final JSON response structure
        $response_data = [
            'overview' => [
                'course_code' => $details['course_code'],
                'title' => $details['title'],
                'credits' => $details['credits'],
                'section' => $details['section_char'] ?? 'N/A',
                'description' => $details['description'],
                'time' => $details['schedule_time'] ?? 'N/A',
                'room' => $details['room'] ?? 'N/A'
            ],
            'faculty' => [
                'name' => $details['faculty_name'] ?? 'TBA',
                'title_dept' => $details['title_dept'] ?? 'N/A',
                'email' => $details['faculty_email'] ?? 'N/A',
                'phone' => $details['faculty_phone'] ?? 'N/A',
                'office' => $details['faculty_office'] ?? 'N/A',
                'education' => $details['faculty_education'] ?? 'N/A'
            ],
            'consulting' => [['day' => 'Monday', 'time' => '02:00 PM - 04:00 PM']], // Mocked for now
            'notes' => [['title' => 'Chapter 1: Intro', 'date' => 'Mar 1, 2024', 'size' => '2.5 MB']], // Mocked
            'assignments' => $assignments,
            'notices' => $notices,
            'results' => [] // Results would be fetched on the results page, not in the modal
        ];

        echo json_encode($response_data);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Course details not found.']);
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}