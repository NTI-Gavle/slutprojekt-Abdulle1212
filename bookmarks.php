<?php
/**
 * bookmarks.php - Saved posts
 */
$pageTitle = 'Bookmarks';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$userId = (int) getUserId();
$bookmarks = getBookmarks($pdo, $userId);

require_once __DIR__ . '/../includes/header.php';
?>

<header class="main-header">
    <h1><i class="bi bi-bookmark-fill me-2"></i>Bookmarks</h1>
    <p>Posts you've saved for later.</p>
</header>

<?php if (empty($bookmarks)): ?>
<div class="card kvitter-card feed-empty-state">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-bookmark"></i>
        <h3 class="mt-3">No bookmarks yet</h3>
        <p>When you bookmark posts, they will show up here.</p>
    </div>
</div>
<?php else: ?>
<div class="feed-post-list">
    <?php $redirectTarget = 'bookmarks.php'; ?>
    <?php foreach ($bookmarks as $post): ?>
        <?php 
        $post['viewer_liked'] = $post['viewer_liked'] ?? false;
        $post['viewer_bookmarked'] = $post['viewer_bookmarked'] ?? true;
        $post['like_count'] = $post['like_count'] ?? 0;
        $post['comment_count'] = $post['comment_count'] ?? 0;
        $post['hashtags'] = [];
        $post['comments'] = [];
        ?>
        <?php require __DIR__ . '/../includes/post_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
