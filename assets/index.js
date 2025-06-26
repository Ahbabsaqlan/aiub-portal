document.addEventListener('DOMContentLoaded', function() {
    // --- Global Toast Notification Function ---
    const toastEl = document.getElementById('toast-notification');
    function showToast(message, type = 'info', duration = 3500) {
        if (!toastEl) return;
        toastEl.textContent = message;
        toastEl.className = 'toast-notification';
        toastEl.classList.add(type, 'show');
        setTimeout(() => { toastEl.classList.remove('show'); }, duration);
    }
    // Make toast globally accessible for page-specific scripts that might need it
    window.showToast = showToast;

    // --- Notification Dropdown Toggle ---
    const notificationIcon = document.querySelector('.notification-icon');
    if (notificationIcon) {
        const notificationDropdown = notificationIcon.querySelector('.notification-dropdown');
        notificationIcon.addEventListener('click', function(event) {
            event.stopPropagation();
            if (notificationDropdown) {
                notificationDropdown.classList.toggle('show');
            }
        });

        document.addEventListener('click', function(event) {
            if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                if (!notificationIcon.contains(event.target)) {
                    notificationDropdown.classList.remove('show');
                }
            }
        });
    }

    // --- Course Details Modal Logic ---
    const courseModal = document.getElementById('course-modal');
    if (courseModal) {
        const closeModalBtn = courseModal.querySelector('.close-modal');
        const modalTabs = courseModal.querySelectorAll('.modal-tab');

        async function openCourseModal(courseCode, semesterKey) {
            if (!courseCode) return;
            
            courseModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            const modalBody = courseModal.querySelector('.modal-body');
            modalBody.style.opacity = 0.5;
            document.getElementById('course-modal-title-text').textContent = 'Loading...';

            try {
                const response = await fetch(`api/get_course_details.php?course_code=${courseCode}&semester_key=${semesterKey}`);
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Failed to fetch course details');
                }
                const data = await response.json();
                populateModal(data); 
            } catch (error) {
                console.error("Modal fetch error:", error);
                showToast(`Error: ${error.message}`, 'error');
                closeCourseModal();
            } finally {
                modalBody.style.opacity = 1;
            }
        }
        
        function populateModal(data) {
            const { overview, faculty, consulting, notes, assignments, notices, results } = data;
            
            // -- Overview Tab --
            document.getElementById('course-modal-title-text').textContent = `${overview.course_code} - ${overview.title}`;
            courseModal.querySelector('.modal-course-code').value = overview.course_code || '';
            courseModal.querySelector('.modal-course-title-input').value = overview.title || '';
            courseModal.querySelector('.modal-course-credit').value = overview.credits || 'N/A';
            courseModal.querySelector('.modal-course-section').value = overview.section || 'N/A';
            courseModal.querySelector('.modal-course-desc').value = overview.description || 'No description available.';
            courseModal.querySelector('.modal-course-time').value = overview.time || 'N/A';
            courseModal.querySelector('.modal-course-room').value = overview.room || 'N/A';
            
            // -- Faculty Tab --
            courseModal.querySelector('.modal-faculty-name').textContent = faculty.name || 'TBA';
            courseModal.querySelector('.modal-faculty-title-dept').textContent = faculty.title_dept || '';
            courseModal.querySelector('.modal-faculty-email').textContent = faculty.email || 'N/A';
            courseModal.querySelector('.modal-faculty-phone').textContent = faculty.phone || 'N/A';
            courseModal.querySelector('.modal-faculty-office').textContent = faculty.office || 'N/A';
            courseModal.querySelector('.modal-faculty-education').textContent = faculty.education || 'N/A';

            // -- Consulting Tab --
            const consultingSchedule = courseModal.querySelector('#consulting-tab .consulting-schedule');
            consultingSchedule.innerHTML = '';
            if (consulting && consulting.length > 0) {
                consulting.forEach(item => {
                    consultingSchedule.innerHTML += `<div class="consulting-item"><div><strong>${item.day}</strong></div><div>${item.time}</div></div>`;
                });
            } else {
                consultingSchedule.innerHTML = '<p>No consulting hours available.</p>';
            }
            
            // Example for Notices:
            const noticesContainer = courseModal.querySelector('#notices-tab');
            noticesContainer.innerHTML = '<h4><i class="fas fa-bullhorn"></i> Course Notices</h4>';
            if(notices && notices.length > 0) {
                 notices.forEach(notice => {
                    noticesContainer.innerHTML += `<div class="notice-item"><div class="notice-title"><i class="fas fa-exclamation-circle"></i> ${notice.title}</div><div class="notice-date"><i class="fas fa-calendar"></i> ${notice.publish_date}</div><div class="announcement-content">${notice.content}</div></div>`;
                });
            } else {
                noticesContainer.innerHTML += '<p>No notices for this course.</p>';
            }
            
            modalTabs.forEach((tab, index) => tab.classList.toggle('active', index === 0));
            courseModal.querySelectorAll('.tab-content').forEach((content, index) => content.classList.toggle('active', index === 0));
        }


        function closeCourseModal() {
            courseModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeCourseModal);
        window.addEventListener('click', (e) => {
            if (e.target === courseModal) closeCourseModal();
        });

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

        document.body.addEventListener('click', function(event) {
            const viewButton = event.target.closest('.view-course-details-btn');
            const scheduleItem = event.target.closest('.schedule-item[data-course-code]');

            let courseCode = null;
            let semesterKey = 'spring2024';

            if (viewButton) {
                courseCode = viewButton.closest('tr').dataset.courseCode;
                const semesterSelect = document.querySelector('#registered-courses .semester-select');
                if (semesterSelect) semesterKey = semesterSelect.options[semesterSelect.selectedIndex].text.includes('Spring') ? 'spring2024' : 'fall2023';
            }

            if (scheduleItem) {
                courseCode = scheduleItem.dataset.courseCode;
                const semesterSelect = document.querySelector('#class-schedule-page .semester-select');
                if (semesterSelect) semesterKey = semesterSelect.options[semesterSelect.selectedIndex].text.includes('Spring') ? 'spring2024' : 'fall2023';
            }

            if (courseCode) {
                openCourseModal(courseCode, semesterKey);
            }
        });
    }
    
    // --- Academic Results Breakdown Toggle ---
    const resultsPage = document.getElementById('academic-results');
    if (resultsPage) {
        resultsPage.addEventListener('click', function(event) {
            const breakdownBtn = event.target.closest('.view-assessment-btn');
            if (!breakdownBtn) return;
            
            event.preventDefault();
            const targetId = `assessment-${breakdownBtn.dataset.courseId}`;
            const targetDiv = document.getElementById(targetId);

            document.querySelectorAll('.assessment-breakdown').forEach(div => {
                if (div.id !== targetId) {
                    div.style.display = 'none';
                }
            });

            if (targetDiv) {
                const isVisible = targetDiv.style.display === 'block';
                targetDiv.style.display = isVisible ? 'none' : 'block';
                if (!isVisible) {
                    targetDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    const examSchedulePage = document.getElementById('exam-schedule');
    if (examSchedulePage) {
        const examTabs = examSchedulePage.querySelectorAll('.exam-tab');
        const examContents = examSchedulePage.querySelectorAll('.exam-content');

        examTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                examTabs.forEach(t => t.classList.remove('active'));
                examContents.forEach(c => c.classList.remove('active'));

                this.classList.add('active');
                
                const targetContentId = `${this.dataset.examType}-exams`;
                const targetContent = document.getElementById(targetContentId);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
    }

    // --- Registration Page Logic ---
    const registrationPage = document.getElementById('registration-schedule');
    if (registrationPage) {
        let tempSelectedCourses = []; 
        let allOfferedSections = []; 
        
        const mainView = document.getElementById('registration-main-view');
        const sectionsView = document.getElementById('offered-sections-view');
        const sectionsTitle = document.getElementById('offered-sections-title');
        const sectionsContainer = document.getElementById('registration-course-list-container');
        const selectedCoursesContainer = document.getElementById('selected-courses-list');
        const confirmBtnContainer = document.getElementById('final-confirm-button-container');
        const courseSearchInput = document.getElementById('program-course-search-input');
        const sectionSearchInput = document.getElementById('section-search-input');

        document.querySelectorAll('.program-course-item').forEach(item => {
            item.addEventListener('click', () => loadOfferedSections(item.dataset.courseCode));
        });

        document.getElementById('back-to-all-courses-btn').addEventListener('click', () => {
            mainView.style.display = 'block';
            sectionsView.style.display = 'none';
        });
        
        courseSearchInput.addEventListener('input', () => {
            const searchTerm = courseSearchInput.value.toLowerCase();
            document.querySelectorAll('.program-course-item').forEach(item => {
                const itemText = item.textContent || item.innerText;
                item.style.display = itemText.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
        });
        
        sectionSearchInput.addEventListener('input', () => {
            const searchTerm = sectionSearchInput.value.toLowerCase();
            const filtered = allOfferedSections.filter(section => 
                Object.values(section).some(val => 
                    String(val).toLowerCase().includes(searchTerm)
                )
            );
            displayOfferedSections(filtered);
        });

        async function loadOfferedSections(courseCode) {
            mainView.style.display = 'none';
            sectionsView.style.display = 'block';
            sectionSearchInput.value = '';
            sectionsContainer.innerHTML = '<p class="no-results">Loading sections...</p>';
            try {
                const response = await fetch(`api/get_offered_sections.php?course_code=${courseCode}`);
                if (!response.ok) {
                    throw new Error(`Network response was not ok (${response.status})`);
                }
                const sections = await response.json();
                if (sections.error) throw new Error(sections.error);
                
                allOfferedSections = sections;
                sectionsTitle.textContent = `Offered Sections for ${sections[0]?.course_code || courseCode} - ${sections[0]?.title || ''}`;
                displayOfferedSections(sections);
            } catch (error) {
                console.error("Fetch error:", error);
                sectionsContainer.innerHTML = `<p class="no-results" style="color: var(--danger);">Could not load sections. Error: ${error.message}</p>`;
            }
        }

        function displayOfferedSections(sections) {
            sectionsContainer.innerHTML = '';
            if (!sections || sections.length === 0) {
                sectionsContainer.innerHTML = '<p class="no-results">No sections available for this course or filter.</p>';
                return;
            }

            sections.forEach(section => {
                const card = document.createElement('div');
                card.className = 'registration-course-card';
                const isFull = section.enrolled >= section.capacity;
                const isAlreadySelected = tempSelectedCourses.some(c => c.id === section.id);
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
                    card.querySelector('.add-course-btn').addEventListener('click', (event) => handleAddToSelection(event, section));
                }
                sectionsContainer.appendChild(card);
            });
        }
        
        async function handleAddToSelection(event, section) {
            const addBtn = event.currentTarget;
            addBtn.disabled = true;
            addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch('api/validate_and_add_course.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        section_id: section.id,
                        temp_selected_sections: tempSelectedCourses
                    })
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Validation failed.');

                tempSelectedCourses.push(section);
                showToast(`${section.course_code} added to selection.`, 'info');
                renderSelectedCoursesView();
                displayOfferedSections(allOfferedSections);

            } catch (error) {
                showToast(`Error: ${error.message}`, 'error');
                addBtn.disabled = false;
                addBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Add to Selection';
            }
        }
        
        function handleRemoveFromSelection(sectionId) {
            const course = tempSelectedCourses.find(c => c.id === sectionId);
            tempSelectedCourses = tempSelectedCourses.filter(c => c.id !== sectionId);
            showToast(`${course.course_code} removed from selection.`, 'warning');
            renderSelectedCoursesView();
            if(sectionsView.style.display === 'block') {
                 displayOfferedSections(allOfferedSections);
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
                    <p><i class="fas fa-map-marker-alt"></i> Room: ${course.room}</p>
                    <p><i class="fas fa-chalkboard-teacher"></i> Faculty: ${course.faculty_name}</p>
                    <p><i class="fas fa-coins"></i> Credits: ${parseFloat(course.credits).toFixed(1)}</p>
                    <button class="btn btn-sm btn-danger remove-temp-course-btn" style="align-self: flex-end;"><i class="fas fa-times-circle"></i> Remove from Selection</button>
                `;
                card.querySelector('.remove-temp-course-btn').addEventListener('click', () => handleRemoveFromSelection(course.id));
                selectedCoursesContainer.appendChild(card);
            });
            
            confirmBtnContainer.innerHTML = `<button class="btn btn-success" id="final-confirm-registration-btn"><i class="fas fa-check-circle"></i> Confirm Registration & Update Portal</button>`;
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
                if (!response.ok) throw new Error(result.error || 'An unknown error occurred.');
                
                showToast(result.message, 'success', 5000);
                setTimeout(() => {
                    window.location.href = 'index.php?page=registered-courses';
                }, 1500);

            } catch (error) {
                console.error("Finalize error:", error);
                showToast(`Registration Failed: ${error.message}`, 'error', 5000);
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Registration & Update Portal';
            }
        }

        renderSelectedCoursesView();
    }
});