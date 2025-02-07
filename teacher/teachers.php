<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php'); // Ensure this file uses mysqli_* functions
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers List - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #4158d0, #c850c0);
        }

        .teacher-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .page-header:hover {
            transform: translateY(-5px);
        }

        .table-container {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        .teacher-card {
            transition: transform 0.3s ease;
        }

        .teacher-card:hover {
            transform: translateY(-5px);
        }

        .department-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
        }

        .email-link {
            color: #4158d0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .email-link:hover {
            color: #c850c0;
        }

        @media (max-width: 768px) {
            .teacher-container {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body class="bg-light">
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
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link active" href="teachers.php">Faculties</a></li>
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
    <div class="container py-4">
        <div class="teacher-container">
            <div class="page-header text-center">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                <h2 class="mb-0">Faculty Members</h2>
            </div>

            <div class="table-responsive table-container">
                <table id="teachersTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Course</th>
                            <!--<th>Actions</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Use mysqli instead of deprecated mysql functions
                        $tcr_query = $mysqli->query("SELECT * FROM teachers ORDER BY tc_id ASC");

                        // Check if the query was successful
                        if ($tcr_query === false) {
                            die("Error executing query: " . $mysqli->error);
                        }

                        while($tcr_data = $tcr_query->fetch_assoc()):
                        ?>
                        <tr class="teacher-card">
                            <td>
                                <span class="fw-bold"><?php echo htmlspecialchars($tcr_data['tc_id']); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($tcr_data['tc_name']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="department-badge">
                                    <?php echo htmlspecialchars($tcr_data['tc_dept']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($tcr_data['tc_email']); ?>" 
                                   class="email-link">
                                    <i class="fas fa-envelope me-2"></i>
                                    <?php echo htmlspecialchars($tcr_data['tc_email']); ?>
                                </a>
                            </td>
                            <td>
                                <i class="fas fa-book me-2 text-success"></i>
                                <?php echo htmlspecialchars($tcr_data['tc_course']); ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Send Message">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
                }
            });
        });
    </script>
</body>
</html>