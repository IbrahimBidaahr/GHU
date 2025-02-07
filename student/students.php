<?php
session_start();

if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Attendance Management System</title>
    
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

        .student-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .search-card {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .search-card:hover {
            transform: translateY(-5px);
        }

        .table-container {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        .student-row {
            transition: all 0.3s ease;
        }

        .student-row:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .btn-search {
            background: white;
            color: #4158d0;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: #f8f9fa;
        }

        .batch-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .student-container {
                padding: 1rem;
            }
            .search-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-dark text-white py-3">
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
                            <li class="nav-item"><a class="nav-link active" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="report.php">Report Section</a></li>
                            <li class="nav-item"><a class="nav-link" href="account.php">My Account</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="student-container">
            <div class="search-card">
                <h3 class="text-center mb-4">
                    <i class="fas fa-search me-2"></i>Search Students by Batch
                </h3>
                <form method="post" class="d-flex justify-content-center gap-3">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <input type="text" name="sr_batch" class="form-control" placeholder="Enter batch year (e.g., 2020)">
                        <button type="submit" name="sr_btn" class="btn btn-search">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <?php if(isset($_POST['sr_btn'])): ?>
            <div class="table-responsive table-container">
                <table id="studentTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Reg. No.</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Batch</th>
                            <th>Semester</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $srbatch = mysql_real_escape_string($_POST['sr_batch']);
                        $all_query = mysql_query("SELECT * FROM students WHERE st_batch = '$srbatch' ORDER BY st_id ASC");
                        
                        while ($data = mysql_fetch_array($all_query)):
                        ?>
                        <tr class="student-row">
                            <td>
                                <span class="fw-bold"><?php echo htmlspecialchars($data['st_id']); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                                    <?php echo htmlspecialchars($data['st_name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($data['st_dept']); ?></td>
                            <td>
                                <span class="batch-badge">
                                    <?php echo htmlspecialchars($data['st_batch']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    Semester <?php echo htmlspecialchars($data['st_sem']); ?>
                                </span>
                            </td>
                            
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#studentTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Quick Search:",
                    lengthMenu: "Show _MENU_ students per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ students"
                }
            });
        });
    </script>
</body>
</html>