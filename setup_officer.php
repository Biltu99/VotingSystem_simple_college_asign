<?php
// ============================================================
// setup_officer.php — Run ONCE, then delete
// ============================================================
require_once 'db.php';

$messages = [];

try {
    // ---- 1. Ensure role column exists with correct enum ----
    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetchAll();

    if (!$cols) {
        $pdo->exec("
            ALTER TABLE users 
            ADD COLUMN role ENUM('voter','officer','admin') DEFAULT 'voter'
            AFTER password
        ");
        $messages[] = "✅ Added 'role' column.";
    } else {
        // Already exists — make sure 'officer' is allowed
        $pdo->exec("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('voter','officer','admin') DEFAULT 'voter'
        ");
        $messages[] = "✅ Role column verified (officer allowed).";
    }

    // ---- 2. Create/update officer account ----
    $username = 'officer1';
    $password = 'officer123';
    $name     = 'Election Officer';
    $hash     = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password = ?, role = 'officer', name = ?
            WHERE id = ?
        ");
        $stmt->execute([$hash, $name, $existing['id']]);
        $messages[] = "✅ Officer account updated.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, username, password, role)
            VALUES (?, ?, ?, 'officer')
        ");
        $stmt->execute([$name, $username, $hash]);
        $messages[] = "✅ Officer account created.";
    }

    $messages[] = "";
    $messages[] = "🔑 Username: officer1";
    $messages[] = "🔑 Password: officer123";
    $messages[] = "";
    $messages[] = "⚠️ Delete this file after use!";

} catch (PDOException $e) {
    $messages[] = "❌ Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Officer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container small">
    <h1>Officer Setup</h1>
    <div class="<?= str_starts_with($messages[0], '✅') ? 'success' : 'error' ?>">
        <?php foreach ($messages as $m) echo '<p>' . htmlspecialchars($m) . '</p>'; ?>
    </div>
    <p style="margin-top:20px;">
        <a href="officer_login.php" class="btn">Go to Officer Login</a>
    </p>
</div>
</body>
</html>