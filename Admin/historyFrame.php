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
            <link rel="stylesheet" type="text/css" href="../Admin/Styles/historyFrame.css">
            <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
            <title>Requests History</title>
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
                        <button class="btn" id="history-btn" onclick="window.location='historyFrame.php'">Requests History</button>
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
                    <div class="history-search-header">
                        <p class="admin-index-text">Requests History</p>
                        <div class="admin-search-history">
                            <div class="searchContainer">
                                <div class="searchForm" >
                                    <input type="text" placeholder="Search" id='historySearch' name="search" onkeyup="tableFilter()">
                                    <span class="searchButton"></span>
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
                        <table class="requestSummary" id="historyTable">
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
                                    $select = "SELECT 
                                                request_id, 
                                                requester_division, 
                                                request_date, 
                                                reservation_date, 
                                                [status]
                                            FROM vehicle_reservation
                                            WHERE status <> 'Pending'

                                            UNION ALL

                                            SELECT 
                                                request_id, 
                                                requester_division, 
                                                request_date, 
                                                reservation_date, 
                                                [status]
                                            FROM facility_reservation
                                            WHERE status <> 'Pending'

                                            ORDER BY request_date DESC;";
                                    $select_run = $conn->prepare($select);
                                    $select_run->execute();

                                    $rows = $select_run->fetchAll(PDO::FETCH_ASSOC);

                                    if(count($rows) > 0){
                                        foreach($rows as $row){
                                ?>
                                            <tr onclick="changeFrame('<?php echo $row['request_id']?>')">
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
        <script src="../Admin/Scripts/historySearch.js"></script>
<?php
    }
?>