<?php
// ============================================================
// login.php — Voter login
// ============================================================
session_start();
require_once 'db.php';

// Already logged in? Redirect appropriately
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'officer') {
        header('Location: officer_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        // Only voters can login here — officers use officer_login.php
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'voter'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Save session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = 'voter';

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container small">
    <h1>Login</h1>

    <?php if (isset($_GET['registered'])): ?>
        <div class="success">Registration successful! Please login.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><p><?= htmlspecialchars($error) ?></p></div>
    <?php endif; ?>

    <form method="post">
        <label>Username
            <input type="text" name="username" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>

    <p>New here? <a href="register.php">Register</a></p>
    <p style="margin-top:10px;">Officer? <a href="officer_login.php">Officer Login</a></p>
</div>
</body>
</html>