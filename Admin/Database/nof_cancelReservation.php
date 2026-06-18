<?php
session_start();
include('../../Login/Database/process-configuration.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_ID = $_POST["requestID"];

    try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("UPDATE facility_reservation 
                            SET [status] = 'Cancelled', for_cancellation = 0 
                            WHERE request_id = ?");
    $stmt->execute([$request_ID]);

    $stmt_summary = $conn->prepare("UPDATE bsp_reservation_summary 
                                    SET request_status = 'Cancelled' 
                                    WHERE request_id = ?");
    $stmt_summary->execute([$request_ID]);

    $conn->commit();
    $conn->prepare("
            INSERT INTO audit_trail (
                account_name, action_type, table_name, record_id, old_value, new_value, action_description
            ) VALUES (?, 'UPDATE', 'facility_reservation', ?, '-', ?, 'Changed the status of request for facility')
        ")->execute([
            $_SESSION['accountname'],
            $request_ID,
            json_encode(['Status' => 'Cancelled'])
    ]);
    echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

}
?>
