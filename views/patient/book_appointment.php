<?php
// Include your existing session file
require_once __DIR__ . '/../../controllers/auth/session.php';

// Check if user is logged in as patient using your existing session system
if ($_SESSION['role'] !== 'patient') {
    header("Location: /Caresync-System/login/login.php");
    exit();
}

// Include header if you have one
// include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - CareSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showSection('appointment-booking')">
                                <i class="fas fa-calendar-plus"></i> Book Appointment
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('my-appointments')">
                                <i class="fas fa-list-alt"></i> My Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('profile')">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                    </ul>
                    
                    <div class="mt-3 p-3 border-top">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0"><?= htmlspecialchars($_SESSION['name']) ?></h6>
                                <small class="text-muted">Patient</small>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Welcome Section -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Patient Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/Caresync-System/controllers/auth/logout.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

                <!-- Dynamic Content Sections -->
                <div id="appointment-booking" class="content-section active">
                    <?php include '../views/patient/book_appointment.php'; ?>
                </div>

                <div id="my-appointments" class="content-section">
                    <!-- This will show the same appointments as in book_appointment.php -->
                    <?php 
                    // You can include the same appointments table here or create a separate view
                    echo '<div class="alert alert-info">Appointments are managed in the Book Appointment section.</div>';
                    ?>
                </div>

                <div id="profile" class="content-section">
                    <div class="card">
                        <div class="card-header">
                            <h2>My Profile</h2>
                        </div>
                        <div class="card-body">
                            <?php
                            // Display patient profile information
                            $user_id = $_SESSION['user_id'];
                            $profile_sql = "SELECT u.name, u.email, u.created_at, p.phone, p.address, p.age, p.gender, p.blood_type 
                                          FROM users u 
                                          JOIN patients p ON u.id = p.user_id 
                                          WHERE u.id = ?";
                            $stmt = $conn->prepare($profile_sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $profile_result = $stmt->get_result();
                            
                            if ($profile_result->num_rows > 0) {
                                $profile = $profile_result->fetch_assoc();
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <?= htmlspecialchars($profile['name']) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
                                        <p><strong>Phone:</strong> <?= htmlspecialchars($profile['phone'] ?? 'Not provided') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Age:</strong> <?= $profile['age'] ?? 'Not provided' ?></p>
                                        <p><strong>Gender:</strong> <?= $profile['gender'] ?? 'Not provided' ?></p>
                                        <p><strong>Blood Type:</strong> <?= $profile['blood_type'] ?? 'Not provided' ?></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <p><strong>Address:</strong> <?= htmlspecialchars($profile['address'] ?? 'Not provided') ?></p>
                                        <p><strong>Member Since:</strong> <?= date('F j, Y', strtotime($profile['created_at'])) ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Show selected section
        document.getElementById(sectionId).classList.add('active');
        
        // Update active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }
    </script>
</body>
</html>