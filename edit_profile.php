<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/social.php';

requireLogin();

$userId = (int) getUserId();
$stmt = $pdo->prepare('SELECT id, username, display_name, bio, location, website, avatar_url, banner_url FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    redirectTo('index.php?error=' . urlencode('Your profile could not be loaded.'));
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $displayName = trim($_POST['display_name'] ?? $user['display_name']);
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $website = trim($_POST['website'] ?? '');
    
    if ($displayName === '') {
        $errors[] = 'Display name cannot be empty.';
    }
    
    if (strlen($bio) > 280) {
        $errors[] = 'Your bio can be max 280 characters.';
    }
    
    if (strlen($location) > 100) {
        $errors[] = 'Location can be max 100 characters.';
    }
    
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid website URL.';
    }
    
    if (empty($errors)) {
        updateProfile($pdo, $userId, [
            'display_name' => $displayName,
            'bio' => $bio,
            'location' => $location,
            'website' => $website,
        ]);
        $success = 'Settings saved.';
        
        $stmt = $pdo->prepare('SELECT id, username, display_name, bio, location, website FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header auth-page-header-alt">
    <div class="container">
        <h1>Settings</h1>
        <p>Update your profile and choose how Kvitter looks.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card auth-card auth-card-modern auth-card-featured profile-edit-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-icon auth-icon-soft">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <h2 class="auth-title">Profile and appearance</h2>
                        <p class="auth-subtitle">Your bio is shown on your profile. Theme is saved in your browser.</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <div class="profile-edit-preview">
                            <div class="profile-avatar-xl small"><?php echo htmlspecialchars(userInitial($user['display_name'] ?? $user['username'])); ?></div>
                            <div>
                                <strong>@<?php echo htmlspecialchars($user['username']); ?></strong>
                                <p class="mb-0 text-muted">This is how you appear in Kvitter.</p>
                            </div>
                        </div>

                        <form method="POST" action="" class="auth-form mt-4">
                            <div class="settings-section">
                                <h3 class="settings-section-title">Profile</h3>
                                
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display name</label>
                                    <input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?>" maxlength="50" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4" maxlength="280" placeholder="Tell us about yourself, your interests, or what you like to share."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    <div class="form-text">Max 280 characters.</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label"><i class="bi bi-geo-alt me-1"></i>Location</label>
                                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" maxlength="100" placeholder="e.g. Stockholm, Sweden">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label"><i class="bi bi-link-45deg me-1"></i>Website</label>
                                        <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" placeholder="https://yourwebsite.com">
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Theme</h3>
                                <p class="settings-help">Choose between light and dark mode. Changes apply instantly.</p>
                                <div class="theme-options">
                                    <label class="theme-choice">
                                        <input type="radio" name="theme" value="light">
                                        <span class="theme-choice-card theme-choice-light">
                                            <strong>Light mode</strong>
                                            <small>Light background and dark text.</small>
                                        </span>
                                    </label>
                                    <label class="theme-choice">
                                        <input type="radio" name="theme" value="dark">
                                        <span class="theme-choice-card theme-choice-dark">
                                            <strong>Dark mode</strong>
                                            <small>Dark surface with blue accent.</small>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-3 flex-wrap mt-4">
                                <button type="submit" class="btn btn-primary">Save profile</button>
                                <a class="btn btn-outline-primary" href="<?php echo htmlspecialchars(appUrl('profile.php?user=' . urlencode($user['username']))); ?>">View my profile</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
