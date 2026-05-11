<?php
/**
 * reset_password.php - Choose new password from reset link
 */
$pageTitle = 'New Password';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];
$token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
$resetRequest = null;

if ($token !== '') {
    $stmt = $pdo->prepare(
        'SELECT pr.id, pr.user_id, pr.token, pr.expires_at, pr.used, u.username
         FROM password_resets pr
         INNER JOIN users u ON u.id = pr.user_id
         WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()'
    );
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$resetRequest) {
        $errors[] = 'The link is invalid or has expired. Request a new reset.';
    }

    if ($password === '') {
        $errors[] = 'Please enter a new password.';
    } else {
        $passwordErrors = validatePassword($password);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
        }
    }

    if ($confirmPassword === '') {
        $errors[] = 'Please confirm your new password.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors) && $resetRequest) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$hashedPassword, $resetRequest['user_id']]);

        $stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
        $stmt->execute([$resetRequest['id']]);

        redirectTo('login.php?password_reset=1');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header auth-page-header-alt">
    <div class="container">
        <h1>Choose a new password</h1>
        <p>Create a new password to regain access to your account.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card auth-card auth-card-modern auth-card-featured">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-icon auth-icon-soft">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h2 class="auth-title">Create new password</h2>

                        <?php if (!$resetRequest): ?>
                            <p class="auth-subtitle">This reset link is no longer valid.</p>
                            <div class="alert alert-danger" role="alert">
                                Request a new reset link to continue.
                            </div>
                            <a class="btn btn-primary w-100 auth-submit" href="<?php echo htmlspecialchars(appUrl('forgot_password.php')); ?>">
                                Request new link
                            </a>
                        <?php else: ?>
                            <p class="auth-subtitle">
                                Account: <strong><?php echo htmlspecialchars($resetRequest['username']); ?></strong>
                            </p>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="auth-info-strip">
                                <div>
                                    <strong>Tips for better security</strong>
                                    <span>Choose at least 8 characters with uppercase, lowercase, and a number.</span>
                                </div>
                                <i class="bi bi-key"></i>
                            </div>

                            <form method="POST" action="" class="auth-form mt-4">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        required
                                        minlength="8"
                                        placeholder="At least 8 characters"
                                    >
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="confirm_password"
                                        name="confirm_password"
                                        required
                                        placeholder="Re-enter your password"
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary w-100 auth-submit">
                                    Save new password
                                </button>
                            </form>
                        <?php endif; ?>

                        <p class="auth-switch text-center mb-0 mt-4">
                            <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>">Back to login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
