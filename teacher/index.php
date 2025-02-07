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
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #4158d0, #c850c0);
        }

        .dashboard-container {
            min-height: calc(100vh - 76px);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
            transition: transform 0.3s ease;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
        }

        .feature-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: var(--primary-gradient);
            color: white;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card:hover .feature-icon {
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-card {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .logo-container {
            max-width: 300px;
            margin: 2rem auto;
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-container img {
            width: 100%;
            height: auto;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .feature-card {
                margin-bottom: 1rem;
            }
        }
        

       
    </style>
</head>

<body>
    <!-- Header -->
    <header class="bg-success text-white py-3">
        <div class="container">
            <h1 class="text-center">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="teachers.php">Faculties</a></li>
                            <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
                            
                            <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="dashboard-container py-4">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-card text-center">
                <h2 class="mb-4">Welcome to Attendance Management System</h2>
                <div class="logo-container">
                    <img src="../img/att.png" alt="AMS Logo" class="img-fluid">
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-users mb-2" style="font-size: 2rem;"></i>
                        <h3>Total Students</h3>
                        <h2>500+</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-chalkboard-teacher mb-2" style="font-size: 2rem;"></i>
                        <h3>Total Faculty</h3>
                        <h2>50+</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-chart-line mb-2" style="font-size: 2rem;"></i>
                        <h3>Attendance Rate</h3>
                        <h2>95%</h2>
                    </div>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-qrcode feature-icon"></i>
                        <h4>Quick Attendance</h4>
                        <p>Take attendance quickly using QR codes or student IDs</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-chart-pie feature-icon"></i>
                        <h4>Real-time Analytics</h4>
                        <p>Get instant insights into attendance patterns</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-file-export feature-icon"></i>
                        <h4>Export Reports</h4>
                        <p>Generate and download detailed attendance reports</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>