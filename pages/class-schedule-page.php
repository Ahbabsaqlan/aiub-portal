<?php
// --- Get all semesters to populate the dropdown ---
$semesters_stmt = $pdo->query("SELECT * FROM semesters ORDER BY id DESC");
$all_semesters = $semesters_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Determine the selected semester ID ---
$selected_semester_id = $_GET['semester_id'] ?? ($all_semesters[2]['id'] ?? null);

// Initialize schedule array
$weekly_schedule = [
    'Sunday' => [], 'Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 'Thursday' => [], 'Friday' => [], 'Saturday' => []
];

// --- Fetch schedule ONLY if a semester is selected ---
if ($selected_semester_id) {
    $stmt = $pdo->prepare("
        SELECT c.course_code, c.title, s.schedule_time, s.room
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        WHERE rg.student_id = ? AND s.semester_id = ?
    ");
    $stmt->execute([$current_student['id'], $selected_semester_id]);
    $schedule_courses = $stmt->fetchAll();

    // Helper function to parse schedule days
    function parse_schedule_days($time_string) {
        if (empty($time_string)) return [];
        preg_match('/^([a-zA-Z]+)\s*/', $time_string, $matches);
        $days_str = $matches[1] ?? '';
        
        $days = [];
        if ($days_str == 'ST') $days = ['Sunday', 'Tuesday'];
        elseif ($days_str == 'MW') $days = ['Monday', 'Wednesday'];
        elseif ($days_str == 'TTh') $days = ['Tuesday', 'Thursday'];
        else {
            if (strpos($days_str, 'S') !== false && strpos($days_str, 'Sa') === false) $days[] = 'Sunday';
            if (strpos($days_str, 'M') !== false) $days[] = 'Monday';
            if (strpos($days_str, 'T') !== false && strpos($days_str, 'Th') === false) $days[] = 'Tuesday';
            if (strpos($days_str, 'W') !== false) $days[] = 'Wednesday';
            if (strpos($days_str, 'Th') !== false) $days[] = 'Thursday';
            if (strpos($days_str, 'F') !== false) $days[] = 'Friday';
            if (strpos($days_str, 'Sa') !== false) $days[] = 'Saturday';
        }
        return $days;
    }

    // Populate the schedule array
    foreach ($schedule_courses as $course) {
        $days = parse_schedule_days($course['schedule_time']);
        foreach ($days as $day) {
            if (isset($weekly_schedule[$day])) {
                $weekly_schedule[$day][] = $course;
            }
        }
    }
}
?>
<section id="class-schedule-page" class="page-content">
    <h2 class="page-title"><i class="fas fa-calendar-alt"></i> Class Schedule</h2>
    
    <div class="semester-selector">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="class-schedule-page">
            <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                <?php if (empty($all_semesters)): ?>
                    <option>No semesters found</option>
                <?php else: ?>
                    <?php foreach ($all_semesters as $semester): ?>
                        <option value="<?php echo $semester['id']; ?>" <?php echo ($selected_semester_id == $semester['id'] ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($semester['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </form>
    </div>

    <div class="card full-width" style="margin-top: 1.5rem; box-shadow: none; border: 1px solid #eee;">
        <div class="card-header" style="border-radius: var(--border-radius) var(--border-radius) 0 0;">
            <h2 id="class-schedule-semester-title">
                <i class="fas fa-calendar-week"></i> Weekly Schedule - 
                <?php 
                    $selected_semester_name = 'N/A';
                    foreach($all_semesters as $sem) {
                        if ($sem['id'] == $selected_semester_id) {
                            $selected_semester_name = $sem['name'];
                            break;
                        }
                    }
                    echo htmlspecialchars($selected_semester_name);
                ?>
            </h2>
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
                        <div class='schedule-item " . ($is_lab ? 'lab' : '') . "' data-course-code='" . htmlspecialchars($class['course_code']) . "'>
                            <div class='course-info'>
                                <div class='course-code'><i class='fas " . ($is_lab ? 'fa-laptop-code' : 'fa-chalkboard-teacher') . "'></i> " . htmlspecialchars($class['course_code']) . " - " . htmlspecialchars($class['title']) . "</div>
                                <div class='course-time'><i class='fas fa-clock'></i> " . htmlspecialchars($class['schedule_time']) . "</div>
                            </div>
                            <div class='course-room'><i class='fas fa-door-open'></i> " . htmlspecialchars($class['room']) . "</div>
                        </div>";
                    }
                    echo "</div>";
                }
            }
            if (!$has_classes) {
                echo "<p class='no-results' style='text-align:center; padding:1rem;'>No registered courses found for the selected semester.</p>";
            }
            ?>
        </div>
    </div>
</section>