<?php
/**
 * forgot_password.php - Request password reset
 */
$pageTitle = 'Forgot Password';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mail.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $errors[] = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));

            $stmt = $pdo->prepare('DELETE FROM password_resets WHERE user_id = ? OR expires_at < NOW()');
            $stmt->execute([$user['id']]);

            $stmt = $pdo->prepare(
                'INSERT INTO password_resets (user_id, token, expires_at, used) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)'
            );
            $stmt->execute([$user['id'], $token]);

            $resetLink = absoluteAppUrl('reset_password.php') . '?token=' . urlencode($token);
            $mailSent = sendPasswordResetMail($user['email'], $user['username'], $resetLink);
            
            if (!$mailSent && $_SERVER['HTTP_HOST'] === 'localhost' && isset($_GET['dev'])) {
                $_SESSION['dev_reset_link'] = $resetLink;
            }
        }

        $success = 'If the email exists in our system, a password reset link has been sent. Check your inbox and spam folder.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header auth-page-header-alt">
    <div class="container">
        <h1>Reset your password</h1>
        <p>No worries. Enter your email and we'll send you a secure link to choose a new password.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card auth-card auth-card-modern auth-card-featured">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-icon auth-icon-soft">
                            <i class="bi bi-envelope-paper-heart"></i>
                        </div>
                        <h2 class="auth-title">Forgot password?</h2>
                        <p class="auth-subtitle">Enter the same email you used to register your account.</p>

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
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                            </div>

                            <div class="auth-info-strip">
                                <div>
                                    <strong>Email sent</strong>
                                    <span>Check your inbox and spam folder if you don't see the email right away.</span>
                                </div>
                                <i class="bi bi-envelope-check"></i>
                            </div>

                            <?php if (isset($_SESSION['dev_reset_link'])): ?>
                                <div class="auth-demo-panel mt-3">
                                    <div class="auth-demo-badge">Dev mode (?dev=1)</div>
                                    <p class="mb-2">Email not configured locally. Use this link for testing:</p>
                                    <a class="auth-reset-link" href="<?php echo htmlspecialchars($_SESSION['dev_reset_link']); ?>">
                                        <?php echo htmlspecialchars($_SESSION['dev_reset_link']); ?>
                                    </a>
                                </div>
                                <?php unset($_SESSION['dev_reset_link']); ?>
                            <?php endif; ?>

                            <div class="auth-actions mt-4">
                                <a class="btn btn-outline-primary w-100" href="<?php echo htmlspecialchars(appUrl('login.php?reset_requested=1')); ?>">
                                    Back to login
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="auth-info-strip">
                                <div>
                                    <strong>Secure reset</strong>
                                    <span>The link works only once and expires after 60 minutes.</span>
                                </div>
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <form method="POST" action="" class="auth-form mt-4">
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email address</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?php echo htmlspecialchars($email); ?>"
                                        required
                                        placeholder="name@email.com"
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary w-100 auth-submit">
                                    Send reset email
                                </button>
                            </form>
                        <?php endif; ?>

                        <p class="auth-switch text-center mb-0 mt-4">
                            Remember your password?
                            <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>">Log in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
