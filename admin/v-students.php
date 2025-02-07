<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
    exit();
}

require_once('../connect.php');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Initialize search parameters
    $batch = filter_input(INPUT_GET, 'batch', FILTER_SANITIZE_STRING);
    $dept = filter_input(INPUT_GET, 'dept', FILTER_SANITIZE_STRING);
    $sem = filter_input(INPUT_GET, 'sem', FILTER_SANITIZE_STRING);

    // Build query with prepared statements
    $sql = "SELECT st_id, st_name, st_dept, st_batch, st_sem FROM students WHERE 1=1";
    $params = [];

    if ($batch) {
        $sql .= " AND st_batch = ?";
        $params[] = $batch;
    }
    if ($dept) {
        $sql .= " AND st_dept = ?";
        $params[] = $dept;
    }
    if ($sem) {
        $sql .= " AND st_sem = ?";
        $params[] = $sem;
    }

    $sql .= " ORDER BY st_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    // Handle delete request
    if (isset($_GET['delete'])) {
        $st_id = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_STRING);
        $delete_stmt = $pdo->prepare("DELETE FROM students WHERE st_id = ?");
        $delete_stmt->execute([$st_id]);
        header("Location: v-students.php"); // Redirect to avoid resubmission
        exit();
    }

} catch(Exception $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .table-responsive {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .search-section {
                padding: 1rem;
            }
            .table-responsive {
                padding: 0.5rem;
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
                            <li class="nav-item"><a class="nav-link" href="index.php">Add Student/Teacher</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_courses.php">Courses</a></li> <!-- New Link -->
                            <li class="nav-item"><a class="nav-link active" href="v-students.php">View Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="v-teachers.php">View Teachers</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container py-4">
        <div class="search-section">
            <h2 class="text-center mb-4">Search Students</h2>
            <form method="GET" action="v-students.php" class="row g-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="batch" class="form-label">Batch</label>
                        <input type="text" id="batch" name="batch" class="form-control" 
                               value="<?php echo htmlspecialchars($batch); ?>" placeholder="Enter batch year">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dept" class="form-label">Department</label>
                        <input type="text" id="dept" name="dept" class="form-control" 
                               value="<?php echo htmlspecialchars($dept); ?>" placeholder="Enter department">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sem" class="form-label">Semester</label>
                        <input type="text" id="sem" name="sem" class="form-control" 
                               value="<?php echo htmlspecialchars($sem); ?>" placeholder="Enter semester">
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="v-students.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="studentsTable" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Registration No.</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Batch</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['st_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['st_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['st_dept']); ?></td>
                        <td><?php echo htmlspecialchars($student['st_batch']); ?></td>
                        <td><?php echo htmlspecialchars($student['st_sem']); ?></td>
                        <td>
                            <a href="update-student.php?id=<?php echo htmlspecialchars($student['st_id']); ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="v-students.php?delete=<?php echo htmlspecialchars($student['st_id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Quick Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ students"
                }
            });
        });
    </script>
</body>
</html>