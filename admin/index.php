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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Handle form submissions
$success_msg = $error_msg = '';

// Handle template download
if (isset($_GET['download_template'])) {
    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $headers = ['Student ID', 'Name', 'Department', 'Batch', 'Semester'];
    foreach ($headers as $index => $header) {
        $column = chr(65 + $index);
        $sheet->setCellValue($column . '1', $header);
        $sheet->getStyle($column . '1')->getFont()->setBold(true);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Add sample data
    $sampleData = [
        ['2023001', 'Ahmed Ali Abdi', 'Computer Science', '2023', '1'],
        ['2023002', 'Ayan Abdi Hirsi', 'Computer Science', '2023', '1']
    ];

    $row = 2;
    foreach ($sampleData as $data) {
        foreach ($data as $index => $value) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $row, $value);
        }
        $row++;
    }

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="student_template.xlsx"');
    header('Cache-Control: max-age=0');

    // Create writer and save file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Handle student Excel upload
    if (isset($_FILES['file']) && isset($_POST['upload'])) {
        $file = $_FILES['file'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed with error code: ' . $file['error']);
        }

        // Validate file extension
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, ['xlsx', 'xls'])) {
            throw new Exception('Only Excel files (xlsx, xls) are allowed');
        }

        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row and process data
            $inserted = 0;
            $errors = [];

            // Start transaction
            $pdo->beginTransaction();

            // Start from index 1 to skip header row
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row data
                if (count($row) !== 5) {
                    $errors[] = "Row " . ($i + 1) . " has invalid number of columns";
                    continue;
                }

                // Validate required fields
                $isEmpty = false;
                foreach ($row as $index => $field) {
                    if (trim($field) === '') {
                        $errors[] = "Empty field found in row " . ($i + 1) . ", column " . ($index + 1);
                        $isEmpty = true;
                        break;
                    }
                }
                if ($isEmpty) continue;

                try {
                    // Check if student ID already exists
                    $check = $pdo->prepare("SELECT COUNT(*) FROM students WHERE st_id = ?");
                    $check->execute([$row[0]]);
                    if ($check->fetchColumn() > 0) {
                        $errors[] = "Student ID '{$row[0]}' in row " . ($i + 1) . " already exists";
                        continue;
                    }

                    // Insert student data
                    $stmt = $pdo->prepare("INSERT INTO students (st_id, st_name, st_dept, st_batch, st_sem) 
                                         VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute($row)) {
                        $inserted++;
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error in row " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            if ($inserted > 0) {
                $pdo->commit();
                $success_msg = "Successfully uploaded $inserted students. ";
            } else {
                $pdo->rollBack();
                if (empty($errors)) {
                    $error_msg = "No valid data found in the Excel file";
                }
            }

            if (!empty($errors)) {
                $error_msg = "Errors occurred: <br>" . implode("<br>", $errors);
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception('Error processing Excel file: ' . $e->getMessage());
        }
    }

    // Handle single student addition
    if (isset($_POST['std'])) {
        $stmt = $pdo->prepare("INSERT INTO students (st_id, st_name, st_dept, st_batch, st_sem) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['st_id'],
            $_POST['st_name'],
            $_POST['st_dept'],
            $_POST['st_batch'],
            $_POST['st_sem'],
        ]);
        $success_msg = "Student added successfully.";
    }

    // Handle teacher addition
    if (isset($_POST['tcr'])) {
        $stmt = $pdo->prepare("INSERT INTO teachers (tc_id, tc_name, tc_dept, tc_email, tc_course) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['tc_id'],
            $_POST['tc_name'],
            $_POST['tc_dept'],
            $_POST['tc_email'],
            $_POST['tc_course']
        ]);
        $success_msg = "Teacher added successfully.";
    }

    // Handle course addition
    if (isset($_POST['add_course'])) {
        $stmt = $pdo->prepare("INSERT INTO course (course_id, course_name, course_description, credits) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['course_id'],
            $_POST['course_name'],
            $_POST['course_description'],
            $_POST['credits'],
        ]);
        $success_msg = "Course added successfully.";
    }

} catch(Exception $e) {
    $error_msg = "Error: " . $e->getMessage();
}
?>
<!-- ... keep all the PHP code at the top the same ... -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.25rem;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .form-section {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(12, 46, 65, 0.57);
            margin: 2rem 0;
        }
        .navbar {
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .file-upload-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .form-section {
                padding: 1rem;
                width: 100%;
            }
            .navbar a {
                display: block;
                margin: 0.5rem 0;
                width: 100%;
            }
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
                            <li class="nav-item"><a class="nav-link active" href="index.php">Add Student/Teacher</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_courses.php">Courses</a></li>
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
        <?php if($success_msg): ?>
            <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if($error_msg): ?>
            <div class="message error"><?php echo nl2br(htmlspecialchars($error_msg)); ?></div>
        <?php endif; ?>

        <div class="text-center mb-4">
            <div class="btn-group" role="group">
                <a href="#student" class="btn btn-primary">Add Student</a>
                <a href="#teacher" class="btn btn-primary">Add Teacher</a>
                <a href="#upload" class="btn btn-primary">Upload Students</a>
                <a href="#course" class="btn btn-primary">Add Course</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Student Form -->
                <div class="form-section" id="student">
                    <h4 class="text-center mb-4">Add Student's Information</h4>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Registration No.</label>
                            <input type="text" name="st_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="st_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="st_dept" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Batch</label>
                            <input type="text" name="st_batch" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <input type="text" name="st_sem" class="form-control" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="std">Add Student</button>
                        </div>
                    </form>
                </div>

                <!-- Teacher Form -->
                <div class="form-section" id="teacher">
                    <h4 class="text-center mb-4">Add Teacher's Information</h4>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Teacher ID</label>
                            <input type="text" name="tc_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="tc_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="tc_dept" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="tc_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject Name</label>
                            <input type="text" name="tc_course" class="form-control" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="tcr">Add Teacher</button>
                        </div>
                    </form>
                </div>

                <!-- Upload Students Form -->
                <div class="form-section" id="upload">
                    <h4 class="text-center mb-4">Upload Students from Excel</h4>
                    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Select Excel File</label>
                            <input type="file" name="file" class="form-control" accept=".xls,.xlsx" required>
                            <div class="file-upload-info">
                                <strong>Excel file should have these columns in order:</strong>
                                <ul>
                                    <li>Student ID</li>
                                    <li>Name</li>
                                    <li>Department</li>
                                    <li>Batch</li>
                                    <li>Semester</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="upload">Upload Students</button>
                            <a href="?download_template" class="btn btn-outline-secondary">Download Template</a>
                        </div>
                    </form>
                </div>

                <!-- Course Form -->
                <div class="form-section" id="course">
                    <h4 class="text-center mb-4">Add Course</h4>
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
                            <label class="form-label">Course Description</label>
                            <textarea name="course_description" class="form-control" required></textarea>
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
            </div>
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