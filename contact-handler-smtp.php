<?php
// Contact Form Handler with SMTP Authentication (PHPMailer version)
// Uses secure config.php for credentials
// Implements POST-redirect-GET pattern to prevent duplicate submissions

// Start session for duplicate submission prevention
session_start();

// Security: Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

// Load secure configuration
require_once 'config.php';

// Check if PHPMailer is available
$phpmailer_available = false;
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $phpmailer_available = true;
    }
}

// If PHPMailer not available, use basic mail() as fallback
if (!$phpmailer_available) {
    // Fallback: Use basic mail() function
    require_once 'contact-handler.php';
    exit;
}

// Use PHPMailer classes
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
    header('Location: contact.html?success=1&duplicate=1');
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
    header('Location: contact.html?error=' . urlencode(implode(', ', $errors)));
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
    
    // Enable verbose debug output (disable in production)
    // $mail->SMTPDebug = 2;
    
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
    // Log error
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
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

// Store submission success in session (prevents duplicate on refresh)
if ($mail_sent) {
    $_SESSION['form_submitted'] = true;
    $_SESSION['submission_token'] = $submission_token;
    $_SESSION['submission_time'] = time();
    
    // Redirect with token (POST-redirect-GET pattern)
    header('Location: contact.html?success=1&token=' . $submission_token);
    exit;
} else {
    // Store error in session and redirect
    $_SESSION['form_error'] = 'Failed to send message. Please try again or contact us directly.';
    header('Location: contact.html?error=1');
    exit;
}
?>

