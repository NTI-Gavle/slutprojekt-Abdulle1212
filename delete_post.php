<?php
/**
 * delete_post.php - Ta bort ett kvitter
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

$postId = intval($_POST['post_id'] ?? 0);

if ($postId <= 0) {
    redirectTo('index.php?error=' . urlencode('Ogiltigt inläggs-ID.'));
}

$stmt = $pdo->prepare('SELECT id, user_id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    redirectTo('index.php?error=' . urlencode('Inlägget hittades inte.'));
}

if ($post['user_id'] != getUserId() && !isAdmin()) {
    redirectTo('index.php?error=' . urlencode('Du har inte behörighet att ta bort detta inlägg.'));
}

$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
$stmt->execute([$postId]);

redirectTo('index.php?success=' . urlencode('Kvittret har tagits bort.'));
?>
