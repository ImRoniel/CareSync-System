<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E8949;      
            --primary-dark: #245033;  
            --primary-light: #AD5057; 
            --secondary: #CFCFCF;     
            --accent: #AD5057;        
            --danger: #AD5057;        
            
            --text-dark: #111814;     
            --text-medium: #2E603D;   
            --text-light: #CFCFCF;    
            
            --bg-white: #FFFFFF;      
            --bg-light: #f5f7f9;      
            --bg-gray: #CFCFCF;       
            
            --border-light: #CFCFCF;  
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .signup-container {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            margin: 20px;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .signup-header h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .signup-header p {
            color: var(--text-medium);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
        }
        
        .role-selection {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .role-option {
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .role-option:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        
        .role-option.selected {
            border-color: var(--primary);
            background-color: rgba(46, 137, 73, 0.05);
        }
        
        .role-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .role-option h4 {
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        
        .role-option p {
            font-size: 0.875rem;
            color: var(--text-medium);
        }
        
        .role-fields {
            margin-top: 20px;
            padding: 20px;
            background-color: rgba(46, 137, 73, 0.05);
            border-radius: var(--radius-md);
            display: none;
        }
        
        .role-fields.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .role-fields h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.25rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            gap: 10px;
            font-size: 1rem;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-medium);
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .role-selection {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .signup-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h2>Create Your Account</h2>
            <p>Join CareSync and select your role to get started</p>
        </div>
        
        <form id="signupForm" action="process_signup.php" method="POST">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
            </div>
            
            <div class="form-group">
                <label>Select Your Role</label>
                <div class="role-selection">
                    <div class="role-option" data-role="patient">
                        <div class="role-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h4>Patient</h4>
                        <p>Book appointments and manage health records</p>
                    </div>
                    
                    <div class="role-option" data-role="doctor">
                        <div class="role-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4>Doctor</h4>
                        <p>Manage schedules and patient care</p>
                    </div>
                    
                    <div class="role-option" data-role="secretary">
                        <div class="role-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h4>Secretary</h4>
                        <p>Handle appointments and clinic operations</p>
                    </div>
                </div>
                <input type="hidden" id="role" name="role" value="">
            </div>
            
            <!-- Patient-specific fields -->
            <div class="role-fields" id="patient-fields">
                <h3>Patient Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Enter your address">
                </div>
                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact</label>
                    <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" placeholder="Name and phone number">
                </div>
            </div>
            
            <!-- Doctor-specific fields -->
            <div class="role-fields" id="doctor-fields">
                <h3>Doctor Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="license_no">Medical License Number</label>
                        <input type="text" id="license_no" name="license_no" class="form-control" placeholder="Enter your license number">
                    </div>
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <select id="specialization" name="specialization" class="form-control">
                            <option value="">Select specialization</option>
                            <option value="cardiology">Cardiology</option>
                            <option value="dermatology">Dermatology</option>
                            <option value="pediatrics">Pediatrics</option>
                            <option value="neurology">Neurology</option>
                            <option value="orthopedics">Orthopedics</option>
                            <option value="psychiatry">Psychiatry</option>
                            <option value="surgery">Surgery</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="years_of_experience">Years of Experience</label>
                    <input type="number" id="years_of_experience" name="years_of_experience" class="form-control" min="0" max="50" placeholder="Years">
                </div>
                <div class="form-group">
                    <label for="qualifications">Qualifications</label>
                    <textarea id="qualifications" name="qualifications" class="form-control" rows="3" placeholder="List your qualifications"></textarea>
                </div>
            </div>
            
            <!-- Secretary-specific fields -->
            <div class="role-fields" id="secretary-fields">
                <h3>Secretary Information</h3>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" class="form-control">
                        <option value="">Select department</option>
                        <option value="reception">Reception</option>
                        <option value="billing">Billing</option>
                        <option value="records">Medical Records</option>
                        <option value="appointments">Appointments</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="shift">Preferred Shift</label>
                    <select id="shift" name="shift" class="form-control">
                        <option value="">Select shift</option>
                        <option value="morning">Morning (8AM - 4PM)</option>
                        <option value="afternoon">Afternoon (12PM - 8PM)</option>
                        <option value="evening">Evening (4PM - 12AM)</option>
                        <option value="night">Night (12AM - 8AM)</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleOptions = document.querySelectorAll('.role-option');
            const roleField = document.getElementById('role');
            const patientFields = document.getElementById('patient-fields');
            const doctorFields = document.getElementById('doctor-fields');
            const secretaryFields = document.getElementById('secretary-fields');
            
            // Function to hide all role-specific fields
            function hideAllRoleFields() {
                patientFields.classList.remove('active');
                doctorFields.classList.remove('active');
                secretaryFields.classList.remove('active');
            }
            
            // Function to select a role and show appropriate fields
            function selectRole(role) {
                // Remove selected class from all options
                roleOptions.forEach(option => {
                    option.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                event.currentTarget.classList.add('selected');
                
                // Set the role value
                roleField.value = role;
                
                // Hide all role fields first
                hideAllRoleFields();
                
                // Show the appropriate role fields
                if (role === 'patient') {
                    patientFields.classList.add('active');
                } else if (role === 'doctor') {
                    doctorFields.classList.add('active');
                } else if (role === 'secretary') {
                    secretaryFields.classList.add('active');
                }
            }
            
            // Add click event listeners to role options
            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const role = this.getAttribute('data-role');
                    selectRole(role);
                });
            });
            
            // Form validation
            document.getElementById('signupForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const role = roleField.value;
                
                // Check if passwords match
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return;
                }
                
                // Check if a role is selected
                if (!role) {
                    e.preventDefault();
                    alert('Please select a role!');
                    return;
                }
            });
        });
    </script>
</body>
</html>