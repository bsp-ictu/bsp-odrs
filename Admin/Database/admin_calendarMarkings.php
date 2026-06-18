<?php
    session_start();
    include('../../Login/Database/process-configuration.php');

    $accountRole = $_SESSION['accountrole'];
    $select = "SELECT 
            request_id, 
            requester_division, 
            request_date, 
            reservation_date, 
            [status]
        FROM vehicle_reservation
        WHERE [status] = 'Approved'

        UNION ALL

        SELECT 
            request_id, 
            requester_division, 
            request_date, 
            reservation_date, 
            [status]
        FROM facility_reservation
        WHERE [status] = 'Approved'
        ORDER BY reservation_date ASC;
";
    $select_run4 = $conn->prepare($select);
    $select_run4->execute();
    $rows = $select_run4->fetchAll(PDO::FETCH_ASSOC);
    
    
    if(count($rows) > 0){
        $markReservations = [];

        foreach($rows as $row4){
            if(strpos($row4['request_id'], 'VRF')){
                $markReservations[] = [
                    'date' => $row4['reservation_date'],
                    'type' => 'Vehicle Reservation'
                ];
            } else{
                $markReservations[] = [
                    'date' => $row4['reservation_date'],
                    'type' => 'Facility Reservation'
                ];
            }
        }
        echo json_encode($markReservations);
    }else{
    echo json_encode([]); // Return an empty array if no data
    }   
    $conn = null;
?>