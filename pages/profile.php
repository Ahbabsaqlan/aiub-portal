<?php
// pages/profile.php
// $current_student is already fetched in index.php
?>
<section id="profile" class="page-content">
    <h2 class="page-title"><i class="fas fa-user-graduate"></i> Student Profile</h2>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group"><label for="studentId">Student ID</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['student_id_str']); ?>" readonly></div>
        </div>
        <div class="form-col">
            <div class="form-group"><label for="program">Program</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['program']); ?>" readonly></div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group"><label for="fullName">Full Name</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['full_name']); ?>" readonly></div>
        </div>
        <div class="form-col">
            <div class="form-group"><label for="email">Email</label><input type="email" class="form-control" value="<?php echo htmlspecialchars($current_student['email']); ?>" readonly></div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group"><label for="phone">Phone</label><input type="tel" class="form-control" value="<?php echo htmlspecialchars($current_student['phone'] ?? '+880 1...'); ?>" readonly></div>
        </div>
        <div class="form-col">
            <div class="form-group"><label for="birthdate">Date of Birth</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['birthdate'] ?? 'Not Set'); ?>" readonly></div>
        </div>
    </div>
    <div class="form-group"><label for="address">Address</label><textarea class="form-control" rows="3" readonly><?php echo htmlspecialchars($current_student['address'] ?? 'Not Set'); ?></textarea></div>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group"><label for="admissionDate">Admission Date</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['admission_date'] ?? 'N/A'); ?>" readonly></div>
        </div>
        <div class="form-col">
            <div class="form-group"><label for="expectedGrad">Expected Graduation</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['expected_grad'] ?? 'N/A'); ?>" readonly></div>
        </div>
    </div>
    <div class="form-group"><label for="emergencyContact">Emergency Contact</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['emergency_contact'] ?? 'Not Set'); ?>" readonly></div>
    
    <h3 style="margin: 1.5rem 0 1rem; color: var(--primary); font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Academic Information</h3>
    <div class="form-row">
        <div class="form-col">
            <div class="form-group"><label for="cgpa">Cumulative GPA</label><input type="text" class="form-control" value="<?php echo number_format($current_student['cgpa'], 2); ?>" readonly></div>
        </div>
        <div class="form-col">
            <div class="form-group"><label for="creditsCompleted">Credits Completed</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($current_student['credits_completed']); ?>" readonly></div>
        </div>
    </div>
    
    <div class="action-buttons"><button class="btn" id="request-change-btn"><i class="fas fa-edit"></i> Request Profile Change</button><button class="btn"><i class="fas fa-file-pdf"></i> Download Profile</button></div>
    
    <div class="change-form" id="change-request-form" style="display: none;"><h3>Request Profile Change</h3><div class="form-group"><label for="changeField">Field to Change</label><select id="changeField" class="form-control"><option value="">Select field</option><option value="phone">Phone Number</option><option value="address">Home Address</option><option value="emergency">Emergency Contact</option></select></div><div class="form-group"><label for="newValue">New Value</label><input type="text" id="newValue" class="form-control"></div><div class="form-group"><label for="changeReason">Reason for Change</label><textarea id="changeReason" class="form-control" rows="3"></textarea></div><div class="action-buttons"><button class="btn"><i class="fas fa-paper-plane"></i> Submit Request</button><button class="btn btn-secondary" id="cancel-change-btn">Cancel</button></div></div>
</section>