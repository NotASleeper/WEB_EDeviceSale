<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST["send"])) {
    $email_address = $_POST["email"];
    if ($email_address !== "") {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phungnguyenkim97102@gmail.com';
        $mail->Password = 'wccjmaqiprbbbqzj';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('phungnguyenkim97102@gmail.com');
        $mail->addAddress($_POST["email"]);
        $mail->isHTML(true);

        $mail->Subject = "Welcome to TechHub";
        $mail->Body = "<p>Dear our value customer,</p>
        <p>Thank you for joining our TechHub! We're thrilled to have you as part of our community. With your subscription, you'll receive updates, exclusive offers, and insights tailored just for you.</p>
        <p>We're committed to keeping you informed and inspired. If you have any questions or feedback, don't hesitate to reach out to us.</p>
        <p>Warm regards,<br>
        TechHub</p>";

        $mail->send();
    }

    $role = $_SESSION['role'];

    unset($_POST["email"]);

    if ($role === 'customer') {
        header('location:home_cus.php');
    } elseif ($role === 'employee') {
        header('location:home.php');
    } else {
        header('location:home_cus.php');
    }
}
