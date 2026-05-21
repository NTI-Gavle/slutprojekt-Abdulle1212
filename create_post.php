<?php
/**
 * create_post.php - Create a new post
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

$redirectTarget = trim($_POST['redirect_to'] ?? 'index.php');
$queryGlue = str_contains($redirectTarget, '?') ? '&' : '?';
$content = trim($_POST['content'] ?? '');

if ($content === '') {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Post cannot be empty.'));
}

if (strlen($content) > 280) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Post must be max 280 characters.'));
}

$userId = (int) getUserId();
$stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (?, ?)');
$stmt->execute([$userId, $content]);
$postId = (int) $pdo->lastInsertId();

saveHashtags($pdo, $postId, $content);

redirectTo($redirectTarget . $queryGlue . 'success=' . urlencode('Your post has been published!'));
?>
