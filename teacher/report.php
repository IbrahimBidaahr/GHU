<?php
session_start();

if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php');

// Handle AJAX course search
$att_msg = "";
$error_msg = "";

try {
    // Database connection (ensure $mysqli is already defined)
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
    } else {
        throw new Exception("Error fetching courses: " . $mysqli->error);
    }
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

// Handle Excel download
if (isset($_POST['download_excel'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course = $_POST['course'];

    $excel_query = $mysqli->query("
        SELECT 
            a.stat_id, 
            s.st_name, 
            s.st_dept, 
            s.st_batch, 
            COUNT(CASE WHEN a.st_status = 'Present' THEN 1 END) as present_count,
            COUNT(*) as total_count,
            (COUNT(CASE WHEN a.st_status = 'Present' THEN 1 END) / COUNT(*)) * 100 as attendance_percentage
        FROM 
            attendance a 
        JOIN 
            students s ON a.stat_id = s.st_id 
        WHERE 
            a.stat_date BETWEEN '$start_date' AND '$end_date' AND a.course='$course'
        GROUP BY a.stat_id
    ");

    if ($excel_query) {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=attendance_report.csv");
        
        echo "Reg. No.,Name,Department,Batch,Attendance Count,Total Classes,Attendance Percentage\n";
        while ($row = $excel_query->fetch_assoc()) {
            echo "{$row['stat_id']},{$row['st_name']},{$row['st_dept']},{$row['st_batch']},{$row['present_count']},{$row['total_count']},{$row['attendance_percentage']}\n";
        }
        exit();
    }
}

// Handle report generation
if (isset($_POST['sr_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course = $_POST['course'];
    $batch = $_POST['batch'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];

    $all_query = $mysqli->query("
        SELECT 
            a.stat_id, 
            s.st_name, 
            s.st_dept, 
            s.st_batch, 
            COUNT(CASE WHEN a.st_status = 'Present' THEN 1 END) as present_count,
            COUNT(*) as total_count,
            (COUNT(CASE WHEN a.st_status = 'Present' THEN 1 END) / COUNT(*)) * 100 as attendance_percentage
        FROM 
            attendance a 
        JOIN 
            students s ON a.stat_id = s.st_id 
        WHERE 
            a.stat_date BETWEEN '$start_date' AND '$end_date' 
            AND a.course='$course' 
            AND s.st_batch='$batch' 
            AND s.st_dept='$department' 
            AND s.st_sem='$semester'
        GROUP BY a.stat_id
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Reports - Attendance Management System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    
    <style>
    .ui-autocomplete {
        z-index: 9999;
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .ui-menu-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .ui-menu-item:hover {
        background-color: #f8f9fa;
    }
    </style>
</head>

<body class="bg-light">
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
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="teachers.php">Faculties</a></li>
                            <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
                            <li class="nav-item"><a class="nav-link active" href="report.php">Report</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Mass Attendance Report</h3>
                        <form method="post" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                         <input type="text" class="form-control" id="courseSearch" placeholder="Search course..." required>
                                         <input type="hidden" name="course" id="course" required>
                                         <label for="courseSearch">Select Course</label>
                                    </div>  
                                 </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Batch</label>
                                    <input type="text" name="batch" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Semester</label>
                                    <input type="text" name="semester" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="sr_date" class="btn btn-primary">
                                    Generate Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if(isset($_POST['sr_date']) && $all_query): ?>
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Attendance Report Results</h4>
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Reg. No.</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Batch</th>
                                        <th>Attendance Count</th>
                                        <th>Total Classes</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($data = mysqli_fetch_array($all_query)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['stat_id']); ?></td>
                                        <td><?php echo htmlspecialchars($data['st_name']); ?></td>
                                        <td><?php echo htmlspecialchars($data['st_dept']); ?></td>
                                        <td><?php echo htmlspecialchars($data['st_batch']); ?></td>
                                        <td><?php echo $data['present_count']; ?></td>
                                        <td><?php echo $data['total_count']; ?></td>
                                        <td><?php echo round($data['attendance_percentage'], 2); ?>%</td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4">
                            <form method="post">
                                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_POST['start_date']); ?>">
                                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_POST['end_date']); ?>">
                                <input type="hidden" name="course" value="<?php echo htmlspecialchars($_POST['course']); ?>">
                                <button type="submit" name="download_excel" class="btn btn-success">
                                    Download Excel
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
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


        // Initialize DataTable
        $('#reportTable').DataTable({
            pageLength: 25
        });

        // Prevent future dates
        $('input[type="date"]').attr('max', new Date().toISOString().split("T")[0]);

        // Form validation
        $('.needs-validation').on('submit', function(e) {
            if (!$(this)[0].checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });
    </script>
</body>
</html>