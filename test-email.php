<?php
/**
 * Email Configuration Test Script
 * Use this to test if your email setup is working correctly
 * 
 * IMPORTANT: Delete this file after testing for security!
 */

// Include PHPMailer
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_username = 'zovionixtech@gmail.com';
$smtp_password = 'kmpu fqmr bgtv kygm';
$test_email = 'zovionixtech@gmail.com'; // Change this to your test email

echo "<h2>Email Configuration Test</h2>";

// Test 1: Check PHPMailer
echo "<h3>Test 1: PHPMailer Library</h3>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✓ PHPMailer is loaded successfully<br>";
} else {
    echo "✗ PHPMailer not found. Please check the path.<br>";
    exit;
}

// Test 2: Test SMTP Connection
echo "<h3>Test 2: SMTP Connection</h3>";
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str) . "<br>";
    };
    
    // Test email
    $mail->setFrom($smtp_username, 'Zovionix Tec Test');
    $mail->addAddress($test_email, 'Test Recipient');
    $mail->Subject = 'Test Email from Zovionix Tec';
    $mail->Body = 'This is a test email. If you receive this, your email configuration is working correctly!';
    $mail->AltBody = 'This is a test email. If you receive this, your email configuration is working correctly!';
    
    echo "Attempting to send test email...<br><br>";
    
    if ($mail->send()) {
        echo "<br><strong style='color: green;'>✓ SUCCESS! Test email sent successfully!</strong><br>";
        echo "Check your inbox at: {$test_email}<br>";
    } else {
        echo "<br><strong style='color: red;'>✗ FAILED! Could not send email.</strong><br>";
        echo "Error: {$mail->ErrorInfo}<br>";
    }
    
} catch (Exception $e) {
    echo "<br><strong style='color: red;'>✗ ERROR!</strong><br>";
    echo "Exception: {$mail->ErrorInfo}<br>";
}

echo "<br><hr>";
echo "<p><strong>Note:</strong> Delete this file after testing for security purposes!</p>";
?>

