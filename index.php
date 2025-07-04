<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Fetch current student's basic info for the header/sidebar
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$current_student = $stmt->fetch();
if (!$current_student) {
    header('Location: logout.php');
    exit;
}

if (isset($_GET['semester_id'])) {
    $_SESSION['selected_semester_id'] = (int)$_GET['semester_id'];
}

if (!isset($_SESSION['selected_semester_id'])) {
    $stmt_default_sem = $pdo->query("SELECT id FROM semesters ORDER BY id DESC LIMIT 1");
    $_SESSION['selected_semester_id'] = $stmt_default_sem->fetchColumn();
}
$selected_semester_id = $_SESSION['selected_semester_id'];


// Determine which page to show. Default to dashboard.
$page = $_GET['page'] ?? 'dashboard';

// A whitelist of allowed pages to prevent security issues like LFI
$allowed_pages = [
    'dashboard', 'class-schedule-page', 'registered-courses', 'academic-results', 
    'financial-statements', 'profile', 'applications-page', 'curriculum', 
    'exam-schedule', 'academic-calendar-page', 'registration-schedule',
    'drop-course-application-page'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard'; 
}

$page_file = "pages/{$page}.php";

// Include the header
include 'includes/header.php';

?>
<!-- This is the main container that was hidden in original HTML -->
<div id="portal-main-container">
    <!-- Main Content Container -->
    <div class="page-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard' ? 'active' : ''); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="index.php?page=class-schedule-page" class="<?php echo ($page === 'class-schedule-page' ? 'active' : ''); ?>"><i class="fas fa-calendar-alt"></i> Class Schedule</a></li>
                <li><a href="index.php?page=registered-courses" class="<?php echo ($page === 'registered-courses' ? 'active' : ''); ?>"><i class="fas fa-book"></i> Registered Courses</a></li>
                <li><a href="index.php?page=academic-results" class="<?php echo ($page === 'academic-results' ? 'active' : ''); ?>"><i class="fas fa-chart-line"></i> Academic Results</a></li>
                <li><a href="index.php?page=financial-statements" class="<?php echo ($page === 'financial-statements' ? 'active' : ''); ?>"><i class="fas fa-file-invoice-dollar"></i> Financial Statement</a></li>
                <li><a href="index.php?page=profile" class="<?php echo ($page === 'profile' ? 'active' : ''); ?>"><i class="fas fa-user-graduate"></i> Student Profile</a></li>
                <li><a href="index.php?page=applications-page" class="<?php echo ($page === 'applications-page' ? 'active' : ''); ?>"><i class="fas fa-clipboard-list"></i> Applications</a></li>
                <li><a href="index.php?page=curriculum" class="<?php echo ($page === 'curriculum' ? 'active' : ''); ?>"><i class="fas fa-book-open"></i> Curriculum</a></li>
                <li><a href="index.php?page=exam-schedule" class="<?php echo ($page === 'exam-schedule' ? 'active' : ''); ?>"><i class="fas fa-calendar-check"></i> Exam Schedule</a></li>
                <li><a href="index.php?page=academic-calendar-page" class="<?php echo ($page === 'academic-calendar-page' ? 'active' : ''); ?>"><i class="fas fa-calendar-week"></i> Academic Calendar</a></li>
                <li><a href="index.php?page=registration-schedule" class="<?php echo ($page === 'registration-schedule' ? 'active' : ''); ?>"><i class="fas fa-edit"></i> Registration</a></li>
                <li><a href="#"><i class="fas fa-question-circle"></i> Help & Support</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            if (file_exists($page_file)) {
                include $page_file;
            } else {
                include 'pages/dashboard.php';
            }
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<!-- Course Details Modal from original HTML -->
<div class="modal" id="course-modal"></div>
<div id="toast-notification" class="toast-notification"></div>

<script src="./assets/index.js"></script>
</body>
</html>