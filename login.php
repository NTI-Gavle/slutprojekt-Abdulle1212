<?php
/**
 * login.php - Login page
 */
$pageTitle = 'Login';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];
$loginInput = '';
$loggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
$passwordResetRequested = isset($_GET['reset_requested']) && $_GET['reset_requested'] === '1';
$passwordResetComplete = isset($_GET['password_reset']) && $_GET['password_reset'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['login'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($loginInput === '') {
        $errors[] = 'Please enter your username or email.';
    }

    if ($password === '') {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash, role
             FROM users
             WHERE username = ? OR email = ?
             LIMIT 1'
        );
        $stmt->execute([$loginInput, $loginInput]);
        $user = $stmt->fetch();

        $passwordMatches = false;

        if ($user) {
            $hash = $user['password_hash'] ?? '';
            $plain = $user['password'] ?? '';
            
            if (!empty($hash)) {
                $passwordMatches = password_verify($password, $hash);
            } elseif (!empty($plain)) {
                $passwordMatches = hash_equals($plain, $password) || password_verify($password, $plain);
            }
        }

        if ($user && $passwordMatches) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';

            redirectTo('index.php');
        }

        $errors[] = 'Invalid username, email, or password.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header">
    <div class="container">
        <h1>Welcome back</h1>
        <p>Log in to continue to your feed.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card auth-card auth-card-modern">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-icon">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <h2 class="auth-title">Login</h2>
                        <p class="auth-subtitle">Use your username or email address.</p>

                        <?php if ($loggedOut): ?>
                            <div class="alert alert-success" role="alert">
                                You have been logged out.
                            </div>
                        <?php endif; ?>

                        <?php if ($passwordResetRequested): ?>
                            <div class="alert alert-info" role="alert">
                                If the email exists in our system, a reset link has been created.
                            </div>
                        <?php endif; ?>

                        <?php if ($passwordResetComplete): ?>
                            <div class="alert alert-success" role="alert">
                                Your password has been updated. You can now log in with your new password.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="auth-form">
                            <div class="mb-3">
                                <label for="login" class="form-label">Username or email</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="login"
                                    name="login"
                                    value="<?php echo htmlspecialchars($loginInput); ?>"
                                    required
                                    placeholder="e.g. username or name@email.com"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="Your password"
                                >
                                <div class="auth-helper-row">
                                    <span class="form-text mb-0">Use your account to continue.</span>
                                    <a class="auth-inline-link" href="<?php echo htmlspecialchars(appUrl('forgot_password.php')); ?>">Forgot password?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 auth-submit">
                                Log in
                            </button>

                            <p class="auth-switch text-center mb-0">
                                Don't have an account?
                                <a href="<?php echo htmlspecialchars(appUrl('register.php')); ?>">Sign up here</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
