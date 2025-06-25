<?php
// pages/class-schedule-page.php
$semesters = [
    'spring2024' => 'Spring 2023-2024',
    'fall2023' => 'Fall 2022-2023',
    'summer2023' => 'Summer 2022-2023'
];
$selected_semester_key = $_GET['semester'] ?? 'spring2024';

// Fetch schedule for the selected semester
$stmt = $pdo->prepare("
    SELECT c.course_code, c.title, s.schedule_time, s.room
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    JOIN semesters sm ON s.semester_id = sm.id
    WHERE rg.student_id = ? AND sm.semester_key = ?
");
$stmt->execute([$current_student['id'], $selected_semester_key]);
$schedule_courses = $stmt->fetchAll();

// Helper function to parse time string like "TTh 10:00-11:30"
function parse_schedule($time_string) {
    if (empty($time_string)) return [];
    $parts = explode(' ', $time_string, 2);
    if (count($parts) < 2) return [];
    
    $days_str = $parts[0];
    $days = [];
    if ($days_str == 'ST') $days = ['Sunday', 'Tuesday'];
    else if ($days_str == 'MW') $days = ['Monday', 'Wednesday'];
    else if ($days_str == 'TTh') $days = ['Tuesday', 'Thursday'];
    else {
        if (strpos($days_str, 'S') !== false) $days[] = 'Sunday';
        if (strpos($days_str, 'M') !== false) $days[] = 'Monday';
        if (strpos($days_str, 'T') !== false && strpos($days_str, 'Th') === false) $days[] = 'Tuesday';
        if (strpos($days_str, 'W') !== false) $days[] = 'Wednesday';
        if (strpos($days_str, 'Th') !== false) $days[] = 'Thursday';
    }
    return $days;
}

$weekly_schedule = [
    'Sunday' => [], 'Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 'Thursday' => []
];

foreach ($schedule_courses as $course) {
    $days = parse_schedule($course['schedule_time']);
    foreach ($days as $day) {
        if (isset($weekly_schedule[$day])) {
            $weekly_schedule[$day][] = $course;
        }
    }
}
?>
<section id="class-schedule-page" class="page-content">
    <h2 class="page-title"><i class="fas fa-calendar-alt"></i> Class Schedule</h2>
    <div class="semester-selector">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="class-schedule-page">
            <select name="semester" class="semester-select" onchange="this.form.submit()">
                <?php foreach ($semesters as $key => $name): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($selected_semester_key === $key ? 'selected' : ''); ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="card full-width" style="margin-top: 1.5rem; box-shadow: none; border: 1px solid #eee;">
        <div class="card-header" style="border-radius: var(--border-radius) var(--border-radius) 0 0;">
            <h2 id="class-schedule-semester-title"><i class="fas fa-calendar-week"></i> Weekly Schedule - <?php echo htmlspecialchars($semesters[$selected_semester_key]); ?></h2>
        </div>
        <div class="card-body schedule-container" id="class-schedule-content-area">
            <?php
            $has_classes = false;
            foreach ($weekly_schedule as $day => $classes) {
                if (!empty($classes)) {
                    $has_classes = true;
                    echo "<div class='schedule-day'>";
                    echo "<div class='day-header'><i class='fas fa-calendar-day'></i> {$day}</div>";
                    
                    foreach ($classes as $class) {
                        $is_lab = stripos($class['title'], 'lab') !== false;
                        echo "
                        <div class='schedule-item " . ($is_lab ? 'lab' : '') . "' data-course-code='" . htmlspecialchars($class['course_code']) . "' data-course-title='" . htmlspecialchars($class['title']) . "'>
                            <div class='course-info'>
                                <div class='course-code'><i class='fas " . ($is_lab ? 'fa-flask' : 'fa-chalkboard-teacher') . "'></i> " . htmlspecialchars($class['course_code']) . " - " . htmlspecialchars($class['title']) . "</div>
                                <div class='course-time'><i class='fas fa-clock'></i> " . htmlspecialchars($class['schedule_time']) . "</div>
                            </div>
                            <div class='course-room'><i class='fas fa-door-open'></i> " . htmlspecialchars($class['room']) . "</div>
                        </div>";
                    }
                    echo "</div>";
                }
            }
            if (!$has_classes) {
                echo "<p class='no-results' style='text-align:center; padding:1rem;'>No schedule available for the selected semester.</p>";
            }
            ?>
        </div>
    </div>
</section>