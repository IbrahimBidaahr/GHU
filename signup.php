<?php
include('connect.php');

try {
    // Get the database connection
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    if (isset($_POST['signup'])) {
        if (empty($_POST['st_id'])) {
            throw new Exception("Student ID can't be empty.");
        }

        if (empty($_POST['pass'])) {
            throw new Exception("Password can't be empty.");
        }

        if (empty($_POST['fname'])) {
            throw new Exception("Full Name can't be empty.");
        }

        if (empty($_POST['phone'])) {
            throw new Exception("Phone Number can't be empty.");
        }

        // Check if the student ID exists in the database
        $st_id = $_POST['st_id'];
        $stmt = $pdo->prepare("SELECT * FROM students WHERE st_id = ?");
        $stmt->execute([$st_id]);
        $student = $stmt->fetch();

        if (!$student) {
            throw new Exception("Student ID does not exist in the database.");
        }

        // Check if the username (student ID) already exists
        $stmt = $pdo->prepare("SELECT * FROM admininfo WHERE username = ?");
        $stmt->execute([$st_id]); // Check if the st_id is already used as a username
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            throw new Exception("Username (Student ID) already exists. Please choose a different Student ID.");
        }

        // Insert into the database
        $stmt = $pdo->prepare("INSERT INTO admininfo (username, password, fname, phone, type, st_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$st_id, $_POST['pass'], $_POST['fname'], $_POST['phone'], 'student', $st_id])) {
            $success_msg = "Account created successfully with Student ID: $st_id!";
        } else {
            throw new Exception("There was a problem creating your account. Please try again.");
        }

        // Redirect to the student panel after successful signup
        header("Location:student/index.php"); // Change this to your actual student panel page
        exit();
    }
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Attendance Management System</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
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
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Signup</h1>
    
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="st_id">Student ID</label>
            <input type="text" name="st_id" class="form-control" id="st_id" placeholder="Enter Student ID" required>
        </div>
        <div class="form-group">
            <label for="fname">Full Name</label>
            <input type="text" name="fname" class="form-control" id="fname" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <label for="pass">Password</label>
            <input type="password" name="pass" class="form-control" id="pass" placeholder="Enter Password" required>
        </div>
        <input type="hidden" name="type" value="student"> <!-- Only student type -->
        <button type="submit" class="btn btn-primary btn-block" name="signup">Signup</button>
    </form>
    
    <p class="text-center mt-3"><strong>Already have an account? <a href="index.php">Login</a> here.</strong></p>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>