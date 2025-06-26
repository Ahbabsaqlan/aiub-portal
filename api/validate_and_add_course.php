<?php
// api/validate_and_add_course.php
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$section_id_to_add = $data['section_id'] ?? null;
$temp_selected_sections = $data['temp_selected_sections'] ?? [];

if (empty($section_id_to_add)) {
    http_response_code(400);
    echo json_encode(['error' => 'Section ID not provided.']);
    exit;
}

$student_id = $_SESSION['user_id'];
$active_semester_id = 1; // Assuming Spring 2024 is ID 1

// ** NEW, ROBUST PARSING FUNCTION **
function parse_schedule_slot($time_string) {
    if (empty($time_string)) return null;

    // Use regex to capture days and times, allowing for formats with or without spaces
    if (!preg_match('/^([a-zA-Z]+)\s*([\d]{2}:[\d]{2}-[\d]{2}:[\d]{2})$/', $time_string, $matches)) {
        return null; // Does not match pattern like "ST 08:30-10:00" or "M09:00-10:30"
    }
    
    $days_str = $matches[1];
    $time_range = $matches[2];

    $day_map = [
        'S' => 'Sunday', 'M' => 'Monday', 'T' => 'Tuesday', 
        'W' => 'Wednesday', 'R' => 'Thursday', 'F' => 'Friday', 'A' => 'Saturday'
    ];
    $days = [];
    if ($days_str == 'ST') $days = ['Sunday', 'Tuesday'];
    elseif ($days_str == 'MW') $days = ['Monday', 'Wednesday'];
    elseif ($days_str == 'TTh') $days = ['Tuesday', 'Thursday'];
    else {
        $day_chars = str_split($days_str);
        foreach($day_chars as $char) {
            if(isset($day_map[$char])) $days[] = $day_map[$char];
        }
    }

    list($start, $end) = explode('-', $time_range);
    
    // Convert time to a comparable integer (e.g., 08:30 -> 830)
    $start_int = (int)str_replace(':', '', $start);
    $end_int = (int)str_replace(':', '', $end);

    return ['days' => $days, 'start' => $start_int, 'end' => $end_int];
}

try {
    // --- 1. Get Details of the Section to Add ---
    $stmt = $pdo->prepare("SELECT c.id as course_id, c.prerequisite_course_id, s.schedule_time FROM sections s JOIN courses c ON s.course_id = c.id WHERE s.id = ?");
    $stmt->execute([$section_id_to_add]);
    $section_to_add = $stmt->fetch();
    if (!$section_to_add) { throw new Exception("Invalid section selected."); }

    // --- 2. Prerequisite Check ---
    if ($section_to_add['prerequisite_course_id'] !== null) {
        $stmt_prereq = $pdo->prepare("SELECT 1 FROM academic_results WHERE student_id = ? AND course_id = ? AND grade_point > 0");
        $stmt_prereq->execute([$student_id, $section_to_add['prerequisite_course_id']]);
        if ($stmt_prereq->fetchColumn() === false) {
            throw new Exception("Prerequisite for this course has not been met.");
        }
    }

    // --- 3. Time Conflict Check ---
    $new_slot = parse_schedule_slot($section_to_add['schedule_time']);
    if ($new_slot) {
        $stmt_registered = $pdo->prepare("SELECT s.schedule_time FROM registrations rg JOIN sections s ON rg.section_id = s.id WHERE rg.student_id = ? AND s.semester_id = ?");
        $stmt_registered->execute([$student_id, $active_semester_id]);
        $existing_schedules = $stmt_registered->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($temp_selected_sections as $temp_section) {
            $existing_schedules[] = $temp_section['schedule_time'];
        }

        foreach ($existing_schedules as $existing_schedule) {
            $existing_slot = parse_schedule_slot($existing_schedule);
            if ($existing_slot) {
                if (array_intersect($new_slot['days'], $existing_slot['days'])) {
                    if (max($new_slot['start'], $existing_slot['start']) < min($new_slot['end'], $existing_slot['end'])) {
                        throw new Exception("Time conflict detected with another course.");
                    }
                }
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Validation successful.']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}