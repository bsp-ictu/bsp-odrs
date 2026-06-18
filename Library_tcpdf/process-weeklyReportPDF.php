<?php
    session_start();
    include('../Login/Database/process-configuration.php');

    require_once('tcpdf.php');

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    function subhead($pdf, $subheadTitle){
        $pdf->setFont('times', 'B');
    
        $pdf->setFontSize(14);
        $pdf->MultiCell(2.5, 7, "", 0,'L',0 ,'0');
        $pdf->MultiCell(79, 7, "$subheadTitle", 0,'L',0 ,'1');

        $pdf->setFontSize(10);
        $pdf->MultiCell(9, 6, "", 0,'C',0 ,'0');
        $pdf->MultiCell(27, 6, "Request ID", 1,'C',0 ,'0', '','', true, 0, false, true, 6, 'M');
        $pdf->MultiCell(32, 6, "Division", 1,'C',0 ,'0', '','', true, 0, false, true, 6, 'M');
        $pdf->MultiCell(24, 6, "Request Date", 1,'C',0 ,'0', '','', true, 0, false, true, 6, 'M');
        $pdf->MultiCell(25, 6, "Reserved Date", 1,'C',0 ,'0', '','', true, 0, false, true, 6, 'M');
        $pdf->MultiCell(17, 6, "Time", 1,'C',0 ,'0', '','', true, 0, false, true, 6, 'M');
        $pdf->MultiCell(46, 6, "Destination/Facility", 1,'C',0 ,'1', '','', true, 0, false, true, 6, 'M');

        $pdf->setFont('times', '');
        $pdf->setFontSize(9);
    }

    function tableContent($pdf, $conn, $referenceDate, $lastSunday, $nextSaturday, $comparator, $status){
        $select_3 = "
            SELECT * 
            FROM bsp_reservation_summary 
            WHERE $referenceDate BETWEEN '$lastSunday' AND '$nextSaturday' 
            AND request_status $comparator '$status'
        ";

        $stmt = $conn->prepare($select_3);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($results) > 0){
            foreach($results as $row){
                $requestId = $row['request_id'];

                if ($row['request_type'] == "Vehicle Reservation"){
                    $select_4 = "SELECT TOP 1 * FROM vehicle_reservation WHERE request_id = :requestId";
                    $select_4_stmt = $conn->prepare($select_4);
                    $select_4_stmt->bindParam(':requestId', $requestId);
                    $select_4_stmt->execute();

                    $rows_2 = $select_4_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($rows_2) {
                        $time = date("h:i A", strtotime($rows_2['etd_from_station'] ?? '00:00:00'));

                        $destination = $rows_2['destination'] ?? '';
                        $stringLength = strlen($destination);

                        if ($stringLength > 24) {
                            $substringDestination = substr($destination, 0, 25);
                            $spacePosition = strrpos($substringDestination, " ");

                            if ($spacePosition !== false) {
                                $destination = substr($substringDestination, 0, $spacePosition);
                            } else {
                                $destination = $substringDestination;
                            }

                            $destination .= "...";
                        }

                        $pdf->MultiCell(9, 6, "", 0, 'C', 0, '0');
                        $pdf->MultiCell(27, 6, $rows_2['request_id'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(32, 6, $rows_2['requester_division'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(24, 6, $rows_2['request_date'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(25, 6, $rows_2['reservation_date'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(17, 6, $time, 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(46, 6, $destination, 1, 'C', 0, '1', '', '', true, 0, false, false, 6, 'M');
                    }
                } 
                else {
                    $select_4 = "SELECT TOP 1 * FROM facility_reservation WHERE request_id = :requestId";
                    $select4_stmt = $conn->prepare($select_4);
                    $select4_stmt->bindParam(':requestId', $requestId);
                    $select4_stmt->execute();
                    $rows_2 = $select4_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($rows_2) {
                        $facility = $rows_2['reserved_facility'];
                        $stringLength = strlen($facility);
                        $time_start = date("h:i A", strtotime($rows_2['reservation_time_start'] ?? '00:00:00'));

                        if ($stringLength > 24) {
                            $substringFacility = substr($facility, 0, 25);
                            $spacePosition = strrpos($substringFacility, " ");

                            if ($spacePosition !== false) {
                                $facility = substr($substringFacility, 0, $spacePosition);
                            } else {
                                $facility = $substringFacility;
                            }

                            $facility .= "...";
                        }

                        $pdf->MultiCell(9, 6, "", 0, 'C', 0, '0');
                        $pdf->MultiCell(27, 6, $rows_2['request_id'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(32, 6, $rows_2['requester_division'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(24, 6, $rows_2['request_date'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(25, 6, $rows_2['reservation_date'], 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(17, 6, $time_start, 1, 'C', 0, '0', '', '', true, 0, false, true, 6, 'M');
                        $pdf->MultiCell(46, 6, $facility, 1, 'C', 0, '1', '', '', true, 0, false, false, 6, 'M');
                    }
                }
            }
        }
    }

    function noRecordsFound($pdf, $message){
        $pdf->setFont('times', '', 11);

        $pdf->MultiCell(9, 6, "", 0,'C',0 ,'0');
        $pdf->MultiCell(171, 18, $message, 1,'C',0 ,'1', '','', true, 0, false, true, 18, 'M');
    }

    $totalReservation = $_SESSION['totalReservation'];
    $vehicleReservation = $_SESSION['vehicleReservation'];
    $facilityReservation = $_SESSION['facilityReservation'];
    $pendingReservation = $_SESSION['pendingReservation'];
    $approvedReservation = $_SESSION['approvedReservation'];
    $rejectedReservation = $_SESSION['rejectedReservation'];
    $scheduledReservation = $_SESSION['scheduledReservation'];

    $lastSunday2 = $_SESSION['lastSunday'];
    $nextSaturday2 = $_SESSION['nextSaturday'];

    $currentDate = date ("m-d-Y");
    $dateFormat_2 = date ("F d, Y");

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('Boy Scouts of the Philippines');
    $pdf->SetAuthor('Boy Scouts of the Philippines');
    $pdf->SetTitle("{$currentDate}-Weekly-Report");
    $pdf->SetSubject('Downloadable PDF');
    $pdf->SetKeywords('BSP, Weekly Report, Report, Reservation, PDF');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(15, 18, 15);
    $pdf->SetAutoPageBreak(true, 18);

    $pdf->AddPage();

    $pdf->SetFont('times', '', 12);

    $weeklyReportContent = <<<EOD
        <table style="width: 100%;">
            <br>
            <tr>
                <td style="font-size: 12px; font-weight: bold; text-align: center;">
                    BOY SCOUTS OF THE PHILIPPINES
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; font-weight: bold; text-align: center;">
                    National Office
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; font-weight: bold; text-align: center;">
                    Manila
                </td>
            </tr>
            <br>
            <tr>
                <td style="width: 75%; font-size: 12px; text-align: right;">
                    Date:
                </td>
                <td style="width: 25%; border-bottom: 1px solid black; font-size: 12px; text-align: center;">
                    {$currentDate}
                </td>
            </tr>
            <br>
            <tr>
                <td style="width: 100%; font-size: 16px; font-weight: bold; text-align: center;">
                    WEEKLY RESERVATION REPORT
                </td>
            </tr>
            <br>
            <tr>
                <td style="width: 100%; font-size: 14px; font-weight: bold;">
                    REPORT SUMMARY
                </td>
            </tr>
            <tr style="line-height: 25px; ">
                <td style="width: 5%;"></td>
                <td style="width: 30%; font-size: 12px; font-weight: bold;">
                    Total Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$totalReservation}
                </td>
                <td style="width: 30%; font-size: 12px; font-weight: bold; display: inline-block; vertical-align: bottom;">
                    Pending Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$pendingReservation}
                </td>
            </tr>
            <tr style="line-height: 25px; ">
                <td style="width: 5%;"></td>
                <td style="width: 30%; font-size: 12px; font-weight: bold;">
                    Vehicle Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$vehicleReservation}
                </td>
                <td style="width: 30%; font-size: 12px; font-weight: bold; display: inline-block; vertical-align: bottom;">
                    Approved Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$approvedReservation}
                </td>
            </tr>
            <tr style="line-height: 25px; ">
                <td style="width: 5%;"></td>
                <td style="width: 30%; font-size: 12px; font-weight: bold;">
                    Facility Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$facilityReservation}
                </td>
                <td style="width: 30%; font-size: 12px; font-weight: bold; display: inline-block; vertical-align: bottom;">
                    Rejected Reservations:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$rejectedReservation}
                </td>
            </tr>
            <br>
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 40%; font-size: 12px; font-weight: bold;">
                    Scheduled Reservations This Week:
                </td>
                <td style="width: 20%; font-size: 12px;">
                    {$scheduledReservation}
                </td>
            </tr>
        </table>
    EOD;
    
    $pdf->writeHTML($weeklyReportContent, true, false, true, false, '');

    subhead($pdf, "SCHEDULED RESERVATIONS");

    if($scheduledReservation != 0){
        tableContent($pdf, $conn, 'reservation_date', $lastSunday2, $nextSaturday2, '=', "Approved");
    } else{
        noRecordsFound($pdf, "As of ". $dateFormat_2 .", no reservations are scheduled for this week.");
    }

    $pdf->MultiCell(2.5, 6, "", 0,'L',0 ,'1');

    subhead($pdf, "APPROVED RESERVATIONS");

    if($approvedReservation != 0){
        tableContent($pdf, $conn, 'request_date', $lastSunday2, $nextSaturday2, '=', "Approved");
    } else{
        noRecordsFound($pdf, "As of ". $dateFormat_2 .", no reservations have been approved this week.");
    }

    $pdf->MultiCell(2.5, 6, "", 0,'L',0 ,'1');

    $pdf->MultiCell(2.5, 6, "", 0,'L',0 ,'1');

    $pdf->Output("{$currentDate}-Weekly-Report.pdf", 'I');
?>