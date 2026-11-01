<?php
// ============================================================
// officer_login.php — Officer login page
// Location: C:\xampp_new\htdocs\VotingSystem_simple\
// ============================================================

session_start();
require_once 'db.php';

// If already logged in as officer, go to dashboard
if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'officer') {
    header('Location: officer_dashboard.php');
    exit;
}

// If logged in as voter, log them out first (clean switch)
if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') !== 'officer') {
    $_SESSION = [];
    session_destroy();
    session_start();
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Look for an officer account only
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'officer'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Success — store in session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = 'officer';

            header('Location: officer_dashboard.php');
            exit;
        } else {
            $error = 'Invalid officer credentials.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Login — Online Voting System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="results.php">Results</a>
    <a href="login.php">Voter Login</a>
    <a href="officer_login.php">Officer Login</a>
</nav>

<div class="container small">
    <h1>👮 Officer Login</h1>
    <p class="lead">Officers can add and manage party names for the election.</p>

    <?php if ($error): ?>
        <div class="error"><p><?= htmlspecialchars($error) ?></p></div>
    <?php endif; ?>

    <form method="post">
        <label>Officer Username
            <input type="text" 
                   name="username" 
                   required 
                   autofocus
                   placeholder="e.g. officer1"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </label>

        <label>Password
            <input type="password" 
                   name="password" 
                   required
                   placeholder="Enter your password">
        </label>

        <button type="submit">Login as Officer</button>
    </form>

    <p style="margin-top: 18px; text-align: center;">
        Not an officer? <a href="login.php">Login as voter</a>
    </p>
</div>

</body>
</html>