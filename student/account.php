<?php
session_start();

// Check authentication
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: ../login.php');
    exit();
}

require_once('../connect.php');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Remove student search functionality
    // ... existing code ...
    if(isset($_POST['done'])) {
        // Validate required fields
        $required = ['name', 'dept', 'batch', 'email'];
        foreach($required as $field) {
            if(empty($_POST[$field])) {
                throw new Exception(ucfirst($field) . " cannot be empty");
            }
        }

        // Validate email format
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Update student information
        $stmt = $pdo->prepare("UPDATE students SET 
            st_name = ?, st_dept = ?, st_batch = ?, 
            st_sem = ?, st_email = ? WHERE st_id = ?");
            
        $stmt->execute([
            $_POST['name'],
            $_POST['dept'],
            $_POST['batch'],
            $_POST['semester'],
            $_POST['email'],
            $_POST['id']
        ]);

        $success_msg = 'Profile updated successfully!';
    }

    // Remove student search handling
    // ... existing code ...
} catch(Exception $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Attendance Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
        .profile-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .profile-header {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(72, 110, 255, 0.25);
        }
        .input-group-text {
            background: #f8f9fa;
        }
        .btn-update {
            background: linear-gradient(45deg, #4158d0, #c850c0);
            border: none;
            padding: 0.8rem 2rem;
            transition: transform 0.3s ease;
        }
        .btn-update:hover {
            transform: translateY(-2px);
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
            color: #4158d0;
        }
        @media (max-width: 768px) {
            .profile-container {
                padding: 1rem;
                width:100%;
            }
            .profile-header {
                padding: 1.5rem;
                width:100%
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
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <!--<li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>--> <!-- Removed student list link -->
                            <li class="nav-item"><a class="nav-link" href="report.php">Report Section</a></li>
                            <li class="nav-item"><a class="nav-link active" href="account.php">My Account</a></li>
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
                <div class="profile-container">
                    <div class="profile-header text-center">
                        <div class="profile-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h2 class="mb-0">Student Profile</h2>
                        <p class="mb-0">Update your information</p>
                    </div>

                    <!-- Removed student search form -->
                    <?php if(isset($student)): ?>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['st_id']); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['st_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" name="dept" class="form-control" value="<?php echo htmlspecialchars($student['st_dept']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="text" name="batch" class="form-control" value="<?php echo htmlspecialchars($student['st_batch']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="text" name="semester" class="form-control" value="<?php echo htmlspecialchars($student['st_sem']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['st_email']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['st_id']); ?>">
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-update" name="done">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
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
    </script>
</body>
</html>