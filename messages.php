<?php
/**
 * messages.php - Direct messaging
 */
$pageTitle = 'Messages';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$userId = (int) getUserId();
$conversations = getConversations($pdo, $userId);
$activeConversation = null;
$messages = [];

$chatWith = (int) ($_GET['with'] ?? 0);
if ($chatWith > 0) {
    $activeConversation = getUserById($pdo, $chatWith);
    if ($activeConversation) {
        $messages = getMessagesWithUser($pdo, $userId, $chatWith);
        markMessagesRead($pdo, $userId, $chatWith);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<header class="main-header">
    <h1><i class="bi bi-envelope-fill me-2"></i>Messages</h1>
</header>

<div class="messages-container">
    <div class="messages-layout">
        <aside class="messages-sidebar">
            <h3>Conversations</h3>
            <?php if (empty($conversations)): ?>
            <p class="text-muted">No conversations yet.</p>
            <?php else: ?>
            <div class="conversation-list">
                <?php foreach ($conversations as $conv): ?>
                <a href="<?php echo htmlspecialchars(appUrl('messages.php?with=' . (int) $conv['id'])); ?>" class="conversation-item <?php echo $chatWith == $conv['id'] ? 'active' : ''; ?>">
                    <div class="conversation-avatar">
                        <?php echo htmlspecialchars(userInitial($conv['display_name'] ?? $conv['username'])); ?>
                    </div>
                    <div class="conversation-info">
                        <strong><?php echo htmlspecialchars($conv['display_name'] ?? $conv['username']); ?></strong>
                        <?php if ((int) $conv['unread_count'] > 0): ?>
                        <span class="unread-badge"><?php echo (int) $conv['unread_count']; ?> new</span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </aside>

        <main class="messages-main">
            <?php if ($activeConversation): ?>
            <div class="messages-header">
                <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($activeConversation['username']))); ?>" class="messages-user-link">
                    <div class="messages-user-avatar">
                        <?php echo htmlspecialchars(userInitial($activeConversation['display_name'] ?? $activeConversation['username'])); ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($activeConversation['display_name'] ?? $activeConversation['username']); ?></strong>
                        <small>@<?php echo htmlspecialchars($activeConversation['username']); ?></small>
                    </div>
                </a>
            </div>

            <div class="messages-body">
                <?php if (empty($messages)): ?>
                <div class="messages-empty">
                    <p>No messages yet. Start the conversation!</p>
                </div>
                <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo (int) $msg['sender_id'] === $userId ? 'message-sent' : 'message-received'; ?>">
                    <div class="message-bubble">
                        <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                    </div>
                    <div class="message-time"><?php echo htmlspecialchars(formatRelativeTime($msg['created_at'])); ?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars(appUrl('send_message.php')); ?>" class="messages-form">
                <input type="hidden" name="recipient_id" value="<?php echo (int) $chatWith; ?>">
                <textarea name="content" rows="2" maxlength="500" required placeholder="Write a message..."></textarea>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill"></i> Send
                </button>
            </form>
            <?php else: ?>
            <div class="messages-placeholder">
                <i class="bi bi-chat-square-text"></i>
                <h3>Select a conversation</h3>
                <p>Choose a conversation from the sidebar or start a new one from a user's profile.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
