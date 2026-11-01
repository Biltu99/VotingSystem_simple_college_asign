<?php
// ============================================================
// index.php — Home page
// ============================================================
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Voting System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="results.php">Results</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (($_SESSION['role'] ?? '') === 'officer'): ?>
            <a href="officer_dashboard.php">Officer Dashboard</a>
        <?php else: ?>
            <a href="dashboard.php">Dashboard</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Voter Login</a>
        <a href="register.php">Register</a>
        <a href="officer_login.php">Officer Login</a>
    <?php endif; ?>
</nav>

<div class="container">
    <h1 class="hero">🗳️ Online Voting System</h1>
    <p class="lead">
        Welcome to the college online voting system.
        Register, login, and cast your vote for your favourite candidate.
    </p>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>!</p>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <a href="results.php" class="btn btn-outline">View Results</a>
    <?php else: ?>
        <a href="register.php" class="btn">Register</a>
        <a href="login.php" class="btn btn-outline">Login</a>
    <?php endif; ?>
</div>

</body>
</html>