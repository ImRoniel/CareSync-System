<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Streamline Your Clinic Operations</title>
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
            --bg-light: #CFCFCF;      
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
        
        html {
            scroll-behavior: auto;
        }
        
        body {
            background-color: var(--bg-white);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        h1 { font-size: 3.5rem; }
        h2 { font-size: 2.5rem; }
        h3 { font-size: 2rem; }
        h4 { font-size: 1.5rem; }
        
        p {
            margin-bottom: 1.5rem;
            font-size: 1.125rem;
            color: var(--text-medium);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .section-title h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
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
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-large {
            padding: 16px 40px;
            font-size: 1.125rem;
        }

        .role-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 50px 40px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            text-align: center;
            border: 2px solid transparent;
            cursor: pointer;
        }
        
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        
        .role-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: rgba(46, 96, 61, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: var(--primary);
            font-size: 2.5rem;
        }
        
        .role-card h3 {
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 1.75rem;
        }
        
        .role-description {
            color: var(--text-medium);
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        header {
            background-color: var(--bg-white);
            box-shadow: var(--shadow-sm);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary);
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .logo:hover {
            opacity: 0.8;
        }
        
        .logo-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .nav-links {
            display: flex;
            gap: 32px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-actions {
            display: flex;
            gap: 16px;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .mobile-menu-btn:hover {
            color: var(--primary);
        }
        
        .hero {
            padding: 160px 0 100px;
            background: var(--primary); 
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" preserveAspectRatio="none"><path fill="%23ffffff" fill-opacity="0.03" d="M0,0 L1000,1000 L0,1000 Z"></path></svg>');
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .hero-actions {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .hero .btn-secondary {
            border-color: white;
            color: white;
        }
        
        .role-selection {
            padding: 100px 0;
            background-color: var(--bg-light);
        }
        
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .features {
            padding: 100px 0;
            background-color: var(--bg-light);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .feature-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 40px 30px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            text-align: center;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(46, 96, 61, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: var(--primary);
            font-size: 2rem;
        }
        
        .feature-card h3 {
            margin-bottom: 16px;
            color: var(--primary);
        }
        
        .pain-points {
            padding: 100px 0;
            background-color: var(--bg-white);
        }
        
        .points-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .point-card {
            display: flex;
            gap: 20px;
            padding: 30px;
            background-color: var(--bg-light);
            border-radius: var(--radius-lg);
            border-left: 4px solid var(--primary);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .point-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .point-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(46, 96, 61, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
        }
        
        .point-content h3 {
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .solutions {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--bg-light) 0%, var(--bg-white) 100%);
        }
        
        .solutions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .solution-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .solution-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .solution-image {
            height: 200px;
            background: var(--primary); 
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .solution-content {
            padding: 30px;
        }
        
        .solution-content h3 {
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .cta {
            padding: 100px 0;
            background: var(--primary); 
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 2.5rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .cta .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: none;
        }
        
        footer {
            background-color: var(--primary-dark); 
            color: var(--text-light);
            padding: 80px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .footer-column h3 {
            color: white;
            margin-bottom: 24px;
            font-size: 1.25rem;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 12px;
        }
        
        .footer-column ul li a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .footer-column p {
            color: var(--text-light);
        }
        
        .copyright p {
            color: var(--text-light);
        }
        
        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .social-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        @media (max-width: 1024px) {
            h1 { font-size: 3rem; }
            h2 { font-size: 2.25rem; }
        }
        
        @media (max-width: 768px) {
            .header-content {
                padding: 15px 0;
            }
            
            .nav-links, .nav-actions {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                padding: 140px 0 80px;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero-actions {
                justify-content: center;
            }
            
            .roles-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .role-card {
                padding: 40px 30px;
            }
            
            .features, .pain-points, .solutions, .cta, .role-selection {
                padding: 80px 0;
            }
        }
        
        @media (max-width: 480px) {
            h1 { font-size: 2rem; }
            h2 { font-size: 1.75rem; }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .btn {
                padding: 12px 24px;
            }
            
            .role-card {
                padding: 30px 20px;
            }
            
            .role-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <img src="assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a href="#features">Features</a>
                    <a href="#problems">Problems Solved</a>
                    <a href="#solutions">How It Works</a>
                    <a href="#contact">Contact</a>
                </nav>
                
                <div class="nav-actions">
                    <a href="login/login.php" class="btn btn-primary">Sign In</a>
                    <a href="signup/role_selection.php" class="btn btn-primary">Sign Up</a>
                </div>
                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Streamline Your Clinic Operations</h1>
                <p>CareSync is a comprehensive clinic management system that integrates patient scheduling, digital prescriptions, and billing into one seamless platform. Save time, reduce errors, and improve patient care.</p>
                <div class="hero-actions">
                    <a href="#features" class="btn btn-primary btn-large">Learn More</a>
                    <a href="signup/signup.php" class="btn btn-secondary">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>Everything you need to manage your clinic efficiently</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Appointment Scheduling</h3>
                    <p>Patients book online, reducing phone calls. Automated system prevents double-booking and saves staff time.</p>
                </div>
                
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Queue Management</h3>
                    <p>Organized patient flow with clear queueing system. Patients know their turn, reducing wait times and frustration.</p>
                </div>
                
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <h3>Digital Prescriptions</h3>
                    <p>Clear, standardized prescriptions stored digitally. Patients can access them anytime, preventing loss or misinterpretation.</p>
                </div>
                
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3>Automated Billing</h3>
                    <p>Reduce manual errors in billing. Track payment status clearly and improve financial management.</p>
                </div>
                
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Centralized Records</h3>
                    <p>All patient data in one secure system. Easy retrieval of records for returning patients.</p>
                </div>
                
                <div class="feature-card" onclick="location.href='#features'">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics & Reports</h3>
                    <p>Gain insights into clinic performance with detailed reports and analytics dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pain-points" id="problems">
        <div class="container">
            <div class="section-title">
                <h2>Problems We Solve</h2>
                <p>Addressing the biggest challenges in clinic management</p>
            </div>
            
            <div class="points-container">
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="point-content">
                        <h3>Manual Appointment Scheduling</h3>
                        <p>Patients calling or walking in leads to long wait times and inefficient staff utilization.</p>
                    </div>
                </div>
                
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="point-content">
                        <h3>Unorganized Patient Flow</h3>
                        <p>No clear queueing system causes confusion and frustration for both patients and staff.</p>
                    </div>
                </div>
                
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-file-prescription"></i>
                    </div>
                    <div class="point-content">
                        <h3>Paper-Based Prescriptions</h3>
                        <p>Illegible handwriting leads to errors, and paper prescriptions get lost easily.</p>
                    </div>
                </div>
                
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="point-content">
                        <h3>Billing Inefficiencies</h3>
                        <p>Manual billing is prone to errors and makes tracking payments difficult.</p>
                    </div>
                </div>
                
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="point-content">
                        <h3>Poor Records Management</h3>
                        <p>Patient data scattered across different systems makes retrieval difficult.</p>
                    </div>
                </div>
                
                <div class="point-card" onclick="location.href='#solutions'">
                    <div class="point-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="point-content">
                        <h3>Time Wasted by Staff</h3>
                        <p>Doctors and secretaries spend too much time on administrative tasks instead of patient care.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="solutions" id="solutions">
        <div class="container">
            <div class="section-title">
                <h2>How CareSync Works</h2>
                <p>Simple, intuitive workflow for clinics of all sizes</p>
            </div>
            
            <div class="solutions-grid">
                <div class="solution-card" onclick="location.href='role_selection.php'">
                    <div class="solution-image">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="solution-content">
                        <h3>Patient Self-Booking</h3>
                        <p>Patients book appointments online through your website or patient portal, reducing phone calls.</p>
                    </div>
                </div>
                
                <div class="solution-card" onclick="location.href='role_selection.php'">
                    <div class="solution-image">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="solution-content">
                        <h3>Staff Management</h3>
                        <p>Secretaries and doctors manage schedules, approve appointments, and track patient flow in real-time.</p>
                    </div>
                </div>
                
                <div class="solution-card" onclick="location.href='role_selection.php'">
                    <div class="solution-image">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="solution-content">
                        <h3>Clinical Workflow</h3>
                        <p>Doctors access patient records, write digital prescriptions, and update treatment plans seamlessly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="role-selection" id="role-selection">
        <div class="container">
            <div class="section-title">
                <h2>Choose Your Role</h2>
                <p>Select your role to get started with CareSync</p>
            </div>
            
            <div class="roles-grid">
                <div class="role-card" onclick="location.href='signup/signup.php?role=patient'">
                    <div class="role-icon">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <h3>I Am a Patient</h3>
                    <p class="role-description">Book appointments, view prescriptions, track your bills, and health records</p>
                    <a href="signup/signup.php?role=patient" class="btn btn-primary">Continue as Patient</a>
                </div>
                
                <div class="role-card" onclick="location.href='signup/signup.php?role=doctor'">
                    <div class="role-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>I Am a Doctor</h3>
                    <p class="role-description">View your schedules, patient details, and billing updates efficiently and accurately</p>
                    <a href="signup/signup.php?role=doctor" class="btn btn-primary">Continue as Doctor</a>
                </div>
                
                <div class="role-card" onclick="location.href='signup/signup.php?role=secretary'">
                    <div class="role-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>I Am a Secretary</h3>
                    <p class="role-description">Manage appointments, handle patients records, and digitize prescription</p>
                    <a href="signup/signup.php?role=secretary" class="btn btn-primary">Continue as Secretary</a>
                </div>
            </div>
        </div>
    </section>

    <section class="cta" id="contact">
        <div class="container">
            <h2>Ready to Transform Your Clinic?</h2>
            <p>Join hundreds of clinics that have streamlined their operations with CareSync. Contact us to learn more about our solution.</p>
            <div class="hero-actions">
                <a href="contact.php" class="btn btn-primary btn-large">Contact Us</a>
                <a href="signup.php" class="btn btn-secondary">Get Started</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CareSync</h3>
                    <p>A comprehensive clinic management system designed to streamline operations and improve patient care.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Product</h3>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#solutions">How It Works</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#">Documentation</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Team</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">API Reference</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 CareSync Clinic Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Add mobile menu functionality
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            const navActions = document.querySelector('.nav-actions');
            
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            navActions.style.display = navActions.style.display === 'flex' ? 'none' : 'flex';
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>