
<?php
session_start();
include('../../Login/Database/process-configuration.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$type = isset($_GET['type']) ? $_GET['type'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$col1 = isset($_GET['editingData1']) ? $_GET['editingData1'] : '';
$col2 = isset($_GET['editingData2']) ? $_GET['editingData2'] : '';
$col3 = isset($_GET['editingData3']) ? $_GET['editingData3'] : '';
$col4 = isset($_GET['editingData4']) ? $_GET['editingData4'] : '';
$col5 = isset($_GET['editingData5']) ? $_GET['editingData5'] : '';

echo $type."<br>\n";
echo $action."<br>\n";
echo "col1: ",$col1."<br>\n";
echo "col2: ",$col2."<br>\n";
echo "col3: ",$col3."<br>\n";
echo "col4: ",$col4."<br>\n";
echo "col5: ",$col5."<br>\n";

if($type=="divOffice"){
        $select_divisionOffice = "SELECT TOP 1 * 
                            FROM bsp_division
                            WHERE id_division = :col3;";
        $select_divisionOffice_run = $conn->prepare($select_divisionOffice);
        $select_divisionOffice_run->bindParam(':col3', $col3);
        $select_divisionOffice_run->execute();
        $select_divisionOffice_run_rows =  $select_divisionOffice_run->fetch(PDO::FETCH_ASSOC);
        if($select_divisionOffice_run_rows) {
            if($action=="save"){

                try {
                    $conn->beginTransaction();
                    if (($col4 === 'true') && ($col5 === 'false')){
                        $conn->prepare("UPDATE bsp_division SET is_signatoreeVRF = 0")->execute();
                        $update_divisionOffice = "UPDATE bsp_division
                                            SET division_name = ?, division_chief = ?, is_signatoreeVRF = ?
                                            WHERE id_division = ?;";
                        $stmt = $conn->prepare($update_divisionOffice);
                        $conn->prepare("
                            INSERT INTO audit_trail (
                                account_name, action_type, table_name, record_id, old_value, new_value, action_description
                            ) VALUES (?, 'UPDATE', 'bsp_division', ?, ?, ?, 'Changed the status of VRF signatoree')
                        ")->execute([
                            $_SESSION['accountname'],
                            $select_divisionOffice_run_rows['id_division'],
                            json_encode(['is_signatoreeVRF' => 0]),
                            json_encode(['is_signatoreeVRF' => 1])
                        ]);
                        $success = $stmt->execute([
                            $col1,
                            $col2,
                            $col4,
                            $select_divisionOffice_run_rows['id_division']
                        ]);
                    }else if(($col4 === 'false' )&& ($col5 === 'true')){
                        $conn->prepare("UPDATE bsp_division SET is_signatoreeFacilities = 0")->execute();  
                        $update_divisionOffice = "UPDATE bsp_division
                                            SET division_name = ?, division_chief = ?, is_signatoreeFacilities = ?
                                            WHERE id_division = ?;";
                        $stmt = $conn->prepare($update_divisionOffice);
                        $conn->prepare("
                            INSERT INTO audit_trail (
                                account_name, action_type, table_name, record_id, old_value, new_value, action_description
                            ) VALUES (?, 'UPDATE', 'bsp_division', ?, ?, ?, 'Changed the status of request of facility use signatoree')
                        ")->execute([
                            $_SESSION['accountname'],
                            $select_divisionOffice_run_rows['id_division'],
                            json_encode(['is_signatoreeFacilities' => 0]),
                            json_encode(['is_signatoreeFacilities' => 1])
                        ]);
                        $success = $stmt->execute([
                            $col1,
                            $col2,                              
                            $col5,
                            $select_divisionOffice_run_rows['id_division']
                        ]);
                    }else if(($col4 === 'true') && ($col5 === 'true')){
                        $conn->prepare("UPDATE bsp_division SET is_signatoreeVRF = 0, is_signatoreeFacilities = 0")->execute();
                        $update_divisionOffice = "UPDATE bsp_division
                                            SET division_name = ?, division_chief = ?, is_signatoreeVRF = ?, is_signatoreeFacilities = ?
                                            WHERE id_division = ?;";
                        $stmt = $conn->prepare($update_divisionOffice);
                        $conn->prepare("
                            INSERT INTO audit_trail (
                                account_name, action_type, table_name, record_id, old_value, new_value, action_description
                            ) VALUES (?, 'UPDATE', 'bsp_division', ?, ?, ?, 'Changed the status of signatoree for both forms')
                        ")->execute([
                            $_SESSION['accountname'],
                            $select_divisionOffice_run_rows['id_division'],
                            json_encode(['is_signatoreeVRF' => 0, 'is_signatoreeFacilities' => 0]),
                            json_encode(['is_signatoreeVRF' => 1, 'is_signatoreeFacilities' => 1])
                        ]);
                        $success = $stmt->execute([
                            $col1,
                            $col2,
                            $col4,
                            $col5,
                            $select_divisionOffice_run_rows['id_division']
                        ]);
                    }else if(($col4 === 'false') && ($col5 === 'false')){
                        $update_divisionOffice = "UPDATE bsp_division
                                            SET division_name = ?, division_chief = ?
                                            WHERE id_division = ?;";
                        $stmt = $conn->prepare($update_divisionOffice);
                        $conn->prepare("
                            INSERT INTO audit_trail (
                                account_name, action_type, table_name, record_id, new_value, action_description
                            ) VALUES (?, 'UPDATE', 'bsp_division', ?, ?, 'Changed the status of signatoree for both forms into false')
                        ")->execute([
                            $_SESSION['accountname'],
                            $select_divisionOffice_run_rows['id_division'],
                            json_encode(['is_signatoreeVRF' => 0, 'is_signatoreeFacilities' => 0])
                        ]);
                        $success = $stmt->execute([
                            $col1,
                            $col2,
                            $select_divisionOffice_run_rows['id_division']
                        ]);
                    }
                    if ($success) {
                        $conn->commit();
                        $conn = null;
                        $stmt = null;
                        header("Location: ../editData.php?save=success");
                        exit();
                    } else {
                        $conn->rollBack();
                        $conn = null;
                        $stmt = null;
                        header("Location: ../editData.php?save=failed");
                        exit();
                    }
                } catch (Exception $e) {
                    $conn->rollBack();  
                    $conn = null;
                    $stmt = null;
                    header("Location: ../editData.php?save=failed");
                    exit();
                }
            } else {
                $delete_divisionOffice = "DELETE FROM bsp_division
                                        WHERE id_division = ?;";
                $stmt = $conn->prepare($delete_divisionOffice);
                $stmt->execute([$col3]);
                $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'DELETE', 'bsp_division', ?, ?, '-', 'Deleted a division office')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_divisionOffice_run_rows['id_division'],
                    json_encode(['Division Name' => $select_divisionOffice_run_rows['division_name'], 'Division Chief' => $select_divisionOffice_run_rows['division_name']])
                ]);
                if($stmt) {
                    $conn = null;
                    $stmt = null;
                    header("Location: ../editData.php?delete=success");
                    exit();
                } else {
                    $conn = null;
                    $stmt = null;
                    header("Location: ../editData.php?delete=failed");
                    exit();
                }
            }
    }
    elseif(!$select_divisionOffice_run_rows && $action == "add"){
        $update_divisionOffice = "INSERT INTO bsp_division (division_name, division_chief)
                                VALUES (?, ?);";
        $stmt = $conn->prepare($update_divisionOffice);
        $stmt->execute([$col1, $col2]);
        $conn->prepare("
                    INSERT INTO audit_trail (account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'INSERT', 'bsp_division', '-', '-', ?, 'Added new division office')
                ")->execute([
                    $_SESSION['accountname'],
                    json_encode(['Division Name' => $col1, 'Division Chief' => $col2])
        ]);
        if($stmt) {
            $conn = null;
            $stmt = null;
            header("Location: ../editData.php?add=success");
            exit();
        } else {
            $conn = null;
            $stmt = null;
            header("Location: ../editData.php?add=failed");
            exit();
        }
    }
    else {
        $conn = null;
        $stmt = null;
        header("Location: ../editData.php?update=failed");
        exit();
    } 
} 
elseif($type=="vehicleInfo") {
    $select_vehicleInfo = "SELECT TOP 1 * 
                        FROM vehicle_info
                        WHERE vehicle_id = '$col3';";
    $select_vehicleInfo_run = $conn->prepare($select_vehicleInfo);
    $select_vehicleInfo_run->execute();
    $select_vehicleInfo_run_row = $select_vehicleInfo_run->fetch(PDO::FETCH_ASSOC);
    if($select_vehicleInfo_run_row) {
        if($action=="save"){
            $update_vehicleInfo = "UPDATE vehicle_info
                                    SET vehicle_model = ?, vehicle_platenum = ?
                                    WHERE vehicle_id = ?;";
            $stmt1 = $conn->prepare($update_vehicleInfo);
            $stmt1->execute([
                $col1,
                $col2,
                $select_vehicleInfo_run_row['vehicle_id']
            ]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'vehicle_info', ?, ?, ?, 'Updated the vehicle list record')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_vehicleInfo_run_row['vehicle_id'],
                    json_encode(['Vehicle Model' => $select_vehicleInfo_run_row['vehicle_model'], 'Plate No.' => $select_vehicleInfo_run_row['vehicle_platenum']]),
                    json_encode(['Vehicle Model' => $col1, 'Plate No.' => $col2])
            ]);
            if($stmt1) {
                $conn= null;
                $stmt1 = null;
                header("Location: ../editData.php?save=success");
                exit();
            } else {
                $conn= null;
                $stmt1 = null;
                header("Location: ../editData.php?save=failed");
                exit();
            }
        }
        else {
            $delete_vehicleInfo = "DELETE FROM vehicle_info
                                    WHERE vehicle_id = ?;";
            $stmt1 = $conn->prepare($delete_vehicleInfo);
            $stmt1->execute([$select_vehicleInfo_run_row['vehicle_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'DELETE', 'vehicle_info', ?, ?, '-', 'Deleted a vehicle list record')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_vehicleInfo_run_row['vehicle_id'],
                    json_encode(['Vehicle Model' => $select_vehicleInfo_run_row['vehicle_model'], 'Plate No.' => $select_vehicleInfo_run_row['vehicle_platenum']]),
            ]);
            if($stmt1) {
                $conn= null;
                $stmt1 = null;
                header("Location: ../editData.php?delete=success");
                exit();
            } else {
                $conn= null;
                $stmt1 = null;
                header("Location: ../editData.php?delete=failed");
                exit();
            }
        }
    }
    elseif(!$select_vehicleInfo_run_row && $action == "add"){
        $update_vehicleInfo = "INSERT INTO vehicle_info (vehicle_model, vehicle_platenum)
                                VALUES (?, ?);";
        $stmt1 = $conn->prepare($update_vehicleInfo);
        $stmt1->execute([$col1, $col2]);
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'INSER', 'vehicle_info', '-', '-', ?, 'Added a vehicle list into record')
            ")->execute([
                $_SESSION['accountname'],
                json_encode(['Vehicle Model' => $col1, 'Plate No.' => $col2])
        ]);
        if($stmt1) {
            $conn= null;
            $stmt1 = null;
            header("Location: ../editData.php?add=success");
            exit();
        } else {
            $conn= null;
            $stmt1 = null;
            header("Location: ../editData.php?add=failed");
            exit();
        }
    }
    else {
        header("Location: ../editData.php?update=failed");
        exit();
    }
} 
elseif($type=="driverInfo") {
    $select_driverInfo = "SELECT TOP 1 * 
                        FROM driver_info
                        WHERE driver_id = :col2;";
    $select_driverInfo_run = $conn->prepare($select_driverInfo);
    $select_driverInfo_run->bindParam(':col2', $col2);
    $select_driverInfo_run->execute();
    $select_driverInfo_run_row = $select_driverInfo_run->fetch(PDO::FETCH_ASSOC);
    if($select_driverInfo_run_row) {
        if($action=="save"){
            $update_driverInfo = "UPDATE driver_info
                                    SET driver_name = ?
                                    WHERE driver_id = ?;";
            $stmt2 = $conn->prepare($update_driverInfo);
            $stmt2->execute([$col1, $select_driverInfo_run_row['driver_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'driver_info', ?, ?, ?, 'Changed the driver list record')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_driverInfo_run_row['driver_id'],
                    json_encode(['Driver Name' => $select_driverInfo_run_row['driver_name']]),
                    json_encode(['Driver Name' => $col1])
            ]);  
            if($stmt2) {
                $conn = null;
                $stmt2 = null;
                header("Location: ../editData.php?save=success");
                exit();
            } else {
                $conn = null;
                $stmt2 = null;
                header("Location: ../editData.php?save=failed");
                exit();
            }
        } else {
            $delete_driverInfo = "DELETE FROM driver_info
                                    WHERE driver_id = ?;";
            $stmt2 = $conn->prepare($delete_driverInfo);
            $stmt2->execute([$select_driverInfo_run_row['driver_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'DELETE', 'driver_info', ?, ?, '-', 'Deleted a vehicle list record')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_driverInfo_run_row['driver_id'],
                    json_encode(['Driver Name' => $select_driverInfo_run_row['driver_name']])
            ]);
            if($stmt2) {
                $conn = null;
                $stmt2 = null;
                header("Location: ../editData.php?delete=success");
                exit();
            } else {
                $conn = null;
                $stmt2 = null;
                header("Location: ../editData.php?delete=failed");
                exit();
        }
        }
    }
    elseif($select_driverInfo_run && $action == "add"){
        $update_driverInfo = "INSERT INTO driver_info (driver_name)
                                VALUES (?);";
        $stmt2 = $conn->prepare($update_driverInfo);
        $stmt2->execute([$col1]);
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'INSERT', 'driver_info','-', '-', ?, 'Added new driver record')
            ")->execute([
                $_SESSION['accountname'],
                json_encode(['Driver Name' => $col1])
        ]);
        if($stmt2) {
            $stmt2 = null;
            header("Location: ../editData.php?add=success");
            exit();
        } else {
            $stmt2 = null;
            header("Location: ../editData.php?add=failed");
            exit();
        }
    }
    else {
        header("Location: ../editData.php?update=failed");
        exit();
    }
}
else if($type=="accountInfo"){
    $select_accountInfo = "SELECT TOP 1 * 
                        FROM bsp_account
                        WHERE account_id = :col3;";
    $select_accountInfo_run = $conn->prepare($select_accountInfo);
    $select_accountInfo_run->bindParam(':col3', $col3);
    $select_accountInfo_run->execute();
    $select_accountInfo_run_row = $select_accountInfo_run->fetch(PDO::FETCH_ASSOC);
    if($select_accountInfo_run_row) {
        if($action=="save"){
            $update_accountInfo = "UPDATE bsp_account
                                    SET account_name = ?, account_email = ?, account_role = ?, account_division = ?
                                    WHERE account_id = ?;";
            $stmt3 = $conn->prepare($update_accountInfo);
            $stmt3->execute([$col1, $col2, $col4, $col5, $select_accountInfo_run_row['account_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'bsp_account', ?, ?, ?, 'Updated the account's information')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_accountInfo_run_row['account_id'],
                    json_encode([
                        'Account Name' => $select_accountInfo_run_row['account_name'],
                        'Account Email' => $select_accountInfo_run_row['account_email'],
                        'Account Role' => $select_accountInfo_run_row['account_role'],
                        'Account Division' => $select_accountInfo_run_row['account_division']]),
                    json_encode([
                        'Account Name' => $col1,
                        'Account Email' => $col2,
                        'Account Role' => $col4,
                        'Account Division' => $col5])
            ]);
            if($stmt3) {
                $stmt3 = null;
                header("Location: ../editAccount.php?save=success");
                exit();
            } else {
                $stmt3 = null;
                error_log("PDO Error: " . $e->getMessage());
                header("Location: ../editAccount.php?save=failed");
                exit();
            }
        } else if($action=="reset"){
            $update_accountInfo = "UPDATE bsp_account
                                    SET account_password = 'BSP1234'
                                    WHERE account_id = ?;";
            $stmt3 = $conn->prepare($update_accountInfo);
            $stmt3->execute([$select_accountInfo_run_row['account_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'bsp_account', ?, '-', '-', 'Reset the selected account's password')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_accountInfo_run_row['account_id']
            ]);
            if($stmt3) {
                $stmt3 = null;
                header("Location: ../editAccount.php?reset=success");
                exit();
            } else {
                $stmt3 = null;
                header("Location: ../editAccount.php?reset=failed");
                exit();
            }
        } else {
            $delete_accountInfo = "DELETE FROM bsp_account
                                    WHERE account_id = ?;";
            $stmt3 = $conn->prepare($delete_accountInfo);
            $stmt3->execute([$select_accountInfo_run_row['account_id']]);
            $conn->prepare("
                    INSERT INTO audit_trail (
                        account_name, action_type, table_name, record_id, old_value, new_value, action_description
                    ) VALUES (?, 'UPDATE', 'bsp_account', ?, '-', '-', 'Deleted the selected account')
                ")->execute([
                    $_SESSION['accountname'],
                    $select_accountInfo_run_row['account_id']
            ]);
            if($stmt3) {
                $stmt3 = null;
                header("Location: ../editAccount.php?delete=success");
                exit();
            } else {
                $stmt3 = null;
                header("Location: ../editAccount.php?delete=failed");
                exit();
            }
        }
    }
    elseif(!$select_accountInfo_run_row && $action == "add"){
        $update_accountInfo = "INSERT INTO bsp_account (account_name, account_email, account_password, account_role, account_division)
                                VALUES (?, ?, 'BSP1234', ?, ?);";
        $stmt3 = $conn->prepare($update_accountInfo);
        $stmt3->execute([$col1, $col2, $col4, $col5]);
        $conn->prepare("
                INSERT INTO audit_trail (
                    account_name, action_type, table_name, record_id, old_value, new_value, action_description
                ) VALUES (?, 'UPDATE', 'bsp_account', '-', '-', ?, 'Added new account')
            ")->execute([
                $_SESSION['accountname'],
                json_encode([
                    'Account Name' => $col1,
                    'Account Email' => $col2,
                    'Account Role' => $col4,
                    'Account Division' => $col5])
        ]);
        if($stmt3) {
            $stmt3 = null;
            header("Location: ../editAccount.php?add=success");
            exit();
        } else {
            $stmt3 = null;
            header("Location: ../editAccount.php?add=failed");
            exit();
        }
    }

}
?> 