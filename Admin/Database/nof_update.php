<?php
session_start();
include('../../Login/Database/process-configuration.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function redirectWith($url, $params = []) {
    $query = http_build_query($params);
    header("Location: {$url}?" . $query);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect input
    $status = $_POST['status'];
    $replacementFacility = $_POST['facility'];
    $currentDate = date('Y-m-d');
    $requestId        = $_POST['request_id'] ?? '';
    $reserved_facility = $_POST['res_facility'] ?? '';
    $reservationDate   = $_POST['res_date'] ?? '';

    echo $requestId;

    // Validate inputs
    if ($requestId <= 0) {
        redirectWith("../facilityRequestsFrame.php", ["update" => "missing_request_id"]);
    }
    if (empty($status) || empty($reservationDate)) {
        redirectWith("../facilityRequestsFrame.php", ["update" => "invalid_input"]);
    }

    // Replace reserved facility if new one provided
    if (!empty($replacementFacility)) {
        $reserved_facility = $replacementFacility;
    }

    // Check if request exists
    $check_sql = "SELECT 1 FROM facility_reservation WHERE request_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$requestId]);
    if (!$check_stmt->fetch()) {
        // echo $requestId;
        redirectWith("../facilityRequestsFrame.php", ["update" => "request_id_not_found"]);
    }

    // If approving, check facility availability
    if ($status === "Approved") {
        $conflict_sql = "
            SELECT 1  
            FROM facility_reservation 
            WHERE reservation_date = ?
              AND [status] = 'Approved'
              AND reserved_facility = ?
        ";
        $conflict_stmt = $conn->prepare($conflict_sql);
        $conflict_stmt->execute([$reservationDate, $reserved_facility]);
        if ($conflict_stmt->fetch()) {
            redirectWith("../nof_Frame.php", [
                "request_id" => $requestId,
                "conflict" => "facility"
            ]);
        }
    }

    // Start transaction so both tables update together
    $conn->beginTransaction();
    try {
        $sql1 = "
            UPDATE facility_reservation 
            SET [status] = ?, date_approved = CAST(? AS DATE), reserved_facility = ?
            WHERE request_id = ?
        ";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$status, $currentDate, $reserved_facility, $requestId]);

        $sql2 = "
            UPDATE bsp_reservation_summary 
            SET request_status = ?, processed_date = CAST(? AS DATE)
            WHERE request_id = ?
        ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$status, $currentDate, $requestId]);

        $conn->commit();
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'UPDATE', 'facility_reservation', ?, ?, ?, 'Changed the status of request for facility')
            ")->execute([
                $_SESSION['accountname'],
                $requestId,
                json_encode(['Status' => 'Pending']),
                json_encode(['Status' => $status])
        ]);
        redirectWith("../nof_Frame.php", ["request_id" => $requestId, "update" => "success"]);

    } catch (Exception $e) {
        $conn->rollBack();
        redirectWith("../nof_Frame.php", ["request_id" => $requestId, "update" => "failed"]);
    }
}

?>
