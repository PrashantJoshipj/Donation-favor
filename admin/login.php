<?php
require_once '../config.php';

// Redirect if already logged in
if (isAdmin()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Server-side validation
    if (empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Check if credentials match admin
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
            // Set admin session
            setAdminSession($email);
            redirect('index.php');
        } else {
            $error = 'Invalid admin credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Donation Favor</title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="../public/index.php" class="logo">Donation Favor</a>
            <ul class="nav-links">
                <li><a href="../public/index.php">Home</a></li>
                <li><a href="../public/login.php">User Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Admin Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" onsubmit="return validateAdminLoginForm()">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <div id="email-error" class="error"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Admin Password</label>
                    <input type="password" id="password" name="password" required>
                    <div id="password-error" class="error"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login as Admin</button>
            </form>
            
            <div class="form-footer">
                <p><a href="../public/login.php">User Login</a></p>
            </div>
        </div>
    </div>

    <script src="../public/app.js"></script>
</body>
</html>
