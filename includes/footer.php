<!-- Course Details Modal -->
<div class="modal" id="course-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-book-reader"></i> <span id="course-modal-title-text">Course Details</span></h3>
            <button class="close-modal">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-tabs">
                <button class="modal-tab active" data-tab="overview"><i class="fas fa-info-circle"></i> Overview</button>
                <button class="modal-tab" data-tab="faculty"><i class="fas fa-chalkboard-teacher"></i> Faculty</button>
                <button class="modal-tab" data-tab="consulting"><i class="fas fa-headset"></i> Consulting</button>
                <button class="modal-tab" data-tab="notes"><i class="fas fa-sticky-note"></i> Notes</button>
                <button class="modal-tab" data-tab="assignments"><i class="fas fa-tasks"></i> Assignments</button>
                <button class="modal-tab" data-tab="notices"><i class="fas fa-bullhorn"></i> Notices</button>
                <button class="modal-tab" data-tab="results-breakdown"><i class="fas fa-poll"></i> Results</button>
            </div>

            <!-- Overview Tab -->
            <div class="tab-content active" id="overview-tab">
                <h4><i class="fas fa-info-circle"></i> Course Information</h4>
                <div class="form-row"><div class="form-col"><div class="form-group"><label>Course Code</label><input type="text" class="form-control modal-course-code" readonly></div></div><div class="form-col"><div class="form-group"><label>Course Title</label><input type="text" class="form-control modal-course-title-input" readonly></div></div></div>
                <div class="form-row"><div class="form-col"><div class="form-group"><label>Credit Hours</label><input type="text" class="form-control modal-course-credit" readonly></div></div><div class="form-col"><div class="form-group"><label>Section</label><input type="text" class="form-control modal-course-section" readonly></div></div></div>
                <div class="form-group"><label>Course Description</label><textarea class="form-control modal-course-desc" rows="4" readonly></textarea></div>
                <div class="form-row"><div class="form-col"><div class="form-group"><label>Class Time</label><input type="text" class="form-control modal-course-time" readonly></div></div><div class="form-col"><div class="form-group"><label>Room</label><input type="text" class="form-control modal-course-room" readonly></div></div></div>
            </div>
            
            <!-- Faculty Tab -->
            <div class="tab-content" id="faculty-tab">
                <div class="faculty-info">
                    <div class="faculty-avatar"><i class="fas fa-user-graduate"></i></div>
                    <div class="faculty-details">
                        <h4 class="modal-faculty-name"></h4>
                        <p class="modal-faculty-title-dept"></p>
                        <p><strong>Email:</strong> <span class="modal-faculty-email"></span></p>
                        <p><strong>Phone:</strong> <span class="modal-faculty-phone"></span></p>
                        <p><strong>Office:</strong> <span class="modal-faculty-office"></span></p>
                        <p><strong>Education:</strong> <span class="modal-faculty-education"></span></p>
                    </div>
                </div>
            </div>

            <!-- Consulting Tab -->
            <div class="tab-content" id="consulting-tab">
                <h4><i class="far fa-clock"></i> Consulting Hours</h4>
                <div class="consulting-schedule">
                    <!-- This will be populated by JS -->
                </div>
                <div class="action-buttons" style="margin-top: 1.5rem;"><button class="btn"><i class="fas fa-calendar-plus"></i> Schedule Appointment</button></div>
            </div>

            <!-- Notes Tab -->
            <div class="tab-content" id="notes-tab">
                <h4><i class="fas fa-book-open"></i> Course Notes & Materials</h4>
                <div class="note-item"><div class="note-title"><i class="far fa-file-pdf"></i> Chapter 1: Introduction to SE</div><div class="note-meta"><i class="fas fa-calendar-alt"></i> Mar 1, 2024    <i class="fas fa-database"></i> 2.5 MB</div><a href="#" class="btn btn-sm btn-outline" download><i class="fas fa-download"></i> Download</a></div>
            </div>

            <!-- Assignments Tab -->
            <div class="tab-content" id="assignments-tab">
                <h4><i class="fas fa-clipboard-check"></i> Assignments</h4>
                <div class="assignment-item"><div class="assignment-title"><i class="fas fa-file-signature"></i> Assignment 1: Requirement Analysis</div><div class="assignment-due-date"><i class="fas fa-calendar-times"></i> Due: March 20, 2024, 11:59 PM</div><p>Status: <span class="assignment-status submitted"><i class="fas fa-check-circle"></i> Submitted</span></p><a href="#" class="btn btn-sm btn-outline" download><i class="fas fa-download"></i> Specs</a></div>
            </div>
            
            <!-- Notices Tab -->
            <div class="tab-content" id="notices-tab">
                <h4><i class="fas fa-bullhorn"></i> Course Notices</h4>
                <div class="notice-item"><div class="notice-title"><i class="fas fa-exclamation-circle"></i> Midterm Exam Schedule</div><div class="notice-date"><i class="fas fa-calendar"></i> March 18, 2024</div><div class="announcement-content">The midterm exam will be held on March 25, 2024.</div></div>
            </div>
            
            <!-- Results/Assessment Tab -->
            <div class="tab-content" id="results-breakdown-tab">
                <h4><i class="fas fa-graduation-cap"></i> Assessment Breakdown</h4>
                <div class="table-container">
                    <table class="modal-assessment-table">
                        <thead><tr><th>Assessment Type</th><th>Weight</th><th>Score</th><th>Max Score</th></tr></thead>
                        <tbody class="modal-assessment-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification Placeholder -->
<div id="toast-notification" class="toast-notification"></div>

<!-- Footer Section -->
<footer>
    <div class="footer-container">
        <div class="footer-section"><h3>Quick Links</h3><ul class="footer-links"><li><a href="#"><i class="fas fa-calendar-alt"></i> Academic Calendar</a></li><li><a href="index.php?page=class-schedule-page"><i class="fas fa-table"></i> Class Schedule</a></li><li><a href="index.php?page=exam-schedule"><i class="fas fa-file-alt"></i> Exam Schedule</a></li><li><a href="#"><i class="fas fa-book"></i> Course Catalog</a></li><li><a href="#"><i class="fas fa-book-open"></i> Library Resources</a></li></ul></div>
        <div class="footer-section"><h3>Student Services</h3><ul class="footer-links"><li><a href="#"><i class="fas fa-briefcase"></i> Career Services</a></li><li><a href="#"><i class="fas fa-comments"></i> Counseling Center</a></li><li><a href="#"><i class="fas fa-graduation-cap"></i> Financial Aid</a></li><li><a href="#"><i class="fas fa-heartbeat"></i> Health Services</a></li><li><a href="#"><i class="fas fa-users"></i> Student Clubs</a></li></ul></div>
        <div class="footer-section"><h3>Contact Us</h3><ul class="footer-links"><li><i class="fas fa-phone"></i> +880 1234-567890</li><li><i class="fas fa-envelope"></i> info@aiub.edu</li><li><i class="fas fa-map-marker-alt"></i> 408/1, Kuratoli, Khilkhet, Dhaka 1229, Bangladesh</li></ul></div>
        <div class="footer-section"><h3>Emergency Contacts</h3><ul class="footer-links"><li><i class="fas fa-shield-alt"></i> Campus Security: +880 1711-223344</li><li><i class="fas fa-ambulance"></i> Medical Emergency: 199</li><li><i class="fas fa-fire-extinguisher"></i> Fire Service: 101</li></ul></div>
    </div>
    <div class="copyright">© <?php echo date("Y"); ?> American International University-Bangladesh. All Rights Reserved.</div>
</footer>