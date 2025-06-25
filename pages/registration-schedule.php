<?php
// pages/registration-schedule.php
// Get all program courses to display initially
$stmt = $pdo->query("SELECT course_code, title, credits FROM courses ORDER BY course_code");
$program_courses = $stmt->fetchAll();

// Get courses the student is ALREADY registered for this semester to check for conflicts
$stmt = $pdo->prepare("
    SELECT c.course_code, s.schedule_time FROM registrations rg
    JOIN sections s ON rg.section_id = s.id
    WHERE rg.student_id = ? AND s.semester_id = 1
");
$stmt->execute([$_SESSION['user_id']]);
$already_registered_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pass this data to JavaScript by embedding it as a JSON string
echo "<script>const alreadyRegisteredCourses = " . json_encode($already_registered_courses) . ";</script>";
?>
<section id="registration-schedule" class="page-content">
    <h2 class="page-title"><i class="fas fa-edit"></i> Course Registration</h2>

    <!-- Selected Courses for Registration Area -->
    <div id="selected-courses-for-registration-container" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">
            <i class="fas fa-shopping-cart"></i> Your Selected Courses (Pending Confirmation)
        </h3>
        <div id="selected-courses-list" class="registration-course-list">
            <p class="no-results">No courses selected yet. Add courses from the list below.</p>
        </div>
        <div style="text-align: right; margin-top: 1.5rem;" id="final-confirm-button-container">
            <!-- Confirm button will be dynamically shown/hidden here by JS -->
        </div>
    </div>
    <hr style="margin: 0 0 2rem 0;">

    <div id="registration-main-view">
        <h3 style="color: var(--primary); margin-bottom: 1rem;">Select a Course to View Offered Sections:</h3>
        <div class="program-course-list-container" id="program-courses-list">
            <?php foreach($program_courses as $course): ?>
                <div class="program-course-item" data-course-code="<?php echo htmlspecialchars($course['course_code']); ?>">
                    <h5><i class="fas fa-book-medical"></i> <?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['title']); ?></h5>
                    <p><?php echo htmlspecialchars($course['credits']); ?> Credits</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="offered-sections-view" style="display: none;">
        <button class="btn btn-outline back-to-courses-btn" id="back-to-all-courses-btn"><i class="fas fa-arrow-left"></i> Back to All Available Courses</button>
        <h3 id="offered-sections-title">Offered Sections</h3>
        <div class="registration-course-list" id="registration-course-list-container">
            <p class="no-results" style="display:none;">Loading...</p>
        </div>
    </div>
</section>