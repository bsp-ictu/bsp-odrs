<?php
session_start();
include('process-configuration.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountemail = $_POST['email'];
    $accountpassword = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM bsp_account WHERE account_email = :email");
    $stmt->bindParam(':email', $accountemail);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($row['login_attempt'] <= 0) {
            header("Location: ../index.php?login-attempts-exceeded");
            exit();
        }

        $storedPassword = $row['account_password'];
        $passwordMatches = false;

        if (strlen($storedPassword) >= 60) {
            $passwordMatches = password_verify($accountpassword, $storedPassword);
        } else {
            $passwordMatches = ($accountpassword === $storedPassword);
        }

        if (!$passwordMatches) {
            // Decrement login_attempt
            $updateStm = $conn->prepare("UPDATE bsp_account SET login_attempt = login_attempt - 1 WHERE account_id = :accountID");
            $updateStm->bindParam(':accountID', $row['account_id'], PDO::PARAM_STR);
            $updateStm->execute();

            header("Location: ../index.php?incorrect-email-or-password");
            exit();
        }

        // Successful login
        $_SESSION['accountname'] = $row['account_name'];
        $_SESSION['accountemail'] = $row['account_email'];
        $_SESSION['accountrole'] = $row['account_role'];
        $_SESSION['accountpic'] = $row['account_pic'];
        $_SESSION['accountdivision'] = $row['account_division'];

        // Reset login_attempt
        $resetStm = $conn->prepare("UPDATE bsp_account SET login_attempt = 10 WHERE account_id = :accountID");
        $resetStm->bindParam(':accountID', $row['account_id'], PDO::PARAM_STR);
        $resetStm->execute();
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'LOG IN', '-', ?, '-', '-', 'User logged in')
            ")->execute([
                $_SESSION['accountname'],
                $row['account_id']
        ]);

        if ($_SESSION['accountrole'] == 'User' || $_SESSION['accountrole'] == 'GuestUser' ) {

            header("Location: ../../User/clientIndex.php");
        } else {
            header("Location: ../../Admin/index.php");
        }
        exit();
    } else {
        header("Location: ../index.php?account-does-not-exist");
        exit();
    }
}
?>
