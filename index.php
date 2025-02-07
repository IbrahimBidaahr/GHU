<?php
session_start();

// Database connection
require_once('connect.php');

if(isset($_POST['login'])) {
    try {
        // Get database connection
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Validate input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        
        if(empty($username)) {
            throw new Exception("Username is required!");
        }
        if(empty($password)) {
            throw new Exception("Password is required!");
        }
        
        // Use prepared statements to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM admininfo WHERE username = ? AND password = ? AND type = ?");
        $stmt->execute([$username, $password, $type]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            $_SESSION['name'] = "oasis";
            // Replace null coalescing operator with isset()
            $_SESSION['user_id'] = isset($user['id']) ? $user['id'] : null;
            $_SESSION['user_type'] = $type;
            
            switch($type) {
                case 'teacher':
                    header('location: teacher/index.php');
                    break;
                case 'student':
                    header('location: student/index.php');
                    break;
                case 'admin':
                    header('location: admin/index.php');
                    break;
                default:
                    throw new Exception("Invalid user type!");
            }
            exit();
        } else {
            throw new Exception("Invalid username, password or role. Please try again!");
        }
    } catch(Exception $e) {
        $error_msg = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgba(50, 194, 105, 0.69),rgb(11, 83, 51));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        .login-container h1 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: bold;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating input {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-floating input:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
        }

        .btn-primary {
            background: #6a11cb;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background:rgb(43, 198, 105);
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6a11cb;
        }

        .alert {
            animation: slideIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center">Login Panel</h1>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <form method="post" class="needs-validation" novalidate>
            <div class="form-floating">
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                <label for="username">Username</label>
            </div>

            <div class="form-floating position-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                <label for="password">Password</label>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye"></i>
                </span>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Login As:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" value="student" checked>
                    <label class="form-check-label">Student</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" value="teacher">
                    <label class="form-check-label">Teacher</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" value="admin">
                    <label class="form-check-label">Admin</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p class="mb-1">
                <a href="reset.php" class="text-decoration-none">Reset Password</a>
            </p>
            <p>
                <a href="signup.php" class="text-decoration-none">Create New Account</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>