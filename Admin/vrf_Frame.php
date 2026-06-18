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
            <link rel="stylesheet" type="text/css" href="Styles/vrf_Frame.css">
            <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
            <title>VRF</title>
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
                        <button class="btn" id="vrf-btn" onclick="window.location='vehicleRequestsFrame.php'">Vehicle Reservation Form (VRF)</button>
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
                    <div class="admin-index-text-container"><p class="admin-index-text">Vehicle Request Form (VRF) Details</p></div>
                    <div class="vrf-info">
                        <?php
                            $select = "SELECT TOP 1 * FROM vehicle_reservation WHERE request_id = ?";
                            $select_run = $conn->prepare($select);
                            $select_run->execute([$requestId]);
                            $row = $select_run->fetch(PDO::FETCH_ASSOC);
                            
                            if($row){
                                ?>
                                <div class="vrf-info-left">
                                    <p><strong>Division:</strong> <?php echo $row['requester_division']; ?></p>
                                    <p><strong>Date of Use:</strong> <?php 
                                                        $reservationDate = new DateTime($row['reservation_date']);
                                                        echo $reservationDate->format('m-d-20y');
                                                    ?></p>
                                    <p><strong>ETD (from Station):</strong> <?php 
                                                            $etds_Time = date("h:i A", strtotime($row['etd_from_station']));
                                                            echo $etds_Time;
                                                            ?></p>
                                    <p><strong>ETD (from Destination):</strong>  <?php 
                                                                $etdd_Time = date("h:i A", strtotime($row['etd_from_destination']));
                                                                echo $etdd_Time;
                                                                ?></p>
                                    <p><strong>Destination/s:</strong> <?php echo $row['destination']; ?></p>
                                    <p><strong>Passenger/s:</strong> <?php echo $row['passenger'];?></p>
                                </div>
                                <div class="vrf-info-right">
                                    <p><strong>Division Chief:</strong> <?php echo $row['division_chief'];?></p>
                                    <p><strong>Date Filled:</strong> <?php 
                                                        $filingDate = new DateTime($row['request_date']);
                                                        echo $filingDate->format('m-d-20y');
                                                    ?></p>
                                    <p><strong>ETA (to Destination):</strong> <?php 
                                                            $etad_Time = date("h:i A", strtotime($row['eta_to_destination']));
                                                            echo $etad_Time;
                                                            ?></p>
                                    <p><strong>ETA (to Station):</strong> <?php 
                                                            $etas_Time = date("h:i A", strtotime($row['eta_to_station']));
                                                            echo $etas_Time;
                                                            ?></p>
                                    <p><strong>Requester:</strong> <?php echo $row['requester_name'];?></p>
                                    <p><strong>Number of Passenger:</strong> <?php echo $row['passenger_number'];?></p>
                                </div>
                    </div>
                                <hr style="width:95%; text-align:center; border:1px solid #d9d9d9">
                                <div class="driver-vehicle-info">
                                    <form class="driver-vehicle-form" id="vrf-form" action="../Admin/Database/vrf_update.php" method="POST">
                                        <input type="hidden" name="request_id" value="<?php echo $requestId; ?>">
                                        <input type="hidden" name="etds" value="<?php echo $row['etd_from_station']; ?>">
                                        <input type="hidden" name="etas" value="<?php echo $row['eta_to_station']; ?>">
                                        <input type="hidden" name="r_date" value="<?php echo $row['reservation_date']; ?>">

                                        <div class="vehicle-info">
                                            <span class="vehicle-label"><strong>Vehicle/Plate No.: </strong> </span>
                                            <div class="list-vehicle">
                                                <select id="vehicle-value" name="vehicle" class="vehicle-dropdown" required>
                                                    <?php
                                                        if($row["vehicle_plate_no"]!=null) { 
                                                    ?>
                                                            <option value="<?php echo $row["vehicle_plate_no"] ?>"><?php echo $row["vehicle_plate_no"] ?></option>
                                                    <?php
                                                        }else{
                                                            $select_vehicle = "SELECT *
                                                                            FROM vehicle_info;";
                                                            $select_vehicle_run = $conn->prepare($select_vehicle);
                                                            $select_vehicle_run->execute();
                                                            $rows1 = $select_vehicle_run->fetchAll(PDO::FETCH_ASSOC);

                                                            if(count($rows1) > 0){
                                                    ?>
                                                                <option value="" disabled selected>Select Vehicle</option>
                                                    <?php
                                                                foreach($rows1 as $row1){
                                                    ?>
                                                                    <option value="<?php echo $row1["vehicle_model"] ?> / <?php echo $row1['vehicle_platenum'] ?>"><?php echo $row1["vehicle_model"] ?> / <?php echo $row1['vehicle_platenum'] ?></option>
                                                    <?php 
                                                                }
                                                            }
                                                        } 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="driver-info">
                                            <span class="driver-name-label"><strong>Driver:</strong> </span>
                                            <div class="driver-names">
                                                <select id="driver-value" name="driver" class="driver-dropdown" required>
                                                    <?php
                                                        if($row["vehicle_driver"]!=null) { 
                                                    ?>
                                                            <option value="<?php echo $row["vehicle_driver"] ?>"><?php echo $row["vehicle_driver"] ?></option>
                                                    <?php
                                                        }else{
                                                            $select_driver = "SELECT *
                                                                            FROM driver_info;";
                                                            $select_driver_run = $conn->prepare($select_driver);
                                                            $select_driver_run->execute();
                                                            $rows2 = $select_driver_run->fetchAll(PDO::FETCH_ASSOC);
                                                            if(count($rows2) > 0){
                                                    ?>
                                                                <option value="" disabled selected>Select Driver</option>
                                                    <?php
                                                                foreach($rows2 as $row2){
                                                    ?>
                                                                    <option value="<?php echo $row2["driver_name"] ?>"><?php echo $row2["driver_name"] ?></option>
                                                    <?php
                                                                }
                                                            }
                                                        }  
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="status-vrf">
                                            <span class="set-status-label"><strong>Set Status:</strong> </span>

                                            <div class="status-dropdown">
                                            
                                                <select id="status-value" name="status" class="status-dropdown-menu" onchange="selectValue()" required>
                                                <?php
                                                    if($row["status"]=="Approved" || $row["status"]=="No Vehicle Available" || $row["status"]=="Lapsed") { 
                                                ?>
                                                        <option value="<?php echo $row["status"] ?>"><?php echo $row["status"] ?></option>
                                                    <?php
                                                        }else{
                                                    ?>
                                                            <option value="" disabled selected>Select Status</option>
                                                            <option value="Approved">Approved</option>
                                                            <option value="No Vehicle Available">No Vehicle Available</option>
                                                    <?php 
                                                        } 
                                                    ?>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </form>
                                </div>

                                <div class="blockBtn-vrf-function">
                                    <button class="btn" 
                                        <?php
                                            if($row["status"]!=="Pending"){
                                                echo "style='display: none;'"; 
                                            } 
                                        ?> 
                                    onclick="openUpdateVRF()"><strong>Update</strong>
                                    </button>
                                    <button class="btn" onclick="printVRF('<?php echo $requestId ?>', '<?php echo $accountRole ?>')"><strong>Print VRF</strong></button>
                                    <button class="cancelRequestbtn" 
                                    <?php
                                        if($row["for_cancellation"]==0){
                                            echo "style='display: none;'"; 
                                        } ?> onclick="openCancelRequestVRF()"><strong>Cancel Request</strong>
                                </button>
                                </div>
                </div>        
            </div>

            <div id="updateModal" class="status-modal">
                <p class="status-dialogue">Are you sure you want to update the status?</p>
                <div class="modal-content">
                    <button class="cancel-update-btn" id="cancelBtn" onclick="closeUpdateVRF()">Cancel</button>
                    <button class="confirm-update-btn" onclick="submitUpdateForm()">Update</button>
                </div>
            </div>
            <div id="cancellationModal" class="cancellation-modal">
                <p class="status-dialogue">Are you sure you want to cancel request?</p>
                <div class="modal-content">
                    <button class="cancel-update-btn" id="cancelBtn" onclick="closeCancelRequestVRF()">Cancel</button>
                    <button class="confirm-update-btn" onclick="cancelRequestVRF('<?php echo $row['request_id']?>')">Cancel Request</button>
                            <?php
                            } 
                            ?>
                </div>
            </div>
            <div class="modal-overlay" id="modalOverlay" ></div>
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
                </div?>
            </div>
            <script src="../Admin/Scripts/script.js"></script>
            <script src="../Admin/Scripts/update_db.js"></script>
            <script src="../Admin/Scripts/vrf_script.js"></script>
            <script src="../Admin/Scripts/print_forms.js"></script>
        </body>
        </html>

<?php
    }   
?>