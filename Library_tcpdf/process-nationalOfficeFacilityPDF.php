<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include('../Login/Database/process-configuration.php');

    require_once('tcpdf.php');
    $reservationID = isset($_GET['request_id']) ? $_GET['request_id'] : $reservationID = $_SESSION['requestIdNOF'];
    if (empty($reservationID)) {
        die("Missing request_id");
    }
    $select = "SELECT TOP 1 * FROM facility_reservation WHERE request_id = :reservationID";
    $result = $conn->prepare($select);
    $result->bindParam(':reservationID', $reservationID);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if($row){
        $conn->prepare("
            INSERT INTO audit_trail (
                account_name, action_type, table_name, record_id, old_value, new_value, action_description
            ) VALUES (?, 'PRINT', 'facility_reservation', ?, '-', '-', 'Printed the request form for facility reservation')
        ")->execute([
            $_SESSION['accountname'],
            $reservationID

        ]);
        $_SESSION['requester_name'] = $row['requester_name'];
        $_SESSION['requester_division'] = $row['requester_division'];
        $_SESSION['division_chief'] = $row['division_chief'];
        $_SESSION['request_date'] = date("m-d-Y", strtotime($row['request_date']));
        $_SESSION['reservation_date'] = date("m-d-Y", strtotime($row['reservation_date']));
        $_SESSION['reservation_time_start'] = date("h:i A", strtotime($row['reservation_time_start']));
        $_SESSION['reservation_time_end'] = date("h:i A", strtotime($row['reservation_time_end']));
        $_SESSION['reserved_facility'] = $row['reserved_facility'];
        $_SESSION['other_facility'] = $row['other_facility'];
        $_SESSION['purpose'] = $row['purpose'];
        $_SESSION['number_of_person'] = $row['number_of_person'];
        if($row['status'] == "Approved"){
            $_SESSION['approved_note'] = "Approved via system on ".$row['date_approved'];
        } else{
            $_SESSION['approved_note'] = "";
        }
    } else if($_SESSION['accountrole']=="Admin" || $_SESSION['accountrole']=="Superadmin"){
        header("Location: ../Admin/vehicleRequestsFrame.php?failed-to-retrieve-data");
    } else {
        header("Location: ../User/clientIndex.html?failed-to-retrieve-data");    
        exit();
    }

    $selectedFacilities = [
        "National Executive Board Room" => false,
        "Educational Hub (Library and Museum)" => false,
        "Conference Hall 6th Floor" => false,
        "4th Floor Veranda" => false,
        "Ground/Parking Area" => false,
        "Others (specify)" => false
    ];

    if ($_SESSION['other_facility']){

        foreach ($selectedFacilities as $facility => $value) {
            $selectedFacilities[$facility] = false;
        }

        $selectedFacilities["Others (specify)"] = true;
    } else {
        $_SESSION['reserved_facility'] = $row['reserved_facility'];
    
        foreach ($selectedFacilities as $facility => $value) {
            if ($facility == $_SESSION['reserved_facility']) {
                $selectedFacilities[$facility] = true;
                break; 
            }
        }
    }

    $approvingOfficer = "SOFRONIO D. HONTANOSAS";
    $officerPosition = "YDO V/ Acting Chief Administrative Officer";

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('Boy Scouts of the Philippines');
    $pdf->SetAuthor('Boy Scouts of the Philippines');
    $pdf->SetTitle("$reservationID");
    $pdf->SetSubject('Downloadable PDF');
    $pdf->SetKeywords('BSP, Reservation, National Office Facility, PDF');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(15, 18, 15);
    $pdf->SetAutoPageBreak(false, 0);

    $pdf->AddPage();

    $pdf->SetFont('times', '', 12);

    function getCheckbox($isChecked) {
        return $isChecked ? '<span style="font-family: DejaVuSans; font-size: 16px;">☑</span>' : '<span style="font-family: DejaVuSans; font-size: 16px;">☐</span>';
    }

    $reservationContent = <<<EOD
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 11px; text-align: right;">
                    BSP Admin Form No. 24-14
                </td>
            </tr>
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
                    {$_SESSION['request_date']}
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; text-align: left;">
                    To: General Services Section, Administration Division
                </td>
            </tr>
            <br>
            <br>
            <tr>
                <td style="width: 100%; font-size: 12px; font-weight: bold; text-align: center;">
                    <u>REQUEST FOR THE USE OF BSP NATIONAL OFFICE FACILITY</u>
                </td>
            </tr>

            <br>

            <tr> 
                <td style="width: 6%; height: 25px; line-height: 25px; font-size: 13px;"></td>
                <td style="width: 49%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_NEB} National Executive Board Room
                </td>
                <td style="width: 45%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_VERANDA} 4th Floor Veranda
                </td>
            </tr>
            <tr>
                <td style="width: 6%; height: 25px; line-height: 25px; font-size: 13px;"></td>
                <td style="width: 49%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_EDUC} Educational Hub (Library and Museum)
                </td>
                <td style="width: 45%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_PARKING} Ground/Parking Area
                </td>
            </tr>
            <tr>
                <td style="width: 6%; height: 25px; line-height: 25px; font-size: 13px;"></td>
                <td style="width: 49%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_HALL} Conference Hall 6th Floor
                </td>
                <td style="width: 45%; height: 25px; line-height: 25px; font-size: 13px;">
                    {CHECK_OTHERS} Others (specify)
                </td>
            </tr>
            <tr>
                <td style="width: 6%; line-height: 22px;"></td>
                <td style="width: 45%; line-height: 22px; font-size: 13px"></td>
                <td style="width: 9.5%;"></td>
                <td style="width: 40%; border-bottom: 1px solid black; text-align: center;">
                    {$_SESSION['other_facility']}
                </td>
            </tr>

            <br>

            <tr>
                <td style="width: 50%; height: 40px; border: 1px solid black; font-size: 12px;">
                    Date of Use: 
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['reservation_date']}<br></p>
                </td>
                <td style="width: 50%; height: 40px; border: 1px solid black; font-size: 12px;">
                    Time of Use:
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['reservation_time_start']} - {$_SESSION['reservation_time_end']}<br></p>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 60px; border: 1px solid black; font-size: 12px;">
                    Purpose:
                    <p style="margin: 0; padding-left: 10px; text-align: center;">   {$_SESSION['purpose']}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 45px; border: 1px solid black; font-size: 12px;">
                    Number of persons:
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['number_of_person']}<br></p>
                </td>
                <td style="width: 50%; height: 45px; border: 1px solid black; font-size: 12px;">
                    Group/Division:
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['requester_division']}<br></p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 60px; border: 1px solid black; font-size: 12px;">
                    Requesting Officer:
                    <br>
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['requester_name']}</p>
                </td>
                <td style="width: 50%; height: 60px; border: 1px solid black; font-size: 12px;">
                    Recommending Approval:
                    <br>
                    <p style="margin: 0; padding: 0; text-align: center;">
                        _____________________________
                        <br>
                        {$_SESSION['division_chief']}
                        <br>
                        Division Chief / Unit Head</p>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 100px; border: 1px solid black; font-size: 12px;">
                    Approved by:
                    <br>
                    <br>
                    <p style="margin-bottom: 0; font-family: monospace; font-weight: 400; text-align: center;">
                        {$_SESSION['approved_note']}
                    </p>
                    <p style="margin-bottom: 0; text-align: center;">
                        _________________________________________
                        <br>
                        {$approvingOfficer}<br>{$officerPosition}
                    </p>
                </td>
            </tr>
            <br>
            <tr>
                <td style="font-size: 11px; text-align: left;">
                    <em>cc: OSG, Admin, EED and requesting Division</em>
                </td>
            </tr>
        </table>
    EOD;

    $reservationContent = str_replace([
        '{CHECK_NEB}', '{CHECK_EDUC}', '{CHECK_HALL}', '{CHECK_VERANDA}', '{CHECK_PARKING}', '{CHECK_OTHERS}'
    ], [
        getCheckbox($selectedFacilities["National Executive Board Room"]),
        getCheckbox($selectedFacilities["Educational Hub (Library and Museum)"]),
        getCheckbox($selectedFacilities["Conference Hall 6th Floor"]),
        getCheckbox($selectedFacilities["4th Floor Veranda"]),
        getCheckbox($selectedFacilities["Ground/Parking Area"]),
        getCheckbox($selectedFacilities["Others (specify)"])
    ], $reservationContent);

    $pdf->writeHTML($reservationContent, true, false, true, false, '');

    $pdf->Output("{$reservationID}.pdf", 'I');
?>