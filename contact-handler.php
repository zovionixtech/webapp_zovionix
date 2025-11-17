<?php
/**
 * Contact Form Handler for Zovionix Tec
 * Handles form submissions and sends emails with auto-reply
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include PHPMailer (you'll need to download PHPMailer library)
// Download from: https://github.com/PHPMailer/PHPMailer
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'zovionixtech@gmail.com');
define('SMTP_PASSWORD', 'kmpu fqmr bgtv kygm');
define('FROM_EMAIL', 'zovionixtech@gmail.com');
define('FROM_NAME', 'Zovionix Tech');
define('TO_EMAIL', 'zovionixtech@gmail.com');
define('TO_NAME', 'Zovionix Tech Team');

// Get and sanitize form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Check for spam (basic honeypot - you can add a hidden field in your form)
if (isset($_POST['website']) && !empty($_POST['website'])) {
    // This is likely a bot
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Spam detected']);
    exit;
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Function to send email
function sendEmail($toEmail, $toName, $subject, $body, $isHTML = true) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($toEmail, $toName);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Prepare email content for notification to admin
$adminSubject = "New Contact Form Submission from {$name}";
$adminBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(90deg, #8A2BE2, #4D4DFF); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; padding: 10px; background: white; border-radius: 4px; }
        .footer { text-align: center; margin-top: 20px; color: #777; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>{$name}</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>{$email}</div>
            </div>
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Submitted On:</div>
                <div class='value'>" . date('F j, Y, g:i a') . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from the Zovionix Tec contact form.</p>
        </div>
    </div>
</body>
</html>
";

// Prepare auto-reply email content
$autoReplySubject = "Thank You for Contacting Zovionix Tech";
$autoReplyBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(90deg, #8A2BE2, #4D4DFF); color: white; padding: 30px 20px; text-align: center; }
        .logo { font-size: 32px; font-weight: bold; margin-bottom: 10px; }
        .content { padding: 30px 20px; background: #f9f9f9; }
        .greeting { font-size: 18px; margin-bottom: 20px; }
        .message { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4D4DFF; }
        .cta { text-align: center; margin: 30px 0; }
        .button { display: inline-block; background: linear-gradient(90deg, #8A2BE2, #4D4DFF); color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { background: #0a0a1a; color: #e0e0e0; padding: 20px; text-align: center; font-size: 12px; }
        .contact-info { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
        .contact-info p { margin: 5px 0; color: #555; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='logo'>ZOVIONIX</div>
            <p>Digital Solutions Provider</p>
        </div>
        <div class='content'>
            <div class='greeting'>
                <p>Dear {$name},</p>
            </div>
            <div class='message'>
                <p>Thank you for reaching out to <strong>Zovionix Tec</strong>! We've received your message and are excited to learn more about your project.</p>
                <p>Our team will review your inquiry and get back to you within <strong>24-48 hours</strong>. We're committed to providing you with the best digital solutions tailored to your needs.</p>
                <p>In the meantime, feel free to explore our services:</p>
                <ul>
                    <li>Custom Web Development</li>
                    <li>Mobile App Development</li>
                    <li>UI/UX Design & Prototyping</li>
                    <li>SEO & Digital Marketing</li>
                    <li>Full-Stack Development</li>
                    <li>Maintenance & Support</li>
                </ul>
            </div>
            <div class='cta'>
                <a href='https://zovionix.com' class='button'>Visit Our Website</a>
            </div>
            <div class='contact-info'>
                <p><strong>Best Regards,</strong></p>
                <p><strong>The Zovionix Tech Team</strong></p>
                <p>Email: zovionixtech@gmail.com</p>
                <p>Phone: +91 98765 43210</p>
                <p>Address: Tech Park One, Pune, India</p>
            </div>
        </div>
        <div class='footer'>
            <p>&copy; 2025 Zovionix Tec. All Rights Reserved.</p>
            <p>This is an automated response. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
";

// Send notification email to admin
$adminEmailSent = sendEmail(TO_EMAIL, TO_NAME, $adminSubject, $adminBody);

// Send auto-reply to user
$autoReplySent = sendEmail($email, $name, $autoReplySubject, $autoReplyBody);

// Response
if ($adminEmailSent && $autoReplySent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. We will get back to you soon.'
    ]);
} elseif ($adminEmailSent) {
    // Admin email sent but auto-reply failed
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been received. We will get back to you soon.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later or contact us directly at zovionixtech@gmail.com'
    ]);
}
?>

