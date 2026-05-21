<?php
/**
 * notifications.php - Notification center
 */
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$userId = (int) getUserId();
$notifications = getNotifications($pdo, $userId, 30);
markNotificationsRead($pdo, $userId);

require_once __DIR__ . '/../includes/header.php';
?>

<header class="main-header">
    <h1><i class="bi bi-bell-fill me-2"></i>Notifications</h1>
</header>

<div class="notifications-container">
    <?php if (empty($notifications)): ?>
    <div class="card kvitter-card feed-empty-state">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-bell-slash"></i>
            <h3 class="mt-3">No notifications yet</h3>
            <p>When someone likes, comments, or follows you, it will show up here.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="notification-list">
        <?php foreach ($notifications as $notif): ?>
        <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
            <div class="notification-icon">
                <?php if ($notif['type'] === 'like'): ?>
                    <i class="bi bi-heart-fill text-danger"></i>
                <?php elseif ($notif['type'] === 'comment'): ?>
                    <i class="bi bi-chat-dots-fill text-primary"></i>
                <?php elseif ($notif['type'] === 'follow'): ?>
                    <i class="bi bi-person-plus-fill text-success"></i>
                <?php elseif ($notif['type'] === 'repost'): ?>
                    <i class="bi bi-repeat text-info"></i>
                <?php else: ?>
                    <i class="bi bi-bell-fill text-warning"></i>
                <?php endif; ?>
            </div>
            <div class="notification-content">
                <div class="notification-text">
                    <strong><?php echo htmlspecialchars($notif['display_name'] ?? $notif['username']); ?></strong>
                    <?php if ($notif['type'] === 'like'): ?>
                        liked your post
                    <?php elseif ($notif['type'] === 'comment'): ?>
                        commented on your post
                    <?php elseif ($notif['type'] === 'follow'): ?>
                        started following you
                    <?php elseif ($notif['type'] === 'repost'): ?>
                        reposted your post
                    <?php endif; ?>
                </div>
                <div class="notification-time"><?php echo htmlspecialchars(formatRelativeTime($notif['created_at'])); ?></div>
            </div>
            <?php if ($notif['post_id']): ?>
            <a href="<?php echo htmlspecialchars(appUrl('post.php?id=' . $notif['post_id'])); ?>" class="notification-link">
                <i class="bi bi-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
