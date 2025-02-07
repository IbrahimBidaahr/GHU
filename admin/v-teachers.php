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
    
    // Fetch teachers with prepared statement
    $stmt = $pdo->prepare("SELECT * FROM teachers ORDER BY tc_id ASC");
    $stmt->execute();
    $teachers = $stmt->fetchAll();
    
} catch(Exception $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teachers - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .table-container {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .teacher-card {
            transition: transform 0.3s ease;
        }
        .teacher-card:hover {
            transform: translateY(-5px);
        }
        .stats-card {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .teacher-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .table-container {
                padding: 1rem;
            }
            .stats-card {
                margin-bottom: 1rem;
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
                            <li class="nav-item"><a class="nav-link" href="v-students.php">View Students</a></li>
                            <li class="nav-item"><a class="nav-link active" href="v-teachers.php">View Teachers</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container py-4">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-chalkboard-teacher teacher-icon"></i>
                    <h3>Total Teachers</h3>
                    <h2><?php echo count($teachers); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-building teacher-icon"></i>
                    <h3>Departments</h3>
                    <h2><?php echo count(array_unique(array_column($teachers, 'tc_dept'))); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="fas fa-book teacher-icon"></i>
                    <h3>Courses</h3>
                    <h2><?php echo count(array_unique(array_column($teachers, 'tc_course'))); ?></h2>
                </div>
            </div>
        </div>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table id="teachersTable" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($teachers as $teacher): ?>
                    <tr class="teacher-card">
                        <td><?php echo htmlspecialchars($teacher['tc_id']); ?></td>
                        <td>
                            <i class="fas fa-user-tie me-2"></i>
                            <?php echo htmlspecialchars($teacher['tc_name']); ?>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                <?php echo htmlspecialchars($teacher['tc_dept']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($teacher['tc_email']); ?>" class="text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>
                                <?php echo htmlspecialchars($teacher['tc_email']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo htmlspecialchars($teacher['tc_course']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
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
            $('#teachersTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Quick Search:",
                    lengthMenu: "Show _MENU_ teachers per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ teachers"
                },
                dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rtip'
            });
        });
    </script>
</body>
</html>