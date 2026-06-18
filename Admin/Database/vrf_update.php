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
    // Collect inputs
    $vehicle          = $_POST['vehicle'] ?? '';
    $driver           = $_POST['driver'] ?? '';
    $status           = $_POST['status'] ?? '';
    $requestId        = $_POST['request_id'] ?? '';
    $reservationDate  = $_POST['r_date'] ?? '';
    $etas             = $_POST['etas'] ?? '';
    $etds             = $_POST['etds'] ?? '';
    $currentDate      = date('Y-m-d');
    echo $requestId;
    // Validate required inputs
    if ($requestId <= 0) {
        redirectWith("../vrf_Frame.php", ["update" => "missing_request_id"]);
    }
    if (empty($status) || empty($reservationDate)) {
        redirectWith("../vrf_Frame.php", ["update" => "invalid_input"]);
    }

    // Whitelist status values
    $allowedStatuses = ["Approved", "Lapsed", "No Vehicle Available", "Pending"];
    if (!in_array($status, $allowedStatuses, true)) {
        redirectWith("../vrf_Frame.php", ["update" => "invalid_status"]);
    }

    // Check if request exists
    $check_sql = "SELECT 1 FROM vehicle_reservation WHERE request_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$requestId]);
    if (!$check_stmt->fetch()) {
        echo "check sql: ".$requestId;
        redirectWith("../vrf_Frame.php", ["update" => "request_id_not_found"]);
    }
     
    if ($status === "Approved") {
        $conflict_sql = "
            SELECT TOP 1 *
            FROM vehicle_reservation
            WHERE (vehicle_driver = ? OR vehicle_plate_no = ?)
              AND reservation_date = ?
              AND [status] = 'Approved'
        ";
        $conflict_stmt = $conn->prepare($conflict_sql);
        $conflict_stmt->execute([$driver, $vehicle, $reservationDate]);
        $conflict = $conflict_stmt->fetch(PDO::FETCH_ASSOC);

        if ($conflict) {
            $conflictTimeOk = true;
            if (!empty($conflict['eta_to_station']) && !empty($etds)) {
                $conflictTimeOk = strtotime($conflict['eta_to_station']) < strtotime($etds);
            }

            if (!$conflictTimeOk) {
                $conflictType = ($conflict['vehicle_driver'] === $driver) ? "driver" : "vehicle";
                redirectWith("../vrf_Frame.php", [
                    "request_id" => $requestId,
                    "conflict"   => $conflictType
                ]);
            }
        }
    }

    $conn->beginTransaction();
    try {
        $sql1 = "
            UPDATE vehicle_reservation
            SET vehicle_plate_no = ?, 
                vehicle_driver   = ?, 
                [status]         = ?, 
                date_approved    = CAST(? AS DATE)
            WHERE request_id = ?
        ";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$vehicle, $driver, $status, $currentDate, $requestId]);

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
                ) VALUES (?, 'UPDATE', 'vehicle_reservation', ?, '-', ?, 'Changed the status of request for vehicle')
            ")->execute([
                $_SESSION['accountname'],
                $requestId,
                json_encode([
                    'Vehicle ' => $vehicle,
                    'Driver ' => $driver,
                    'Status ' => $vehicle,
                ])
        ]);
        redirectWith("../vrf_Frame.php", [
            "request_id" => $requestId,
            "update"     => "success"
        ]);

    } catch (Exception $e) {
        $conn->rollBack();

        echo "from catch: ".$requestId;    
        redirectWith("../vrf_Frame.php", [
            "request_id" => $requestId,
            "update"     => "failed"
        ]);
    }
}
?>