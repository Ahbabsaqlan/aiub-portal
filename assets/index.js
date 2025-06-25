document.addEventListener('DOMContentLoaded', function() {
    // --- General UI Interactions ---
    const toastEl = document.getElementById('toast-notification');
    function showToast(message, type = 'info', duration = 3500) {
        if (!toastEl) return;
        toastEl.textContent = message;
        toastEl.className = 'toast-notification';
        toastEl.classList.add(type, 'show');
        setTimeout(() => { toastEl.classList.remove('show'); }, duration);
    }

    // --- Course Details Modal Logic (The Main Fix) ---
    const courseModal = document.getElementById('course-modal');
    if (courseModal) {
        const closeModalBtn = courseModal.querySelector('.close-modal');
        const modalTabs = courseModal.querySelectorAll('.modal-tab');

        // Function to fetch data and open the modal
        async function openCourseModal(courseCode, semesterKey) {
            if (!courseCode) return;
            
            // Show the modal immediately with a loading state
            courseModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            const modalBody = courseModal.querySelector('.modal-body');
            modalBody.style.opacity = 0.5; // Visual feedback for loading
            document.getElementById('course-modal-title-text').textContent = 'Loading...';

            try {
                // Use the API to get course details
                const response = await fetch(`api/get_course_details.php?course_code=${courseCode}&semester_key=${semesterKey}`);
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Failed to fetch course details');
                }
                const details = await response.json();
                populateModal(details); // Populate with fetched data
            } catch (error) {
                console.error("Modal fetch error:", error);
                showToast(`Error: ${error.message}`, 'error');
                closeCourseModal(); // Close modal on error
            } finally {
                modalBody.style.opacity = 1; // Restore opacity
            }
        }
        
        // Function to populate the modal with data
        function populateModal(details) {
            document.getElementById('course-modal-title-text').textContent = `${details.course_code} - ${details.title}`;
            courseModal.querySelector('.modal-course-code').value = details.course_code;
            courseModal.querySelector('.modal-course-title-input').value = details.title;
            courseModal.querySelector('.modal-course-credit').value = details.credits || 'N/A';
            courseModal.querySelector('.modal-course-section').value = details.section_char || 'N/A';
            courseModal.querySelector('.modal-course-desc').value = details.description || 'No description available.';
            courseModal.querySelector('.modal-course-time').value = details.schedule_time || 'N/A';
            courseModal.querySelector('.modal-course-room').value = details.room || 'N/A';
            
            courseModal.querySelector('.modal-faculty-name').textContent = details.faculty_name || 'TBA';
            courseModal.querySelector('.modal-faculty-email').textContent = details.faculty_info?.email || 'N/A';
            
            const assessmentTableBody = courseModal.querySelector('.modal-assessment-table-body');
            assessmentTableBody.innerHTML = '';
            if (details.assessments && details.assessments.length > 0) {
                details.assessments.forEach(asm => {
                    const row = assessmentTableBody.insertRow();
                    row.insertCell().textContent = asm.type;
                    row.insertCell().textContent = asm.weight;
                    row.insertCell().textContent = asm.score;
                    row.insertCell().textContent = asm.max;
                });
            } else {
                assessmentTableBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No assessment details available.</td></tr>';
            }
            
            // Reset to the first tab
            modalTabs.forEach((tab, index) => tab.classList.toggle('active', index === 0));
            courseModal.querySelectorAll('.tab-content').forEach((content, index) => content.classList.toggle('active', index === 0));
        }

        // Function to close the modal
        function closeCourseModal() {
            courseModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Attach close event listeners
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeCourseModal);
        window.addEventListener('click', (e) => {
            if (e.target === courseModal) closeCourseModal();
        });

        // Attach tab switching event listeners
        modalTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                modalTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                courseModal.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    if(content.id.startsWith(tabId)) {
                        content.classList.add('active');
                    }
                });
            });
        });

        // *** ROBUST EVENT LISTENER FOR ALL "VIEW" BUTTONS AND SCHEDULE ITEMS ***
        document.body.addEventListener('click', function(event) {
            const viewButton = event.target.closest('.view-course-details-btn');
            const scheduleItem = event.target.closest('.schedule-item');

            let courseCode = null;
            let semesterKey = 'spring2024'; // Default semester

            if (viewButton) {
                courseCode = viewButton.closest('tr').dataset.courseCode;
                // Get the semester from the dropdown on the "Registered Courses" page
                const semesterSelect = document.querySelector('#registered-courses .semester-select');
                if (semesterSelect) semesterKey = semesterSelect.value;
            }

            if (scheduleItem) {
                courseCode = scheduleItem.dataset.courseCode;
                 // Get the semester from the dropdown on the "Class Schedule" page
                const semesterSelect = document.querySelector('#class-schedule-page .semester-select');
                if (semesterSelect) semesterKey = semesterSelect.value;
            }

            if (courseCode) {
                openCourseModal(courseCode, semesterKey);
            }
        });
    }

    // Exam Schedule Tabs
    document.querySelectorAll('#exam-schedule .exam-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const targetType = this.dataset.examType;
            document.querySelectorAll('#exam-schedule .exam-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('#exam-schedule .exam-content').forEach(content => {
                content.classList.toggle('active', content.id === `${targetType}-exams`);
            });
        });
    });

    // --- Course Registration Page Logic (The Main Fix) ---
    const registrationPage = document.getElementById('registration-schedule');
    if (registrationPage) {
        // This variable `alreadyRegisteredCourses` is passed from pages/registration-schedule.php
        // It's a global variable within this script's scope.
        
        let tempSelectedCourses = []; // Holds full section objects selected by the user
        
        const mainView = document.getElementById('registration-main-view');
        const sectionsView = document.getElementById('offered-sections-view');
        const sectionsTitle = document.getElementById('offered-sections-title');
        const sectionsContainer = document.getElementById('registration-course-list-container');
        const selectedCoursesContainer = document.getElementById('selected-courses-list');
        const confirmBtnContainer = document.getElementById('final-confirm-button-container');

        // Initial setup
        document.querySelectorAll('.program-course-item').forEach(item => {
            item.addEventListener('click', () => loadOfferedSections(item.dataset.courseCode));
        });
        document.getElementById('back-to-all-courses-btn').addEventListener('click', () => {
            mainView.style.display = 'block';
            sectionsView.style.display = 'none';
        });

        async function loadOfferedSections(courseCode) {
            mainView.style.display = 'none';
            sectionsView.style.display = 'block';
            sectionsContainer.innerHTML = '<p class="no-results">Loading sections...</p>';
            
            try {
                const response = await fetch(`api/get_offered_sections.php?course_code=${courseCode}`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const sections = await response.json();
                if (sections.error) throw new Error(sections.error);
                
                sectionsTitle.textContent = `Offered Sections for ${sections[0]?.course_code || courseCode} - ${sections[0]?.title || ''}`;
                displayOfferedSections(sections);
            } catch (error) {
                console.error("Fetch error:", error);
                sectionsContainer.innerHTML = `<p class="no-results">Could not load sections. Error: ${error.message}</p>`;
            }
        }

        function displayOfferedSections(sections) {
            sectionsContainer.innerHTML = '';
            if (!sections || sections.length === 0) {
                sectionsContainer.innerHTML = '<p class="no-results">No sections available for this course.</p>';
                return;
            }

            sections.forEach(section => {
                const card = document.createElement('div');
                card.className = 'registration-course-card';

                const isFull = section.enrolled >= section.capacity;
                const isAlreadySelected = tempSelectedCourses.some(c => c.id === section.id);
                // Check if the same course (regardless of section) is already selected or registered
                const isCourseTaken = tempSelectedCourses.some(c => c.course_code === section.course_code) || 
                                      (typeof alreadyRegisteredCourses !== 'undefined' && alreadyRegisteredCourses.some(c => c.course_code === section.course_code));
                
                let buttonHTML = '';
                if (isAlreadySelected) {
                    buttonHTML = `<button class="btn btn-sm btn-warning" disabled><i class="fas fa-check-circle"></i> Selected</button>`;
                } else if (isCourseTaken) {
                    buttonHTML = `<button class="btn btn-sm btn-secondary" disabled title="You have already selected or registered for this course."><i class="fas fa-ban"></i> Course Taken</button>`;
                } else if (isFull) {
                    buttonHTML = `<button class="btn btn-sm btn-danger" disabled><i class="fas fa-times-circle"></i> Full</button>`;
                } else {
                    buttonHTML = `<button class="btn btn-sm btn-success add-course-btn"><i class="fas fa-plus-circle"></i> Add to Selection</button>`;
                }

                card.innerHTML = `
                    <h4>${section.course_code} - Section ${section.section_char}</h4>
                    <p><i class="fas fa-clock"></i> ${section.schedule_time}</p>
                    <p><i class="fas fa-map-marker-alt"></i> Room: ${section.room}</p>
                    <p><i class="fas fa-chalkboard-teacher"></i> Faculty: ${section.faculty_name}</p>
                    <p><i class="fas fa-users"></i> Enrollment: <span class="enrollment-status">${section.enrolled}/${section.capacity}</span></p>
                    ${buttonHTML}
                `;

                if (!isFull && !isAlreadySelected && !isCourseTaken) {
                    card.querySelector('.add-course-btn').addEventListener('click', () => handleAddToSelection(section));
                }
                sectionsContainer.appendChild(card);
            });
        }
        
        function handleAddToSelection(section) {
            tempSelectedCourses.push(section);
            showToast(`${section.course_code} added to your selection.`, 'info');
            renderSelectedCoursesView();
            // Refresh the offered sections view to update button states
            loadOfferedSections(section.course_code);
        }
        
        function handleRemoveFromSelection(sectionId) {
            const course = tempSelectedCourses.find(c => c.id === sectionId);
            const courseCode = course.course_code;
            tempSelectedCourses = tempSelectedCourses.filter(c => c.id !== sectionId);
            showToast(`${course.course_code} removed from selection.`, 'warning');
            renderSelectedCoursesView();
            // If the user is currently viewing sections for the removed course, refresh the view
            if(sectionsView.style.display === 'block' && sectionsTitle.textContent.includes(courseCode)) {
                loadOfferedSections(courseCode);
            }
        }

        function renderSelectedCoursesView() {
            selectedCoursesContainer.innerHTML = '';
            confirmBtnContainer.innerHTML = '';

            if (tempSelectedCourses.length === 0) {
                selectedCoursesContainer.innerHTML = '<p class="no-results">No courses selected yet. Add courses from the list below.</p>';
                return;
            }

            tempSelectedCourses.forEach(course => {
                const card = document.createElement('div');
                card.className = 'registration-course-card';
                card.innerHTML = `
                    <h4>${course.course_code} - ${course.title} (Section ${course.section_char})</h4>
                    <p><i class="fas fa-clock"></i> ${course.schedule_time}</p>
                    <p><i class="fas fa-coins"></i> Credits: ${course.credits}</p>
                    <button class="btn btn-sm btn-danger remove-temp-course-btn" style="align-self: flex-end;"><i class="fas fa-times-circle"></i> Remove</button>
                `;
                card.querySelector('.remove-temp-course-btn').addEventListener('click', () => handleRemoveFromSelection(course.id));
                selectedCoursesContainer.appendChild(card);
            });
            
            confirmBtnContainer.innerHTML = `<button class="btn btn-success" id="final-confirm-registration-btn"><i class="fas fa-check-circle"></i> Confirm Registration</button>`;
            document.getElementById('final-confirm-registration-btn').addEventListener('click', handleFinalizeRegistration);
        }
        
        async function handleFinalizeRegistration() {
            if (tempSelectedCourses.length === 0) {
                showToast('Please select at least one course.', 'error');
                return;
            }
            const confirmBtn = document.getElementById('final-confirm-registration-btn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            const sectionIds = tempSelectedCourses.map(c => c.id);
            
            try {
                const response = await fetch('api/finalize_registration.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ section_ids: sectionIds })
                });
                
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'An unknown error occurred.');
                }
                
                showToast(result.message, 'success', 5000);
                // Redirect after a short delay to allow the user to read the message
                setTimeout(() => {
                    window.location.href = 'index.php?page=registered-courses';
                }, 1500);

            } catch (error) {
                console.error("Finalize error:", error);
                showToast(`Registration Failed: ${error.message}`, 'error', 5000);
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Registration';
            }
        }

        // Initial render for the selected courses section
        renderSelectedCoursesView();
    }
});