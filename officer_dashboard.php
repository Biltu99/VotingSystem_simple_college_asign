<?php
// ============================================================
// officer_dashboard.php — Officer panel
// Add / view / delete party names
// ============================================================

session_start();
require_once 'db.php';

// Must be logged in as officer
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'officer') {
    header('Location: officer_login.php');
    exit;
}

$errors  = [];
$success = '';

// ---- Handle form submissions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD
    if ($action === 'add') {
        $name  = trim($_POST['name'] ?? '');
        $party = trim($_POST['party'] ?? '');

        if ($name === '')  $errors[] = 'Candidate name is required.';
        if ($party === '') $errors[] = 'Party name is required.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM candidates WHERE name = ? AND party = ?");
            $stmt->execute([$name, $party]);
            if ($stmt->fetch()) {
                $errors[] = 'This candidate + party already exists.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO candidates (name, party) VALUES (?, ?)");
                $stmt->execute([$name, $party]);
                $success = "✅ Added: $name ($party)";
            }
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->execute([$id]);
        $success = "✅ Candidate deleted.";
    }
}

// ---- Fetch all candidates ----
$candidates = $pdo->query("SELECT * FROM candidates ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="officer_dashboard.php">Officer Dashboard</a>
    <a href="results.php">Results</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h1>👮 Officer Dashboard</h1>
    <p class="lead">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><p><?= htmlspecialchars($success) ?></p></div>
    <?php endif; ?>

    <!-- ADD FORM -->
    <div class="card" style="background:#fafafa;padding:22px;border-radius:10px;margin-bottom:30px;">
        <h2 style="font-size:18px;margin-bottom:15px;">➕ Add New Party / Candidate</h2>

        <form method="post">
            <input type="hidden" name="action" value="add">

            <label>Candidate Name
                <input type="text" name="name" required placeholder="e.g. Rahul Kumar">
            </label>

            <label>Party Name
                <input type="text" name="party" required placeholder="e.g. Student Unity Front">
            </label>

            <button type="submit">Add Party</button>
        </form>
    </div>

    <!-- LIST -->
    <h2 style="font-size:20px;margin-bottom:15px;">🏛️ Existing Parties</h2>

    <?php if (empty($candidates)): ?>
        <p class="muted">No parties added yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>#</th>
                <th>Candidate</th>
                <th>Party</th>
                <th>Action</th>
            </tr>
            <?php foreach ($candidates as $i => $c): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><strong><?= htmlspecialchars($c['party']) ?></strong></td>
                    <td>
                        <form method="post" style="margin:0;"
                              onsubmit="return confirm('Delete <?= htmlspecialchars($c['name']) ?>?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                            <button type="submit"
                                    style="width:auto;padding:6px 14px;font-size:13px;margin:0;background:#DC2626;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p style="margin-top:25px;">
        <a href="index.php" class="btn btn-black">Back to Home</a>
    </p>
</div>

</body>
</html>