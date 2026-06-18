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
            <link rel="stylesheet" type="text/css" href="../Admin/Styles/editAccount.css">
            <link rel="stylesheet" type="text/css" href="../Admin/Styles/updatePhoto.css">
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
                        <button class="btn" onclick="window.location='editData.php'">Form Settings</button>
                        <?php
                        if($accountRole == 'Superadmin'){
                        ?>      
                            <button class="btn" id="editAccBtn" onclick="window.location='editAccount.php'">Accounts Settings</button>
                            <button class="btn" onclick="window.location='auditTrailFrame.php'">Audit Trail</button>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="editAccountContainer">
                    <div class="accountManage">
                        <div class="accountManage-header">
                                <div class="accountManage-text">
                                    <h3>Account Management</h3>
                                </div>
                                <div class="default-pass-container">
                                </div>
                                <button class="addAcc-btn" onclick="addRow('bspAccountTable', 'accountInfo', 4)"> ADD </button>
                        </div>

                        <div class="summarySubcontainer">
                            <div class="accounts-summary">
                                <table class="bsp-account-table" id="bspAccountTable">
                                    <thead>
                                        <tr>
                                            <th id="accountNameCol">Account Name</th>
                                            <th id="emailCol">Account Email</th>
                                            <th id="accountRoleCol">Role</th>
                                            <th id="accountDivCol">Division</th>
                                            <th style="display:none">id_division</th>
                                            <th id="editCol" colspan="1" style="text-align: center;">Action</th>
                                            <th id="saveCol"></th>
                                            <th id="resetCol"></th>
                                            <th id="deleteCol"></th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $select_accounts = "SELECT *
                                                                FROM bsp_account;";
                                            $select_accounts_run = $conn->prepare($select_accounts);
                                            $select_accounts_run->execute();
                                            $rows4 = $select_accounts_run->fetchAll(PDO::FETCH_ASSOC);
                                            if(count($rows4) > 0) {
                                                foreach($rows4 as $row4){
                                        ?> 
                                                    <tr class="accounts-row" id="accountRow" contenteditable="false">
                                                        <td><input type="text" value="<?php echo $row4['account_name'] ?>" style="border: none; font-size: 14px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                        <td><input type="email" value="<?php echo $row4['account_email'] ?>" style="border: none; font-size: 14px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                        <td style="display:none"><input type="text" value="<?php echo $row4['account_id'];?>" style="border: none; font-size: 16px; outline: none; width: 1%; background: transparent;" disabled></td>
                                                        <td><input type="role" value="<?php echo $row4['account_role'] ?>" style="border: none; font-size: 14px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                        <td><input type="text" value="<?php echo $row4['account_division'] ?>" style="border: none; font-size: 14px; outline: none; width: 100%; background: transparent;" disabled></td>
                                                        <td id="editRow"><span class="edit-icon tooltip" onclick="editRow(this, 'bspAccountTable')"><span class="tooltiptext">Edit</span></span></td>
                                                        <td id="saveRow"><span class="save-icon tooltip" onclick="saveRow('bspAccountTable', this)"><span class="tooltiptext">Save</span></span></td>
                                                        <td id="resetRow"><span class="reset-icon tooltip" onclick="resetRow('bspAccountTable', this)"><span class="tooltiptext">Reset</span></span></td>
                                                        <td id="deleteRow"><span class="delete-icon tooltip" onclick="deleteRow('bspAccountTable', this)"><span class="tooltiptext">Delete</span></span></td>
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

                    <div class="myProfile">
                        <div class="myProfile-header">
                            <div class="myProfile-text">
                                <h3>My Profile</h3>
                            </div>
                        </div>

                        <div class="profile-setting-container">
                            <div class="photoPlaceholder" style="background-image: url('../Images/Employee_Pic/<?php echo $accountPic;?>');" onclick="openUploadPhotoForm()">
                                <h5><br><br>UPLOAD</h5>
                            </div>

                            <div class="fullNameContainer">
                                <h3><?php echo mb_strtoupper($accountName, 'UTF-8');?></h3>
                            </div>

                            <div class="emailContainer">
                                <div class="emailIcon"></div>
                                <div class="emailAccount" style="font-size: 12px;">
                                    <h4><?php echo $accountEmail;?></h4>
                                </div>
                            </div>

                            <form id="accountFormSettings" class="formSettings">
                                <div class="formAction" style="font-size: 14px; margin-left: 5px;">
                                    <h3>Change Password</h3>
                                </div>

                                <div class="updatePassword">
                                    <label>Current Password</label>
                                    <div class="passwordInputContainer">
                                        <input id="currentPass" type="password" name="inputCurrentPassword" maxlength="16" placeholder="Enter current password" required>
                                        <div id="showCurrentPassword" class="showHidePassword" onclick="showPasswordInput('showCurrentPassword', 'hideCurrentPassword', 'currentPass')"></div>
                                        <div id="hideCurrentPassword" class="showHidePassword" onclick="hidePasswordInput('hideCurrentPassword', 'showCurrentPassword', 'currentPass')"></div>
                                    </div>
                                </div>

                                <div class="updatePassword">
                                    <label>New Password</label>
                                    <div class="passwordInputContainer">
                                        <input id="newPass" type="password" name="inputNewPassword" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{7,16}$" maxlength="16" placeholder="Enter new password" required>
                                        <div id="showNewPassword" class="showHidePassword" onclick="showPasswordInput('showNewPassword', 'hideNewPassword', 'newPass')"></div>
                                        <div id="hideNewPassword" class="showHidePassword" onclick="hidePasswordInput('hideNewPassword', 'showNewPassword', 'newPass')"></div>
                                    </div>
                                </div>
                                
                                <div class="requiredPasswordPattern">
                                    <span>*Password must be at least 7 characters long, contain 1 uppercase letter, 1 lowercase letter, 1 number, and no spaces.</span>
                                </div>

                                <div class="updatePassword">
                                    <label>Confirm New Password</label>
                                    <div class="passwordInputContainer">
                                        <input id="confirmPass" type="password" name="inputConfirmPassword" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{7,16}$" maxlength="16" placeholder="Re-enter new password" required>
                                        <div id="showConfirmPassword" class="showHidePassword" onclick="showPasswordInput('showConfirmPassword', 'hideConfirmPassword', 'confirmPass')"></div>
                                        <div id="hideConfirmPassword" class="showHidePassword" onclick="hidePasswordInput('hideConfirmPassword', 'showConfirmPassword', 'confirmPass')"></div>
                                    </div>
                                </div>
                                
                                <div class="saveButtonContainer">
                                <button type="submit" class="savePasswordUpdate">SAVE</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="uploadPhoto" class="uploadPhotoMainContainer">
                    <div id="photoForm" class="photoFormContainer">
                        <form class="updatePhoto" action="Database/update_photo.php" method="POST" enctype="multipart/form-data">
                            <h2>Upload Photo</h2>   
                            <div class="inputFileContainer">
                                <label class="mainPlaceholderTitle">Image (Accepts only JPG, JPEG, and PNG)</label>
                                
                                <div class="subPlaceholderTitle">
                                    <h4>File name: </h4>
                                    <input id="fileName" type="text" placeholder="Image file name" readonly required>
                                </div>
                                
                                <div class="subPlaceholderTitle">
                                    <h4>File size: </h4>
                                    <input id="fileSize" type="text" placeholder="Image file size" readonly required>
                                </div>

                                <input id="imageFileInput" class="fileInput" type="file" accept=".jpg, .jpeg, .png" name="photo" required>
                                <br>
                                <div class="dummyButtonContainer">
                                    <label for="imageFileInput" class="dummyFileInput">UPLOAD</label>
                                </div>
                            </div>
                            
                            <div class="photoButtonContainer">
                                <button type="button" class="cancelUploadButton" onclick="closeUploadPhotoForm()">CANCEL</button>
                                <button type="submit" class="proceedUploadButton" name="submit">SAVE</button>
                            </div>
                        </form>
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
        <script src="../Admin/Scripts/editAccount.js"></script>
<?php
    }
?>