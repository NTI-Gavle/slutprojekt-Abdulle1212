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
$content = trim($_POST['content'] ?? '');
$userId = (int) getUserId();

if ($postId <= 0) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Invalid post.'));
}

if ($content === '') {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Comment cannot be empty.'));
}

if (strlen($content) > 280) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Comment must be max 280 characters.'));
}

$stmt = $pdo->prepare('SELECT id, user_id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Post not found.'));
}

addComment($pdo, $postId, $userId, $content);
createNotification($pdo, (int) $post['user_id'], $userId, 'comment', $postId, $content);

redirectTo($redirectTarget . $queryGlue . 'success=' . urlencode('Your comment has been published.'));
?>
