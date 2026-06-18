<?php
    session_start();
    include('../Login/Database/process-configuration.php');

    $accountName = $_SESSION['accountname'];
    $accountEmail = $_SESSION['accountemail'];
    $accountRole = $_SESSION['accountrole'];
    $accountPic = $_SESSION['accountpic'];

    if (is_null($accountEmail)){
        header('location: ../Login/index.php');
        exit();
    } else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../Admin/Styles/vehicleReqsFrame.css">
    <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
    <title>Requests</title>
</head>
<body>    
    <div class = "background-blur">
        <section id="header">
            <div class="container">
                <div class="row">
                    <div class="header-content">
                        <span id="bsp-logo"></span>
                        <h1>BSP Reservation System</h1>
                        <button id="logout-btn" onclick="openLogoutConfirmation()"></button>
                    </div>
                </div>
            </div>
        </section>
    
        <div class="sidebar-menu">
            <div class="blockBtn">
                <button class="btn" id="home-key" onclick="window.location='index.php'"><strong>Document Requests</strong></button>
                <hr style="width:90%; text-align:center; border:1px solid #d9d9d9">
                    <button class="btn" id="vrf-btn" disabled>Vehicle Reservation Form (VRF)</button>
                    <button class="btn" onclick="window.location='facilityRequestsFrame.php'">Request for the Use of National Office Facility</button>
                <hr style="width:90%; text-align:center; border:1px solid #d9d9d9">
                <button class="btn" onclick="window.location='historyFrame.php'">Requests History</button>
                <button class="btn" onclick="window.location='weeklyReportFrame.php'">Weekly Report</button>
                <button class="btn" onclick="window.location='editData.php'">Form Settings</button>
                <?php
                    if($accountRole == 'Superadmin'){
                ?>      
                        <button class="btn" onclick="window.location='editAccount.php'">Accounts Settings</button>
                        <button class="btn" onclick="window.location='auditTrailFrame.php'">Audit Trail</button>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="admin-container">
            <div class="admin-pending-header">
                <p class="admin-index-text">Vehicle Requests</p>
                <div class="admin-search-pending-request">
                    <div class="searchContainer">
                        <div class="searchForm" action="">
                            <input type="text" placeholder="Search" name="search" id="pendingSearch" onkeyup="tableFilter()">
                            <span type="submit" class="searchButton"></span>
                        </div>
                    </div>
                    <div class="yearContainer">
                        <select name="yearSelect" class="yearDropDown" id="yearFilter" onchange="filterByYear()">
                        <?php 
                            $currentYear = date("Y");

                            if ($currentYear != 2025){
                                while ($currentYear != 2024){
                                    
                            ?>
                                    <option value=<?php echo $currentYear ?>><?php echo $currentYear ?></option>
                            <?php
                                    $currentYear--;
                                    }
                                } else {
                            ?>
                                    <option value=<?php echo $currentYear ?>><?php echo $currentYear ?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
             <br>
            <div class="tableContainer">
                <table class="pendingRequestSummary" id="pendingTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Requesting Division</th>
                            <th>Request Date</th>
                            <th>Reservation Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $select = "SELECT * FROM vehicle_reservation 
                                    WHERE [status] = 'Pending'
                                    ORDER BY request_date ASC;";
                                    
                            $select_run = $conn->prepare($select);
                            $select_run->execute();
                            $vehicle_reservation_rows = $select_run->fetchAll(PDO::FETCH_ASSOC);
                            if(count($vehicle_reservation_rows) > 0){
                                foreach($vehicle_reservation_rows as $row){
                                    if (strtotime($row['reservation_date']) < strtotime(date('Y-m-d'))) {
                                        if ($row['status'] == 'Pending') {
                                            $update = "UPDATE vehicle_reservation 
                                                       SET [status] = 'Lapsed' 
                                                       WHERE request_id = '".$row['request_id']."';";
                                            $update_run = $conn->prepare($update);
                                            $update_run->execute();

                                            $updateSummary = "UPDATE bsp_reservation_summary
                                                            SET [request_status] = 'Lapsed' 
                                                            WHERE request_id = '".$row['request_id']."';";
                                            $updateSummary_run = $conn->prepare($updateSummary);
                                            $updateSummary_run->execute();
                                        }
                                    }
                                    ?>
                                    <tr onclick="window.location.href = 'vrf_Frame.php?request_id=<?php echo $row['request_id']; ?>'">
                                        <td><?php echo $row['request_id']; ?></td>
                                        <td><?php echo $row['requester_division']; ?></td>
                                        <td>
                                            <?php 
                                            $requestDate = new DateTime($row['request_date']); 
                                            echo $requestDate->format('m-d-Y'); 
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $reservationDate = new DateTime($row['reservation_date']); 
                                            echo $reservationDate->format('m-d-Y'); 
                                            ?>
                                        </td>
                                        <td><?php echo $row['status']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <!-- Corrected "No records found" row inside the table -->
                                <tr class="no-record-row">
                                    <td colspan="6" style="text-align: center;">
                                        <div class="noRecordsFound">
                                            <h4 style="color: #989898;">No records found</h4>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="notificationModal" class="notificationContainer">
            <div id="logOutConfirmation" class="logOutNotification">
                <h2>Confirm Logout</h2>
                <h4>Are you sure you want to log out?</h4>
                <div class="button-container">
                    <button class="cancelButton" onclick="closeLogoutConfirmation()">CANCEL</button>
                    <button class="okButton" onclick="window.location.href='Database/process-logout.php';">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script src="../Admin/Scripts/script.js"></script>
<script src="../Admin/Scripts/pendingSearch.js"></script>
<?php
    }
?>