function showPage(pageId) {
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            document.getElementById(pageId).classList.add('active');
        }

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileNav = document.getElementById('mobileNav');
        
        mobileMenuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
        });
        
        function hideMobileNav() {
            mobileNav.classList.remove('active');
        }
        
        document.getElementById('book-appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Appointment request submitted successfully!');
            closeModal('book-appointment-modal');
            this.reset();
        });
        
        document.getElementById('reschedule-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Appointment rescheduled successfully!');
            closeModal('reschedule-modal');
        });
        
        document.getElementById('request-refill-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Prescription refill request submitted!');
            closeModal('request-refill-modal');
        });
        
        document.getElementById('request-records-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Medical records request submitted successfully!');
            closeModal('request-records-modal');
            this.reset();
        });
        
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Payment processed successfully!');
            closeModal('payment-modal');
        });
        
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Profile updated successfully!');
            closeModal('edit-profile-modal');
        });
                // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Form validation
        document.getElementById('book-appointment-form')?.addEventListener('submit', function(e) {
            const appointmentDate = document.getElementById('appointment-date').value;
            const appointmentTime = document.getElementById('appointment-time').value;
            const selectedDateTime = new Date(appointmentDate + ' ' + appointmentTime);
            
            if (selectedDateTime < new Date()) {
                e.preventDefault();
                alert('Cannot book appointment in the past. Please select a future date and time.');
                return false;
            }
        });
        // document.getElementById('edit-profile-form').addEventListener('submit', async (e) => {
        //     e.preventDefault();
        //     const formData = new FormData(e.target);

        //     const res = await fetch(e.target.action, { method: 'POST', body: formData });
        //     const text = await res.text();
        //     alert('Profile updated successfully!');
        //     location.reload();
        // });
        document.getElementById('edit-profile-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const response = await fetch('/CareSync-System/controllers/admin/UpdateProfileController.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                // Update the name in the dashboard instantly
                document.querySelector('.user-info p').innerText = formData.get('name');
                document.querySelector('.dashboard-header strong').innerText = formData.get('name');
            }
            });
            if (result.status === 'success') {
                location.reload();
            }
            document.getElementById("edit-profile-form").addEventListener("submit", function(e) {
            e.preventDefault(); // Stop normal reload

            const formData = new FormData(this);

            fetch("../../controllers/patiens/UpdatePatientProfile.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                alert("Profile updated successfully!");
                closeModal('edit-profile-modal');
                location.reload(); // refresh to show updated info
                } else {
                alert("Failed to update: " + data.message);
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Something went wrong. Check console for details.");
            });
            });
            document.getElementById("edit-profile-form").addEventListener("submit", function(e) {
                e.preventDefault(); // Prevent page reload

                const formData = new FormData(this);

                fetch("../../controllers/patients/UpdatePatientProfile.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Profile updated successfully!");
                        closeModal('edit-profile-modal');
                        location.reload(); // Refresh to show updated info
                    } else {
                        alert("Failed to update: " + data.message);
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Something went wrong. Check console for details.");
                });
            });

