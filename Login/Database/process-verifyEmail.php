<?php
session_start();
include('process-configuration.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';
require '../../PHPMailer/Exception.php';

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentEmailUTF8 = $_POST["verifyEmail"] ?? '';
    $currentEmailUTF8 = trim($currentEmailUTF8);
    $currentEmail = iconv('UTF-8', 'UTF-16LE', $currentEmailUTF8); // crucial

    $_SESSION['currentEmail'] = $currentEmail;

    error_log("Converted email: " . bin2hex($currentEmail));

    $stmt = $conn->prepare("SELECT TOP 1 * FROM bsp_account WHERE account_email = :email");
    $stmt->bindParam(':email', $currentEmail);
    $stmt->execute();

    error_log("Row count: " . $stmt->rowCount());
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $generatedCode = '';
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 4; $i++) {
            $generatedCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bsp.odrs1912@gmail.com';
            $mail->Password   = 'snluytpzdrwhvjav';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('bsp.odrs1912@gmail.com', 'PHPMailer-ODRS');
            $mail->addAddress($currentEmailUTF8);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password Code';
            $mail->Body    = 'Password reset code for BSP Reservation System account: <b>' . $generatedCode . '</b>';

            $mail->send();

            echo json_encode(["status" => "success", "email" => $currentEmailUTF8, "code" => $generatedCode]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
            exit;
        }
    } else {
        echo json_encode(["status" => "accountDoesNotExists", "debug" => $currentEmailUTF8]);
        exit;
    }
}
?>
