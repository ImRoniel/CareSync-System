<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Add New User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E8949;
            --primary-dark: #245033;
            --text-dark: #111814;
            --text-medium: #2E603D;
            --bg-white: #FFFFFF;
            --bg-light: #f5f7f9;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .role-selection {
            text-align: center;
        }

        .section-title {
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .section-title p {
            font-size: 1.125rem;
            color: var(--text-medium);
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .back-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .role-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 35px 25px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            text-align: center;
            border: 2px solid transparent;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
            text-decoration: none;
            color: inherit;
        }

        .role-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: rgba(46, 137, 73, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary);
            font-size: 1.8rem;
        }

        .role-card h3 {
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 1.4rem;
        }

        .role-description {
            color: var(--text-medium);
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            gap: 8px;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .admin-card {
            border-color: #AD5057;
        }

        .admin-card:hover {
            border-color: #AD5057;
        }

        .admin-card .role-icon {
            background-color: rgba(173, 80, 87, 0.1);
            color: #AD5057;
        }

        .admin-card h3 {
            color: #AD5057;
        }

        @media (max-width: 768px) {
            .roles-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <a href="../Admin_Dashboard1.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>

    <div class="container">
        <section class="role-selection">
            <div class="section-title">
                <h2>Add New User</h2>
                <p>Select the role for the new user you want to add</p>
            </div>
            
            <div class="roles-grid">
                <!-- Admin Card -->
                <a href="sign_up.php?role=admin" class="role-card admin-card">
                    <div class="role-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Add Administrator</h3>
                    <p class="role-description">Create a new administrator account with full system access and management privileges</p>
                    <div class="btn btn-primary">Add Administrator</div>
                </a>
                
                <!-- Doctor Card -->
                <a href="sign_up.php?role=doctor" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Add Doctor</h3>
                    <p class="role-description">Create a new doctor account for managing appointments and prescriptions</p>
                    <div class="btn btn-primary">Add Doctor</div>
                </a>
                
                <!-- Secretary Card -->
                <a href="sign_up.php?role=secretary" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Add Secretary</h3>
                    <p class="role-description">Create a new secretary account for managing appointments and records</p>
                    <div class="btn btn-primary">Add Secretary</div>
                </a>

                <!-- Patient Card -->
                <a href="sign_up.php?role=patient" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <h3>Add Patient</h3>
                    <p class="role-description">Create a new patient account for appointment booking and medical records</p>
                    <div class="btn btn-primary">Add Patient</div>
                </a>
            </div>
        </section>
    </div>
</body>
</html>