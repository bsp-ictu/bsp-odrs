<?php
    session_start();
    include('../../Login/Database/process-configuration.php');
    $conn->prepare("
            INSERT INTO audit_trail (
                account_name, action_type, table_name, record_id, old_value, new_value, action_description
            ) VALUES (?, 'LOG OUT', '-', '-', '-', '-', 'User logged out')
        ")->execute([
            $_SESSION['accountname']
    ]);
    session_unset();

    session_destroy();

    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    header("Location: ../../Login/index.php");
?>