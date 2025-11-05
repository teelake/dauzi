<?php
// Contact Form Handler with SMTP Authentication (PHPMailer version)
// Uses secure config.php for credentials
// Implements POST-redirect-GET pattern to prevent duplicate submissions

// Start output buffering to prevent headers already sent errors
ob_start();

// Start session for duplicate submission prevention
session_start();

// Security: Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    ob_end_flush();
    exit;
}

// Load secure configuration (must be before any output)
require_once 'config.php';

// Check if PHPMailer is available
$phpmailer_available = false;
$phpmailer_error = '';

// Try to load PHPMailer
if (file_exists('vendor/autoload.php')) {
    try {
        require 'vendor/autoload.php';
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $phpmailer_available = true;
        } else {
            $phpmailer_error = 'PHPMailer class not found after autoload';
        }
    } catch (Exception $e) {
        $phpmailer_error = 'Error loading PHPMailer: ' . $e->getMessage();
        error_log("PHPMailer Load Error: " . $phpmailer_error);
    }
} else {
    $phpmailer_error = 'vendor/autoload.php not found';
    error_log("PHPMailer not found: vendor/autoload.php missing");
}

// If PHPMailer not available, redirect with error
if (!$phpmailer_available) {
    $base_path = dirname($_SERVER['SCRIPT_NAME']);
    if ($base_path === '/') {
        $base_path = '';
    }
    ob_end_clean();
    error_log("PHPMailer unavailable: " . $phpmailer_error);
    header('Location: ' . $base_path . '/contact.html?error=' . urlencode('Email service temporarily unavailable. Please contact us directly at info@dauziconsulting.com'));
    exit;
}

// Use PHPMailer classes (must be at top level, not inside if statement)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check for duplicate submission (same content within 30 seconds)
$submission_hash = isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']) 
    ? md5($_POST['name'] . $_POST['email'] . $_POST['message']) 
    : '';

if (isset($_SESSION['last_submission_hash']) && 
    $_SESSION['last_submission_hash'] === $submission_hash && 
    isset($_SESSION['last_submission_time']) && 
    (time() - $_SESSION['last_submission_time']) < 30) {
    // Duplicate submission detected - redirect without processing
    $base_path = dirname($_SERVER['SCRIPT_NAME']);
    if ($base_path === '/') {
        $base_path = '';
    }
    ob_end_clean();
    header('Location: ' . $base_path . '/contact.html?success=1&duplicate=1');
    exit;
}

// Sanitize and validate input
$name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
$subject = isset($_POST['subject']) ? trim(htmlspecialchars($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    $base_path = dirname($_SERVER['SCRIPT_NAME']);
    if ($base_path === '/') {
        $base_path = '';
    }
    ob_end_clean();
    header('Location: ' . $base_path . '/contact.html?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Email body
$email_body = "New contact form submission from Dauzi Consulting website\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: " . ($phone ? $phone : 'Not provided') . "\n";
$email_body .= "Subject: $subject\n\n";
$email_body .= "Message:\n$message\n\n";
$email_body .= "---\n";
$email_body .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
$email_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";

$mail_sent = false;

try {
    $mail = new PHPMailer(true);
    
    // SMTP Configuration from secure config file
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    
    // Enable verbose debug output for troubleshooting (disable after fixing)
    $mail->SMTPDebug = 0; // Set to 2 for detailed debug output
    
    // Sender
    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    $mail->addReplyTo($email, $name);
    
    // Recipients
    $mail->addAddress(EMAIL_PRIMARY);
    $mail->addCC(EMAIL_SECONDARY);
    
    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Contact Form Submission: ' . $subject;
    $mail->Body    = $email_body;
    $mail->CharSet = 'UTF-8';
    
    $mail->send();
    $mail_sent = true;
    
} catch (Exception $e) {
    // Log detailed error
    $error_message = "PHPMailer Error: " . $mail->ErrorInfo;
    error_log($error_message);
    error_log("Exception: " . $e->getMessage());
    $mail_sent = false;
}

// Save to database if enabled
$db_saved = false;
if (defined('ENABLE_DATABASE') && ENABLE_DATABASE) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, subject, message, submitted_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$name, $email, $phone ?: null, $subject, $message, $_SERVER['REMOTE_ADDR']]);
        $db_saved = true;
    } catch (PDOException $e) {
        // Log error but don't fail the form submission
        error_log("Database error: " . $e->getMessage());
    }
}

// Store submission hash and time to prevent duplicates
if ($mail_sent && $submission_hash) {
    $_SESSION['last_submission_hash'] = $submission_hash;
    $_SESSION['last_submission_time'] = time();
}

// Generate unique submission token to prevent duplicates
$submission_token = bin2hex(random_bytes(16));

// Get base path for subdirectory support
$base_path = dirname($_SERVER['SCRIPT_NAME']);
if ($base_path === '/') {
    $base_path = '';
}
$redirect_url = $base_path . '/contact.html';

// Store submission success in session (prevents duplicate on refresh)
if ($mail_sent) {
    $_SESSION['form_submitted'] = true;
    $_SESSION['submission_token'] = $submission_token;
    $_SESSION['submission_time'] = time();
    
    // Clear output buffer before redirect
    ob_end_clean();
    
    // Redirect with token (POST-redirect-GET pattern)
    header('Location: ' . $redirect_url . '?success=1&token=' . $submission_token);
    exit;
} else {
    // Store error in session and redirect
    $_SESSION['form_error'] = 'Failed to send message. Please try again or contact us directly.';
    
    // Clear output buffer before redirect
    ob_end_clean();
    
    header('Location: ' . $redirect_url . '?error=1');
    exit;
}
?>

