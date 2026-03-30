<?php
/**
 * login.php - Inloggningssida
 */
$pageTitle = 'Logga in';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$errors = [];
$loginInput = '';
$loggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($loginInput === '') {
        $errors[] = 'Fyll i användarnamn eller e-postadress.';
    }

    if ($password === '') {
        $errors[] = 'Fyll i ditt lösenord.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$loginInput, $loginInput]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            redirectTo('index.php');
        }

        $errors[] = 'Felaktigt användarnamn, e-postadress eller lösenord.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-page-header">
    <div class="container">
        <h1>Välkommen tillbaka</h1>
        <p>Logga in för att fortsätta till ditt flöde.</p>
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
                        <h2 class="auth-title">Logga in</h2>
                        <p class="auth-subtitle">Använd ditt användarnamn eller din e-postadress.</p>

                        <?php if ($loggedOut): ?>
                            <div class="alert alert-success" role="alert">
                                Du har loggat ut.
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
                                <label for="login" class="form-label">Användarnamn eller e-post</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="login"
                                    name="login"
                                    value="<?php echo htmlspecialchars($loginInput); ?>"
                                    required
                                    placeholder="t.ex. abdulle eller namn@epost.se"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Lösenord</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="Ditt lösenord"
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100 auth-submit">
                                Logga in
                            </button>

                            <p class="auth-switch text-center mb-0">
                                Har du inget konto?
                                <a href="<?php echo htmlspecialchars(appUrl('register.php')); ?>">Registrera dig här</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
