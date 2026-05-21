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
$commentId = (int) ($_POST['comment_id'] ?? 0);

if ($commentId <= 0) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('Invalid comment.'));
}

$deleted = deleteComment($pdo, $commentId, (int) getUserId());

if (!$deleted) {
    redirectTo($redirectTarget . $queryGlue . 'error=' . urlencode('You do not have permission to delete this comment.'));
}

redirectTo($redirectTarget . $queryGlue . 'success=' . urlencode('Comment has been deleted.'));
?>
