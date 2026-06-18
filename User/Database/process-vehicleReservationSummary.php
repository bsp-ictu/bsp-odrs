<?php
    session_start();
    include('../../Login/Database/process-configuration.php');

    if (isset($_GET['request_id'])) {
        $requestId = $_GET['request_id'];
        $_SESSION['requestIdVRF'] = $requestId;

        try {
            $select = "SELECT TOP 1 * FROM vehicle_reservation WHERE request_id = ?";
            $statement = $conn->prepare($select);
            $statement->execute([$requestId]);

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                echo json_encode($row);
            } else {
                header("Location: ../clientIndex.php?data-retrieval-unsuccessfully");
                exit();
            }

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
        $statement = null;
        $conn = null;
    }
?>
