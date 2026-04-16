<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // بيانات النموذج
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // إعداد PHPMailer
    $mail = new PHPMailer();

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.yahoo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'katmerayt@yahoo.com';
        $mail->Password = 'mypassword';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 465;

        // المستلم
        $mail->setFrom('cs@graniteartiststx.com', 'Granite Artists');
        $mail->addAddress('cs@graniteartiststx.com');  // البريد الذي سيتم إرسال الرسالة إليه
        $mail->addReplyTo($email, $name);  // الرد سيكون إلى البريد المدخل في النموذج

        // إعداد الرسالة
        $mail->isHTML(true);
        $mail->Subject = $subject;

        $body = "<strong>Name:</strong> $name <br><strong>E-mail:</strong> $email <br><strong>Message:</strong> $message";

        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);  // النص البديل في حالة عدم دعم HTML

        // إرسال البريد الإلكتروني
        if ($mail->send()) {
            echo '<script>document.getElementById("success").style.display = "block";</script>';
        } else {
            echo '<script>document.getElementById("error").style.display = "block";</script>';
        }
    } catch (Exception $e) {
        echo '<script>document.getElementById("error").style.display = "block";</script>';
    }
}
