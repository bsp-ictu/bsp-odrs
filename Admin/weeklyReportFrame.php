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
    <link rel="stylesheet" type="text/css" href="../Admin/Styles/weeklyReportFrame.css">
    <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
    <title>Weekly Report</title>
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
                <button class="btn" onclick="window.location='vehicleRequestsFrame.php'">Vehicle Reservation Form (VRF)</button>
                <button class="btn" onclick="window.location='facilityRequestsFrame.php'">Request for the Use of National Office Facility</button>
                <hr style="width:90%; text-align:center; border:1px solid #d9d9d9">
                <button class="btn" onclick="window.location='historyFrame.php'">Requests History</button>
                <button class="btn" id="weekly-report-btn" onclick="window.location='weeklyReportFrame.php'">Weekly Report</button>
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
        <div class="weeklyReportMainContainer">
            <div class="reportSummaryContainer">
                <div class="currentDateContainer">
                    <div class="currentDateSubcontainer">
                        <h2>Weekly Reservation Report</h2>
                    </div>

                    <div class="currentDateSubcontainer date">
                        <p>
                            <strong> As of 
                                <?php 
                                    $currentDate = date("F d, Y");
                                    echo $currentDate;
                                ?>
                            </strong>
                        </p>
                    </div>
                </div>

                <div class="summarySubcontainer">
                    <?php
                        $weeklyTotalReservation = 0;
                        $weeklyVehicleReservation = 0;
                        $weeklyFacilityReservation = 0;
                        $weeklyPendingReservation = 0;
                        $weeklyApprovedReservation = 0;
                        $weeklyRejectedReservation = 0;
                        $weeklyScheduledReservation = 0;

                        $dateNow = date("Y-m-d");
                        $lastSunday = date("Y-m-d", strtotime('last sunday', strtotime($dateNow)));
                        $nextSaturday = date("Y-m-d",strtotime('next saturday', strtotime($dateNow)));

                        $_SESSION['lastSunday'] = $lastSunday;
                        $_SESSION['nextSaturday'] = $nextSaturday;

                        $select = " SELECT * FROM bsp_reservation_summary WHERE request_date BETWEEN ? AND ?";
                        $select_run6 = $conn->prepare($select);
                        $select_run6->execute([$lastSunday, $nextSaturday]);
                        $rows6 = $select_run6->fetchAll(PDO::FETCH_ASSOC);

                        $reservations = [];

                        if(count($rows6) > 0){
                            foreach($rows6 as $row6){
                                $weeklyTotalReservation ++;
                                $reservations[] = $row6;

                                if ($row6['request_type'] == "Vehicle Reservation"){
                                    $weeklyVehicleReservation++;
                                } else {
                                    $weeklyFacilityReservation++;
                                }
                                
                                if ($row6['request_status'] == "Pending"){
                                    $weeklyPendingReservation++;
                                } else if ($row6['request_status'] == "Approved"){
                                    $weeklyApprovedReservation++;
                                } else {
                                    $weeklyRejectedReservation++;
                                }
                            }
                        }

                        $select_2 = "SELECT * FROM bsp_reservation_summary WHERE reservation_date BETWEEN ? AND 
                        
                        
                        
                        ? AND request_status='Approved'";
                        $select_run7 = $conn->prepare($select_2);
                        $select_run7->execute([$lastSunday, $nextSaturday]);
                        
                        $rows7 = $select_run7->fetchAll(PDO::FETCH_ASSOC);


                        $weeklyScheduledReservation = count($rows7);

                        $_SESSION['totalReservation'] = $weeklyTotalReservation;
                        $_SESSION['vehicleReservation'] = $weeklyVehicleReservation;
                        $_SESSION['facilityReservation'] = $weeklyFacilityReservation;
                        $_SESSION['pendingReservation'] = $weeklyPendingReservation;
                        $_SESSION['approvedReservation'] = $weeklyApprovedReservation;
                        $_SESSION['rejectedReservation'] = $weeklyRejectedReservation;
                        $_SESSION['scheduledReservation'] = $weeklyScheduledReservation;
                    ?> 

                    <div class="summaryInfo">
                        <div class="totalReservationContainer">
                            <h3>Total Reservations: <span><?php echo $weeklyTotalReservation ?></span></h3>
                        </div>
                        <div class="reservationCount">
                            <h4>Vehicle Reservations: <span><?php echo $weeklyVehicleReservation ?></span></h4>
                        </div>
                        <div class="reservationCount">
                            <h4>Facility Reservations: <span><?php echo $weeklyFacilityReservation ?></span></h4>
                        </div>
                    </div>

                    <div class="summaryInfo">
                        <div class="reservationCount">
                            <h4>Pending Reservations: <span><?php echo $weeklyPendingReservation ?></span></h4>
                        </div>
                        <div class="reservationCount">
                            <h4>Approved Reservations: <span><?php echo $weeklyApprovedReservation ?></span></h4>
                        </div>
                        <div class="reservationCount">
                            <h4>Rejected Reservations: <span><?php echo $weeklyRejectedReservation ?></span></h4>
                        </div>
                    </div>

                    <div class="summaryInfo">
                        <div class="reservationCount">
                            <h4>Scheduled This Week: <span><?php echo $weeklyScheduledReservation ?></span></h4>
                        </div>
                        <div class="pdfContainer">
                            <button onclick="pdfWeeklyReport()">Generate PDF</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="weeklyReportSubcontainer">
                <div id="leftReportContainer" class="detailedReportContainer">
                    <div class="detailedSubcontainer">
                        <div class="titleContainer">
                            <h3>Approved Reservations</h3>
                        </div>

                        <div class="tableContainer">
                            <table class="tableReservations">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Request Type</th>
                                        <th>Request Date</th>
                                        <th>Reservation Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if ($weeklyApprovedReservation != 0) {
                                            foreach ($reservations as $row7) {
                                                if ($row7['request_status'] == "Approved"){
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo htmlspecialchars($row7['request_id']);?>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars(substr_replace($row7['request_type'],"",8));?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                $requestDate = new DateTime($row7['request_date']); 
                                                                echo $requestDate->format('m-d-y'); 
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                $reservedDate = new DateTime($row7['reservation_date']); 
                                                                echo $reservedDate->format('m-d-y'); 
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($row7['request_status']);?>
                                                        </td>
                                                    </tr>
                                    <?php
                                                } 
                                            }
                                        }
                                    ?>   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="rightReportContainer" class="detailedReportContainer">
                    <div class="detailedSubcontainer">
                        <div class="titleContainer">
                            <h3>Pending Reservations</h3>
                        </div>

                        <div class="tableContainer">
                            <table class="tableReservations"> 
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Request Type</th>
                                        <th>Request Date</th>
                                        <th>Reservation Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                        if (($weeklyPendingReservation != 0) || ($weeklyRejectedReservation != 0)) {
                                            foreach ($reservations as $row7) {
                                                if ($row7['request_status'] == "Pending"){
                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo htmlspecialchars($row7['request_id']);?>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars(substr_replace($row7['request_type'],"",8));?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                $requestDate = new DateTime($row7['request_date']); 
                                                                echo $requestDate->format('m-d-y'); 
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                $reservedDate = new DateTime($row7['reservation_date']); 
                                                                echo $reservedDate->format('m-d-y'); 
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo htmlspecialchars($row7['request_status']);?>
                                                        </td>
                                                    </tr>
                                    <?php
                                                }
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function pdfWeeklyReport() {
                window.open('../Library_tcpdf/process-weeklyReportPDF.php', '_blank');
            }
        </script>
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
<script src="../Admin/Scripts/historySearch.js"></script>
<script src="../Admin/Scripts/history_script.js"></script>
<?php
    }
?>