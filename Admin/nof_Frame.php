<?php
    session_start();
    $requestId = isset($_GET['request_id']) ? $_GET['request_id'] : '';
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
            <link rel="stylesheet" type="text/css" href="Styles/nof_Frame.css">
            <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
            <title>Use of National Office Facility</title>
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
                        <button class="btn" id="national-office-btn" onclick="window.location='facilityRequestsFrame.php'">Request for the Use of National Office Facility</button>
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
                    <div class= "admin-index-text-container">
                        <p class="admin-index-text">Request for the Use of National Office Facility</p>
                    </div>
                    <?php
                        $select = "SELECT TOP 1 * FROM facility_reservation WHERE request_id = ?";
                        $select_run = $conn->prepare($select);
                        $select_run->execute([$requestId]);
                        $row = $select_run->fetch(PDO::FETCH_ASSOC);
                        if($row){
                    ?>
                            <div class="nof-info">
                                <div class="nof-info-left">
                                    <p><strong>Facility:</strong> <?php echo $row['reserved_facility']?></p>
                                    <p><strong>Date Filed:</strong> <?php 
                                                        $filingDate = new DateTime($row['request_date']);
                                                        echo $filingDate->format('m-d-20y');
                                                    ?></p>
                                    <p><strong>Date of Use:</strong> <?php 
                                                        $reservationDate = new DateTime($row['reservation_date']);
                                                        echo $reservationDate->format('m-d-20y');
                                                    ?></p>
                                    <p><strong>Time of Use (Start):</strong> <?php 
                                                        $use_time_start = date("h:i A", strtotime($row['reservation_time_start']));
                                                        echo $use_time_start;
                                                    ?></p>
                                    <p><strong>Time of Use (End):</strong> <?php 
                                                        $use_time_end = date("h:i A", strtotime($row['reservation_time_end']));
                                                        echo $use_time_end;
                                                    ?></p>
                                    <p><strong>Number of Persons:</strong> <?php echo $row["number_of_person"] ?></p>
                                    <p><strong>Purpose:</strong> <?php echo $row["purpose"] ?></p>
                                </div>
                                <div class="nof-info-right">
                                    <p><strong>Requesting Officer:</strong> <?php echo $row["requester_name"] ?></p>
                                    <p><strong>Group/Division:</strong> <?php echo $row["requester_division"] ?></p>
                                    <p><strong>Division Chief:</strong> <?php echo $row["division_chief"] ?></p>
                                </div>
                            </div>
                            <hr style="width:90%; text-align:cebnter; border:1px solid #d9d9d9">
                            <div class="status-update-container">
                                <form class="status-form" action="../Admin/Database/nof_update.php" method="POST" id="updateStatusForm">
                                    <input type="hidden" name="request_id" value="<?php echo $requestId ?>">
                                    <input type="hidden" name="res_facility" value="<?php echo $row['reserved_facility'] ?>">
                                    <input type="hidden" name="res_date" value="<?php echo $row['reservation_date'] ?>">
                                    <div class="status-nof">
                                        <div class="set-status" style="display: block;">
                                            <span class="set-status-label"><strong>Set Status:</strong></span>
                                            <div class="status-dropdown">
                                                <select id="status-value" name="status" class="status-dropdown-menu" required>
                                                <?php
                                                    if($row["status"]=="Approved" || $row["status"]=="Not Available" || $row["status"]=="Lapsed") { ?>
                                                        <option value="<?php echo $row["status"] ?>"><?php echo $row["status"] ?></option>
                                                <?php
                                                    }else{
                                                ?>
                                                        <option value="" disabled selected>Select Status</option>
                                                        <option value="Approved">Approved</option>
                                                        <option value="Not Available">Not Available</option>
                                                <?php 
                                                    } 
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="set-facility" style="display: block; margin-left: 50px;">
                                            <span class="set-facility-label" ><strong>Set Facility:</strong></span>
                                            <div class="facility-dropdown">
                                                <select id="facility-value" name="facility" class="facility-dropdown-menu">
                                                <?php
                                                    if($row['status']=="Pending"){
                                                ?>
                                                        <option value="" selected>Select Facility</option>
                                                        <option value="Educational Hub (Library and Museum)">Educational Hub (Library and Museum)</option>
                                                        <option value="Conference Hall 6th Floor">Conference Hall 6th Floor</option>
                                                        <option value="National Executive Boardroom">National Executive Boardroom</option>
                                                        <option value="4th Floor Veranda">4th Floor Veranda</option>
                                                        <option value="Ground/Parking Area">Ground/Parking Area</option>
                                                <?php
                                                    }else if($row['status']!="Pending"){
                                                ?>
                                                        <option value="<?php echo ($row['other_facility']=="")? $row['reserved_facility']:$row['other_facility']; ?>" selected disabled><?php echo ($row['other_facility']=="")? $row['reserved_facility']:$row['other_facility']; ?></option>
                                                <?php
                                                    }
                                                ?>
                                                </select>
                                                <div class="help-text">Optional - Reassign facility if needed.</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="blockBtn-nof-function">
                                <button class="btn" 
                                    <?php
                                        if($row["status"]!="Pending"){
                                            echo "style='display: none;'"; 
                                        } ?> onclick="openUpdateNOF()"><strong>Update</strong>
                                </button>
                                <button class="btn" onclick="printNOF('<?php echo $requestId ?>', '<?php echo $accountRole ?>')"><strong>Print Request Form</strong></button>
                                <button class="cancelRequestbtn" 
                                    <?php
                                        if($row["for_cancellation"]==0){
                                            echo "style='display: none;'"; 
                                        } ?> onclick="openCancelRequestNOF()"><strong>Cancel Request</strong>
                                </button>
                            </div>
                </div>        
            </div>
            <div class="status-confirmation-modal" id="statusConfirmationModal">
                <hr style="width:90%; text-align:center; margin:20px; border:1px solid #d9d9d9">
                <p class="status-confirmation-label">Status Updated!</p>
                <button class="status-confirm-btn" onclick="closeModal()"><strong>Close</strong></button>
            </div>
            <div id="cancellationModal" class="cancellation-modal">
                <p class="status-dialogue">Are you sure you want to cancel request?</p>
                <div class="modal-content">
                    <button class="cancel-update-btn" id="cancelBtn" onclick="closeCancelRequestNOF()">Cancel</button>
                    <button class="confirm-update-btn" onclick="cancelRequestNOF('<?php echo $row['request_id']?>')">Cancel Request</button>
                </div>
                <?php
                    } 
                ?>
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

                <div id="conflictMsg" class="conflict-msg">
                    <div id="messageContentConflictHeader" class="conflict-msg-header"></div>
                    <div id="messageContentConflict" class="conflict-msg-body"></div>
                    <button class="okButton" onclick="closeConflictMsg()">OK</button>
                </div>

                <div id="updateModal" class="status-modal">
                    <p class="status-dialogue">Are you sure you want to update the status?</p>
                    <div class="modal-content">
                        <button class="cancel-update-btn" id="cancelBtn" onclick="closeUpdateNOF()">Cancel</button>
                        <button class="confirm-update-btn" onclick="submitUpdateForm()">Update</button>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <script src="../Admin/Scripts/script.js"></script>
        <script src="../Admin/Scripts/calendar.js"></script>
        <script src="../Admin/Scripts/print_forms.js"></script>
        <script src="../Admin/Scripts/nof_script.js"></script>
<?php
    }
?>