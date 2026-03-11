<?php
include 'config.php';

$success = '';
$error = '';
$valid_token = false;
$token = '';

// Get token from GET or POST
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
}

// Verify token
if (!empty($token)) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, full_name, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $valid_token = true;
        $user = $result->fetch_assoc();
    } else {
        $error = 'Invalid or expired reset token. Please request a new password reset.';
    }
    
    $stmt->close();
    $conn->close();
} else {
    $error = 'No reset token provided.';
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $conn = getDBConnection();
        
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        
        if ($stmt->execute()) {
            $success = 'Your password has been successfully reset. You can now login with your new password.';
            $valid_token = false; // Prevent form from showing again
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include 'header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Reset Password</h1>
        <p>Create your new password</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">New Password</h3>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-primary">Go to Login</a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($valid_token && !$success): ?>
                        <form method="POST" action="reset-password.php">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Reset Password</button>
                        </form>
                        <?php elseif (!$success): ?>
                        <div class="text-center">
                            <a href="forgot-password.php" class="btn btn-primary">Request New Reset Link</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>