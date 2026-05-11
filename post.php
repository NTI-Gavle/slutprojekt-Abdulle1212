<?php
/**
 * post.php - View individual post
 */
$pageTitle = 'Post';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

$postId = (int) ($_GET['id'] ?? 0);

if ($postId <= 0) {
    redirectTo('index.php');
}

$viewerId = isLoggedIn() ? (int) getUserId() : null;

$stmt = $pdo->prepare(
    'SELECT p.*, u.username, u.display_name, u.avatar_url, u.is_verified, u.bio,
            COALESCE(pl.like_count, 0) AS like_count,
            COALESCE(cc.comment_count, 0) AS comment_count
     FROM posts p
     JOIN users u ON p.user_id = u.id
     LEFT JOIN (SELECT post_id, COUNT(*) AS like_count FROM post_likes GROUP BY post_id) pl ON pl.post_id = p.id
     LEFT JOIN (SELECT post_id, COUNT(*) AS comment_count FROM comments GROUP BY post_id) cc ON cc.post_id = p.id
     WHERE p.id = ?'
);
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    $pageTitle = 'Post not found';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="empty-state"><h3>Post not found</h3><p>This post does not exist anymore.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$displayName = $post['display_name'] ?? $post['username'];
$pageTitle = $displayName . ': "' . substr($post['content'], 0, 50) . '"';

$stmt = $pdo->prepare(
    'SELECT c.*, u.username, u.display_name, u.avatar_url, u.is_verified
     FROM comments c
     JOIN users u ON c.user_id = u.id
     WHERE c.post_id = ?
     ORDER BY c.created_at ASC'
);
$stmt->execute([$postId]);
$comments = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<header class="main-header">
    <a href="javascript:history.back()" class="back-link">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1>Post</h1>
</header>

<article class="post-detail-card">
    <div class="post-detail-header">
        <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($post['username']))); ?>" class="post-detail-avatar">
            <?php echo htmlspecialchars(userInitial($displayName)); ?>
        </a>
        <div class="post-detail-user-info">
            <strong>
                <?php echo htmlspecialchars($displayName); ?>
                <?php if ($post['is_verified']): ?>
                <i class="bi bi-patch-check-fill verified-badge"></i>
                <?php endif; ?>
            </strong>
            <span class="post-detail-handle">@<?php echo htmlspecialchars($post['username']); ?></span>
        </div>
    </div>

    <div class="post-detail-content">
        <?php 
        $content = htmlspecialchars($post['content']);
        $content = preg_replace('/#([\w]+)/u', '<a href="' . htmlspecialchars(appUrl('search.php?q=%23$1')) . '" class="hashtag-link">#$1</a>', $content);
        echo nl2br($content);
        ?>
    </div>

    <div class="post-detail-time">
        <?php echo date('h:i A · M j, Y', strtotime($post['created_at'])); ?>
    </div>

    <div class="post-detail-stats">
        <span><strong><?php echo (int) $post['comment_count']; ?></strong> Comments</span>
        <span><strong><?php echo (int) $post['like_count']; ?></strong> Likes</span>
    </div>

    <?php if (isLoggedIn()): ?>
    <div class="post-detail-actions">
        <form method="POST" action="<?php echo htmlspecialchars(appUrl('toggle_like.php')); ?>">
            <input type="hidden" name="post_id" value="<?php echo (int) $post['id']; ?>">
            <input type="hidden" name="redirect_to" value="post.php?id=<?php echo (int) $post['id']; ?>">
            <button type="submit" class="post-action-btn">
                <i class="bi bi-heart"></i> Like
            </button>
        </form>
    </div>
    <?php endif; ?>
</article>

<?php if (isLoggedIn()): ?>
<form method="POST" action="<?php echo htmlspecialchars(appUrl('add_comment.php')); ?>" class="comment-form-detail">
    <input type="hidden" name="post_id" value="<?php echo (int) $post['id']; ?>">
    <input type="hidden" name="redirect_to" value="post.php?id=<?php echo (int) $post['id']; ?>">
    <textarea name="content" rows="3" maxlength="280" required placeholder="Write a reply..."></textarea>
    <button type="submit" class="btn btn-primary">Reply</button>
</form>
<?php endif; ?>

<div class="comments-section-detail">
    <?php if (empty($comments)): ?>
    <div class="empty-state">
        <p>No replies yet.</p>
    </div>
    <?php else: ?>
    <?php foreach ($comments as $comment): ?>
    <div class="comment-detail">
        <div class="comment-detail-header">
            <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($comment['username']))); ?>" class="comment-detail-avatar">
                <?php echo htmlspecialchars(userInitial($comment['display_name'] ?? $comment['username'])); ?>
            </a>
            <div>
                <strong><?php echo htmlspecialchars($comment['display_name'] ?? $comment['username']); ?></strong>
                <span class="comment-detail-handle">@<?php echo htmlspecialchars($comment['username']); ?></span>
                <span class="comment-detail-time"><?php echo htmlspecialchars(formatRelativeTime($comment['created_at'])); ?></span>
            </div>
        </div>
        <div class="comment-detail-content">
            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
