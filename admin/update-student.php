<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
    exit();
}

require_once('../connect.php'); // Ensure this file connects to the database correctly

// Get the database connection
$db = Database::getInstance();
$pdo = $db->getConnection();

if (isset($_GET['id'])) {
    $st_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    
    // Fetch the student data
    $stmt = $pdo->prepare("SELECT * FROM students WHERE st_id = ?");
    $stmt->execute([$st_id]);
    $student = $stmt->fetch();

    if (!$student) {
        die("Student not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update student information
    $st_name = filter_input(INPUT_POST, 'st_name', FILTER_SANITIZE_STRING);
    $st_dept = filter_input(INPUT_POST, 'st_dept', FILTER_SANITIZE_STRING);
    $st_batch = filter_input(INPUT_POST, 'st_batch', FILTER_SANITIZE_STRING);
    $st_sem = filter_input(INPUT_POST, 'st_sem', FILTER_SANITIZE_STRING);

    $update_stmt = $pdo->prepare("UPDATE students SET st_name = ?, st_dept = ?, st_batch = ?, st_sem = ? WHERE st_id = ?");
    $update_stmt->execute([$st_name, $st_dept, $st_batch, $st_sem, $st_id]);

    header("Location: v-students.php"); // Redirect after update
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(24, 24, 23);
        }
        .container {
            width:100%;
            margin-top: 50px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(32, 32, 31, 0.49);
        }
        h2 {
            margin-bottom: 20px;
            color: #343a40;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            margin-left: 10px;
        }
        @media (max-width: 768px){
            container {
            width:100%;}
            mb-3{
                width:100%;
            }
            text-center{
                width: 100%;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Update Student Information</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="st_name" class="form-control" value="<?php echo htmlspecialchars($student['st_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <input type="text" name="st_dept" class="form-control" value="<?php echo htmlspecialchars($student['st_dept']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Batch</label>
                <input type="text" name="st_batch" class="form-control" value="<?php echo htmlspecialchars($student['st_batch']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Semester</label>
                <input type="text" name="st_sem" class="form-control" value="<?php echo htmlspecialchars($student['st_sem']); ?>" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="v-students.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>