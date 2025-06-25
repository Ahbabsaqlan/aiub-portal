<?php
// pages/dashboard.php
// This file is included by index.php, so $pdo and $current_student are available.

// Fetch current semester credits
$stmt_credits = $pdo->prepare("
    SELECT IFNULL(SUM(c.credits), 0) as current_credits
    FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    JOIN courses c ON s.course_id = c.id
    JOIN semesters sm ON s.semester_id = sm.id
    WHERE rg.student_id = ? AND sm.semester_key = 'spring2024'
");
$stmt_credits->execute([$current_student['id']]);
$current_credits_info = $stmt_credits->fetch();

// Fetch TODAY'S Schedule for Dashboard
$current_day_of_week = date('l'); // e.g., 'Sunday', 'Monday'
$day_shorthands = [
    'Sunday' => ['ST', 'S'],
    'Monday' => ['MW', 'M'],
    'Tuesday' => ['ST', 'TTh', 'T'],
    'Wednesday' => ['MW', 'W'],
    'Thursday' => ['TTh', 'Th']
];
$today_shorthands = $day_shorthands[$current_day_of_week] ?? [];

$placeholders = rtrim(str_repeat('?,', count($today_shorthands)), ',');
$todays_classes = [];
if (!empty($today_shorthands)) {
    $sql = "
        SELECT c.course_code, c.title, s.schedule_time, s.room
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        JOIN semesters sm ON s.semester_id = sm.id
        WHERE rg.student_id = ? AND sm.semester_key = 'spring2024'
        AND SUBSTRING_INDEX(s.schedule_time, ' ', 1) IN ($placeholders)
        ORDER BY SUBSTRING_INDEX(s.schedule_time, ' ', -1)
    ";
    $stmt_today = $pdo->prepare($sql);
    $params = array_merge([$current_student['id']], $today_shorthands);
    $stmt_today->execute($params);
    $todays_classes = $stmt_today->fetchAll();
}
?>

<section id="dashboard">
    <!-- Welcome Section (remains the same) -->
    <section class="welcome-section">
        <div class="welcome-text">
            <h2>Welcome back, <?php echo htmlspecialchars($current_student['full_name']); ?>!</h2>
            <p><?php echo htmlspecialchars($current_student['program']); ?> | <br>ID: <?php echo htmlspecialchars($current_student['student_id_str']); ?> | <span id="dashboard-current-semester-display">Spring 2023-2024</span></p>
        </div>
        <div class="quick-stats" id="dashboard-quick-stats">
             <div class="stat-card"><div class="value" data-stat="cgpa"><?php echo $current_student['cgpa']; ?></div><div class="label">CGPA</div></div>
            <div class="stat-card"><div class="value" data-stat="attendance"><?php echo $current_student['credits_completed']; ?></div><div class="label">Credits Completed</div></div>
            <div class="stat-card"><div class="value" data-stat="credits"><?php echo $current_credits_info['current_credits']; ?></div><div class="label">Credits Current</div></div>
            <div class="stat-card"><div class="value" data-stat="due">0</div><div class="label">Due Fees</div></div>
        </div>
    </section>

    <div class="dashboard-grid">
        <!-- Today's Schedule -->
        <div class="card full-width">
            <div class="card-header">
                <h2><i class="fas fa-calendar-day"></i> Today's Schedule (<?php echo $current_day_of_week; ?>)</h2>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="card-body schedule-container" id="dashboard-schedule-content">
                <?php if (empty($todays_classes)): ?>
                    <p class='no-results' style='text-align:center; padding:1rem;'>You have no classes scheduled for today.</p>
                <?php else: ?>
                    <?php foreach ($todays_classes as $class): ?>
                        <?php $is_lab = stripos($class['title'], 'lab') !== false; ?>
                        <div class='schedule-item <?php echo $is_lab ? 'lab' : ''; ?>' data-course-code='<?php echo htmlspecialchars($class['course_code']); ?>' data-course-title='<?php echo htmlspecialchars($class['title']); ?>'>
                            <div class='course-info'>
                                <div class='course-code'><i class='fas <?php echo $is_lab ? 'fa-flask' : 'fa-chalkboard-teacher'; ?>'></i> <?php echo htmlspecialchars($class['course_code']); ?> - <?php echo htmlspecialchars($class['title']); ?></div>
                                <div class='course-time'><i class='fas fa-clock'></i> <?php echo htmlspecialchars($class['schedule_time']); ?></div>
                            </div>
                            <div class='course-room'><i class='fas fa-door-open'></i> <?php echo htmlspecialchars($class['room']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div style="text-align: right; margin-top: 1rem;">
                    <a href="index.php?page=class-schedule-page" class="btn btn-outline">View Full Weekly Schedule</a>
                </div>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-bullhorn"></i> Recent Announcements</h2><div class="icon"><i class="fas fa-exclamation-circle"></i></div></div>
            <div class="card-body">
                <div class="announcement-item"><div class="announcement-title"><i class="fas fa-file-alt"></i> Midterm Exam Schedule</div><div class="announcement-date"><i class="fas fa-calendar"></i> March 15, 2024</div><div class="announcement-content">The midterm examination schedule for Spring 2023-2024 has been published...</div></div>
                <div class="announcement-item"><div class="announcement-title"><i class="fas fa-graduation-cap"></i> Scholarship Application</div><div class="announcement-date"><i class="fas fa-calendar"></i> March 12, 2024</div><div class="announcement-content">Merit-based scholarship applications are now open...</div></div>
                <div class="announcement-item"><div class="announcement-title"><i class="fas fa-briefcase"></i> Career Development Workshop</div><div class="announcement-date"><i class="fas fa-calendar"></i> March 10, 2024</div><div class="announcement-content">Join our career development workshop on March 25th...</div></div>
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

        <!-- ***** NEWLY ADDED: Upcoming Deadlines Card ***** -->
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-exclamation-triangle"></i> Upcoming Deadlines</h2><div class="icon"><i class="fas fa-clock"></i></div></div>
            <div class="card-body">
                <div class="schedule-item" style="cursor:default; border-left-color: var(--danger);">
                    <div>
                        <div class="course-code" style="color: var(--dark-emphasis);">Midterm Exams</div>
                        <div class="course-time">March 25 - April 5, 2024</div>
                    </div>
                    <div style="color: var(--danger); font-weight: bold;">7 days left</div>
                </div>
                <div class="schedule-item" style="cursor:default; border-left-color: var(--warning);">
                    <div>
                        <div class="course-code" style="color: var(--dark-emphasis);">Project Submission</div>
                        <div class="course-time">CSE 3205 - April 10, 2024</div>
                    </div>
                    <div style="color: var(--warning); font-weight: bold;">22 days left</div>
                </div>
                <div class="schedule-item" style="cursor:default; border-left-color: var(--success);">
                    <div>
                        <div class="course-code" style="color: var(--dark-emphasis);">Tuition Fee Payment</div>
                        <div class="course-time">Spring 2023-2024</div>
                    </div>
                    <div style="color: var(--success); font-weight: bold;">Paid</div>
                </div>
            </div>
        </div>
        <!-- *************************************************** -->

    </div>
</section>