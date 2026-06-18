<?php
    session_start();
    include('../Login/Database/process-configuration.php');
    $select = "SELECT 
                request_id, 
                requester_division, 
                request_date, 
                reservation_date, 
                status
            FROM vehicle_reservation
            WHERE for_cancellation = 1

            UNION ALL

            SELECT 
                request_id, 
                requester_division, 
                request_date, 
                reservation_date, 
                status
            FROM facility_reservation
            WHERE for_cancellation = 1

            ORDER BY request_date ASC";

    $select_stmt = $conn->prepare($select);
    $select_stmt->execute();
    $rows = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($rows);
?>