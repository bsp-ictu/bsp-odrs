<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
include('../../Login/Database/process-configuration.php');

if (!isset($_SESSION['accountemail']) || !isset($_SESSION['accountname'])) {
    echo json_encode([
        "status" => "missingSession",
        "debug" => [
            "accountEmail" => $_SESSION['accountemail'] ?? '',
            "accountName" => $_SESSION['accountname'] ?? ''
        ]
    ]);
    exit;
}

$accountEmail = iconv('UTF-8', 'UTF-16LE', trim($_SESSION['accountemail']));
$accountName = iconv('UTF-8', 'UTF-16LE', trim($_SESSION['accountname']));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPass = $_POST['currentPass'] ?? '';
    $newPass = $_POST['newPass'] ?? '';
    $confirmPass = $_POST['confirmPass'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT TOP 1 * FROM bsp_account WHERE account_email = ? AND account_name = ?");
        $stmt->execute([$accountEmail, $accountName]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(["status" => "stillNoMatch"]);
            exit;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $accountId = $row['account_id'];
        $accountPassword = $row['account_password'];

        if (strlen($accountPassword) >= 60) {
            if (!password_verify($currentPass, $accountPassword)) {
                echo json_encode(["status" => "incorrectPassword"]);
                exit;
            }
        } else {
            if ($currentPass !== $accountPassword) {
                echo json_encode(["status" => "incorrectPassword"]);
                exit;
            }
        }

        $hashedPassword = password_hash($confirmPass, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE bsp_account SET account_password = ? WHERE account_id = ? AND account_email = ?");
        $result = $updateStmt->execute([$hashedPassword, $accountId, $accountEmail]);

        if ($result) {
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'bsp_account', ?, '-', '-', 'Updated the user's password')
                ")->execute([
                    $_SESSION['accountname'],
                    $accountId
            ]);
            echo json_encode(["status" => "updateSuccessful"]);
        } else {
            $errorInfo = $updateStmt->errorInfo();
            echo json_encode(["status" => "updateFailed", "error" => $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "sqlError", "error" => $e->getMessage()]);
    }

    $conn = null;
    exit;
}

echo json_encode(["status" => "unknownError"]);
exit;
?>
