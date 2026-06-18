<?php
    session_start();
    include('../../Login/Database/process-configuration.php');

    $select = "SELECT * FROM bsp_division";
    $select_stmt = $conn->prepare($select);
    $select_stmt->execute();
    $rows = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        $divisionArray = [];

        foreach ($rows as $row) {
            $divisionArray[] = [
                'division_name' => $row['division_name'],
                'division_chief' => $row['division_chief']
            ];
        }
        echo json_encode($divisionArray);
    } else {
        header("Location: ../clientIndex.php?data-retrieval-unsuccessfully");
        exit();
    }
    $conn=null;
?>