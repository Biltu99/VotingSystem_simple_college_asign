<?php
// ============================================================
// results.php — Public results page with progress bars
// ============================================================
require_once 'db.php';

// ---- Fetch vote counts per candidate ----
$stmt = $pdo->query("
    SELECT c.id, c.name, c.party, COUNT(v.id) AS total
    FROM candidates c
    LEFT JOIN votes v ON v.candidate_id = c.id
    GROUP BY c.id
    ORDER BY total DESC
");
$results = $stmt->fetchAll();

// ---- Total votes cast ----
$totalVotes = (int)$pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();

// ---- Find highest vote count (for winner highlight) ----
$maxVotes = 0;
foreach ($results as $r) {
    if ((int)$r['total'] > $maxVotes) {
        $maxVotes = (int)$r['total'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="results.php">Results</a>
</nav>

<div class="container">
    <h1>📊 Election Results</h1>
    <p>Total votes cast: <strong><?= $totalVotes ?></strong></p>

    <?php if (empty($results)): ?>
        <div class="error">No candidates found.</div>
    <?php elseif ($totalVotes === 0): ?>
        <div class="error">No votes have been cast yet.</div>
    <?php else: ?>

        <div class="result-list">
            <?php foreach ($results as $r): 
                $isWinner = ((int)$r['total'] === $maxVotes && $maxVotes > 0);
                $percent  = $totalVotes > 0 
                    ? round(((int)$r['total'] / $totalVotes) * 100, 1) 
                    : 0;
            ?>
                <div class="result-item <?= $isWinner ? 'winner' : '' ?>">
                    <div class="result-header">
                        <span class="result-name"><?= htmlspecialchars($r['name']) ?></span>
                        <span class="result-party"><?= htmlspecialchars($r['party']) ?></span>
                        <span class="result-count">
                            <?= (int)$r['total'] ?> votes (<?= $percent ?>%)
                        </span>
                    </div>
                    <div class="result-bar-track">
                        <div class="result-bar" style="width: <?= $percent ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <p style="margin-top: 25px;">
        <a href="index.php" class="btn">Back to Home</a>
    </p>
</div>

</body>
</html>