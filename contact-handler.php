<?php
// Contact Form Handler for Dauzi Consulting
// Sends emails to info@dauziconsulting.com and dauziconsulting@gmail.com

// Security: Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
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

// If validation fails, redirect back with error
if (!empty($errors)) {
    header('Location: contact.html?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Email configuration
$to_primary = 'info@dauziconsulting.com';
$to_secondary = 'dauziconsulting@gmail.com';
$from_email = 'no-reply@dauziconsulting.com';
$from_name = 'Dauzi Consulting Contact Form';
$smtp_password = 'Temp_Pass123';

// Email subject
$email_subject = 'New Contact Form Submission: ' . $subject;

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

// Email headers with SMTP authentication
$headers = "From: $from_name <$from_email>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// Configure PHP mail to use SMTP (for cPanel)
ini_set('SMTP', 'localhost');
ini_set('smtp_port', '587');
ini_set('sendmail_from', $from_email);

// Send email to primary address
$mail_sent_primary = @mail($to_primary, $email_subject, $email_body, $headers);

// Send email to secondary address (copy)
$mail_sent_secondary = @mail($to_secondary, $email_subject, $email_body, $headers);

// Load config if available (for fallback scenarios)
if (file_exists('config.php')) {
    require_once 'config.php';
    
    // Save to database if enabled
    if (defined('ENABLE_DATABASE') && ENABLE_DATABASE) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, subject, message, submitted_at, ip_address) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
            $stmt->execute([$name, $email, $phone ?: null, $subject, $message, $_SERVER['REMOTE_ADDR']]);
        } catch (PDOException $e) {
            // Log error but don't fail the form submission
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Redirect based on result
if ($mail_sent_primary || $mail_sent_secondary) {
    // Success - redirect to thank you page or back with success message
    header('Location: contact.html?success=1');
} else {
    // Failure
    header('Location: contact.html?error=' . urlencode('Failed to send message. Please try again or contact us directly.'));
}
exit;
?>

