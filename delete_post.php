<?php
/**
 * delete_post.php - Delete a post
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

$redirectTarget = trim($_POST['redirect_to'] ?? 'index.php');
$queryGlue = str_contains($redirectTarget, '?') ? '&' : '?';
$postId = (int) ($_POST['post_id'] ?? 0);

if ($postId <= 0) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Invalid post ID.'));
}

$stmt = $pdo->prepare('SELECT id, user_id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Post not found.'));
}

if ((int) $post['user_id'] !== (int) getUserId() && !isAdmin()) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('You do not have permission to delete this post.'));
}

$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
$stmt->execute([$postId]);

redirectTo($redirectTarget . $queryGlue . 'success=' . urlencode('Post has been deleted.'));
?>
