<?php
session_start();
include('../../Login/Database/process-configuration.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['accountemail'])) {
        die("Error: Session expired or not set.");
    }

    $accountemail = $_SESSION['accountemail'];
    $requesterName = $_POST['requesterName'];
    $divisionSelect = $_POST['divisionSelect'];
    $divisionChief = $_POST['divisionChief'];
    $reservedDate = $_POST['reservedDate'];
    $etdFromStation = $_POST['etdFromStation'];
    $etaToDestination = $_POST['etaToDestination'];
    $etdFromDestination = $_POST['etdFromDestination'];
    $etaToStation = $_POST['etaToStation'];
    $destination = $_POST['destination'];
    $tripPurpose = $_POST['tripPurpose'];
    $passengerNumber = $_POST['passengerNumber'];
    $passengers = $_POST['passengers'];

    date_default_timezone_set("Asia/Manila");
    $requestTime = date("H:i:s");

    $destinationNames = implode("; ", $destination);
    $passengerNames = implode("; ", $passengers);

    $currentYear = date("Y");

    $select = "SELECT TOP 1 request_id FROM vehicle_reservation ORDER BY request_id DESC";
    $result = $conn->prepare($select);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $latestRequest = $row['request_id'];
        $latestRequestNo = (int) substr($latestRequest, -5); 
        $newRequestNo = str_pad($latestRequestNo + 1, 5, "0", STR_PAD_LEFT);
    } else {
        $newRequestNo = "00001";
    }

    $requestId = $currentYear . "-VRF-" . $newRequestNo;

    $insert = "INSERT INTO vehicle_reservation
        (request_id, focal_person_email, requester_name, requester_division, division_chief, request_date, request_time, reservation_date, etd_from_station, eta_to_destination, etd_from_destination, eta_to_station, destination, purpose, passenger_number, passenger) 
        VALUES (?, ?, ?, ?, ?, GETDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $insertSummary = "INSERT INTO bsp_reservation_summary
        (request_id, requester_email, request_type, request_date, reservation_date)
        VALUES (?, ?, 'Vehicle Reservation', GETDATE(), ?)";

    $firstPreparedStatement = $conn->prepare($insert);
    $secondPreparedStatement = $conn->prepare($insertSummary);

    if (!$firstPreparedStatement || !$secondPreparedStatement) {
        die("Error preparing SQL statements.");
    }

    try {
        $conn->beginTransaction();

        $firstPreparedStatement->execute([ 
            $requestId,
            $accountemail, 
            $requesterName, 
            $divisionSelect, 
            $divisionChief,
            $requestTime,
            $reservedDate, 
            $etdFromStation, 
            $etaToDestination, 
            $etdFromDestination, 
            $etaToStation, 
            $destinationNames,
            $tripPurpose,
            $passengerNumber,
            $passengerNames
        ]);

        $secondPreparedStatement->execute([$requestId, $accountemail, $reservedDate]);

        $conn->commit();
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'INSERT', 'vehicle_reservation', ?, '-', ?, 'Added new vehicle reservation')
            ")->execute([
                $_SESSION['accountname'],
                $requestId,
                json_encode([
                    'Focal\'s Email' => $accountemail,
                    'Focal\'s Name' => $requesterName,
                    'Division' => $divisionSelect,
                    'Chief' => $divisionChief,
                    'Reservation Date' => $reservedDate,
                    'Destination' => $destinationNames,
                    'Purpose' => $tripPurpose,
                    'Pax' => $passengerNumber
                ])
        ]);
        header("Location: ../clientIndex.php?request-submitted-successfully");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error during request submission: " . $e->getMessage());
    }

    $conn = null;
}
?>