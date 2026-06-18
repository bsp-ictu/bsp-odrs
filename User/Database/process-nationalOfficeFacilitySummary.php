<?php
    session_start();
    include('../../Login/Database/process-configuration.php');

    if(isset($_GET['request_id'])){
        $requestId = $_GET['request_id'];
        $_SESSION['requestIdNOF'] = $requestId;
        
        $select = "SELECT * FROM facility_reservation WHERE request_id =?";
        $statement = $conn->prepare($select);
        $statement->execute([$requestId]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0){
            foreach ($rows as $row){
                echo json_encode($row);
            }
        } else {
            header("Location: ../clientIndex.php?data-retrieval-unsuccessfully");
            exit();
        }

        $statement=null;
        $conn=null;
    }
?>