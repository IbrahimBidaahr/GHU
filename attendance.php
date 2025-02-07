<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php');

$att_msg = "";
$error_msg = "";

try {
    if (isset($_POST['mark_attendance'])) {
        // Retrieve form data
        $attendance_date = $_POST['attendance_date'];
        $course = $_POST['course'];
        $batch = $_POST['batch'];
        $department = $_POST['department'];
        $semester = $_POST['semester'];
        $stat_ids = $_POST['stat_id'];
        $statuses = $_POST['st_status'];

        foreach ($stat_ids as $index => $stat_id) {
            $status = $statuses[$index];
            $query = "INSERT INTO attendance (stat_id, course, stat_date, st_status) 
                     VALUES ('$stat_id', '$course', '$attendance_date', '$status')";
            if (!mysql_query($query)) {
                throw new Exception("Error inserting attendance for student ID $stat_id: " . mysql_error());
            }
        }

        $att_msg = "🎉 Attendance marked successfully!";
    }
} catch (Exception $e) {
    $error_msg = "❌ " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Confetti CSS -->
    <link href="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4158d0;
            --secondary-color: #c850c0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #333;
        }

        .attendance-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .search-form {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .btn-gradient {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .attendance-table {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .status-present, .status-absent {
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .status-present {
            background: #d4edda;
            color: #155724;
        }

        .status-absent {
            background: #f8d7da;
            color: #721c24;
        }

        .form-check-input:checked ~ .status-present {
            background: #28a745;
            color: white;
        }

        .form-check-input:checked ~ .status-absent {
            background: #dc3545;
            color: white;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .attendance-container {
                padding: 1rem;
            }

            .search-form {
                padding: 1rem;
            }

            .table-responsive {
                margin: 0 -0.5rem;
            }

            .attendance-table {
                font-size: 0.875rem;
            }

            .form-check {
                margin: 0.5rem 0;
            }

            .form-check-input {
                width: 1.2rem;
                height: 1.2rem;
            }

            .d-flex.justify-content-center.gap-4 {
                flex-direction: column;
                gap: 0.5rem !important;
            }

            .table > :not(caption) > * > * {
                padding: 0.5rem;
            }

            .status-present, .status-absent {
                display: block;
                width: 100%;
                text-align: center;
                margin: 0.2rem 0;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="text-center h3 mb-3">🎓 Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="students.php">👨‍🎓 Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="teachers.php">👩‍🏫 Faculties</a></li>
                            <li class="nav-item"><a class="nav-link active" href="attendance.php">📅 Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="report.php">📊 Report</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">🚪 Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="attendance-container">
            <!-- Messages -->
            <?php if($att_msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $att_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Search Form -->
            <div class="search-form">
                <h3 class="text-center mb-4">🔍 Search Students</h3>
                <form method="post" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="batch" name="batch" required>
                                <label for="batch">Batch (e.g., 2024)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="department" name="department" required>
                                <label for="department">Department (e.g., CSE)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="semester" name="semester" required>
                                <label for="semester">Semester (e.g., 2)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="course" name="course" required>
                                    <option value="CS112">Communication Skills</option>
                                    <option value="IB100">Introduction to Business</option>
                                    <option value="SBU106">Introduction to C Programming</option>
                                    <option value="IWB108">Introduction to Web Design</option>
                                    <option value="P107">Python</option>
                                </select>
                                <label for="course">Select Course</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="search" class="btn btn-gradient px-4 py-2">
                            <i class="fas fa-search me-2"></i>Search Students
                        </button>
                    </div>
                </form>
            </div>

            <!-- Attendance Table -->
            <?php if(isset($_POST['search'])): ?>
                <?php
                $batch = $_POST['batch'];
                $department = $_POST['department'];
                $semester = $_POST['semester'];

                $students_query = mysql_query("SELECT st_id, st_name, st_dept, st_batch, st_sem 
                    FROM students 
                    WHERE st_batch = '$batch' AND st_dept = '$department' AND st_sem = '$semester'");

                if (mysql_num_rows($students_query) > 0):
                ?>
                <div class="attendance-section mt-4">
                    <form action="" method="post" class="needs-validation" novalidate>
                        <!-- Hidden fields -->
                        <input type="hidden" name="batch" value="<?php echo $batch; ?>">
                        <input type="hidden" name="department" value="<?php echo $department; ?>">
                        <input type="hidden" name="semester" value="<?php echo $semester; ?>">
                        <input type="hidden" name="course" value="<?php echo $_POST['course']; ?>">

                        <!-- Date Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6 mx-auto">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="attendance_date" 
                                           name="attendance_date" required
                                           max="<?php echo date('Y-m-d'); ?>">
                                    <label for="attendance_date">📅 Attendance Date</label>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div class="table-responsive attendance-table">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Reg. No.</th>
                                        <th>Name</th>
                                        <th>Sem</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 0;
                                    while ($student = mysql_fetch_array($students_query)):
                                    ?>
                                    <tr class="align-middle">
                                        <td>
                                            <?php echo $student['st_id']; ?>
                                            <input type="hidden" name="stat_id[]" 
                                                   value="<?php echo $student['st_id']; ?>">
                                        </td>
                                        <td><?php echo $student['st_name']; ?></td>
                                        <td><?php echo $student['st_sem']; ?></td>
                                        <td>
                                            <div class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attendance-checkbox" 
                                                           type="checkbox" 
                                                           name="st_status[<?php echo $i; ?>]" 
                                                           value="Present"
                                                           id="present_<?php echo $i; ?>"
                                                           checked>
                                                    <label class="form-check-label status-present" 
                                                           for="present_<?php echo $i; ?>">
                                                        <i class="fas fa-check-circle me-1"></i>Present
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    $i++;
                                    endwhile;
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" name="mark_attendance" class="btn btn-gradient btn-lg">
                                <i class="fas fa-save me-2"></i>Save Attendance
                            </button>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No students found for the specified criteria.
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Confetti JS -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <!-- Custom Scripts -->
    <script>
        // Confetti animation
        function celebrate() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }

        // Show confetti on successful attendance marking
        <?php if($att_msg): ?>
            celebrate();
        <?php endif; ?>

        // Prevent future dates in date picker
        document.getElementById('attendance_date').max = new Date().toISOString().split('T')[0];

        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>