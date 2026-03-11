<?php
include 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
                        // Generate secure random token
            $token = bin2hex(random_bytes(32));
            
            // Spara token till databasen och sätt utgångstid till +1 timme baserat på databasens klocka
            $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
            $update_stmt->bind_param("si", $token, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            
            // Send reset email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/Gymnsiearbete/reset-password.php?token=" . $token;
            
            require 'phpmailer/PHPMailer.php';
            require 'phpmailer/SMTP.php';
            require 'phpmailer/Exception.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;
                
                // Email settings
                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($email, $user['full_name']);
                
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - Grand Aurora Hotel';
                $mail->Body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <h2 style='color: #3D52A0;'>Password Reset Request</h2>
                            <p>Hello {$user['full_name']},</p>
                            <p>We received a request to reset your password for your Grand Aurora Hotel account.</p>
                            <p>Click the button below to reset your password:</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$reset_link}' style='background-color: #3D52A0; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                            </p>
                            <p>Or copy and paste this link into your browser:</p>
                            <p style='background-color: #f4f4f4; padding: 10px; word-break: break-all;'>{$reset_link}</p>
                            <p><strong>This link will expire in 1 hour.</strong></p>
                            <p>If you didn't request this password reset, please ignore this email.</p>
                            <p>Best regards,<br>Grand Aurora Hotel Team</p>
                        </div>
                    </body>
                    </html>
                ";
                
                $mail->send();
                $success = 'Password reset instructions have been sent to your email address.';
            } catch (Exception $e) {
                $error = 'Failed to send reset email. Please try again later.';
            }
        } else {
            // Don't reveal if email exists or not for security
            $success = 'If an account exists with that email, password reset instructions have been sent.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include 'header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Forgot Password</h1>
        <p>Enter your email to reset your password</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Reset Password</h3>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="forgot-password.php">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your registered email">
                                <small class="text-muted">We'll send you a password reset link</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
                            
                            <div class="text-center">
                                <p class="text-muted mb-0">Remember your password? <a href="login.php">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
