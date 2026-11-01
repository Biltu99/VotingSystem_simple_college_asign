<?php
// ============================================================
// vote.php — Processes the vote (no HTML output)
// ============================================================
session_start();
require_once 'db.php';

// ---- 1. Must be logged in ----
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ---- 2. Officers cannot vote ----
if (($_SESSION['role'] ?? '') === 'officer') {
    header('Location: officer_dashboard.php');
    exit;
}

$userId      = $_SESSION['user_id'];
$candidateId = (int)($_GET['id'] ?? 0);

// ---- 3. Validate the candidate exists ----
$stmt = $pdo->prepare("SELECT id FROM candidates WHERE id = ?");
$stmt->execute([$candidateId]);
if (!$stmt->fetch()) {
    die('Invalid candidate.');
}

// ---- 4. Check if user has already voted ----
$stmt = $pdo->prepare("SELECT id FROM votes WHERE user_id = ?");
$stmt->execute([$userId]);
if ($stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}

// ---- 5. Insert the vote ----
try {
    $stmt = $pdo->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
    $stmt->execute([$userId, $candidateId]);
    header('Location: dashboard.php?voted=1');
    exit;
} catch (PDOException $e) {
    // If the UNIQUE constraint blocks a duplicate, land here
    die('You have already voted.');
}