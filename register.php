<?php
/**
 * register.php - Registration page
 */
$pageTitle = 'Sign Up';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];
$success = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } else {
        $passwordErrors = validatePassword($password);
        $errors = array_merge($errors, $passwordErrors);
    }

    if ($confirmPassword === '') {
        $errors[] = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errors[] = 'The username or email is already registered.';
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $displayName = ucfirst($username);
        
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash, display_name) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $email, $hashedPassword, $displayName]);

        $success = 'Account created! You can now log in.';
        $username = '';
        $email = '';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header">
    <div class="container">
        <h1>Create account</h1>
        <p>Sign up to start kvitting and joining the feed.</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card auth-card auth-card-modern">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h2 class="auth-title">Create account</h2>
                        <p class="auth-subtitle">Fill in the details below to create a new account.</p>

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
                                <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>" class="alert-link">Log in here</a>.
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="auth-form">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    required
                                    minlength="3"
                                    maxlength="30"
                                    pattern="[a-zA-Z0-9_]+"
                                    placeholder="Choose a username"
                                >
                                <div class="form-text">Letters, numbers, and underscores only.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    required
                                    placeholder="your@email.com"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    minlength="8"
                                    placeholder="At least 8 characters"
                                >
                                <div class="form-text">Must include uppercase, lowercase, and a number.</div>
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
                                Create account
                            </button>

                            <p class="auth-switch text-center mb-0">
                                Already have an account?
                                <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>">Log in here</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
