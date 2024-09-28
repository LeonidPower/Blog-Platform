<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 2; // Set to 0 in production to disable debug output
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'asd@hotmail.com'; // Your Gmail address
        $mail->Password   = 'asd';      
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('LeonidPower@hotmail.com'); // Recipient email address

        // Content
        $mail->isHTML(false);
        $mail->Subject = "Contact Form Submission: " . $subject;
        $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        $mail->send();
        echo "<h1>Message Sent Successfully!</h1>";
    } catch (Exception $e) {
        echo "<h1>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</h1>";
    }
}
?>
