<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php'); // Ensure this file connects to the database correctly

$att_msg = "";
$error_msg = "";

// Initialize variables
$total = 0;
$present = 0;
$absent = 0;
$attendance_rate = 0;
$error_msg = '';
$success_msg = '';
$show_report = false;

try {
    // Handle report generation
    if (isset($_POST['sr_btn'])) {
        $sr_id = mysqli_real_escape_string($conn, $_POST['sr_id']);
        $course = mysqli_real_escape_string($conn, $_POST['course']); // Changed to 'course'

        // Verify if student exists
        $student_query = "SELECT * FROM students WHERE st_id='$sr_id'";
        $student_result = $conn->query($student_query);
        
        // Check if the query was successful
        if (!$student_result) {
            throw new Exception("Error in query: " . $conn->error);
        }

        if ($student_result->num_rows == 0) {
            throw new Exception("Student not found with Registration No: $sr_id");
        }

        // Fetch attendance data
        $attendance_query = "SELECT COUNT(*) as total_classes, 
                                    SUM(st_status = 'Present') as present_classes 
                             FROM attendance 
                             WHERE stat_id='$sr_id' AND course='$course'";
        $attendance_result = $conn->query($attendance_query);

        if (!$attendance_result) {
            throw new Exception("Error in attendance query: " . $conn->error);
        }

        $attendance_data = $attendance_result->fetch_assoc();
        $total = $attendance_data['total_classes'];
        $present = $attendance_data['present_classes'];
        $absent = $total - $present;
        $attendance_rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        $show_report = true; // Set to true to show the report
    }
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

// Fetch courses for autocomplete
$courses = [];
$course_query = "SELECT course_id, course_name FROM course ORDER BY course_name";
$result = $conn->query($course_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = [
            'id' => $row['course_id'],
            'label' => $row['course_name'], // Display the course name
            'value' => $row['course_id'] // Value to be submitted
        ];
    }
} else {
    throw new Exception("Error fetching courses: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <title>Student Report - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .report-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .stats-card {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            margin: 2rem 0;
            height: 300px;
        }
        .subject-select {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .progress {
            height: 1rem;
            margin: 1rem 0;
        }
        .navbar {
            padding: 1rem 0;
        }
        .navbar-nav {
            margin: 0 auto;
        }
        .nav-item {
            margin: 0 1rem;
        }
        .btn-generate {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            border: none;
            color: white;
            transition: transform 0.3s ease;
        }
        .btn-generate:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        @media (max-width: 768px) {
            .report-container {
                padding: 1rem;
                width:100%;
            }
            .chart-container {
                height: 200px;
                width:100%;
            }
            .nav-item {
                margin: 0.5rem 0;
                width:100%;
            }
        }
        @media print {
            .no-print {
                display: none;
            }
            .report-container {
                box-shadow: none;
            }
        }
        /* Equal size for input fields */
        .form-control {
            width: 100%; /* Ensure full width */
        }
        /* Ensure equal height and alignment for input fields */
        .input-group {
            width: 100%; /* Make the input group take full width */
        }

        .form-floating {
             width: 100%; /* Ensure the floating label input takes full width */
        }

        .form-control {
            height: calc(2.5rem + 2px); /* Set a consistent height for inputs */
            padding: 0.375rem 0.75rem; /* Adjust padding for better alignment */
        }

        .btn-generate {
             height: calc(2.5rem + 2px); /* Match button height with input fields */
        }
    </style>
</head>

<body class="bg-light">
    <header class="bg-dark text-white py-3 no-print">
        <div class="container">
            <h1 class="text-center">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link active" href="report.php">Report Section</a></li>
                            <li class="nav-item"><a class="nav-link" href="account.php">My Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container py-4">
        <div class="report-container">
            <h2 class="text-center mb-4">Student Attendance Report</h2>
            
            <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
            <div class="alert alert-warning alert-dismissible fade show no-print" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="post" class="needs-validation no-print" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="courseSearch" placeholder="Search course..." required>
                            <input type="hidden" name="course" id="course" required>
                            <label for="courseSearch">Select Course</label>
                        </div>  
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Registration Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="sr_id" class="form-control" required>
                                <button type="submit" class="btn btn-generate" name="sr_btn">
                                    <i class="fas fa-search me-2"></i>Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?php if($show_report): ?>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-calendar-check mb-2" style="font-size: 2rem;"></i>
                        <h3>Total Classes</h3>
                        <h2><?php echo $total; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-user-check mb-2" style="font-size: 2rem;"></i>
                        <h3>Present Days</h3>
                        <h2><?php echo $present; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <i class="fas fa-percentage mb-2" style="font-size: 2rem;"></i>
                        <h3>Attendance Rate</h3>
                        <h2><?php echo $attendance_rate; ?>%</h2>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
            </div>

            <div class="progress-section">
                <h4>Attendance Progress</h4>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?php echo $attendance_rate; ?>%">
                        <?php echo $attendance_rate; ?>%
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 no-print">
                <button class="btn btn-generate" onclick="window.print()">
                    <i class="fas fa-download me-2"></i>Download Report
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if($show_report): ?>
    <script>
        // Chart initialization
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?php echo $present; ?>, <?php echo $absent; ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            // Initialize course autocomplete
            var courses = <?php echo json_encode($courses); ?>;

            $("#courseSearch").autocomplete({
                source: courses,
                minLength: 1,
                select: function(event, ui) {
                    $("#course").val(ui.item.id);
                    $(this).val(ui.item.label);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };

            // Clear hidden course input when search is cleared
            $("#courseSearch").on('input', function() {
                if (!$(this).val()) {
                    $("#course").val('');
                }
            });

            // Form validation
            (function () {
                'use strict'
                var forms = document.querySelectorAll('.needs-validation')
                Array.from(forms).forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
            })()
        });
    </script>
</body>
</html>