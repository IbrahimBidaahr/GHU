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

    if(isset($_POST['signup'])) {
        // Validate inputs
        $required_fields = [
            'email' => 'Email',
            'uname' => 'Username',
            'pass' => 'Password',
            'fname' => 'Full Name',
            'phone' => 'Phone Number',
            'type' => 'Role'
        ];

        foreach($required_fields as $field => $label) {
            if(empty($_POST[$field])) {
                throw new Exception("$label cannot be empty!");
            }
        }

        // Validate email format
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format!");
        }

        // Hash password
        $hashed_password = password_hash($_POST['pass'], PASSWORD_DEFAULT);

        // Insert user with prepared statement
        $stmt = $pdo->prepare("INSERT INTO admininfo (username, password, email, fname, phone, type) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['uname'],
            $hashed_password,
            $_POST['email'],
            $_POST['fname'],
            $_POST['phone'],
            $_POST['type']
        ]);

        $success_msg = "User created successfully!";
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
    <title>Create User - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .role-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }
        .password-strength {
            height: 5px;
            transition: all 0.3s ease;
            margin-top: 5px;
        }
        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
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
                            <li class="nav-item"><a class="nav-link active" href="signup.php">Create Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php">Add Student/Teacher</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_courses.php">Courses</a></li> <!-- New Link -->
                            <li class="nav-item"><a class="nav-link" href="v-students.php">View Students</a></li>
                            <li class="nav-item"><a class="nav-link" href="v-teachers.php">View Teachers</a></li>
                            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container py-4">
        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <h2 class="text-center mb-4">Create New User</h2>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                            <label for="email">Email address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="uname" class="form-control" id="username" placeholder="Username" required>
                            <label for="username">Username</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" name="pass" class="form-control" id="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <div class="password-strength"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="fname" class="form-control" id="fullname" placeholder="Full Name" required>
                            <label for="fullname">Full Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" name="phone" class="form-control" id="phone" placeholder="Phone Number" required>
                            <label for="phone">Phone Number</label>
                        </div>

                        <div class="role-section">
                            <label class="form-label">Select Role</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="student" id="roleStudent" checked>
                                    <label class="form-check-label" for="roleStudent">
                                        <i class="fas fa-user-graduate me-1"></i> Student
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="teacher" id="roleTeacher">
                                    <label class="form-check-label" for="roleTeacher">
                                        <i class="fas fa-chalkboard-teacher me-1"></i> Teacher
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" value="admin" id="roleAdmin">
                                    <label class="form-check-label" for="roleAdmin">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" name="signup">
                                <i class="fas fa-user-plus me-2"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = document.querySelector('.password-strength');
            const strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
            const mediumRegex = new RegExp("^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{6,})");

            if(strongRegex.test(password)) {
                strength.style.backgroundColor = '#28a745';
                strength.style.width = '100%';
            } else if(mediumRegex.test(password)) {
                strength.style.backgroundColor = '#ffc107';
                strength.style.width = '60%';
            } else {
                strength.style.backgroundColor = '#dc3545';
                strength.style.width = '30%';
            }
        });
    </script>
</body>
</html>