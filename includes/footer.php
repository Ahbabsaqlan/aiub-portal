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
                <button class="modal-tab" data-tab="results-breakdown"><i class="fas fa-poll"></i> Results</button>
                <!-- Add other tabs like notes, assignments etc. here if needed -->
            </div>
            
            <!-- Overview Tab Content -->
            <div class="tab-content active" id="overview-tab">
                <h4><i class="fas fa-info-circle"></i> Course Information</h4>
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group"><label>Course Code</label><input type="text" class="form-control modal-course-code" readonly></div>
                    </div>
                    <div class="form-col">
                        <div class="form-group"><label>Course Title</label><input type="text" class="form-control modal-course-title-input" readonly></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group"><label>Credit Hours</label><input type="text" class="form-control modal-course-credit" readonly></div>
                    </div>
                    <div class="form-col">
                        <div class="form-group"><label>Section</label><input type="text" class="form-control modal-course-section" readonly></div>
                    </div>
                </div>
                <div class="form-group"><label>Course Description</label><textarea class="form-control modal-course-desc" rows="4" readonly></textarea></div>
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group"><label>Class Time</label><input type="text" class="form-control modal-course-time" readonly></div>
                    </div>
                    <div class="form-col">
                        <div class="form-group"><label>Room</label><input type="text" class="form-control modal-course-room" readonly></div>
                    </div>
                </div>
            </div>
            
            <!-- Faculty Tab Content -->
            <div class="tab-content" id="faculty-tab">
                <div class="faculty-info">
                    <div class="faculty-avatar"><i class="fas fa-user-graduate"></i></div>
                    <div class="faculty-details">
                        <h4 class="modal-faculty-name"></h4>
                        <p class="modal-faculty-title-dept"></p>
                        <p><strong>Email:</strong> <span class="modal-faculty-email"></span></p>
                    </div>
                </div>
            </div>
            
            <!-- Results/Assessment Tab Content -->
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
        <!-- ... Paste your original footer links here if they are missing ... -->
        <div class="footer-section"><h3>Quick Links</h3><ul class="footer-links"><li><a href="#"><i class="fas fa-calendar-alt"></i> Academic Calendar</a></li><li><a href="index.php?page=class-schedule-page"><i class="fas fa-table"></i> Class Schedule</a></li><li><a href="index.php?page=exam-schedule"><i class="fas fa-file-alt"></i> Exam Schedule</a></li></ul></div>
        <div class="footer-section"><h3>Student Services</h3><ul class="footer-links"><li><a href="#"><i class="fas fa-briefcase"></i> Career Services</a></li><li><a href="#"><i class="fas fa-comments"></i> Counseling Center</a></li></ul></div>
        <div class="footer-section"><h3>Contact Us</h3><ul class="footer-links"><li><i class="fas fa-phone"></i> +880 1234-567890</li><li><i class="fas fa-envelope"></i> info@aiub.edu</li></ul></div>
    </div>
    <div class="copyright">© <?php echo date("Y"); ?> American International University-Bangladesh. All Rights Reserved.</div>
</footer>