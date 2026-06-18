<?php
    session_start();
    include('../Login/Database/process-configuration.php');

    $accountName = $_SESSION['accountname'];
    $accountEmail = $_SESSION['accountemail'];
    $accountRole = $_SESSION['accountrole'];
    $accountPic = $_SESSION['accountpic'];
    $accountDivision = $_SESSION['accountdivision'];

    if (is_null($accountEmail)){
        header('location: ../Login/index.php');
        exit();
    }else if ($accountRole == "Admin") {
        header('location: ../Admin/index.php');
        exit();
    }
    else{
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="Styles/burgerContainer.css">
        <link rel="stylesheet" href="Styles/clientIndex.css">
        <link rel="stylesheet" href="Styles/calendarContainer.css">
        <link rel="stylesheet" href="Styles/notificationContainer.css">
        <link rel="stylesheet" href="Styles/requestContainer.css">
        <link rel="stylesheet" href="Styles/requestSummaryContainer.css">
        <link rel="stylesheet" href="Styles/settingsContainer.css">
        <link rel="stylesheet" href="Styles/updatePhoto.css">
        <link rel="stylesheet" href="Styles/useOfNationalOfficeForm.css">
        <link rel="stylesheet" href="Styles/vehicleReservationForm.css">

        <title>BSP-ODRS</title>
        <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
    </head>

    <body>
        <div class="clientBackground">
            <section id="header">
                <div class="container">
                    <div class="row">
                        <div class="header-content">
                            <span id="bsp-logo"></span>
                            <h1>BSP Reservation System</h1>
                            <button id="burger-menu-btn" onclick="openBurgerMenu()"></button>
                        </div>
                    </div>
                </div>
            </section>

            <div id="clientCalendarMainContainer" class="clientAndCalendarContainer">
                <div id="clientTableContainer" class="clientContainer" style="display: block;">
                    <div class="informationContainer">
                        <div class="employeePicture">
                            <img src="../Images/Employee_Pic/<?php echo $accountPic; ?>" alt="Employee Picture">
                        </div>

                        <div class="informationSubcontainer">
                            <div class="employeeName">
                                <h2><?php echo mb_strtoupper($accountName, 'UTF-8'); ?></h2>
                            </div>                        

                            <div class="emailAndSearchContainer">
                                <div class="employeeAccount">
                                    <div class="emailIcon"></div>
                                    <div class="emailAccount">
                                        <h3><?php echo $accountEmail; ?></h3>
                                    </div>
                                </div>

                                <div class="searchContainer">
                                    <input type="text" id="searchTableSummary" onkeyup="searchTableContent()" placeholder="Search" name="search">
                                </div>

                                <div class="yearContainer">
                                    <select id="filterYear" name="yearSelect" class="yearDropDown" onchange="filterByYear(this.value)">
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
                    </div>

                    <hr class="tableHorizontalLine">

                    <div class="tableHeaderContainer">
                        <table id="tableHeader" class="requestSummary">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>&nbsp;Document Type</th>
                                    <th>&nbsp;Request Date</th>
                                    <th>Reservation Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="tableContainer">
                        <table id="requestTable" class="requestSummary">
                            
                            <tbody>
                                <?php
                                $select = "SELECT * FROM bsp_reservation_summary WHERE requester_email = :accountEmail ORDER BY request_number DESC";
                                $select_stmt = $conn->prepare($select);
                                $select_stmt->bindParam(':accountEmail', $accountEmail);
                                $select_stmt->execute();

                                $rows = $select_stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($rows) > 0) {
                                    foreach ($rows as $row) {
                                        $jsFunction = ($row['request_type'] == 'Vehicle Reservation') 
                                                    ? 'openVehicleRequestSummary' 
                                                    : 'openUseofNationalOfficeFacilitySummary';
                            ?>
                                        <tr style="cursor: pointer;" onclick="<?php echo $jsFunction; ?>('<?php echo $row['request_id']; ?>')">
                                            <td class="firstColumn"><?php echo $row['request_id']; ?></td>
                                            <td><?php echo $row['request_type']; ?></td>
                                            <td>
                                                <?php 
                                                    $requestDate = new DateTime($row['request_date']); 
                                                    echo $requestDate->format('m-d-20y'); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $reservationDate = new DateTime($row['reservation_date']);
                                                    echo $reservationDate->format('m-d-20y'); 
                                                ?>
                                            </td>
                                            <td class="fifthColumn">&nbsp;<?php echo $row['request_status']; ?></td>
                                        </tr>
                            <?php
                                    }
                                } else {
                                ?>
                                    <div class="noRecordsFound">
                                        <h4>No records found</h4>
                                    </div>
                                <?php
                                    }
                                ?>
                            </tbody>    
                        </table>
                    </div>
                    
                    <div class="newRequestContainer">
                        <button type="button" class="requestButton" onclick="openChooseForms()">New Request</button>
                    </div>
                </div>

                <div id="reservedDatesContainer" class="calendarContainer">
                    <div class="calendarSubcontainer">
                        <div class="calendarHeader">
                            <button id="previousMonth">&lt;</button>
                            <h2 id="currentMonthYear"></h2>
                            <button id="nextMonth">&gt;</button>
                        </div>

                        <div class="calendar">
                            <div class="calendarWeekdays"></div> 
                            <div class="calendarDays"></div>
                        </div>
                    </div>

                    <div class="legendContainer">
                        <div class="legendVehicleReservation">
                            <div class="colorVehicleReservation"></div>
                            <label>Vehicle Reservation</label>
                        </div>

                        <div class="legendNationalOffice">
                            <div class="colorNationalOffice"></div>
                            <label>Office Facility</label>
                        </div>
                    </div>
                    <hr>
                    <div class="upcomingReservationContainer">
                        <?php
                            $dateNow = date("Y-m-d");
                            $selectApproved = "SELECT * FROM bsp_reservation_summary WHERE requester_email = :accountEmail AND request_status = 'Approved' AND reservation_date >= :dateNow ORDER BY reservation_date ASC";
                            $selectApproved_stmt = $conn->prepare($selectApproved);
                            $selectApproved_stmt->bindParam('accountEmail', $accountEmail);
                            $selectApproved_stmt->bindParam(':dateNow', $dateNow);
                            $selectApproved_stmt->execute();
                            $rows2 = $selectApproved_stmt->fetchAll(PDO::FETCH_ASSOC);

                            if(count($rows2) > 0){
                                foreach($rows2 as $row2){
                                    $approvedId = $row2['request_id'];

                                    if($row2['request_type']=='Vehicle Reservation'){
                                        $selectUpcomingReservation = "SELECT * FROM vehicle_reservation WHERE focal_person_email = :accountEmail AND request_id = :approvedId";
                                        $selectUpcomingReservation_stmt = $conn->prepare($selectUpcomingReservation);
                                        $selectUpcomingReservation_stmt->bindParam(':accountEmail', $accountEmail);
                                        $selectUpcomingReservation_stmt->bindParam(':approvedId', $approvedId);
                                        $selectUpcomingReservation_stmt->execute();
                                        $rows3 = $selectUpcomingReservation_stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if(count($rows3)> 0){
                                            foreach($rows3 as $row3){                                           
                        ?>
                                                <div class="reservationDetails" onclick="openVehicleRequestSummary('<?php echo $row3['request_id']; ?>')">
                                                    <div class="reservationDate">
                                                        <span class="reservedMonth">
                                                            <?php
                                                                $month = date("M", strtotime($row3['reservation_date']));
                                                                echo strtoupper($month);
                                                            ?>
                                                        </span>
                                                        <br>
                                                        <span class="reservedDate">
                                                            <?php
                                                                $day = date("d", strtotime($row3['reservation_date']));
                                                                echo strtoupper($day);
                                                            ?>
                                                        </span>
                                                    </div>

                                                    <div class="reservationOtherInfo">
                                                        <div class="reservationType"><span >VEHICLE RESERVATION</span></div>
                                                        <div class="destinationFacility">
                                                            <span ><strong>
                                                                <?php
                                                                    $approvedDestination = $row3['destination'];
                                                                    $stringLength = strlen($approvedDestination);
                                                                    $multipleDestination = strpos("$approvedDestination", ";");

                                                                    if ($stringLength > 29){
                                                                        $substringDestination = substr($approvedDestination, 0, 30);
                                                                        $spacePosition = strrpos($substringDestination, " ");
                                                                        
                                                                        $approvedDestination = substr($substringDestination, 0, $spacePosition);
                                                                        echo $approvedDestination . "...";
                                                                    } else {
                                                                        echo $approvedDestination;
                                                                    }
                                                                ?>
                                                            </strong></span>
                                                        </div>
                                                        <div class="reservedTime">
                                                            <div class="reservedTimeIcon"></div>
                                                            <span>
                                                                <?php
                                                                    $time = date("h:i A", strtotime($row3['etd_from_station']));
                                                                    echo strtoupper($time);
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                        <?php
                                            }
                                        }
                                    } else {
                                        $selectUpcomingReservation = "SELECT * FROM facility_reservation WHERE focal_person_email = :accountEmail AND request_id = :approvedId";
                                        $selectUpcomingReservation_stmt = $conn->prepare($selectUpcomingReservation);
                                        $selectUpcomingReservation_stmt->bindParam(':accountEmail', $accountEmail);
                                        $selectUpcomingReservation_stmt->bindParam('approvedId', $approvedId);
                                        $selectUpcomingReservation_stmt->execute();
                                        $rows3 = $selectUpcomingReservation_stmt->fetchAll(PDO::FETCH_ASSOC);
                                        if(count($rows3) > 0){
                                            foreach($rows3 as $row3){ 
                        ?>
                                                <div class="reservationDetails" onclick="openUseofNationalOfficeFacilitySummary('<?php echo $row3['request_id']; ?>')">
                                                    <div class="reservationDate">
                                                        <span class="reservedMonth">
                                                            <?php
                                                                $month = date("M", strtotime($row3['reservation_date']));
                                                                echo strtoupper($month);
                                                            ?>
                                                        </span>
                                                        <br>
                                                        <span class="reservedDate">
                                                            <?php
                                                                $day = date("d", strtotime($row3['reservation_date']));
                                                                echo strtoupper($day);
                                                            ?>
                                                        </span>
                                                    </div>

                                                    <div class="reservationOtherInfo">
                                                        <div class="reservationType"><span >NATIONAL OFFICE FACILITY</span></div>
                                                        <div class="destinationFacility">
                                                            <span ><strong>
                                                                <?php
                                                                    $approvedFacility = $row3['reserved_facility'];
                                                                    $stringLength2 = strlen($approvedFacility);

                                                                    if ($stringLength2 > 34){
                                                                        $substringFacility = substr($approvedFacility, 0, 35);
                                                                        $spacePosition2 = strrpos($substringFacility, " ");
                                                                        
                                                                        $approvedFacility = substr($substringFacility, 0, $spacePosition2);
                                                                        echo $approvedFacility . "...";
                                                                    } else {
                                                                        echo $approvedFacility;
                                                                    }
                                                                ?>
                                                            </strong></span>
                                                        </div>
                                                        <div class="reservedTime">
                                                            <div class="reservedTimeIcon"></div>
                                                            <span>
                                                                <?php
                                                                     echo " " . date("g:i A", strtotime($row3['reservation_time_start']))   . " - " . date("g:i A", strtotime($row3 ['reservation_time_end']));
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                        <?php
                                            }
                                        } 
                                    }   
                                }
                            }   else {
                        ?>
                                    <div class="noApprovedReservation">
                                        <h4>No approved reservations found</h4>
                                    </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>    
            </div>

            <div id="settingsMainContainer" class="accountSettingsContainer">
                <div class="formSettingsContainer">
                    <div class="formTitle">
                        <h2>ACCOUNT SETTINGS</h2>
                    </div>

                    <div class="photoPlaceholder" style="background-image: url('../Images/Employee_Pic/<?php echo $accountPic;?>');" onclick="openUploadPhotoForm()">
                        <h5><br><br>UPLOAD</h5>
                    </div>

                    <div class="fullNameContainer">
                        <h3><?php echo mb_strtoupper($accountName, 'UTF-8');?></h3>
                    </div>

                    <div class="emailContainer">
                        <div class="emailIcon"></div>

                        <div class="emailAccount">
                            <h4><?php echo $accountEmail;?></h4>
                        </div>
                    </div>

                    <form id="accountFormSettings" class="formSettings">
                        <div class="formAction">
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
        
        <div id="requestModal" class="requestContainer">
            <div id="chooseForm" class="popupChooseForm">
                <div class="formHeader">
                    <div class="formTitle">
                        <h3><strong>TYPE OF REQUEST</strong></h3>
                    </div>
                    <button type="button" class="closeFormButton" onclick="closeChooseForms()"></button>
                </div>
                
                <div class="chooseFormButtonContainer">
                    <?php
                        if($accountRole == 'GuestUser'){
                    ?>
                            <button type="button" class="formOption" onclick="openUseOfNationalOfficeForm()">Use of National Office Facility</button>
                    <?php
                        } else{
                    ?>
                            <button type="button" class="formOption" onclick="openVehicleReservationForm()">Vehicle Reservation Form (VRF)</button>
                            <br>
                            <button type="button" class="formOption" onclick="openUseOfNationalOfficeForm()">Use of National Office Facility</button>
                    <?php    
                        }
                    ?>
                </div>
            </div>

            <div id="vehicleReservationForm" class="popupVehicleReservation">
                <form action="Database/process-vehicleReservation.php" method="post">
                    <div class="formTitleContainer">
                        <h2>Vehicle Reservation Form (VRF)</h2>
                    </div>
                    <div class="formDetails">
                        <div class="dateAndTime">
                            <span id="date"></span>
                            <div class="dateIcon">date</div>
                        </div>

                        <div class="dateAndTime">
                            <span id="time"></span>
                            <div class="timeIcon">time</div>
                        </div>
                        
                        <div class="flexDivisionDateContainer">
                            <div class="divisionContainer">
                                <label>Division:</label>
                                <br>
                                <select id="division" name="divisionSelect" class="divisionDropDown" style="pointer-events: none;" required>
                                    <option value="" selected>Choose division...</option>
                                    <?php
                                        $chooseDivision = "SELECT TOP 1 * FROM bsp_account WHERE account_email=:email";
                                        $chooseDivision_stmt = $conn->prepare($chooseDivision);
                                        $chooseDivision_stmt->bindParam(':email', $_SESSION['accountemail']);
                                        $chooseDivision_stmt->execute();

                                        $rows = $chooseDivision_stmt->fetch(PDO::FETCH_ASSOC);

                                        if($rows){
                                    ?>
                                            <option value="<?php echo $rows['account_division']?>" selected ><?php echo $rows['account_division']?></option>
                                    <?php
                                        } else {
                                    ?> 
                                                <option value="">No division found</option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="dateContainer">
                                <label>Date of Use:</label>
                                <br>
                                <input type="date" name="reservedDate" class="dateOfUse" required>
                            </div>
                        </div>   
                        <div class="etdMainContainer">
                            <div class="etdSubContainer">
                                <div class="inputETD">
                                    <label>ETD (from Station):</label>
                                    <br>
                                    <input type="time" id="etdFrStation" name="etdFromStation" class="fromStation" required>
                                </div>
                                <div class="inputETD">
                                    <label>ETD (from Destination):</label>
                                    <br>
                                    <input type="time" id="etdFrDestination" name="etdFromDestination" class="fromDestination" required>
                                </div>
                            </div>

                            <div class="etdSubContainer">
                                <div class="inputETD">
                                    <label>ETA (to Destination):</label>
                                    <br>
                                    <input type="time" id="etaToDestination" name="etaToDestination" class="toDestination" required>
                                </div>

                                <div class="inputETD">
                                    <label>ETA (to Station):</label>
                                    <br>
                                    <input type="time" id="etaToStation" name="etaToStation" class="toStation" required>
                                </div>
                            </div>
                        </div>

                        <div class="destinationContainer">
                            <label>Destination/s:</label>
                            <div id="destinationNameContainer">
                                <input type="text" name="destination[]" placeholder="Enter destination" required>
                                <button type="button" id="addDestinationButton" class="destinationButton" onclick="addDestination()">+</button>
                            </div>
                        </div>

                        <div class="purposeContainer">
                            <label>Purpose of Trip:</label>
                            <br>
                            <input type="text" name="tripPurpose" class="purposeOfTrip" placeholder="Enter purpose" required>
                        </div>
                        
                        <div class="passengerNumberContainer">
                            <label>Number of Passenger/s:</label>
                            <br>
                            <input id="passengerCount" type="number" name="passengerNumber" min="1" max="19" value="1" oninput="adjustPassengerPlaceholder()" placeholder="Enter number of passengers" required>
                        </div>

                        <div class="passengerContainer">
                            <label>Passenger/s:</label>
                            <div id="passengerNameContainer">
                                <input type="text" name="passengers[]" placeholder="Enter name" required>
                                <button type="button" id="addNameButton" class="addPassengerButton" onclick="addPassengerName()">+</button>
                            </div>
                        </div>

                        <div class="flexDivisionDateContainer">
                            <div class="requesterContainer">
                                <label>Requested by:</label>
                                <br>
                                <input type="text" name="requesterName" class="requestedBy" placeholder="Enter Name" required>
                            </div>

                            <div class="divisionChiefContainer">
                                <label>Division Chief:</label>
                                <br>
                                <?php 
                                    $divisionChief = "SELECT TOP 1 * FROM bsp_division WHERE division_name=:divisionName";
                                    $divisionChiefStmt = $conn->prepare($divisionChief);
                                    $divisionChiefStmt->bindParam(':divisionName', $accountDivision);
                                    $divisionChiefStmt->execute();
                                    $divisionRow = $divisionChiefStmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    if($divisionRow){
                                ?>
                                    <input type="text" id="chief" name="divisionChief" class="divisionChief" value="<?php echo $divisionRow['division_chief']; ?>" readonly>
                                <?php
                                    }else{
                                ?>
                                        <input type="text" id="chief" name="divisionChief" class="divisionChief" value="No Chief Division Available" readonly>
                                <?php        
                                    }
                                ?>
                            </div>
                        </div>
                        
                        <div class="buttonFormContainer">
                            <button type="button" class="cancelForm" onclick="closeVehicleReservationForm()">Cancel</button>
                            <button type="submit" class="submitForm">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="officeRequestMainContainer" class="officeRequestContainer">
                <div id="useOfNationalOfficeForm" class="popupUseOfNationalOffice">
                    <form action="Database/process-nationalOfficeFacility.php" method="post">
                        <div class="formTitleContainer">
                            <h2>Use of National Office Facility</h2>
                        </div>

                        <div class="formDetails">
                            <div class="dateAndTime">
                                <span id="date2"></span>
                                <div class="dateIcon">date</div>
                            </div>

                            <div class="dateAndTime">
                                <span id="time2"></span>
                                <div class="timeIcon">time</div>
                            </div>

                            <div class="facilityMainContainer">
                                <div class="facilitySubContainer">
                                    <div class="facilityOption">
                                        <input type="radio" id="executiveBoard" name="chosenFacility" value="National Executive Board Room">
                                        <div class="checkbox" onclick="selectRadio('executiveBoard')"></div>
                                        <label for="executiveBoard">National Executive Board Room</label>
                                    </div> 

                                    <div class="facilityOption">
                                        <input type="radio" id="educationalHub" name="chosenFacility" value="Educational Hub (Library and Museum)">
                                        <div class="checkbox" onclick="selectRadio('educationalHub')"></div>
                                        <label for="educationalHub">Educational Hub (Library and Museum)</label>
                                    </div> 

                                    <div class="facilityOption">
                                        <input type="radio" id="conferenceHall" name="chosenFacility" value="Conference Hall 6th Floor">
                                        <div class="checkbox" onclick="selectRadio('conferenceHall')"></div>
                                        <label for="conferenceHall">Conference Hall 6th Floor</label>
                                    </div> 
                                </div>

                                <div class="facilitySubContainer">
                                    <div class="facilityOption">
                                        <input type="radio" id="veranda" name="chosenFacility" value="4th Floor Veranda">
                                        <div class="checkbox" onclick="selectRadio('veranda')"></div>
                                        <label for="veranda">4th Floor Veranda</label>
                                    </div>

                                    <div class="facilityOption">
                                        <input type="radio" id="groundParkingArea" name="chosenFacility" value="Ground/Parking Area">
                                        <div class="checkbox" onclick="selectRadio('groundParkingArea')"></div>
                                        <label for="groundParkingArea">Ground/Parking Area</label>
                                    </div>

                                    <div class="facilityOption">
                                        <input type="radio" id="others" name="chosenFacility" value="Others">
                                        <div class="checkbox" onclick="selectRadio('others')"></div>
                                        <label for="others">Others (specify)</label>
                                    </div>

                                    <input type="text" id="chosenOtherFacility" name="otherFacility" class="specifyOthers" placeholder="Only if applicable" readonly>
                                </div>
                            </div>
                            <br>
                            <div class="dateTimeOfUseContainer">
                                <div class="dateInput">
                                    <label>Date of Use:</label>
                                    <br>
                                    <input type="date" name="reservedDate" class="dateOfUse" required>
                                </div>

                                <div class="timeInputStart">
                                    <label>Time of Use:</label>
                                    <div class="timeContainer">
                                        <div class="startTimeContainer">
                                            <label style="margin-right: 5%; margin-top: 2%">START: </label>
                                            <input type="Time" name="startTime" required>
                                        </div>
                                        <div style="display: flex;">
                                            <label style="margin-right: 5%; margin-top: 2%">END: </label>
                                            <input type="Time" name="endTime" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="purposeInformationContainer">
                                <label>Purpose:</label>
                                <br>
                                <input id="facilityPurpose" type="text" name="purpose" class="purposeInput" placeholder="Enter purpose" required>
                            </div>
                            
                            <div class="officeFlexContainer">
                                <div class="otherInformationContainer">
                                    <label>No. of Persons:</label>
                                    <br>
                                    <input id="numOfPerson" type="number" name="numberOfPersons" class="numberOfPersonsInput" min="1" max="200" placeholder="Enter number of persons" required>
                                </div>

                                <div class="otherInformationContainer">
                                    <label>Division:</label>
                                    <br>
                                    <select id="division_2" name="divisionSelect" class="divisionDropDown" onchange="updateDivisionChiefPlaceholder_2()" style="pointer-events: none;" required>
                                        <option value="<?php echo $accountDivision ?>" selected><?php echo $accountDivision ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="officeFlexContainer">
                                <div class="otherInformationContainer">
                                    <label>Requested by:</label>
                                    <input id="facilityRequester" type="text" name="requesterName" class="requesterInput" placeholder="Enter Name" required>
                                </div>

                                <div class="otherInformationContainer"> 
                                    <label>Division Chief:</label>
                                    <?php 
                                        $divisionChief1 = "SELECT TOP 1 * FROM bsp_division WHERE division_name=:divisionName";
                                        $divisionChief1Stmt = $conn->prepare($divisionChief1);
                                        $divisionChief1Stmt->bindParam(':divisionName', $accountDivision);
                                        $divisionChief1Stmt->execute();
                                        $divisionRow1 = $divisionChief1Stmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        if($divisionRow1){
                                    ?>
                                        <input id="chief_2" type="text" name="divisionChief" class="divisionChiefInput" value="<?php echo $divisionRow1['division_chief'] ?>" readonly>
                                    <?php  
                                        } else {
                                    ?>
                                        <input id="chief_2" type="text" name="divisionChief" class="divisionChiefInput" value="No Division Chief Found" readonly>
                                    <?php        
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="buttonFormContainer">
                                <button type="button" class="cancelForm" onclick="closeUseOfNationalOfficeForm()">Cancel</button>
                                <button type="submit" class="submitForm">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="reservedOfficeSummary">
                    <div class="formTitleContainer">
                        <h2>Reserved Facilities</h2>
                    </div>
                    
                    <div class="reservedFacilityDetails">
                        <?php
                            $dateNowResFacility = date("Y-m-d");

                            $sql = "
                                SELECT * 
                                FROM facility_reservation 
                                WHERE [status] = 'Approved' 
                                AND CAST(reservation_date AS DATE) >= :dateNow
                                ORDER BY reservation_date ASC
                            ";

                            $stmt = $conn->prepare($sql);
                            $stmt->bindValue(':dateNow', $dateNowResFacility);
                            $stmt->execute();

                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if($rows){
                               foreach($rows as $row5){
                                    $reservedFacilityId = $row5['request_id'];
                        ?>
                            <div class="detailContainer">
                                <div class="dateContainer">
                                    <span class="reservedMonth">
                                        <?php
                                            $month = date("M", strtotime($row5['reservation_date']));
                                            echo strtoupper($month);
                                        ?>
                                    </span>
                                    <br>
                                    <span class="reservedDate">
                                        <?php
                                            $day = date("d", strtotime($row5['reservation_date']));
                                            echo strtoupper($day);
                                        ?>
                                    </span>
                                </div>
                                <div class="otherDetailsContainer">
                                    <div class="clientDivision">
                                        <span>
                                            <?php
                                               echo  strtoupper($row5['requester_division']);
                                            ?>
                                        </span>
                                    </div>
                                    <div class="reservedFacility">
                                        <span ><strong>
                                            <?php
                                                $reservedFacility = $row5['reserved_facility'];
                                                $stringLength3 = strlen($reservedFacility);

                                                if ($stringLength3 > 25){
                                                    $substringFacility = substr($reservedFacility, 0, 26);
                                                    $spacePosition3 = strrpos($substringFacility, " ");
                                                    
                                                    $reservedFacility = substr($substringFacility, 0, $spacePosition2);
                                                    echo $reservedFacility . "...";
                                                } else {
                                                    echo $reservedFacility;
                                                }
                                            ?>                      
                                        </strong></span>
                                    </div>
                                    <div class="reservedPeriod">
                                        <div class="timeIcon"></div>
                                            <span>
                                                <?php
                                                    echo  " " . date("g:i A", strtotime($row5['reservation_time_start']))   . " - " . date("g:i A", strtotime($row5['reservation_time_end']));
                                                ?>               
                                            </span>
                                    </div>
                                </div>
                            </div>
                        <?php
                                }
                            } else {
                        ?>
                                <div class="noUpcomingReservation">
                                    <h4>No upcoming reservations found</h4>
                                </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="summaryContainer" class="requestSummaryContainer">
            <div id="vehicleReservation" class="vehicleReservationSummary">
                <div class="summaryHeader">
                    <div class="summaryTitle">
                        <h2>Vehicle Reservation Form (VRF)</h2>
                    </div>
                    <button type="button" class="closeSummaryButton" onclick="closeVehicleRequestSummary()"></button>
                </div>
                
                <div class="summaryMainContainer">
                    <div class="summarySubContainer">
                        <p><strong>STATUS: </strong><span id="summaryStatus"></span></p>
                        <p><strong>Division: </strong><span id="summaryDivision"></span></p>
                        <p><strong>Division Chief: </strong><span id="summaryChief"></span></p>
                        <p><strong>ETD (from Station): </strong><span id="summaryETDfrStation"></span></p>
                        <p><strong>ETD (from Destination): </strong><span id="summaryETDfrDestination"></span></p>
                        <p><strong>Requester: </strong><span id="summaryRequester"></span></p>
                        <p><strong>Passenger/s: </strong><span id="summaryPassenger"></span></p>
                    </div>

                    <div class="summarySubContainer">
                        <p><strong>REQUEST ID: </strong><span id="summaryRequestID"></span></p>
                        <p><strong>Date Filed: </strong><span id="summaryRequestDate"></span></p>
                        <p><strong>Date of Use: </strong><span id="summaryReservedDate"></span></p>
                        <p><strong>ETA (to Destination): </strong><span id="summaryETAtoDestination"></span></p>
                        <p><strong>ETA (to Station): </strong><span id="summaryETAtoStation"></span></p>
                        <p><strong>Destination/s: </strong><span id="summaryDestination"></span></p>   
                        <p><strong>Purpose: </strong><span id="summaryPurpose"></span></p>
                    </div>
                </div>
                <div style="display: flex; justify-content: center;">
                    <button type="button" class="printRequestForm" onclick="pdfVehicleReservation()">Download PDF</button>
                    <button type="button" id="cancelBtnVehicle" class="cancelRequestForm" onclick="cancelReservation()">Cancel Request</button>
                </div>
            </div>

            <div id="useOfNationalOffice" class="useOfNationalOfficeSummary">
                <div class="summaryHeader">
                    <div class="summaryTitle">
                        <h2>Use of National Office Facility</h2>
                    </div>
                    <button type="button" class="closeSummaryButton" onclick="closeUseofNationalOfficeFacilitySummary()"></button>
                </div>
                
                <div class="summaryMainContainer">
                    <div class="summarySubContainer">
                        <p><strong>STATUS: </strong><span id="summaryStatus2"></span></p>
                        <p><strong>Group/Division: </strong><span id="summaryDivision2"></span></p>
                        <p><strong>Division Chief: </strong><span id="summaryChief2"></span></p>
                        <p><strong>Requesting Officer: </strong><span id="summaryRequester2"></span></p>
                        <p><strong>Facility: </strong><span id="summaryReservedFacility"></span></p>
                        <p><strong>Number of Persons: </strong><span id="summaryNumOfPerson"></span></p>
                        <p><strong>Purpose: </strong><span id="summaryPurpose2"></span></p>
                    </div>

                    <div class="summarySubContainer">
                        <p><strong>REQUEST ID: </strong><span id="summaryRequestID2"></span></p>
                        <p><strong>Date Filed: </strong><span id="summaryRequestDate2"></span></p>
                        <p><strong>Date of Use: </strong><span id="summaryReservedDate2"></span></p>
                        <p><strong>Start of Use: </strong><span id="summaryReservedStartTime"></span></p>
                        <p><strong>End of Use: </strong><span id="summaryReservedEndTime"></span></p>
                    </div>
                </div>

                <div style="display: flex; justify-content: center;">
                    <button type="button" class="printRequestForm" onclick="pdfFacilityReservation()">Download PDF</button>
                    <button type="button" id="cancelBtnFacility" class="cancelRequestForm" onclick="cancelReservation()">Cancel Request</button>
                </div>
            </div>
        </div>
        
        <div id="uploadPhoto" class="uploadPhotoMainContainer">
            <div id="photoForm" class="photoFormContainer">
                <form class="updatePhoto" action="Database/process-updatePhoto.php" method="POST" enctype="multipart/form-data">
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
            <div id="successfulRequest" class="successfulNotification">
                <h2>Request successfully submitted!</h2>
                <h4>You will be notified once it is processed.</h4>
                <button class="confirmationButton" onclick="closeSuccessfulNotification()">OK</button>
            </div>

            <div id="unsuccessfulRequest" class="unsuccessfulNotification">
                <h2>Oops! Something went wrong!</h2>
                <h4>We could not submit your request. Please try again later.</h4>
                <button class="confirmationButton" onclick="closeUnsuccessfulNotification()">OK</button>
            </div>

            <div id="successfulCancellation" class="successfulNotification">
                <h2>Request for cancellation submitted!</h2>
                <h4>Please wait for admin to approve your request.</h4>
                <button class="confirmationButton" onclick="closeSuccessfulNotification()">OK</button>
            </div>

            <div id="usuccessfulCancellation" class="unsuccessfulNotification">
                <h2>Failed to process cancellation!</h2>
                <h4>The system encountered an error while processing your request.</h4>
                <button class="confirmationButton" onclick="closeUnsuccessfulNotification()">OK</button>
            </div>
            
            <div id="unavailableFacility" class="unavailableNotification">
                <h2>Facility not Available!</h2>
                <h4>Sorry, but the facility you requested is not available on the selected date.</h4>
                <button class="confirmationButton" onclick="closeUnavailableNotification()">OK</button>
            </div>
             
            <div id="changePassword" class="updateNotification">
                <h2>Confirm change password</h2>
                <h4>Are you sure you want to change your password?</h4>
                <div class="notification-button-container">
                    <button class="cancelButton" onclick="cancelUpdatePassword()">CANCEL</button>
                    <button class="okButton" onclick="proceedUpdatePassword()">OK</button>
                </div>
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

            <div id="logOutConfirmation" class="logOutNotification">
                <h2>Confirm Logout</h2>
                <h4>Are you sure you want to log out?</h4>
                <div class="notification-button-container">
                    <button class="cancelButton" onclick="closeLogoutConfirmation()">CANCEL</button>
                    <button class="okButton" onclick="window.location.href='Database/process-logout.php';">OK</button>
                </div>
            </div>
        </div>

        <div id="burgerMenuModal" class="burgerContainer">
            <div id="burgerMainContainer" class="burgerMenu">
                <div class="burgerHeader">
                    <div class="burgerSubcontainer">
                        <div class="closeBurgerContainer">
                            <button type="button" class="closeBurgerButton" onclick="closeBurgerMenu()"></button>
                        </div>

                        <div class="burgerPicNameContainer">
                             <div class="burgerEmployeePicture">
                                <img src="../Images/Employee_Pic/<?php echo $accountPic;?>" alt="Employee Picture">
                            </div>

                            <div class="burgerGreetings">
                                <span class="greet">Hi,</span>
                                <span class="firstName"><strong>
                                    <?php
                                        $firstBlank = strpos( $accountName, " ");
                                        echo strtoupper(substr($accountName,0,$firstBlank)) . "!";
                                    ?>
                                </strong></span>
                            </div>    
                        </div>
                    </div>
                </div>

                <div class="burgerLinks">
                    <div id="burgerHomeMain" class="linkContainer" onclick="burgerMainHome()">
                        <div class="iconContainer" style="background-image: url('../Images/dashboard-Green.png');"></div>
                        <div class="linkName"><h4>HOME</h4></div>
                    </div>

                    <div id="burgerHome" class="linkContainer" onclick="burgerHome()">
                        <div class="iconContainer" style="background-image: url('../Images/dashboard-Green.png');"></div>
                        <div class="linkName"><h4>HOME</h4></div>
                    </div>

                    <div id="burgerCalendar" class="linkContainer" onclick="burgerCalendar()">
                        <div class="iconContainer" style="background-image: url('../Images/calendar-Green.png');"></div>
                        <div class="linkName"><h4>CALENDAR</h4></div>
                    </div>
                    
                    <div id="burgerAccount" class="linkContainer" onclick="burgerSettings()">
                        <div class="iconContainer" style="background-image: url('../Images/settings-Green.png');"></div>
                        <div class="linkName"><h4>ACCOUNT SETTINGS</h4></div>
                    </div>

                    <div class="linkContainer" onclick="burgerLogout()">
                        <div class="iconContainer" style="background-image: url('../Images/logout-Green.png');"></div>
                        <div class="linkName"><h4>LOG OUT</h4></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="Scripts/accountSettings.js"></script>  
        <script src="Scripts/burgerContainer.js"></script>  
        <script src="Scripts/calendarContainer.js"></script>    
        <script src="Scripts/chooseRequestForm.js"></script>
        <script src="Scripts/clientIndex.js"></script>
        <script src="Scripts/notificationResponse.js"></script>
        <script src="Scripts/requestSummary.js"></script>
        <script src="Scripts/searchTable.js"></script>
        <script src="Scripts/updatePhoto.js"></script>
    </body>
</html>

<?php
    }
?>