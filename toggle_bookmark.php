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

toggleBookmark($pdo, $postId, $userId);
redirectTo($redirectTarget);
?>
