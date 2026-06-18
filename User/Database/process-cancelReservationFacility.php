<?php
session_start();
include('../../Login/Database/process-configuration.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$reqID = isset($_GET['request_id']) ? $_GET['request_id'] : "";

if (!empty($reqID)) {
    $sql = "UPDATE facility_reservation 
            SET for_cancellation = 1
            WHERE request_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$reqID])) {
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'UPDATE', 'facility_reservation', ?, ?, ?, 'Updated facility reservation - tagged for cancellation')
            ")->execute([
                $_SESSION['accountname'],
                $reqID,
                json_encode(['For Cancellation' => '0']),
                json_encode(['For Cancellation' => '1'])
        ]);
        $stmt = null;
        $conn = null;
        header("Location: ../clientIndex.php?cancellation=success");
        exit();
    } else {
        echo "Failed to update cancellation.";
    }

} else {
    echo "Invalid request ID.";
}
?>