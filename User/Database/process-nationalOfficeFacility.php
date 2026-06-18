<?php
    session_start();
    include('../../Login/Database/process-configuration.php');

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if($_SERVER['REQUEST_METHOD']=='POST'){
        $accountemail = $_SESSION['accountemail'];
        $requesterName = $_POST['requesterName'];
        $divisionSelect = $_POST['divisionSelect'];
        $divisionChief = $_POST['divisionChief'];

        $chosenFacility = $_POST['chosenFacility'];
        $otherFacility = $_POST['otherFacility'];
        $reservedDate = $_POST['reservedDate'];
        $reservedStartTime = $_POST['startTime'];
        $reservedEndTime = $_POST['endTime'];

        $purpose = $_POST['purpose'];
        $numberOfPersons = $_POST['numberOfPersons'];

        if ($otherFacility != null) {
            $checkAvailability = "SELECT * FROM facility_reservation 
                                WHERE (reserved_facility = :chosenFacility OR other_facility = :otherFacility) 
                                AND reservation_date = :reservedDate 
                                AND reservation_time_start < :reservedEndTime 
                                AND reservation_time_end > :reservedStartTime  
                                AND [status] = 'Approved'";
        } else {
            $checkAvailability = "SELECT * FROM facility_reservation 
                                WHERE reserved_facility = :chosenFacility 
                                AND reservation_date = :reservedDate 
                                AND reservation_time_start < :reservedEndTime 
                                AND reservation_time_end > :reservedStartTime  
                                AND [status] = 'Approved'";
        }

        $runAvailability = $conn->prepare($checkAvailability);
        $runAvailability->bindParam(':chosenFacility', $chosenFacility);

        // Only bind :otherFacility if it exists in the query
        if ($otherFacility != null) {
            $runAvailability->bindParam(':otherFacility', $otherFacility);
        }

        $runAvailability->bindParam(':reservedDate', $reservedDate);
        $runAvailability->bindParam(':reservedStartTime', $reservedStartTime);
        $runAvailability->bindParam(':reservedEndTime', $reservedEndTime);
        $runAvailability->execute();

        $rowrunAvailability = $runAvailability->fetchAll(PDO::FETCH_ASSOC);

        if (count($rowrunAvailability) > 0) {
            header("Location: ../clientIndex.php?facility-not-available&purpose=$purpose&numberOfPersons=$numberOfPersons&division=$divisionSelect&requesterName=$requesterName&divisionChief=$divisionChief");
            exit();
            
        } else {
            echo "Account Email: " . $accountemail . "<br>";
            echo "Requester Name: " . $requesterName . "<br>";
            echo "Division: " . $divisionSelect . "<br>";
            echo "Division Chief: " . $divisionChief . "<br>";

            echo "Chosen Facility: " . $chosenFacility . "<br>";
            echo "Other Facility: " . $otherFacility . "<br>";
            echo "Reserved Date: " . $reservedDate . "<br>";
            echo "Start Time: " . $reservedStartTime . "<br>";
            echo "End Time: " . $reservedEndTime . "<br>";

            echo "Purpose: " . $purpose . "<br>";
            echo "Number of Persons: " . $numberOfPersons . "<br>"; 
            $currentYear = date("Y");

            $select = "SELECT TOP 1 request_id FROM facility_reservation ORDER BY request_id DESC";
            $result = $conn->prepare($select);
            $result->execute();

            // Fetch one row
            $row = $result->fetch(PDO::FETCH_ASSOC);

            if ($row && isset($row['request_id'])) {
                $latestRequest = $row['request_id'];
                $latestRequestNo = (int) substr($latestRequest, -5); 
                $newRequestNo = str_pad($latestRequestNo + 1, 5, "0", STR_PAD_LEFT);
            } else {
                $newRequestNo = "00001";
            }

            $requestId = $currentYear . "-NOF-" . $newRequestNo;

            $insert = "INSERT INTO facility_reservation
                    (request_id, focal_person_email, requester_name, requester_division, division_chief, request_date, reservation_date, reservation_time_start, reservation_time_end, reserved_facility, other_facility, purpose, number_of_person) 
                    VALUES (?, ?, ?, ?, ?, GETDATE(), ?, ?, ?, ?, ?, ?, ?)";

            $insertSummary = "INSERT INTO bsp_reservation_summary
                    (request_id, requester_email, request_type, request_date, reservation_date)
                    VALUES (?, ?, 'Facility Reservation', GETDATE(), ?)";
            
            try{
                $conn->beginTransaction();
                $firstPreparedStatement = $conn->prepare($insert);
                $secondPreparedStatement = $conn->prepare($insertSummary);
                if ($firstPreparedStatement && $secondPreparedStatement) {
                    $firstPreparedStatement->execute([
                        $requestId,
                        $accountemail,
                        $requesterName, 
                        $divisionSelect, 
                        $divisionChief,
                        $reservedDate,
                        $reservedStartTime,
                        $reservedEndTime,
                        $chosenFacility,
                        $otherFacility,
                        $purpose,
                        $numberOfPersons
                    ]);

                    $secondPreparedStatement->execute([$requestId, $accountemail, $reservedDate]);
                    $conn->commit();
                    $conn->prepare("
                            INSERT INTO audit_trail (
                                account_name, action_type, table_name, record_id, old_value, new_value, action_description
                            ) VALUES (?, 'INSERT', 'facility_reservation', ?, '-', ?, 'Added new facility reservation')
                        ")->execute([
                            $_SESSION['accountname'],
                            $requestId,
                            json_encode([
                                'Focal\'s Email' => $accountemail,
                                'Focal\'s Name' => $requesterName,
                                'Division' => $divisionSelect,
                                'Chief' => $divisionChief,
                                'Request Data' => $reservedDate,
                                'Start' => $reservedStartTime,
                                'End' => $reservedEndTime,
                                'Facility' => $chosenFacility,
                                'Other Facility' => $otherFacility,
                                'Purpose' => $purpose,
                                'Pax' => $numberOfPersons
                            ])
                    ]);
                    header("Location: ../clientIndex.php?request-submitted-successfully");
                    exit();
                }
            }catch(PDOException $e){
                $conn->rollBack();
                error_log("PDO Error: " . $e->getMessage());
                header("Location: ../clientIndex.php?request-submitted-unsuccessfully");
            }
        }
        $conn=null;
    }
?>
