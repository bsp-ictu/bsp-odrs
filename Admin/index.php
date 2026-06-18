<?php
session_start();
include('../Login/Database/process-configuration.php');

$accountName = $_SESSION['accountname'];
$accountEmail = $_SESSION['accountemail'];
$accountRole = $_SESSION['accountrole'];
$accountPic = $_SESSION['accountpic'];

if (is_null($accountEmail)) {
    header('location: ../Login/index.php');
    exit();
} else if ($accountRole == "User") {
    header('location: ../User/clientIndex.php');
    exit();
} else {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="Styles/index.css">
        <link rel="stylesheet" type="text/css" href="Styles/calendar_index.css">
        <link rel="icon" type="image/x-icon" href="../Images/BSP_Logo.png">
        <title>Admin-ODRS</title>
    </head>

    <body>
        <div class="background-blur">
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
                    ?>      <button class="btn" onclick="window.location='editAccount.php'">Accounts Settings</button>
                            <button class="btn" onclick="window.location='auditTrailFrame.php'">Audit Trail</button>
                    <?php
                        }
                    ?>
                    
                </div>
            </div>
            <div class="admin-container">
                <div class="flex-container">
                    <div class="admin-profile">
                        <div class="admin-text-container">
                            <p class="admin-index-text">Admin Profile</p>
                        </div>
                        <div class="admin-profile-content">
                            <div class="employeePicture">
                                <img src="../Images/Employee_Pic/<?php echo $accountPic; ?>" alt="Employee Picture">
                            </div>
                            <div class="employeeInformation">
                                <div class="employeeName">
                                    <h2 class="employee-name"><?php echo mb_strtoupper($accountName, 'UTF-8'); ?></h2>
                                </div>
                                <div class="employeeAccount">
                                    <div class="emailIcon">
                                    </div>
                                    <div class="emailAccount">
                                        <h3><?php echo $accountEmail; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="recent-request">
                        <div class="admin-text-container">
                            <p class="admin-index-text">Recent Requests</p>
                        </div>
                        <div class="tableContainer">
                            <table class="requestSummary">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Requesting Division</th>
                                        <th>Request Date</th>
                                        <th>Reservation Date</th>
                                    </tr>
                                </thead>
                                <tbody id="requestSummaryID">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="cancellation-list">
                        <div class="admin-text-container">
                            <p class="admin-index-text">For Cancellation</p>
                        </div>
                        <div class="tableContainer">
                            <table class="requestSummary">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Requesting Division</th>
                                        <th>Request Date</th>
                                        <th>Reservation Date</th>
                                    </tr>
                                </thead>
                                <tbody id="forCancellationSummaryID">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="calendarContainer">
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
                    <hr style="width:90%; text-align:center; border:1px solid #d9d9d9">
                    <div class="upcomingReservationContainer">
                        <?php
                        $dateNow = date("Y-m-d");
                        $selectApproved = "SELECT * FROM (
                                                SELECT 
                                                    request_id, 
                                                    requester_division, 
                                                    request_date, 
                                                    reservation_date, 
                                                    status
                                                FROM vehicle_reservation
                                                WHERE status = 'Approved'

                                                UNION ALL

                                                SELECT 
                                                    request_id, 
                                                    requester_division, 
                                                    request_date, 
                                                    reservation_date, 
                                                    status
                                                FROM facility_reservation
                                                WHERE status = 'Approved'
                                            ) AS combined_reservations
                                            WHERE reservation_date >= CAST(GETDATE() AS DATE)
                                            ORDER BY reservation_date ASC;";

                        $selectApproved_stmt = $conn->prepare($selectApproved);
                        $selectApproved_stmt->execute();

                        $approvedRows = $selectApproved_stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($approvedRows) > 0) {
                            foreach ($approvedRows as $row2) {
                                $approvedId = $row2['request_id'];

                                if (strpos($approvedId, "VRF") !== false) {
                                    $selectUpcomingReservation = "SELECT * FROM vehicle_reservation WHERE request_id = :approvedId";
                                    $selectUpcomingReservation_stmt = $conn->prepare($selectUpcomingReservation);
                                    $selectUpcomingReservation_stmt->bindParam(':approvedId', $approvedId);
                                    $selectUpcomingReservation_stmt->execute();

                                    $row3_results = $selectUpcomingReservation_stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($row3_results) > 0) {
                                        foreach ($row3_results as $row3) {
                        ?>
                                            <div class="reservationDetails">
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
                                                    <div class="reservationType"><span>VEHICLE RESERVATION</span></div>
                                                    <div class="destinationFacility">
                                                        <span><strong>
                                                                <?php
                                                                $approvedDestination = $row3['destination'];
                                                                $stringLength = strlen($approvedDestination);
                                                                $multipleDestination = strpos("$approvedDestination", ";");

                                                                if ($stringLength > 24) {
                                                                    $substringDestination = substr($approvedDestination, 0, 25);
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
                                } else if (strpos($approvedId, "NOF") !== false) {
                                    $selectUpcomingReservation = "SELECT * FROM facility_reservation WHERE request_id = :approvedId";
                                    $selectUpcomingReservation_stmt = $conn->prepare($selectUpcomingReservation);
                                    $selectUpcomingReservation_stmt->bindParam(':approvedId', $approvedId);
                                    $selectUpcomingReservation_stmt->execute();

                                    $rows_upcomingReservation = $selectUpcomingReservation_stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($rows_upcomingReservation) > 0) {
                                        foreach ($rows_upcomingReservation as $row3) {
                                        ?>
                                            <div class="reservationDetails">
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
                                                    <div class="reservationType"><span>NATIONAL OFFICE FACILITY</span></div>
                                                    <div class="destinationFacility">
                                                        <span><strong>
                                                                <?php
                                                                $approvedFacility = $row3['reserved_facility'];
                                                                $stringLength2 = strlen($approvedFacility);

                                                                if ($stringLength2 > 25) {
                                                                    $substringFacility = substr($approvedFacility, 0, 26);
                                                                    $spacePosition2 = strrpos($substringFacility, " ");

                                                                    $approvedFacility = substr($substringFacility, 0, $spacePosition2);
                                                                    echo $approvedFacility . "...";
                                                                } else {
                                                                    echo $approvedFacility;
                                                                }
                                                                ?>
                                                            </strong></span>
                                                    </div>
                                                    <div class="reservedTimeNOF">
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
                        } else {
                            ?>
                            <div class="noApprovedReservation">
                                <h4>No upcoming reservations found</h4>
                            </div>
                        <?php
                        }
                        ?>
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
            </div>
        </div>
        <audio id="pingSound" src="Database/audio/notif-bell.wav" preload="auto"></audio>
        <script src="../Admin/Scripts/calendar.js"></script>
        <script src="../Admin/Scripts/script.js"></script>
        <script src="../Admin/Scripts/index_script.js"></script>
        <script src="../Admin/Scripts/requestsUpdate.js"></script>
    </body>
    </html>
<?php
}
?>