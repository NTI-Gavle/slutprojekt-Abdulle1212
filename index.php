<?php
/**
 * index.php - Home Feed
 */
$pageTitle = 'Feed';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$viewerId = (int) getUserId();
$posts = fetchPosts($pdo, $viewerId);
$suggestedProfiles = fetchSuggestedProfiles($pdo, $viewerId);
$trendingHashtags = getTrendingHashtags($pdo, 5);

$userCountStmt = $pdo->query('SELECT COUNT(*) FROM users');
$userCount = (int) $userCountStmt->fetchColumn();
$postCount = count($posts);
$latestPostDate = $postCount > 0 ? formatRelativeTime($posts[0]['created_at']) : 'No activity yet';

$myPosts = 0;
$totalLikes = 0;

foreach ($posts as $post) {
    if ((int) $post['user_id'] === $viewerId) {
        $myPosts++;
    }
    if ($post['viewer_liked']) {
        $totalLikes++;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if (isAdmin()): ?>
<section class="feed-hero">
    <div class="feed-hero-grid">
        <div class="feed-hero-main">
            <div class="feed-badge">
                <i class="bi bi-lightning-charge-fill"></i>
                Phase 1 live
            </div>
            <h1>Welcome back, <?php echo htmlspecialchars(getUsername() ?? 'friend'); ?></h1>
            <p>Kvitter now has profiles, followers, likes, comments, bookmarks, and direct messages. The feed is starting to feel like a real social network.</p>

            <div class="feed-stats">
                <div class="feed-stat-card">
                    <span class="feed-stat-value"><?php echo $postCount; ?></span>
                    <span class="feed-stat-label">Posts in feed</span>
                </div>
                <div class="feed-stat-card">
                    <span class="feed-stat-value"><?php echo $userCount; ?></span>
                    <span class="feed-stat-label">Users</span>
                </div>
                <div class="feed-stat-card">
                    <span class="feed-stat-value"><?php echo $myPosts; ?></span>
                    <span class="feed-stat-label">Your posts</span>
                </div>
                <div class="feed-stat-card">
                    <span class="feed-stat-value"><?php echo $totalLikes; ?></span>
                    <span class="feed-stat-label">Liked by you</span>
                </div>
            </div>
        </div>

        <aside class="feed-hero-side">
            <h2>Quick overview</h2>
            <div class="feed-side-list">
                <div class="feed-side-item">
                    <span>Latest activity</span>
                    <strong><?php echo htmlspecialchars($latestPostDate); ?></strong>
                </div>
                <div class="feed-side-item">
                    <span>Your role</span>
                    <strong><?php echo isAdmin() ? 'Admin' : 'Member'; ?></strong>
                </div>
                <div class="feed-side-item">
                    <span>Profile</span>
                    <strong><a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode(getUsername() ?? ''))); ?>">Open your page</a></strong>
                </div>
            </div>
        </aside>
    </div>
</section>
<?php endif; ?>

<div class="row g-4">
    <div class="col-xl-4 col-lg-5">
        <div class="feed-sidebar-stack">
            <div class="clock-shell">
                <div class="clock-shell-header">
                    <span class="clock-shell-label">Live now</span>
                    <strong>Local time</strong>
                </div>
                <div class="clock-container">
                    <canvas id="clockCanvas"></canvas>
                </div>
            </div>

            <div class="card kvitter-form-card compose-card">
                <div class="card-body">
                    <div class="compose-card-header">
                        <div>
                            <h2>Write a new kvitter</h2>
                            <p>Share a thought, an update, or start a discussion.</p>
                        </div>
                        <div class="compose-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo htmlspecialchars(appUrl('create_post.php')); ?>">
                        <input type="hidden" name="redirect_to" value="index.php">
                        <div class="mb-3">
                            <textarea
                                class="form-control"
                                name="content"
                                id="kvitterContent"
                                rows="4"
                                maxlength="280"
                                required
                                placeholder="What's on your mind?"
                            ></textarea>
                        </div>
                        <div class="compose-actions">
                            <span class="char-counter" id="charCounter">280 characters left</span>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-fill me-1"></i>Publish
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="feed-tip-card profile-preview-card">
                <div class="profile-preview-head">
                    <div class="profile-avatar-large"><?php echo htmlspecialchars(userInitial(getUsername() ?? 'K')); ?></div>
                    <div>
                        <h3><?php echo htmlspecialchars(getUsername() ?? 'User'); ?></h3>
                        <p class="mb-0">Your profile is now public inside Kvitter.</p>
                    </div>
                </div>
                <a class="btn btn-outline-primary w-100 mt-3" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode(getUsername() ?? ''))); ?>">
                    View my profile
                </a>
            </div>

            <?php if (!empty($trendingHashtags)): ?>
                <div class="feed-tip-card">
                    <h3><i class="bi bi-hash me-2"></i>Trending</h3>
                    <div class="trending-list">
                        <?php foreach ($trendingHashtags as $trend): ?>
                        <a href="<?php echo htmlspecialchars(appUrl('search.php?q=%23' . urlencode($trend['tag']))); ?>" class="trending-item">
                            <span class="trending-tag">#<?php echo htmlspecialchars($trend['tag']); ?></span>
                            <span class="trending-count"><?php echo (int) $trend['posts_count']; ?> posts</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($suggestedProfiles)): ?>
                <div class="feed-tip-card">
                    <h3><i class="bi bi-person-plus me-2"></i>Who to follow</h3>
                    <div class="suggestion-list">
                        <?php foreach ($suggestedProfiles as $suggestedProfile): ?>
                            <div class="suggestion-item">
                                <a class="suggestion-user" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($suggestedProfile['username']))); ?>">
                                    <span class="suggestion-avatar"><?php echo htmlspecialchars(userInitial($suggestedProfile['display_name'] ?? $suggestedProfile['username'])); ?></span>
                                    <span>
                                        <strong><?php echo htmlspecialchars($suggestedProfile['display_name'] ?? $suggestedProfile['username']); ?></strong>
                                        <small><?php echo (int) $suggestedProfile['follower_count']; ?> followers</small>
                                    </span>
                                </a>
                                <form method="POST" action="<?php echo htmlspecialchars(appUrl('toggle_follow.php')); ?>">
                                    <input type="hidden" name="followed_id" value="<?php echo (int) $suggestedProfile['id']; ?>">
                                    <input type="hidden" name="redirect_to" value="index.php">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Follow</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="feed-header-row">
            <div>
                <h2><i class="bi bi-rss me-2"></i>Feed</h2>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="card kvitter-card feed-empty-state">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-chat-dots"></i>
                    <p class="mt-3 mb-0">No posts yet. Be the first to create some activity.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="feed-post-list">
                <?php $redirectTarget = 'index.php'; ?>
                <?php foreach ($posts as $post): ?>
                    <?php require __DIR__ . '/../includes/post_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('kvitterContent');
    const counter = document.getElementById('charCounter');

    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            const remaining = 280 - this.value.length;
            counter.textContent = remaining + ' characters left';
            counter.classList.toggle('warning', remaining <= 20);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
