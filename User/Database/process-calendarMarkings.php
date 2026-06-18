<?php
session_start();
include('../../Login/Database/process-configuration.php');

$accountEmail = $_SESSION['accountemail'];

$select = "SELECT reservation_date, request_type FROM bsp_reservation_summary 
           WHERE requester_email = :accountEmail AND request_status = 'Approved'";
$select_stmt = $conn->prepare($select);
$select_stmt->bindParam(':accountEmail', $accountEmail);
$select_stmt->execute();

$rows = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) > 0) {
    $markReservations = [];

    foreach ($rows as $row) {
        $markReservations[] = [
            'date' => $row['reservation_date'],
            'type' => $row['request_type']
        ];
    }

    echo json_encode($markReservations);
} else {
    echo json_encode([]); // Return an empty array if no data
}

$conn = null;
?>
    