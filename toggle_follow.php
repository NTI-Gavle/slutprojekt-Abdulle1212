<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

$redirectTarget = trim($_POST['redirect_to'] ?? 'index.php');
$queryGlue = str_contains($redirectTarget, '?') ? '&' : '?';
$followedId = (int) ($_POST['followed_id'] ?? 0);
$userId = (int) getUserId();

if ($followedId <= 0) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Invalid user.'));
}

$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = ?');
$stmt->execute([$followedId]);
$user = $stmt->fetch();

if (!$user) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('User not found.'));
}

$following = toggleFollow($pdo, $userId, $followedId);

if ($following) {
    createNotification($pdo, $followedId, $userId, 'follow');
}

redirectTo($redirectTarget);
?>
