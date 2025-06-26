<?php
// --- Get the active semester ---
$stmt_sem = $pdo->query("SELECT * FROM semesters WHERE is_active_registration = 1 LIMIT 1");
$active_semester = $stmt_sem->fetch();

if (!$active_semester) {
    echo "<section class='page-content'><h2 class='page-title'><i class='fas fa-edit'></i> Course Registration</h2><p class='no-results'>Course registration is not active at this time.</p></section>";
    return;
}

$student_id = $current_student['id'];
$active_semester_id = $active_semester['id'];

// --- Get IDs of all courses the student has EVER taken ---
$stmt_completed = $pdo->prepare("SELECT course_id FROM academic_results WHERE student_id = ?");
$stmt_completed->execute([$student_id]);
$completed_course_ids = $stmt_completed->fetchAll(PDO::FETCH_COLUMN);

$stmt_registered = $pdo->prepare("SELECT c.id as course_id, c.course_code, s.schedule_time FROM registrations rg JOIN sections s ON rg.section_id = s.id JOIN courses c ON s.course_id = c.id WHERE rg.student_id = ? AND s.semester_id = ?");
$stmt_registered->execute([$student_id, $active_semester_id]);
$registered_courses_info = $stmt_registered->fetchAll(PDO::FETCH_ASSOC);
$registered_course_ids = array_column($registered_courses_info, 'course_id');

$ineligible_course_ids = array_unique(array_merge($completed_course_ids, $registered_course_ids));

// --- Get all available program courses, EXCLUDING the ineligible ones ---
$sql_courses = "
    SELECT c.course_code, c.title, c.credits, 
    (SELECT COUNT(*) FROM sections s WHERE s.course_id = c.id AND s.semester_id = ?) as section_count 
    FROM courses c 
";
$params = [$active_semester_id];

if (!empty($ineligible_course_ids)) {
    $placeholders = rtrim(str_repeat('?,', count($ineligible_course_ids)), ',');
    $sql_courses .= " WHERE c.id NOT IN ($placeholders)";
    $params = array_merge($params, $ineligible_course_ids);
}
$sql_courses .= " ORDER BY c.course_code";

$stmt_courses = $pdo->prepare($sql_courses);
$stmt_courses->execute($params);
$program_courses = $stmt_courses->fetchAll();

echo "<script>const alreadyRegisteredCourses = " . json_encode($registered_courses_info) . ";</script>";
?>


<section id="registration-schedule" class="page-content">
    <h2 class="page-title"><i class="fas fa-edit"></i> Course Registration</h2>

    <!-- Selected Courses Area -->
    <div id="selected-courses-for-registration-container" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">
            <i class="fas fa-shopping-cart"></i> Your Selected Courses (Pending Confirmation)
        </h3>
        <div id="selected-courses-list" class="registration-course-list">
            <p class="no-results">No courses selected yet. Add courses from the list below.</p>
        </div>
        <div style="text-align: right; margin-top: 1.5rem;" id="final-confirm-button-container">
        </div>
    </div>
    <hr style="margin: 0 0 2rem 0;">

    <!-- Main View: List of all courses -->
    <div id="registration-main-view">
        <div class="search-container">
            <input type="text" id="program-course-search-input" placeholder="Search available program courses (e.g., CSE3205)...">
        </div>
        <h3 style="color: var(--primary); margin-bottom: 1rem;">Select a Course to View Offered Sections:</h3>
        <div class="program-course-list-container" id="program-courses-list">
            <?php if (empty($program_courses)): ?>
                <p class="no-results">No new courses available for registration.</p>
            <?php else: ?>
                <?php foreach($program_courses as $course): ?>
                    <div class="program-course-item" data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>">
                        <h5><i class="fas fa-book-medical"></i> <?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['title']); ?></h5>
                        <p><?php echo htmlspecialchars($course['credits']); ?> Credits 
                           <span style="margin-left:10px; color:var(--accent); font-weight:500;">(<?php echo $course['section_count']; ?> Section<?php echo ($course['section_count'] != 1 ? 's' : ''); ?> Available)</span>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sections View: Shows sections for a selected course -->
    <div id="offered-sections-view" style="display: none;">
        <button class="btn btn-outline back-to-courses-btn" id="back-to-all-courses-btn"><i class="fas fa-arrow-left"></i> Back to All Available Courses</button>
        <h3 id="offered-sections-title">Offered Sections</h3>
        <div class="search-container">
            <input type="text" id="section-search-input" placeholder="Filter sections by faculty, time, room...">
        </div>
        <div class="registration-course-list" id="registration-course-list-container">
            <p class="no-results">Loading...</p>
        </div>
    </div>
</section>