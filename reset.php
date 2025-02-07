<?php
include('connect.php');

if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $row = 0;
    $query = mysql_query("SELECT password FROM admininfo WHERE email = '$email'");
    $row = mysql_num_rows($query);

    if ($row == 0) {
        $error_msg = "Email is not associated with any account. Contact OAMS 1.0";
    } else {
        $query = mysql_query("SELECT password FROM admininfo WHERE email = '$email'");
        $recovery_data = [];
        while ($dat = mysql_fetch_array($query)) {
            $recovery_data[] = $dat;
        }
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
            background: linear-gradient(135deg,rgb(29, 180, 97), #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .recovery-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            animation: fadeIn 1s ease-in-out;
        }

        .recovery-container h1 {
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
            background:rgb(37, 229, 136);
        }

        .alert {
            animation: slideIn 0.5s ease-in-out;
        }

        .recovery-message {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
    <div class="recovery-container">
        <h1 class="text-center">Recover Your Password</h1>
        
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <form method="post" class="needs-validation" novalidate>
            <div class="form-floating">
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                <label for="email">Email</label>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="reset" class="btn btn-primary">Recover Password</button>
            </div>
        </form>

        <?php if (isset($recovery_data)): ?>
            <div class="recovery-message">
                <p><strong>Hi there!</strong></p>
                <p>You requested for a password recovery. You may <a href="index.php">Login here</a> and enter this key as your password to login.</p>
                <p><strong>Recovery key:</strong> <mark><?php echo htmlspecialchars($recovery_data[0]['password']); ?></mark></p>
                <p><strong>Regards,</strong><br>Attendance Management System</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>