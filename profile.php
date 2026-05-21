<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$username = trim($_GET['user'] ?? '');
if ($username === '') {
    redirectTo('index.php');
}

$viewerId = (int) getUserId();
$profile = getProfileByUsername($pdo, $username, $viewerId);

if (!$profile) {
    redirectTo('index.php?error=' . urlencode('User not found.'));
}

$posts = fetchPosts($pdo, $viewerId, (int) $profile['id']);
$suggestedProfiles = fetchSuggestedProfiles($pdo, $viewerId, 3);
$pageTitle = '@' . $profile['username'];

$activeTab = $_GET['tab'] ?? 'posts';
$followersList = [];
$followingList = [];

if ($activeTab === 'followers') {
    $followersList = getFollowers($pdo, (int) $profile['id']);
} elseif ($activeTab === 'following') {
    $followingList = getFollowing($pdo, (int) $profile['id']);
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="profile-layout">
    <aside class="profile-side-nav">
        <div class="profile-side-card">
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('index.php')); ?>">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </a>
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('search.php')); ?>">
                <i class="bi bi-search"></i>
                <span>Search</span>
            </a>
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('notifications.php')); ?>">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
            <a class="profile-side-link active" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']))); ?>">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('bookmarks.php')); ?>">
                <i class="bi bi-bookmark"></i>
                <span>Bookmarks</span>
            </a>
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('messages.php')); ?>">
                <i class="bi bi-envelope"></i>
                <span>Messages</span>
                <?php $unreadMsg = getUnreadMessageCount($pdo, $viewerId); if ($unreadMsg > 0): ?>
                    <span class="msg-badge"><?php echo $unreadMsg > 99 ? '99+' : $unreadMsg; ?></span>
                <?php endif; ?>
            </a>
            <a class="profile-side-link" href="<?php echo htmlspecialchars(appUrl('edit_profile.php')); ?>">
                <i class="bi bi-sliders"></i>
                <span>Settings</span>
            </a>
        </div>
    </aside>

    <section class="profile-main-column">
        <article class="profile-card">
            <div class="profile-cover"></div>
            <div class="profile-card-body">
                <div class="profile-top-row">
                    <div class="profile-avatar-xl profile-avatar-float">
                        <?php echo htmlspecialchars(userInitial($profile['display_name'] ?? $profile['username'])); ?>
                    </div>
                    <div class="profile-top-actions">
                        <?php if ($profile['is_self']): ?>
                            <a class="btn btn-outline-primary" href="<?php echo htmlspecialchars(appUrl('edit_profile.php')); ?>">
                                <i class="bi bi-pencil me-1"></i>Edit profile
                            </a>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars(appUrl('messages.php?with=' . (int) $profile['id'])); ?>" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="bi bi-envelope"></i> Message
                            </a>
                            <form method="POST" action="<?php echo htmlspecialchars(appUrl('toggle_follow.php')); ?>" class="d-inline">
                                <input type="hidden" name="followed_id" value="<?php echo (int) $profile['id']; ?>">
                                <input type="hidden" name="redirect_to" value="profile.php?user=<?php echo urlencode($profile['username']); ?>">
                                <button type="submit" class="btn <?php echo $profile['is_following'] ? 'btn-outline-primary' : 'btn-primary'; ?>">
                                    <?php echo $profile['is_following'] ? 'Unfollow' : 'Follow'; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-identity">
                    <h1>
                        <?php echo htmlspecialchars($profile['display_name'] ?? $profile['username']); ?>
                        <?php if ($profile['is_verified']): ?>
                        <i class="bi bi-patch-check-fill verified-badge"></i>
                        <?php endif; ?>
                    </h1>
                    <p class="profile-handle">@<?php echo htmlspecialchars($profile['username']); ?></p>
                    <p class="profile-bio-text"><?php echo htmlspecialchars($profile['bio'] !== '' ? $profile['bio'] : 'No bio yet.'); ?></p>
                </div>

                <div class="profile-meta-line">
                    <?php if ($profile['location']): ?>
                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($profile['location']); ?></span>
                    <?php endif; ?>
                    <?php if ($profile['website']): ?>
                    <span><i class="bi bi-link-45deg"></i> <a href="<?php echo htmlspecialchars($profile['website']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($profile['website']); ?></a></span>
                    <?php endif; ?>
                    <span><i class="bi bi-calendar3"></i> Joined <?php echo date('M Y', strtotime($profile['created_at'])); ?></span>
                </div>

                <div class="profile-follow-stats">
                    <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']) . '&tab=following')); ?>" class="stat-link">
                        <strong><?php echo (int) $profile['following_count']; ?></strong> following
                    </a>
                    <a href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']) . '&tab=followers')); ?>" class="stat-link">
                        <strong><?php echo (int) $profile['follower_count']; ?></strong> followers
                    </a>
                </div>
            </div>
        </article>

        <section class="profile-tab-shell">
            <div class="profile-tab-header">
                <a class="profile-tab <?php echo $activeTab === 'posts' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']) . '&tab=posts')); ?>">Posts</a>
                <a class="profile-tab <?php echo $activeTab === 'followers' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']) . '&tab=followers')); ?>">Followers</a>
                <a class="profile-tab <?php echo $activeTab === 'following' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($profile['username']) . '&tab=following')); ?>">Following</a>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <?php if ($activeTab === 'followers'): ?>
                <?php if (empty($followersList)): ?>
                    <div class="card kvitter-card feed-empty-state mt-3">
                        <div class="card-body text-center text-muted py-5">
                            <p>No followers yet.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="user-list mt-3">
                        <?php foreach ($followersList as $user): ?>
                            <a class="user-list-item" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($user['username']))); ?>">
                                <span class="user-list-avatar"><?php echo htmlspecialchars(userInitial($user['display_name'] ?? $user['username'])); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?></strong>
                                    <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php elseif ($activeTab === 'following'): ?>
                <?php if (empty($followingList)): ?>
                    <div class="card kvitter-card feed-empty-state mt-3">
                        <div class="card-body text-center text-muted py-5">
                            <p>Not following anyone yet.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="user-list mt-3">
                        <?php foreach ($followingList as $user): ?>
                            <a class="user-list-item" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($user['username']))); ?>">
                                <span class="user-list-avatar"><?php echo htmlspecialchars(userInitial($user['display_name'] ?? $user['username'])); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?></strong>
                                    <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <?php if (empty($posts)): ?>
                    <div class="card kvitter-card feed-empty-state mt-3">
                        <div class="card-body text-center text-muted py-5">
                            <i class="bi bi-chat-square-text"></i>
                            <p class="mt-3 mb-0">No posts yet on this profile.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="feed-post-list mt-3">
                        <?php $redirectTarget = 'profile.php?user=' . urlencode($profile['username']); ?>
                        <?php foreach ($posts as $post): ?>
                            <?php require __DIR__ . '/../includes/post_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </section>

    <aside class="profile-right-rail">
        <a href="<?php echo htmlspecialchars(appUrl('search.php')); ?>" class="profile-rail-card profile-rail-search-link">
            <i class="bi bi-search"></i>
            <span>Search Kvitter</span>
        </a>

        <?php if (!empty($suggestedProfiles)): ?>
            <div class="profile-rail-card">
                <h3>You might like</h3>
                <div class="profile-rail-list">
                    <?php foreach ($suggestedProfiles as $suggestedProfile): ?>
                        <a class="profile-rail-item profile-rail-link" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($suggestedProfile['username']))); ?>">
                            <span class="suggestion-avatar"><?php echo htmlspecialchars(userInitial($suggestedProfile['display_name'] ?? $suggestedProfile['username'])); ?></span>
                            <div>
                                <strong><?php echo htmlspecialchars($suggestedProfile['display_name'] ?? $suggestedProfile['username']); ?></strong>
                                <small>@<?php echo htmlspecialchars($suggestedProfile['username']); ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-rail-card">
            <h3>Overview</h3>
            <div class="feed-side-list compact">
                <div class="feed-side-item">
                    <span>Username</span>
                    <strong>@<?php echo htmlspecialchars($profile['username']); ?></strong>
                </div>
                <div class="feed-side-item">
                    <span>Status</span>
                    <strong><?php echo $profile['is_self'] ? 'This is you' : ($profile['is_following'] ? 'Following' : 'Not following yet'); ?></strong>
                </div>
            </div>
        </div>
    </aside>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
