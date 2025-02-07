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
    <title>Students List - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Animate.css for animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(45deg, #4158d0, #c850c0);
            --secondary-gradient: linear-gradient(45deg, #ff9a9e, #fad0c4);
        }

        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .student-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(12, 56, 156, 0.1);
            margin-top: 2rem;
            animation: fadeIn 1s ease-in-out;
        }

        .search-card {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .student-row:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        .table-container {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .btn-download {
            background: var(--secondary-gradient);
            border: none;
            color: white;
            transition: transform 0.3s ease;
        }

        .btn-download:hover {
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Enhanced Media Queries */
        @media (max-width: 768px) {
            .student-container {
                padding: 1rem;
            }

            .search-card {
                padding: 1.5rem;
            }

            .input-group {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }

            .btn {
                width: 100%;
                min-width: 150px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-success text-white py-3 shadow-sm">
        <div class="container">
            <h1 class="text-center animate__animated animate__fadeInDown">Attendance Management System</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link active" href="students.php">Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="teachers.php">Faculties</a></li>
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
    <div class="container">
        <div class="student-container">
            <div class="search-card animate__animated animate__fadeInUp">
                <h3 class="text-center">
                    <i class="fas fa-search me-2"></i>Search Students
                </h3>
                <form method="post" class="d-flex flex-column align-items-center gap-3" style="max-width: 600px; margin: auto;">
                    <div class="input-group">
                        <span class="input-group-text">Batch</span>
                        <input type="text" name="sr_batch" class="form-control" placeholder="Enter Batch Year (e.g., 2024)" required>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">Department</span>
                        <input type="text" name="sr_department" class="form-control" placeholder="Enter Department (e.g., CSE)" required>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">Semester</span>
                        <input type="text" name="sr_semester" class="form-control" placeholder="Enter Semester (e.g., 2)" required>
                    </div>
                    <button type="submit" name="sr_btn" class="btn btn-light">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </form>
            </div>

            <?php if(isset($_POST['sr_btn'])): ?>
            <div class="table-responsive table-container animate__animated animate__fadeIn">
                <table id="studentsTable" class="table table-hover align-middle">
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
                        // Use mysqli instead of deprecated mysql functions
                        $srbatch = $mysqli->real_escape_string($_POST['sr_batch']);
                        $srdepartment = $mysqli->real_escape_string($_POST['sr_department']);
                        $srsemester = $mysqli->real_escape_string($_POST['sr_semester']);

                        $query = "SELECT * FROM students WHERE st_batch = '$srbatch' AND st_dept = '$srdepartment' AND st_sem = '$srsemester' ORDER BY st_id ASC";
                        $all_query = $mysqli->query($query);
                        
                        // Check if the query was successful
                        if ($all_query === false) {
                            die("Error executing query: " . $mysqli->error);
                        }

                        while ($data = $all_query->fetch_assoc()):
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
                            <td><?php echo htmlspecialchars($data['st_batch']); ?></td>
                            <td><?php echo htmlspecialchars($data['st_sem']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="text-end mt-3">
                    <button class="btn btn-download" onclick="downloadStudentList()">
                        <i class="fas fa-download me-2"></i>Download Student List
                    </button>
                </div>
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
            $('#studentsTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Quick Search:",
                    lengthMenu: "Show _MENU_ students per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ students"
                }
            });
        });

        function downloadStudentList() {
            const rows = Array.from(document.querySelectorAll('#studentsTable tbody tr'));
            const csvContent = "data:text/csv;charset=utf-8," 
                + "Reg. No.,Name,Department,Batch,Semester\n" 
                + rows.map(row => {
                    const cells = row.querySelectorAll('td');
                    return Array.from(cells).map(cell => cell.innerText).join(',');
                }).join('\n');

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'student_list.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>