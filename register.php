<?php
/**
 * register.php - Registreringssida
 */
$pageTitle = 'Registrera';
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
        $errors[] = 'Användarnamn krävs.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Användarnamnet måste vara mellan 3 och 50 tecken.';
    }

    if ($email === '') {
        $errors[] = 'E-postadress krävs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ange en giltig e-postadress.';
    }

    if ($password === '') {
        $errors[] = 'Lösenord krävs.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Lösenordet måste vara minst 6 tecken.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Bekräfta ditt lösenord.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Lösenorden matchar inte.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $errors[] = 'Användarnamnet eller e-postadressen är redan registrerad.';
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $email, $hashedPassword, 'user']);

        $success = 'Kontot skapades! Du kan nu logga in.';
        $username = '';
        $email = '';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header">
    <div class="container">
        <h1>Skapa konto</h1>
        <p>Registrera dig för att börja kvittra och delta i flödet.</p>
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
                        <h2 class="auth-title">Registrera konto</h2>
                        <p class="auth-subtitle">Fyll i uppgifterna nedan för att skapa ett nytt konto.</p>

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
                                <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>" class="alert-link">Logga in här</a>.
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="auth-form">
                            <div class="mb-3">
                                <label for="username" class="form-label">Användarnamn</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    required
                                    minlength="3"
                                    maxlength="50"
                                    placeholder="Välj ett användarnamn"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-postadress</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    required
                                    placeholder="din@epost.se"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Lösenord</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    minlength="6"
                                    placeholder="Minst 6 tecken"
                                >
                                <div class="form-text">Välj ett lösenord med minst 6 tecken.</div>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Bekräfta lösenord</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirm_password"
                                    name="confirm_password"
                                    required
                                    placeholder="Skriv lösenordet igen"
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100 auth-submit">
                                Skapa konto
                            </button>

                            <p class="auth-switch text-center mb-0">
                                Har du redan ett konto?
                                <a href="<?php echo htmlspecialchars(appUrl('login.php')); ?>">Logga in här</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
