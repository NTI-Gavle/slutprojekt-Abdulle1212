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
$postId = (int) ($_POST['post_id'] ?? 0);
$userId = (int) getUserId();

if ($postId <= 0) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Invalid post.'));
}

$stmt = $pdo->prepare('SELECT id, user_id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Post not found.'));
}

$liked = toggleLike($pdo, $postId, $userId);

if ($liked) {
    createNotification($pdo, (int) $post['user_id'], $userId, 'like', $postId);
}

redirectTo($redirectTarget);
?>
