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
            <link rel="stylesheet" type="text/css" href="../Admin/Styles/auditTrailFrame.css">
            <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
            <title>Audit Trail</title>
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
                            <button class="btn" id="auditTrailBtn" onclick="window.location='auditTrailFrame.php'">Audit Trail</button>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="editAccountContainer">
                    <div class="accountManage">
                        <div class="accountManage-header">
                                <div class="accountManage-text">
                                    <h3>Audit Trail</h3>
                                </div>
                        </div>

                        <div class="auditTrailSubcontainer">
                            <table class="bsp-account-table" id="bspAccountTable">
                                <thead>
                                    <tr>
                                        <th id="auditIDColumn">ID</th>
                                        <th id="accountNameColumn">Account Name</th>
                                        <th id="actionTypeColumn">Action Type</th>
                                        <th id="tableNameColumn">Table Name</th>
                                        <th id="recordIDColumn">Record ID</th>
                                        <th id="oldValuesColumn">Old Values</th>
                                        <th id="newValuesColumn">New Values</th>
                                        <th id="actionDescColumn">Action Description</th>
                                        <th id="actionTSColumn">Action Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php
                                        $sql = "SELECT 
                                                    audit_id,
                                                    account_name,
                                                    action_type,
                                                    table_name,
                                                    record_id,
                                                    old_value,
                                                    new_value,
                                                    action_description,
                                                    FORMAT(created_at, 'yyyy-MM-dd hh:mm tt') AS created_at
                                                FROM audit_trail
                                                ORDER BY audit_id DESC;
                                                ";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();

                                        // Fetch all rows first
                                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        echo "<!-- rows fetched: " . count($rows) . " -->";
                                        if (count($rows) > 0) {
                                            foreach ($rows as $row) {
                                                echo "<tr>";
                                                echo "<td>".htmlspecialchars($row['audit_id'])."</td>";
                                                echo "<td>".htmlspecialchars($row['account_name'])."</td>";
                                                echo "<td>".htmlspecialchars($row['action_type'])."</td>";
                                                echo "<td>".htmlspecialchars($row['table_name'])."</td>";
                                                echo "<td>".htmlspecialchars($row['record_id'])."</td>";

                                                // Old Values
                                                echo "<td>";
                                                $oldValues = json_decode($row['old_value'], true);
                                                if ($oldValues) {
                                                    echo "<ul style='margin:0; padding-left:15px;'>";
                                                    foreach ($oldValues as $key => $value) {
                                                        echo "<li><strong>".htmlspecialchars($key)."</strong>: ".htmlspecialchars($value)."</li>";
                                                    }
                                                    echo "</ul>";
                                                } else {
                                                    echo htmlspecialchars($row['old_value']);
                                                }
                                                echo "</td>";

                                                // New Values
                                                echo "<td>";
                                                $newValues = json_decode($row['new_value'], true);
                                                if ($newValues) {
                                                    echo "<ul style='margin:0; padding-left:15px;'>";
                                                    foreach ($newValues as $key => $value) {
                                                        echo "<li><strong>".htmlspecialchars($key)."</strong>: ".htmlspecialchars($value)."</li>";
                                                    }
                                                    echo "</ul>";
                                                } else {
                                                    echo htmlspecialchars($row['new_value']);
                                                }
                                                echo "</td>";

                                                // Action Description
                                                echo "<td>".htmlspecialchars($row['action_description'])."</td>";

                                                echo "<td>".htmlspecialchars($row['created_at'])."</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr class='no-record-row'>
                                                    <td colspan='9' style='text-align: center;'>
                                                        <div class='noRecordsFound'>
                                                            <h4 style='color: #989898;'>No records found</h4>
                                                        </div>
                                                    </td>
                                                </tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="notificationModal" class="notificationContainer">
                    <div id="resetPasswordModal" class="resetPasswordContainer">
                        <div class="reset-password-header">
                            <div class="resetPass-text">
                                <h3>Password Reset</h3>
                            </div>
                        </div>

                        <form id="passwordResetForm" class="password-reset-form">
                            <div class="set-threshold-container">
                                <label for="setThreshold">Maximum Login Attempts</label>
                                <input type="number" id="setThreshold" class="set-treshold-input" min="1" max="50" placeholder="Set Threshold" oninput="limitInputLength(this)"/>
                            </div>

                            <div class="reset-password-container">
                                <label for="resetPassword">Reset Password</label>
                                <div class="reset-password-input-container">
                                    <input type="password" id="resetPassword" class="reset-password-input" maxlength="16" placeholder="Enter Password" autocomplete="new-password"/>
                                    <div id="showConfirmPassword" class="showHidePassword" onclick="showPasswordInput('showConfirmPassword', 'hideConfirmPassword', 'confirmPass')"></div>
                                    <div id="hideConfirmPassword" class="showHidePassword" onclick="hidePasswordInput('hideConfirmPassword', 'showConfirmPassword', 'confirmPass')"></div>
                                </div>
                            </div>

                            <div class="reset-password-action-btn">
                                    <button class="submit-reset-pass-btn" id="submitResetPwButton" onclick="">SUBMIT</button>
                                    <button class="cancel-reset-pass-btn" id="cancelResetPwButton" onclick="">CANCEL</button>
                            </div>
                        </form>
                    </div>

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

                    <div id="changePassword" class="updateNotification">
                        <h2>Confirm change password</h2>
                        <p>Are you sure you want to change your password?<p>
                        <button class="cancelButton" onclick="cancelUpdatePassword()">CANCEL</button>
                        <button class="okButton" onclick="proceedUpdatePassword()">OK</button>
                    </div>

                    <div id="sameOldNewPassword" class="updateNotification">
                        <h2>Invalid password!</h2>
                        <h4>New password must be different from the current password. Please check and try again.</h4>
                        <button class="okButton" onclick="closeSamePassword()">OK</button>
                    </div>

                    <div id="differentConfirmationPassword" class="updateNotification">
                        <h2>Invalid password!</h2>
                        <h4>New password and confirmation password do not match. Please check and try again.</h4>
                        <button class="okButton" onclick="closeDifferentPassword()">OK</button>
                    </div>

                    <div id="incorrectPassword" class="updateNotification">
                        <h2>Incorrect password!</h2>
                        <h4>The entered current password is incorrect. Please check and try again.</h4>
                        <button class="okButton" onclick="closeIncorrectPassword()">OK</button>
                    </div>

                    <div id="successfulUpdatePassword" class="updateNotification">
                        <h2>Password successfully updated!</h2>
                        <h4>Your new password has been set. You can now use it to log in.</h4>
                        <button class="okButton" onclick="closeSuccessfulUpdatePassword()">OK</button>
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