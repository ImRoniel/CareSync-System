<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Choose Your Role</title>
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
            max-width: 1200px;
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

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .role-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 40px 30px;
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(46, 137, 73, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary);
            font-size: 2rem;
        }

        .role-card h3 {
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .role-description {
            color: var(--text-medium);
            margin-bottom: 20px;
            line-height: 1.6;
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

        .login-link {
            margin-top: 30px;
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
    <div class="container">
        <section class="role-selection">
            <div class="section-title">
                <h2>Choose Your Role</h2>
                <p>Select your role to get started with CareSync</p>
            </div>
            
            <div class="roles-grid">
                <!-- Patient Card -->
                <a href="signup.php?role=patient" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <h3>I Am a Patient</h3>
                    <p class="role-description">Book appointments, view prescriptions, track your bills, and health records</p>
                    <div class="btn btn-primary">Continue as Patient</div>
                </a>
                
                Doctor Card
                <a href="signup.php?role=doctor" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>I Am a Doctor</h3>
                    <p class="role-description">View your schedules, patient details, and billing updates</p>
                    <div class="btn btn-primary">Continue as Doctor</div>
                </a>
                
                <!-- Secretary Card -->
                <a href="signup.php?role=secretary" class="role-card">
                    <div class="role-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>I Am a Secretary</h3>
                    <p class="role-description">Manage appointments, handle patients records, and digitize prescription</p>
                    <div class="btn btn-primary">Continue as Secretary</div>
                </a>
            </div>

            <div class="login-link">
                Already have an account? <a href="../login/login.php">Log in here</a>
            </div>
        </section>
    </div>
</body>
</html>