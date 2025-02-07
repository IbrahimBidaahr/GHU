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
    // Fetch courses for autocomplete
    $courses = [];
    $course_query = "SELECT course_id, course_name FROM course ORDER BY course_name";
    $result = $mysqli->query($course_query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = [
                'id' => $row['course_id'],
                'label' => $row['course_id'] . ' - ' . $row['course_name'],
                'value' => $row['course_id']
            ];
        }
    }

    if (isset($_POST['mark_attendance'])) {
        // Retrieve form data
        $attendance_date = $_POST['attendance_date'];
        $course = $_POST['course'];
        $batch = $_POST['batch'];
        $department = $_POST['department'];
        $semester = $_POST['semester'];
        $stat_ids = $_POST['stat_id'];
        $statuses = $_POST['st_status'];

        // Verify course exists
        $check_course = $mysqli->prepare("SELECT course_id FROM course WHERE course_id = ?");
        $check_course->bind_param("s", $course);
        $check_course->execute();
        $course_result = $check_course->get_result();

        if ($course_result->num_rows === 0) {
            throw new Exception("Selected course does not exist in the system.");
        }

        // Check for duplicate attendance
        $check_duplicate = $mysqli->prepare("SELECT COUNT(*) as count FROM attendance 
                                           WHERE course = ? AND stat_date = ?");
        $check_duplicate->bind_param("ss", $course, $attendance_date);
        $check_duplicate->execute();
        $duplicate_result = $check_duplicate->get_result()->fetch_assoc();

        if ($duplicate_result['count'] > 0) {
            throw new Exception("Attendance for this course and date already exists!");
        }

        // Begin transaction
        $mysqli->begin_transaction();

        try {
            // Prepare the insert statement
            $insert_stmt = $mysqli->prepare("INSERT INTO attendance (stat_id, course, stat_date, st_status) 
                                           VALUES (?, ?, ?, ?)");

            if ($insert_stmt === false) {
                throw new Exception("Error preparing statement: " . $mysqli->error);
            }

            $success_count = 0;
            foreach ($stat_ids as $index => $stat_id) {
                $status = isset($statuses[$index]) ? 'Present' : 'Absent';
                $insert_stmt->bind_param("ssss", $stat_id, $course, $attendance_date, $status);
                
                if (!$insert_stmt->execute()) {
                    throw new Exception("Error inserting attendance for student ID $stat_id");
                }
                $success_count++;
            }

            $mysqli->commit();
            $att_msg = "🎉 Successfully marked attendance for $success_count students!";

        } catch (Exception $e) {
            $mysqli->rollback();
            throw new Exception("Error marking attendance: " . $e->getMessage());
        }

        $insert_stmt->close();
    }
} catch (Exception $e) {
    $error_msg = "❌ " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    
    <style>
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1050;
        }
        .attendance-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-gradient {
            background: linear-gradient(45deg, #007bff, #6610f2);
            border: none;
            color: white;
        }
        .btn-gradient:hover {
            background: linear-gradient(45deg, #0056b3, #520dc2);
            color: white;
        }
        .form-check-input {
            width: 1.5em;
            height: 1.5em;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-success text-white py-3">
        <div class="container">
            <h1 class="text-center h3 mb-3">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="teachers.php">Faculties</a></li>
                            <li class="nav-item"><a class="nav-link active" href="attendance.php">Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Messages -->
        <?php if($att_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $att_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="text-center mb-4">Search Students</h3>
                <form method="post" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="batch" name="batch" required>
                                <label for="batch">Batch (e.g., 2024)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="department" name="department" required>
                                <label for="department">Department (e.g., CSE)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="semester" name="semester" required>
                                <label for="semester">Semester (e.g., 2)</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" name="search" class="btn btn-gradient px-4 py-2">
                            <i class="fas fa-search me-2"></i>Search Students
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
        <?php if(isset($_POST['search'])): ?>
            <?php
            $batch = $_POST['batch'];
            $department = $_POST['department'];
            $semester = $_POST['semester'];

            $stmt = $mysqli->prepare("SELECT st_id, st_name, st_dept, st_batch, st_sem 
                                    FROM students 
                                    WHERE st_batch = ? AND st_dept = ? AND st_sem = ?
                                    ORDER BY st_id");
            $stmt->bind_param("sss", $batch, $department, $semester);
            $stmt->execute();
            $students_query = $stmt->get_result();

            if ($students_query->num_rows > 0):
            ?>
            <div class="card">
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate>
                        <!-- Hidden fields -->
                        <input type="hidden" name="batch" value="<?php echo htmlspecialchars($batch); ?>">
                        <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
                        <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">

                        <!-- Date and Course Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="attendance_date" 
                                           name="attendance_date" required>
                                    <label for="attendance_date">Attendance Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="courseSearch" 
                                           placeholder="Search course..." required>
                                    <input type="hidden" name="course" id="course" required>
                                    <label for="courseSearch">Select Course</label>
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
                                        <th>Department</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $i = 0;
                                while ($student = $students_query->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($student['st_id']); ?>
                                        <input type="hidden" name="stat_id[]" 
                                               value="<?php echo htmlspecialchars($student['st_id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($student['st_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['st_dept']); ?></td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="st_status[<?php echo $i; ?>]" 
                                                   id="present_<?php echo $i; ?>">
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
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No students found for the specified criteria.
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
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

        // Set max date to today for attendance date
        var today = new Date().toISOString().split('T')[0];
        $("#attendance_date").attr('max', today);

        // Form validation
        $('.needs-validation').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Additional validation for course selection
            if ($(this).find('[name="mark_attendance"]').length > 0) {
                if (!$("#course").val()) {
                    e.preventDefault();
                    alert("Please select a valid course from the list");
                    $("#courseSearch").focus();
                    return false;
                }

                // Confirm before submitting attendance
                if (!confirm('Are you sure you want to save the attendance?')) {
                    e.preventDefault();
                    return false;
                }
            }

            $(this).addClass('was-validated');
        });

        // Quick select/deselect all checkboxes
        $('thead').on('click', function() {
            var checkboxes = $('input[type="checkbox"]');
            var allChecked = checkboxes.length === checkboxes.filter(':checked').length;
            checkboxes.prop('checked', !allChecked);
        });

        // Highlight row on checkbox change
        $('input[type="checkbox"]').on('change', function() {
            $(this).closest('tr').toggleClass('table-active', this.checked);
        });

        // Show loading indicator during form submission
        $('form').on('submit', function() {
            if (this.checkValidity()) {
                $(this).find('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>