<?php
// --- Calculate Real CGPA and Completed Credits ---
$stmt_cgpa = $pdo->prepare("SELECT SUM(ar.grade_point * c.credits) / SUM(c.credits) as cgpa, SUM(c.credits) as credits_completed FROM academic_results ar JOIN courses c ON ar.course_id = c.id WHERE ar.student_id = ? AND ar.grade_point > 0");
$stmt_cgpa->execute([$current_student['id']]);
$academic_summary = $stmt_cgpa->fetch();
$cgpa = $academic_summary['cgpa'] ?? 0.00;
$credits_completed = $academic_summary['credits_completed'] ?? 0;

// Fetch current semester credits
$stmt_credits = $pdo->prepare("SELECT IFNULL(SUM(c.credits), 0) as current_credits FROM registrations rg JOIN sections s ON rg.section_id = s.id JOIN courses c ON s.course_id = c.id JOIN semesters sm ON s.semester_id = sm.id WHERE rg.student_id = ? AND sm.is_active_registration = 1");
$stmt_credits->execute([$current_student['id']]);
$current_credits_info = $stmt_credits->fetch();

// --- Fetch and Process FULL Weekly Schedule for Dashboard ---
$stmt_schedule = $pdo->prepare("
    SELECT c.course_code, c.title, s.schedule_time, s.room
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    JOIN semesters sm ON s.semester_id = sm.id
    WHERE rg.student_id = ? AND sm.is_active_registration = 1
");
$stmt_schedule->execute([$current_student['id']]);
$schedule_courses = $stmt_schedule->fetchAll();

// Helper function to parse schedule times like "TTh 10:00-11:30"
function parse_schedule_days($time_string) {
    if (empty($time_string)) return [];
    $parts = explode(' ', $time_string, 2);
    if (count($parts) < 2) return [];
    
    $days_str = $parts[0];
    $days = [];
    if ($days_str == 'ST') $days = ['Sunday', 'Tuesday'];
    else if ($days_str == 'MW') $days = ['Monday', 'Wednesday'];
    else if ($days_str == 'TTh') $days = ['Tuesday', 'Thursday'];
    else { // Handle single days like 'M', 'T', 'W', etc.
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

$weekly_schedule = [
    'Sunday' => [], 'Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 'Thursday' => [], 'Friday' => [], 'Saturday' => []
];

foreach ($schedule_courses as $course) {
    $days = parse_schedule_days($course['schedule_time']);
    foreach ($days as $day) {
        if (isset($weekly_schedule[$day])) {
            $weekly_schedule[$day][] = $course;
        }
    }
}

// --- Fetch and Combine Upcoming Deadlines ---
$stmt_registered = $pdo->prepare("SELECT s.id as section_id, c.id as course_id FROM registrations rg JOIN sections s ON rg.section_id = s.id JOIN courses c ON s.course_id = c.id WHERE rg.student_id = ? AND s.semester_id = 1");
$stmt_registered->execute([$current_student['id']]);
$registered_courses_info = $stmt_registered->fetchAll();

$registered_section_ids = array_column($registered_courses_info, 'section_id');
$registered_course_ids = array_column($registered_courses_info, 'course_id');

$upcoming_deadlines = [];

// Fetch upcoming assignments
if (!empty($registered_section_ids)) {
    $placeholders = rtrim(str_repeat('?,', count($registered_section_ids)), ',');
    $stmt_assignments = $pdo->prepare("SELECT a.title, a.due_date, c.course_code FROM assignments a JOIN sections s ON a.section_id = s.id JOIN courses c ON s.course_id = c.id WHERE a.section_id IN ($placeholders) AND a.due_date >= CURDATE()");
    $stmt_assignments->execute($registered_section_ids);
    while($row = $stmt_assignments->fetch()) {
        $upcoming_deadlines[] = [
            'title' => $row['title'],
            'subtitle' => $row['course_code'],
            'date' => new DateTime($row['due_date']),
            'type' => 'assignment'
        ];
    }
}

// Fetch upcoming exams
if (!empty($registered_course_ids)) {
    $placeholders = rtrim(str_repeat('?,', count($registered_course_ids)), ',');
    $stmt_exams = $pdo->prepare("SELECT es.exam_type, es.exam_datetime, c.course_code FROM exam_schedule es JOIN courses c ON es.course_id = c.id WHERE es.course_id IN ($placeholders) AND es.exam_datetime >= CURDATE()");
    $stmt_exams->execute($registered_course_ids);
    while($row = $stmt_exams->fetch()) {
        $upcoming_deadlines[] = [
            'title' => htmlspecialchars($row['exam_type']) . ' Exam',
            'subtitle' => $row['course_code'],
            'date' => new DateTime($row['exam_datetime']),
            'type' => 'exam'
        ];
    }
}

// Sort all deadlines by date
usort($upcoming_deadlines, function($a, $b) {
    return $a['date'] <=> $b['date'];
});

// --- Fetch recent announcements  ---
$stmt_announcements = $pdo->query("SELECT * FROM announcements ORDER BY publish_date DESC LIMIT 3");
$announcements = $stmt_announcements->fetchAll();

?>


<section id="dashboard">
    <!-- Welcome Section -->
    <section class="welcome-section">
        <div class="welcome-text">
            <h2>Welcome back, <?php echo htmlspecialchars($current_student['full_name']); ?>!</h2>
            <p><?php echo htmlspecialchars($current_student['program']); ?> | <br>ID: <?php echo htmlspecialchars($current_student['student_id_str']); ?> | <span>Spring 2023-2024</span></p>
        </div>
        <div class="quick-stats" id="dashboard-quick-stats">
            <div class="stat-card"><div class="value"><?php echo number_format($cgpa, 2); ?></div><div class="label">CGPA</div></div>
            <div class="stat-card"><div class="value"><?php echo (int)$credits_completed; ?></div><div class="label">Credits Completed</div></div>
            <div class="stat-card"><div class="value"><?php echo $current_credits_info['current_credits']; ?></div><div class="label">Credits Current</div></div>
            <div class="stat-card"><div class="value">0</div><div class="label">Due Fees</div></div>
        </div>
    </section>

    <div class="dashboard-grid">
        <!-- Today's Schedule -->
        <div class="card full-width">
            <div class="card-header">
                <h2><i class="fas fa-calendar-week"></i> Weekly Schedule</h2>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="card-body schedule-container" id="dashboard-schedule-content">
                <?php
                $has_classes = false;
                $current_day_name = date('l'); 
                $day_icons = ['Sunday' => 'fa-sun', 'Monday' => 'fa-cloud', 'Tuesday' => 'fa-calendar-day', 'Wednesday' => 'fa-cloud-sun', 'Thursday' => 'fa-cloud-rain', 'Friday' => 'fa-wind', 'Saturday' => 'fa-star'];
                
                foreach ($weekly_schedule as $day => $classes) {
                    if (!empty($classes)) {
                        $has_classes = true;
                        $is_today = ($current_day_name === $day);
                        $day_header_class = $is_today ? 'day-header current-day' : 'day-header';
                        $day_icon = $day_icons[$day] ?? 'fa-calendar-alt';
                        
                        echo "<div class='schedule-day'>";
                        echo "<div class='{$day_header_class}'><i class='fas {$day_icon}'></i> {$day} " . ($is_today ? '(Today)' : '') . "</div>";
                        
                        usort($classes, function($a, $b) {
                            $time_a = explode(' ', $a['schedule_time'])[1] ?? '00:00';
                            $time_b = explode(' ', $b['schedule_time'])[1] ?? '00:00';
                            return strcmp($time_a, $time_b);
                        });

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
                    echo "<p class='no-results'>No classes scheduled for the current semester.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-bullhorn"></i> Recent Announcements</h2><div class="icon"><i class="fas fa-exclamation-circle"></i></div></div>
            <div class="card-body">
                <?php if (empty($announcements)): ?>
                    <p>No recent announcements.</p>
                <?php else: ?>
                    <?php foreach($announcements as $announcement): ?>
                    <div class="announcement-item">
                        <div class="announcement-title">
                            <i class="fas <?php echo htmlspecialchars($announcement['icon']); ?>"></i> <?php echo htmlspecialchars($announcement['title']); ?>
                        </div>
                        <div class="announcement-date">
                            <i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($announcement['publish_date'])); ?>
                        </div>
                        <div class="announcement-content">
                            <?php echo htmlspecialchars($announcement['content']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-bolt"></i> Quick Actions</h2><div class="icon"><i class="fas fa-rocket"></i></div></div>
            <div class="card-body">
                <div class="action-buttons">
                    <a href="index.php?page=registration-schedule" class="btn"><i class="fas fa-edit"></i> Course Registration</a>
                    <a href="index.php?page=academic-results" class="btn"><i class="fas fa-chart-bar"></i> View Results</a>
                    <a href="index.php?page=financial-statements" class="btn"><i class="fas fa-credit-card"></i> Fee Payment</a>
                    <a href="index.php?page=applications-page" class="btn"><i class="fas fa-file-certificate"></i> Applications</a>
                    <a href="index.php?page=profile" class="btn btn-secondary"><i class="fas fa-user-edit"></i> Update Profile</a>
                    <a href="index.php?page=academic-calendar-page" class="btn"><i class="fas fa-calendar-alt"></i> Academic Calendar</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines Card  -->
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-exclamation-triangle"></i> Upcoming Deadlines</h2><div class="icon"><i class="fas fa-clock"></i></div></div>
            <div class="card-body">
                <?php if (empty($upcoming_deadlines)): ?>
                    <p>No upcoming deadlines found.</p>
                <?php else: ?>
                    <?php foreach(array_slice($upcoming_deadlines, 0, 3) as $deadline): 
                        $today = new DateTime();
                        $interval = $today->diff($deadline['date']);
                        $days_left = $interval->invert ? 0 : $interval->days; 
                        
                        $color_class = 'var(--success)';
                        $border_class = 'var(--lecture)'; 
                        if ($deadline['type'] === 'exam') $border_class = 'var(--danger)';
                        if ($deadline['type'] === 'assignment') $border_class = 'var(--warning)';

                        if ($days_left <= 7) $color_class = 'var(--danger)';
                        elseif ($days_left <= 21) $color_class = 'var(--warning)';
                    ?>
                    <div class="schedule-item" style="cursor:default; border-left-color: <?php echo $border_class; ?>;">
                        <div>
                            <div class="course-code" style="color: var(--dark-emphasis);"><?php echo htmlspecialchars($deadline['title']); ?></div>
                            <div class="course-time"><?php echo htmlspecialchars($deadline['subtitle']); ?> - <?php echo $deadline['date']->format('F j, Y'); ?></div>
                        </div>
                        <div style="color: <?php echo $color_class; ?>; font-weight: bold;"><?php echo $days_left; ?> days left</div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>