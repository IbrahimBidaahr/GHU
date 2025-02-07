<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
    exit();
}

require_once('../connect.php');
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Initialize variables
$success_msg = ''; // Initialize success message variable
$error_msg = '';   // Initialize error message variable

// Handle form submissions
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Handle single course addition
    if (isset($_POST['add_course'])) {
        $stmt = $pdo->prepare("INSERT INTO course (course_id, course_name, credits) 
                              VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['course_id'],
            $_POST['course_name'],
            $_POST['credits'],
        ]);
        $success_msg = "Course added successfully.";
    }

    // Handle multiple courses from Excel file
    if (isset($_POST['upload_courses'])) {
        $file = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $row) {
            // Assuming the Excel columns are in the order: course_id, course_name, credits
            if (!empty($row['A'])) { // Check if course_id is not empty
                try {
                    $stmt = $pdo->prepare("INSERT INTO course (course_id, course_name, credits) 
                                          VALUES (?, ?, ?)");
                    $stmt->execute([
                        $row['A'], // course_id
                        $row['B'], // course_name
                        $row['C'], // credits
                    ]);
                } catch (PDOException $e) {
                    // Check if the error is a duplicate entry error
                    if ($e->getCode() == 23000) { // Integrity constraint violation
                        $error_msg = "Error: Duplicate entry for Course ID '" . htmlspecialchars($row['A']) . "'. This course already exists.";
                    } else {
                        $error_msg = "Error: " . $e->getMessage();
                    }
                    // Optionally, you can break the loop or continue based on your needs
                    // break; // Uncomment to stop on first error
                }
            }
        }
        if (empty($error_msg)) {
            $success_msg = "Courses added successfully from Excel.";
        }
    }
} catch(Exception $e) {
    $error_msg = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Custom styles for active nav link */
        .nav-link.active {
            color: white; /* Change text color */
        }

        .nav-link:hover {
            color: white; /* Change text color on hover */
        }
    </style>
</head>

<body class="bg-light">
<header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="text-center">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="signup.php">Create Users</a></li>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Add Student/Teacher</a></li>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_courses.php' ? 'active' : ''; ?>" href="manage_courses.php">Courses</a></li>
                            <li class="nav-item"><a class="nav-link" href="v-students.php">View Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="v-teachers.php">View Teachers</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2 class="text-center mt-4">Manage Courses</h2>

        <?php if($success_msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if($error_msg): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- Form for adding a single course -->
        <div class="form-section mt-4">
            <h4>Add Course</h4>
            <form method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label class="form-label">Course ID</label>
                    <input type="text" name="course_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Credits</label>
                    <input type="number" name="credits" class="form-control" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" name="add_course">Add Course</button>
                </div>
            </form>
        </div>

        <!-- Form for uploading courses from Excel -->
        <div class="form-section mt-4">
            <h4>Upload Courses from Excel</h4>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Select Excel File</label>
                    <input type="file" name="file" class="form-control" accept=".xls,.xlsx" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" name="upload_courses">Upload Courses</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Form validation script -->
    <script>
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
    </script>
</body>
</html>