<?php
// Get section IDs for the student's current registration
$stmt_sections = $pdo->prepare("
    SELECT s.id FROM registrations rg 
    JOIN sections s ON rg.section_id = s.id 
    WHERE rg.student_id = ? AND s.semester_id = 1
");
$stmt_sections->execute([$current_student['id']]);
$section_ids = $stmt_sections->fetchAll(PDO::FETCH_COLUMN);

$notifications = [];
// Fetch notices for these sections
if(!empty($section_ids)) {
    $placeholders = rtrim(str_repeat('?,', count($section_ids)), ',');
    $stmt_notices = $pdo->prepare("
        SELECT n.title, n.content, n.publish_date, c.course_code 
        FROM notices n
        JOIN sections s ON n.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        WHERE n.section_id IN ($placeholders)
        ORDER BY n.publish_date DESC LIMIT 4
    ");
    $stmt_notices->execute($section_ids);
    $notifications = $stmt_notices->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- The title can be made dynamic -->
    <title>AIUB Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php" class="logo">AIUB<span>Portal</span></a>
            </div>
            <div class="nav-container">
                <ul class="nav-menu">
                    <li><a href="index.php?page=dashboard" class="<?php echo ($page === 'dashboard' ? 'active' : ''); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="index.php?page=registered-courses" class="<?php echo ($page === 'registered-courses' ? 'active' : ''); ?>"><i class="fas fa-book"></i> Courses</a></li>
                    <li><a href="index.php?page=academic-results" class="<?php echo ($page === 'academic-results' ? 'active' : ''); ?>"><i class="fas fa-chart-line"></i> Results</a></li>
                    <li><a href="index.php?page=financial-statements" class="<?php echo ($page === 'financial-statements' ? 'active' : ''); ?>"><i class="fas fa-file-invoice-dollar"></i> Finance</a></li>
                </ul>
                <div class="user-controls">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if(!empty($notifications)): ?>
                            <div class="notification-badge"><?php echo count($notifications); ?></div>
                        <?php endif; ?>
                        <div class="notification-dropdown">
                            <div class="notification-header">
                                Notifications
                                <span class="mark-all-read">Mark all as read</span>
                            </div>
                            <div class="notification-list">
                                <?php if(empty($notifications)): ?>
                                    <div class="notification-item">No new notifications.</div>
                                <?php else: ?>
                                    <?php foreach($notifications as $notice): 
                                        $publish_date = new DateTime($notice['publish_date']);
                                        $now = new DateTime();
                                        $interval = $now->diff($publish_date);
                                        $time_ago = $interval->d > 0 ? $interval->d . ' days ago' : ($interval->h > 0 ? $interval->h . ' hours ago' : $interval->i . ' mins ago');
                                    ?>
                                    <div class="notification-item">
                                        <div class="icon-wrapper info"><i class="fas fa-file-alt"></i></div>
                                        <div class="notification-content">
                                            <div class="title"><?php echo htmlspecialchars($notice['title']); ?> (<?php echo htmlspecialchars($notice['course_code']); ?>)</div>
                                            <div class="message"><?php echo htmlspecialchars($notice['content']); ?></div>
                                            <div class="time"><?php echo $time_ago; ?></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="notification-footer">
                                <a href="#">View All Notifications</a>
                            </div>
                        </div>
                    </div>
                    <div class="user-profile">
                        <a href="index.php?page=profile" aria-label="User Profile"><i class="fas fa-user"></i></a>
                    </div>
                    <a href="logout.php" id="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>