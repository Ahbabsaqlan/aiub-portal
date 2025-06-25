<?php
session_start();
require_once 'config/database.php';

// If user is already logged in via session, redirect to the main portal
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id_str = $_POST['login-username'] ?? '';
    $password = $_POST['login-password'] ?? '';

    if (empty($student_id_str) || empty($password)) {
        $login_error = 'Student ID and Password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, student_id_str, password FROM students WHERE student_id_str = ?");
        $stmt->execute([$student_id_str]);
        $student = $stmt->fetch();

        if ($student['student_id_str'] === $student_id_str &&  $student['password'] === $password) {
            $_SESSION['user_id'] = $student['id'];
            $_SESSION['student_id_str'] = $student['student_id_str'];
            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Invalid Student ID or Password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIUB Student Portal - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <!-- Login Overlay -->
    <div class="login-overlay" style="display: flex;">
        <div class="login-container">
            <div class="login-header">
                <div class="login-logo">AIUB<span>Portal</span></div>
                <h2>Student Login</h2>
            </div>
            <form id="login-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="login-username"><i class="fas fa-user"></i> Student ID</label>
                    <input type="text" id="login-username" name="login-username" class="form-control" placeholder="e.g., 21-123456-1" required>
                </div>
                <div class="form-group">
                    <label for="login-password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="login-password" name="login-password" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="login-options">
                    <label><input type="checkbox" id="remember-me" name="remember-me"> Remember Me</label>
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn login-btn"><i class="fas fa-sign-in-alt"></i> Login</button>
                <p class="login-message"><?php echo htmlspecialchars($login_error); ?></p>
            </form>
            <div class="login-footer">
                © <?php echo date("Y"); ?> American International University-Bangladesh
            </div>
        </div>
    </div>
</body>
</html>