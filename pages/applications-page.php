<?php
// --- Check for Active Registration Status ---
$stmt_sem = $pdo->query("SELECT * FROM semesters WHERE is_active_registration = 1 LIMIT 1");
$current_semester = $stmt_sem->fetch();

$stmt_reg_check = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE student_id = ? AND section_id IN (SELECT id FROM sections WHERE semester_id = ?)");
$stmt_reg_check->execute([$current_student['id'], $current_semester['id']]);
$has_registration = $stmt_reg_check->fetchColumn() > 0;

$all_statuses = [];
if ($has_registration) {
    $all_statuses[] = [
        'title' => $current_semester['name'] . ' Registration',
        'status' => 'Approved'
    ];
}

// --- Fetch Other Application Statuses ---
$stmt_apps = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? ORDER BY request_date DESC");
$stmt_apps->execute([$current_student['id']]);
$other_applications = $stmt_apps->fetchAll();

foreach ($other_applications as $app) {
    $title = $app['application_type'];
    if ($app['application_type'] === 'Course Drop') {
         if (preg_match('/course: ([\w\s]+) - ([\w\s\d\(\)]+)\s\(Section\s([\w\d]+)\)/', $app['details'], $matches)) {
            $title = 'Drop Request: ' . $matches[1] . ' (' . $matches[3] . ') - ' . $current_semester['name'];
        }
    }
    $all_statuses[] = [
        'title' => $title,
        'status' => $app['status']
    ];
}
?>

<section id="applications-page" class="page-content">
    <h2 class="page-title"><i class="fas fa-clipboard-list"></i> Applications Hub</h2>
    
    <!-- Application Status Card -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h2><i class="fas fa-tasks"></i> Application Status</h2>
        </div>
        <div class="card-body" id="application-status-card-content">
            <?php if (empty($all_statuses)): ?>
                <p class="no-results" style="padding: 1rem; text-align: center;">No active applications or recent statuses.</p>
            <?php else: ?>
                <?php foreach($all_statuses as $item): 
                    $status_class = strtolower($item['status']); // approved, pending, rejected
                ?>
                <div class="status-badge status-item dynamic-status">
                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                    <span class="status-badge status-<?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($item['status']); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Application Action Cards -->
    <div class="application-cards-container">
        <a href="index.php?page=drop-course-application-page" class="application-card">
            <div class="app-icon"><i class="fas fa-minus-circle"></i></div>
            <h3>Course Drop Application</h3>
            <p>Request to drop one or more registered courses for the current semester.</p>
            <div class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply Now</div>
        </a>
        <div class="application-card"> <div class="app-icon"><i class="fas fa-exchange-alt"></i></div> <h3>Program Change</h3> <p>Apply to change your current academic program.</p> <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply</button> </div>
        <div class="application-card"> <div class="app-icon"><i class="fas fa-stream"></i></div> <h3>Major Change</h3> <p>Request to change your major specialization.</p> <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply</button> </div>
        <div class="application-card"> <div class="app-icon"><i class="fas fa-graduation-cap"></i></div> <h3>Convocation Application</h3> <p>Apply for graduation and convocation.</p> <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply</button> </div>
        <div class="application-card"> <div class="app-icon"><i class="fas fa-award"></i></div> <h3>Scholarship Application</h3> <p>Apply for various scholarships offered.</p> <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply</button> </div>
        <div class="application-card"> <div class="app-icon"><i class="fas fa-id-card"></i></div> <h3>ID Card Requisition</h3> <p>Request for a new or replacement ID card.</p> <button class="btn btn-primary"><i class="fas fa-arrow-right"></i> Apply</button> </div>
    </div>
</section>