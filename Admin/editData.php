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
            <link rel="stylesheet" type="text/css" href="../Admin/Styles/editData.css">
            <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
            <title>Form Settings</title>
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
                        <button class="btn" onclick="window.location='weeklyReportFrame.php'">Weekly Report</button>
                        <button class="btn" id="editFormBtn" onclick="window.location='editData.php'">Form Settings</button>
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
                    <div class="container-top">
                        <div id="topLeft" class="reportSummaryContainer">
                            <div class="titleContainer">
                                <h3>Division Office</h3>
                                <button class="division-btn" onclick="addRow('divisionInfoTable', 'divisionOffice', 4)"> ADD </button>
                            </div>

                            <div class="summarySubcontainer">
                                <div class="division-summary">
                                    <table class="division-info-table" id="divisionInfoTable">
                                        <thead>
                                            <tr>
                                                <th class="div-name">Division Name</th>
                                                <th class="div-chief">Division Chief</th>
                                                <th style="display:none">id_division</th>
                                                <th style="text-align:center">Vehicle</th>
                                                <th style="text-align:center">Use of Facilities</th>
                                                <th class="editCol"></th>
                                                <th class="saveCol"></th>
                                                <th class="deleteCol"></th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $select_division = "SELECT * FROM bsp_division;";
                                                $select_division_run = $conn->prepare($select_division);
                                                $select_division_run->execute();
                                                $rows1 = $select_division_run->fetchAll(PDO::FETCH_ASSOC);
                                                if(count($rows1) > 0) {
                                                    foreach($rows1 as $row1){
                                            ?>
                                                        <tr class="division-row" id="divisionRow" contenteditable="false">
                                                            <td><input type="text" value="<?php echo $row1['division_name'] ?>" style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td><input type="text" value="<?php echo $row1['division_chief'] ?>" style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td style="display:none"><input type="text" value="<?php echo $row1['id_division'];?>" style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td style="text-align:center"><input type="checkbox" class="vrfSelected" <?php echo ($row1['is_signatoreeVRF']==1) ? "Checked" : "" ?>></td>
                                                            <td style="text-align:center"><input type="checkbox" class="facilitiesSelected" <?php echo ($row1['is_signatoreeFacilities']==1) ? "Checked" : "" ?>></td>
                                                            <td id="editRow"><span class="edit-icon tooltip" onclick="editRow(this)"><span class="tooltiptext">Edit</span></span></td>
                                                            <td id="saveRow"><span class="save-icon tooltip" onclick="saveRow('divisionOffice', this)"><span class="tooltiptext">Save</span></span></td>
                                                            <td id="deleteRow"><span class="delete-icon tooltip" onclick="deleteRow('divisionOffice', this)"><span class="tooltiptext">Delete</span></span></td>
                                                        </tr>
                                            <?php    
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="weeklyReportSubcontainer">
                        <div id="leftReportContainer" class="detailedReportContainer">
                            <div class="detailedSubcontainer">
                                <div class="titleContainer">
                                    <h3>Vehicle Information</h3>
                                    <button class="vehicle-drive-btn" onclick="addRow('vehicleInfoTable', 'vehicleInfo', 2)"> ADD </button>
                                </div>

                                <div class="tableContainer">
                                    <table class="vehicle-info-table" id="vehicleInfoTable">
                                        <thead>
                                            <tr>
                                                <th id="vehicleColHeader">Vehicle Model</th>
                                                <th id="plateNumColHeader">Plate Number</th>
                                                <th style="display:none">vehicle_id</th>
                                                <th class="editCol"></th>
                                                <th class="saveCol"></th>
                                                <th class="deleteCol"></th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php
                                                $select_vehicle = "SELECT *
                                                                FROM vehicle_info;";
                                                $select_vehicle_run = $conn->prepare($select_vehicle);
                                                $select_vehicle_run->execute();
                                                $rows2 = $select_vehicle_run->fetchAll(PDO::FETCH_ASSOC);
                                                if(count($rows2) > 0) {
                                                    foreach($rows2 as $row2){
                                            ?>
                                                        <tr>
                                                            <td><input type="text" value="<?php echo $row2['vehicle_model'];?>" style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td id="plateNumRow"><input type="text" value="<?php echo $row2['vehicle_platenum'];?>"  style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td style="display:none"><input type="text" value="<?php echo $row2['vehicle_id'];?>" style="border: none; font-size: 16px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td id="editRow"><span class="edit-icon tooltip" onclick="editRow(this)"><span class="tooltiptext">Edit</span></span></td>
                                                            <td id="saveRow"><span class="save-icon tooltip" onclick="saveRow('vehicleInfo', this)"><span class="tooltiptext">Save</span></span></td>
                                                            <td id="deleteRow"><span class="delete-icon tooltip" onclick="deleteRow('vehicleInfo', this)"><span class="tooltiptext">Delete</span></span></td>
                                                        </tr>
                                            <?php 
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
                                    <h3>Driver Information</h3>
                                    <button class="vehicle-drive-btn" onclick="addRow('driverInfoTable','driverInfo', 1)"> ADD </button>
                                </div>

                                <div class="tableContainer" id="">
                                    <table class="driver-info-table" id="driverInfoTable"> 
                                        <thead>
                                            <tr>
                                                <th>Driver Name</th>
                                                <th style="display:none">vehicle_id</th>
                                                <th class="editCol"></th>
                                                <th class="saveCol"></th>
                                                <th class="deleteCol"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $select_driver = "SELECT * FROM driver_info;";
                                                $select_driver_run = $conn->prepare($select_driver);
                                                $select_driver_run->execute();
                                                $rows3 = $select_driver_run->fetchAll(PDO::FETCH_ASSOC);
                                                if(count($rows3) > 0) {
                                                    foreach($rows3 as $row3){
                                            ?>
                                                        <tr>
                                                            <td><input type="text" value="<?php echo $row3['driver_name'];?>"  style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td style="display:none"><input type="text" value="<?php echo $row3['driver_id'];?>" style="border: none; font-size: 18px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                            <td id="editRow"><span class="edit-icon tooltip" onclick="editRow(this)"><span class="tooltiptext">Edit</span></span></td>
                                                            <td id="saveRow"><span class="save-icon tooltip" onclick="saveRow('driverInfo',this)"><span class="tooltiptext">Save</span></span></td>
                                                            <td id="deleteRow"><span class="delete-icon tooltip" onclick="deleteRow('driverInfo',this)"><span class="tooltiptext">Delete</span></span></td>
                                                        </tr>
                                            <?php
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
                <div id="notificationModal" class="notificationContainer">
                    <div id="logOutConfirmation" class="logOutNotification">
                        <h2>Confirm Logout</h2>
                        <h4>Are you sure you want to log out?</h4>
                        <div class="button-container">
                            <button class="cancelButton" onclick="closeLogoutConfirmation()">CANCEL</button>
                            <button class="okButton" onclick="window.location.href='Database/process-logout.php';">OK</button>
                        </div>
                    </div>

                    <div id="confirmationModal" class="status-modal">
                        <p class="status-dialogue">Are you sure you want to <span id="actionConfirmation"></span></p>
                        <div class="modal-content">
                            <button class="cancel-update-btn" id="cancelBtn" onclick="closeConfirmMsg()">Cancel</button>
                            <button class="confirm-update-btn" id="actionBtn" onclick=""></button>
                        </div>
                    </div>
                    
                    <div id="statusModal" class="status-modal">
                        <span id="statusModalIcon" class="status-modal-icon"></span>
                        <p id="contentStatusModal" class="status-dialogue"></p>
                        <div class="modal-content">
                            <button class="confirm-update-btn" id="actionBtn" onclick="closeConfirmMsg()">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <script src="../Admin/Scripts/script.js"></script>
        <script src="../Admin/Scripts/historySearch.js"></script>
        <script src="../Admin/Scripts/editData.js"></script>
<?php
    }
?>