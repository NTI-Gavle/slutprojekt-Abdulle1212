<?php
/**
 * search.php - Search users, posts, and hashtags
 */
$pageTitle = 'Search';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$viewerId = (int) getUserId();
$query = trim($_GET['q'] ?? '');
$users = [];
$posts = [];
$trendingHashtags = getTrendingHashtags($pdo, 10);

if (!empty($query)) {
    $pageTitle = 'Search: ' . $query;
    
    if (strpos($query, '#') === 0) {
        $tag = ltrim($query, '#');
        $posts = getPostsByHashtag($pdo, $tag, $viewerId);
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, username, display_name, bio, avatar_url, is_verified
             FROM users
             WHERE username LIKE ? OR display_name LIKE ?
             ORDER BY username ASC
             LIMIT 20'
        );
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        $users = $stmt->fetchAll();
        
        $stmt = $pdo->prepare(
            'SELECT p.*, u.username, u.display_name, u.avatar_url, u.is_verified
             FROM posts p
             JOIN users u ON p.user_id = u.id
             WHERE p.content LIKE ?
             ORDER BY p.created_at DESC
             LIMIT 20'
        );
        $stmt->execute(['%' . $query . '%']);
        $posts = $stmt->fetchAll();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<header class="main-header">
    <h1><i class="bi bi-search me-2"></i>Search</h1>
</header>

<div class="search-container">
    <div class="search-box-wrapper">
        <form action="search.php" method="GET" class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" name="q" placeholder="Search users, posts, or #hashtags" value="<?php echo htmlspecialchars($query); ?>" autofocus>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <?php if (empty($query)): ?>
    <div class="trending-section">
        <h2><i class="bi bi-hash me-2"></i>Trending now</h2>
        <?php if (empty($trendingHashtags)): ?>
        <p class="text-muted">No trending hashtags yet.</p>
        <?php else: ?>
        <div class="trending-grid">
            <?php foreach ($trendingHashtags as $trend): ?>
            <a href="<?php echo htmlspecialchars(appUrl('search.php?q=%23' . urlencode($trend['tag']))); ?>" class="trending-card">
                <span class="trending-tag">#<?php echo htmlspecialchars($trend['tag']); ?></span>
                <span class="trending-count"><?php echo (int) $trend['posts_count']; ?> posts</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    
    <?php if (!empty($users)): ?>
    <div class="search-results-section">
        <h3><i class="bi bi-people me-2"></i>People</h3>
        <div class="user-list">
            <?php foreach ($users as $user): ?>
            <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($user['username']))); ?>" class="user-card">
                <div class="user-avatar">
                    <?php echo htmlspecialchars(userInitial($user['display_name'] ?? $user['username'])); ?>
                </div>
                <div class="user-info">
                    <strong>
                        <?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?>
                        <?php if ($user['is_verified']): ?>
                        <i class="bi bi-patch-check-fill verified-badge"></i>
                        <?php endif; ?>
                    </strong>
                    <span class="user-handle">@<?php echo htmlspecialchars($user['username']); ?></span>
                    <?php if ($user['bio']): ?>
                    <p class="user-bio"><?php echo htmlspecialchars(substr($user['bio'], 0, 100)); ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($posts)): ?>
    <div class="search-results-section">
        <h3><i class="bi bi-chat-dots me-2"></i>Posts</h3>
        <div class="feed-post-list">
            <?php $redirectTarget = 'search.php?q=' . urlencode($query); ?>
            <?php foreach ($posts as $post): ?>
                <?php require __DIR__ . '/../includes/post_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($users) && empty($posts)): ?>
    <div class="card kvitter-card feed-empty-state">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-search"></i>
            <h3 class="mt-3">No results found</h3>
            <p>Try a different search term.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
