<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    include('Database/process-configuration.php');

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (isset($_SESSION['accountemail'])) {
        header("Location: ../User/clientIndex.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="index.css">

        <title>BSP-ODRS</title>
        <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
    </head>

    <body>
        <div class="loginBackground">
            <div class="loginForm">
                <form action="Database/process-login.php" method="post">
                    <div class="lineSpace"></div>
                    <div class="bspLogo"></div>
                    
                    <div class="formTitle">
                        <h1>BSP Reservation System</h1>
                    </div>

                    <div class="loginCredentials">
                        <label>Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter email" required >
                    </div>

                    <div class="loginCredentials">
                        <label>Password</label>
                        <div class="passwordContainer">
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                            <div id="showPasswordLogin" class="showHidePassword" onclick="showPassword('showPasswordLogin', 'hidePasswordLogin', 'password')"></div>
                            <div id="hidePasswordLogin" class="showHidePassword" onclick="hidePassword('hidePasswordLogin', 'showPasswordLogin', 'password')"></div>
                        </div>
                    </div>

                    <div id="forgotPasswordContainer">
                        <h4 onclick="openForgotPassword()">Forgot password?</h4>
                    </div>

                    <div class="loginButton">
                        <input type="submit" name="submit" class="formButton" value="Sign In">
                    </div>
                </form>
            </div>

            <div id="errorLogin" class="errorContainer">
                <div class="incorrectCredentials">
                    <h2>Sign in Error</h2>
                    <h4>Incorrect email or password. Please try again.</h4>
                    <button class="confirmationButton" onclick="closeErrorModal('errorLogin')">OK</button>
                </div>
            </div>

            <div id="errorAccount" class="errorContainer">
                <div class="incorrectCredentials">
                    <h2>Account does not exist</h2>
                    <h4>Incorrect email. Please try again.</h4>
                    <button class="confirmationButton" onclick="closeErrorModal('errorAccount')">OK</button>
                </div>
            </div>

             <div id="exceedLoginAttempt" class="errorContainer">
                    <div class="incorrectCredentials">
                        <h2>Login Attempt Exceeded</h2>
                        <h4>Account locked after too many failed login attempts. Please reach out to the admin for assistance.</h4>
                        <button class="confirmationButton" onclick="closeErrorModal('exceedLoginAttempt')">OK</button>
                    </div>
            </div>

            <div id="forgotPassword" class="errorContainer">
                <div id="verificationContainer" class="verifyEmailContainer">
                    <form id="verifyAccount" class="verifyAccountForm">
                        <h2>Verify Account</h2>
                        <h4>Please provide your email address where a verification code will be sent.</h4>
                        <div class="verifyEmailPlaceholder">
                            <label>Email</label>
                            <br>
                            <input id="currentVerifyEmail" name="verifyEmail" type="email" placeholder="Enter email" required>
                        </div>

                        <div class="verifyButtonContainer">
                            <button class="cancelVerifyButton" onclick="cancelForgotPassword('verificationContainer')">CANCEL</button>
                            <button type="submit" class="verifyButton">SUBMIT</button>
                        </div>
                    </form>
                </div> 
                
                <div id="uniqueCode" class="uniqueCodeContainer">
                    <form id="verifyCode" class="verifyAccountForm"> 
                        <h2>Verify Account</h2>
                        <h4>Please check your email and enter the verification code. If you don't see it in your inbox, check your spam or junk folder.</h4>
                        <div class="verifyEmailPlaceholder">
                            <label>Verification Code</label>
                            <br>
                            <input id="verifyCode1" class="codeInput" type="text" maxlength="1" oninput="moveToNextInput(this, 0)" onkeydown="moveToPreviousInput(event, 0)" required>
                            <input id="verifyCode2" class="codeInput" type="text" maxlength="1" oninput="moveToNextInput(this, 1)" onkeydown="moveToPreviousInput(event, 1)" required>
                            <input id="verifyCode3" class="codeInput" type="text" maxlength="1" oninput="moveToNextInput(this, 2)" onkeydown="moveToPreviousInput(event, 2)" required>
                            <input id="verifyCode4" class="codeInput" type="text" maxlength="1" oninput="moveToNextInput(this, 3)" onkeydown="moveToPreviousInput(event, 3)" required>
                        </div>

                        <div class="verifyButtonContainer">
                            <button class="cancelVerifyButton" onclick="cancelForgotPassword('uniqueCode')">CANCEL</button>
                            <button type="submit" class="verifyButton">SUBMIT</button>
                        </div>
                    </form>
                </div>

                <div id="newPassword" class="newPasswordContainer">
                    <form id="createPassword" class="newPasswordForm"> 
                        <h2>Create New Password</h2>
                        <div class="passwordPlaceholder">
                            <label>New Password</label>
                            <br>
                            <div class="passwordInput">
                                <input id="createNewPassword" type="password" maxlength="16" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{7,16}$" placeholder="Enter new password" required>
                                <div id="showNewPasswordIcon" class="showPassword" onclick="showPassword('showNewPasswordIcon', 'hideNewPasswordIcon', 'createNewPassword')"></div>
                                <div id="hideNewPasswordIcon" class="hidePassword" onclick="hidePassword('hideNewPasswordIcon', 'showNewPasswordIcon', 'createNewPassword')"></div>
                            </div>
                        </div>

                        <div class="requiredPasswordPattern">
                            <span>*Password must be at least 7 characters long, contain 1 uppercase letter, 1 lowercase letter, 1 number, and no spaces.</span>
                        </div>

                        <div class="passwordPlaceholder">
                            <label>Confirm Password</label>
                            <br>
                            <div class="passwordInput">
                                <input id="confirmNewPassword" type="email" maxlength="16" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{7,16}$" placeholder="Re-enter new password" required>
                                <div id="showConfirmPasswordIcon" class="showPassword" onclick="showPassword('showConfirmPasswordIcon', 'hideConfirmPasswordIcon', 'confirmNewPassword')"></div>
                                <div id="hideConfirmPasswordIcon" class="hidePassword" onclick="hidePassword('hideConfirmPasswordIcon', 'showConfirmPasswordIcon', 'confirmNewPassword')"></div>
                            </div>
                        </div>

                        <div class="verifyButtonContainer">
                            <button class="cancelVerifyButton" onclick="cancelForgotPassword('newPassword')">CANCEL</button>
                            <button type="submit" class="verifyButton">SUBMIT</button>
                        </div>
                    </form>
                </div>

                <div id="noAccountFound" class="errorContainer">
                    <div class="incorrectCredentials">
                        <h2>Account does not exist</h2>
                        <h4>Email address could not be found. Please try again.</h4>
                        <button class="confirmationButton" onclick="closeErrorModal('noAccountFound')">OK</button>
                    </div>
                </div>

               

                <div id="incorrectCode" class="errorContainer">
                    <div class="incorrectCredentials">
                        <h2>Incorrect code</h2>
                        <h4>The verification code you entered is incorrect. Please check your email and try again.</h4>
                        <button class="confirmationButton" onclick="closeErrorModal('incorrectCode')">OK</button>
                    </div>
                </div>

                <div id="passwordDoesNotMatch" class="errorContainer">
                    <div class="incorrectCredentials">
                        <h2>Invalid password!</h2>
                        <h4>New password and confirmation password do not match. Please check and try again.</h4>
                        <button class="confirmationButton" onclick="closeErrorModal('passwordDoesNotMatch')">OK</button>
                    </div>
                </div>

                <div id="newPasswordSuccessful" class="errorContainer">
                    <div class="incorrectCredentials">
                        <h2>Password changed successfully!</h2>
                        <h4>You may now log in with your new password.</h4>
                        <button class="confirmationButton" onclick="cancelForgotPassword('newPasswordSuccessful')">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="index.js"></script>
    </body>
</html>

