<?php
/**
 * send_message.php - Send a direct message
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('messages.php');
}

$recipientId = (int) ($_POST['recipient_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$userId = (int) getUserId();

if ($recipientId <= 0 || $recipientId === $userId) {
    redirectTo('messages.php?error=' . urlencode('Invalid recipient.'));
}

if ($content === '') {
    redirectTo('messages.php?with=' . $recipientId . '&error=' . urlencode('Message cannot be empty.'));
}

if (strlen($content) > 500) {
    redirectTo('messages.php?with=' . $recipientId . '&error=' . urlencode('Message must be max 500 characters.'));
}

$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = ?');
$stmt->execute([$recipientId]);
$recipient = $stmt->fetch();

if (!$recipient) {
    redirectTo('messages.php?error=' . urlencode('User not found.'));
}

sendMessage($pdo, $userId, $recipientId, $content);
createNotification($pdo, $recipientId, $userId, 'message', null, $content);

redirectTo('messages.php?with=' . $recipientId);
?>
