<?php
header('Content-Type: application/json');
include('process-configuration.php');

if (!isset($_POST['newPassword']) || !isset($_POST['verifyEmail'])) {
    echo json_encode(["status" => "missingData"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentEmail = trim($_POST['verifyEmail']);
    $newConfirmedPassword = $_POST['newPassword'] ?? '';
    $emailHex = bin2hex($currentEmail);

    $verifyEmail = "SELECT TOP 1 account_id FROM bsp_account WHERE account_email = ?";
    $verifyEmailRun = $conn->prepare($verifyEmail);
    $verifyEmailRun->execute([$currentEmail]);
    $row = $verifyEmailRun->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $accountId = $row['account_id'];
        $hashedPassword = password_hash($newConfirmedPassword, PASSWORD_DEFAULT);

        $proceedNewPassword = "UPDATE bsp_account SET account_password = ? WHERE account_id = ? AND account_email = ?";
        $updateStmt = $conn->prepare($proceedNewPassword);

        if ($updateStmt->execute([$hashedPassword, $accountId, $currentEmail])) {
            echo json_encode([
                "status" => "updateSuccessful",
                "debug" => [
                    "sessionEmail" => $currentEmail,
                    "emailHex" => $emailHex
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "updateFailed",
                "error" => $updateStmt->errorInfo()[2],
                "debug" => [
                    "sessionEmail" => $currentEmail,
                    "emailHex" => $emailHex
                ]
            ]);
        }
    } else {
        echo json_encode([
            "status" => "accountDoesNotExists",
            "debug" => [
                "sessionEmail" => $currentEmail,
                "emailHex" => $emailHex
            ]
        ]);
    }
}
?>
