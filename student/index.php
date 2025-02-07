<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .hero-section {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #4158d0, #c850c0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .quick-actions {
            margin-top: 2rem;
        }
        .action-btn {
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 0.5rem;
            display: inline-block;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .stats-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
                width:100%;
            }
            .feature-card {
                margin-bottom: 1rem;
                width:100%;
            }
        }
    </style>
</head>

<body class="bg-light">
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="text-center">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark ">
                <div class="container-fluid text-center">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                           <!--<li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>-->
                            <li class="nav-item"><a class="nav-link" href="report.php">Report Section</a></li>
                            <li class="nav-item"><a class="nav-link" href="account.php">My Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="hero-section">
        <div class="container text-center">
            <h2 class="display-4 mb-4">Welcome to Your Dashboard</h2>
            <p class="lead">Manage attendance efficiently and effectively</p>
            <div class="quick-actions">
               <!-- <a href="students.php" class="action-btn btn btn-light">-->
                    <i class="fas fa-users me-2"></i>View Students
                </a>
                <a href="report.php" class="action-btn btn btn-outline-light">
                    <i class="fas fa-chart-bar me-2"></i>Generate Reports
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-user-graduate feature-icon"></i>
                    <h3>Total Students</h3>
                    <h2>150</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>Classes Today</h3>
                    <h2>8</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3>Attendance Rate</h3>
                    <h2>95%</h2>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Feature Cards -->
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-qrcode feature-icon"></i>
                    <h3>Quick Attendance</h3>
                    <p>Take attendance quickly using QR codes or student IDs</p>
                    <a href="#" class="btn btn-primary">Take Attendance</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-file-alt feature-icon"></i>
                    <h3>Reports</h3>
                    <p>Generate and download detailed attendance reports</p>
                    <a href="report.php" class="btn btn-primary">View Reports</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-cog feature-icon"></i>
                    <h3>Settings</h3>
                    <p>Customize your dashboard and notification preferences</p>
                    <a href="account.php" class="btn btn-primary">Manage Settings</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>