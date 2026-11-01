<?php
// ============================================================
// dashboard.php — Voter dashboard (shows candidates + vote buttons)
// ============================================================
session_start();
require_once 'db.php';

// ---- 1. Must be logged in ----
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ---- 2. Officers should NOT be here ----
if (($_SESSION['role'] ?? '') === 'officer') {
    header('Location: officer_dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];

// ---- 3. Has this user already voted? ----
$stmt = $pdo->prepare("SELECT candidate_id FROM votes WHERE user_id = ?");
$stmt->execute([$userId]);
$myVote = $stmt->fetch();

// ---- 4. Load all candidates ----
$candidates = $pdo->query("SELECT * FROM candidates ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="results.php">Results</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h1>

    <?php if ($myVote): ?>
        <!-- Already voted -->
        <div class="success">
            ✅ You have already voted. Thank you for participating!
        </div>
        <a href="results.php" class="btn">View Results</a>
    <?php else: ?>
        <!-- Not voted yet — show candidates -->
        <p>Select a candidate to cast your vote:</p>

        <div class="candidate-grid">
            <?php foreach ($candidates as $c): ?>
                <div class="candidate-card">
                    <h3><?= htmlspecialchars($c['name']) ?></h3>
                    <p><?= htmlspecialchars($c['party']) ?></p>
                    <a href="vote.php?id=<?= (int)$c['id'] ?>" class="btn"
                       onclick="return confirm('Vote for <?= htmlspecialchars($c['name']) ?>? You cannot change it later.');">
                        Vote
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>