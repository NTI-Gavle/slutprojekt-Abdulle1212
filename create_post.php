<?php
/**
 * create_post.php - Skapa ett nytt kvitter
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('index.php');
}

$content = trim($_POST['content'] ?? '');

if ($content === '') {
    redirectTo('index.php?error=' . urlencode('Kvittret kan inte vara tomt.'));
}

if (strlen($content) > 280) {
    redirectTo('index.php?error=' . urlencode('Kvittret får vara max 280 tecken.'));
}

$stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (?, ?)');
$stmt->execute([getUserId(), $content]);

redirectTo('index.php?success=' . urlencode('Ditt kvitter har publicerats!'));
?>
