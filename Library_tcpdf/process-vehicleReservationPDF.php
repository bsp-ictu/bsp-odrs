<?php
    session_start();
    include('../Login/Database/process-configuration.php');

    require_once('tcpdf.php');
    $reservationID = isset($_GET['request_id']) ? $_GET['request_id'] : $reservationID = $_SESSION['requestIdVRF'];

    $select = "SELECT TOP 1 * FROM vehicle_reservation WHERE request_id = :reservationID";
    $result = $conn->prepare($select);
    $result->bindParam(':reservationID', $reservationID);
    $result->execute();

    $row = $result->fetch(PDO::FETCH_ASSOC);

    if($row){
        $conn->prepare("
            INSERT INTO audit_trail (
                account_name, action_type, table_name, record_id, old_value, new_value, action_description
            ) VALUES (?, 'PRINT', 'vehicle_reservation', ?, '-', '-', 'Printed the request form for vehicle reservation')
        ")->execute([
            $_SESSION['accountname'],
            $reservationID

        ]);
        $_SESSION['requester_name'] = $row['requester_name'];
        $_SESSION['requester_division'] = $row['requester_division'];
        $_SESSION['division_chief'] = $row['division_chief'];
        $_SESSION['request_date'] = date("m-d-Y", strtotime($row['request_date']));
        $_SESSION['reservation_date'] = date("m-d-Y", strtotime($row['reservation_date']));
        $_SESSION['request_time'] = date("h:i A", strtotime($row['request_time']));


        $_SESSION['etd_from_station'] = date("h:i A", strtotime($row['etd_from_station']));
        $_SESSION['eta_to_destination'] = date("h:i A", strtotime($row['eta_to_destination']));
        $_SESSION['etd_from_destination'] = date("h:i A", strtotime($row['etd_from_destination']));
        $_SESSION['eta_to_station'] = date("h:i A", strtotime($row['eta_to_station']));

        $_SESSION['destination'] = $row['destination'];
        $_SESSION['purpose'] = $row['purpose'];
        $_SESSION['passenger'] = $row['passenger'];
        $_SESSION['vehicle_plate_no.'] = $row['vehicle_plate_no'];
        $_SESSION['vehicle_driver'] = $row['vehicle_driver'];

        if($row['status'] === "No Vehicle Available"){
            $_SESSION['vehicle_plate_no.'] = $row['status'];
            $_SESSION['approved_note'] = "Processed via system on ".$row['date_approved'];
        } else if($row['status'] === "Approved"){
            $_SESSION['approved_note'] = "Approved via system on ".$row['date_approved'];
        } else{
            $_SESSION['approved_note'] = "";
        }

    } else {
        if($_SESSION['accountrole']=="Admin"){
            header("Location: ../Admin/vehicleRequestsFrame.php?failed-to-retrieve-data");
        } else {
            header("Location: ../User/clientIndex.php?failed-to-retrieve-data");    
            exit();
        }
    }
    $approvingOfficer = "SOFRONIO D. HONTANOSAS";
    $officerPosition = "YDO V/ Acting Chief Administrative Officer";

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('Boy Scouts of the Philippines');
    $pdf->SetAuthor('Boy Scouts of the Philippines');
    $pdf->SetTitle("$reservationID");
    $pdf->SetSubject('Downloadable PDF');
    $pdf->SetKeywords('BSP, Reservation, Vehicle Reservation Form, PDF');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(15, 10, 15);
    $pdf->SetAutoPageBreak(false, 0);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetFont('helvetica', '', 11.5);

    $pdf->AddPage();
    
     
    $reservationContent = <<<EOD
        <table style="width: 100%;">
            <tr>
                <td style="width: 26%; text-align: right;">
                    <img src="..\Images\BSP_Logo.png" alt="logoBSP" style="height: 120px;"/>
                </td>
                <td style="width: 46%; font-size: 14px; font-weight: bold; text-align: center;">
                    <br><br><br>
                    Republic of the Philippines
                    <br>
                    BOY SCOUTS OF THE PHILIPPINES
                    <br>
                    National Office
                    <br>
                    Manila
                </td>
                <td style="width: 28%; font-size: 12px; text-align: right;">
                    <br><br><br>
                    BSP Admin Form No. 14-A
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 45px; border: 1px solid black; font-size: 20px; font-weight: bold; text-align: center;">
                   <br><br>
                   VEHICLE RESERVATION FORM (VRF)
                </td>
            </tr>
            <tr>
                <td style="width: 33.33%; height: 30px; border: 1px solid black;">
                    <strong>VRS No: </strong>{$reservationID}
                </td>
                <td style="width: 33.33%; height: 30px; border: 1px solid black;">
                    <strong>Date: </strong>{$_SESSION['request_date']}
                </td>
                <td style="width: 33.33%; height: 30px; border: 1px solid black;">
                    <strong>Time: </strong>{$_SESSION['request_time']}
                </td>
            </tr>
            <tr>
                <td style="width: 33.33%; border-left: 1px solid black;">
                    <br><br>
                    <strong>Please reserve a vehicle for  </strong>   
                </td>
                <td style="width: 61.5%; border-bottom: 1px solid black; text-align: center;">
                    <br><br>
                    {$_SESSION['requester_division']}
                </td>
                <td style="width: 5.17%; border-right: 1px solid black;">
                </td>
            </tr>
            <tr>
                <td style="width: 55%; border-left: 1px solid black;">

                </td>
                <td style="width: 45%; border-right: 1px solid black;">
                    <strong>Division/Office</strong>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 30px; border-left: 1px solid black; border-right: 1px solid black;">
                    <strong>Date of Use: </strong>{$_SESSION['reservation_date']}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 25px; border-left: 1px solid black;">
                    <strong>ETD (from Station): </strong>{$_SESSION['etd_from_station']}
                </td>
                <td style="width: 50%; height: 25px; border-right: 1px solid black;">
                    <strong>ETA (to Destination): </strong>{$_SESSION['eta_to_destination']}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 25px; border-left: 1px solid black;">
                    <strong>ETD (from Destination): </strong>{$_SESSION['etd_from_destination']}
                </td>
                <td style="width: 50%; height: 25px; border-right: 1px solid black;">
                    <strong>ETA (to Station): </strong>{$_SESSION['eta_to_station']}
                    <br>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 60px; border-left: 1px solid black; border-right: 1px solid black;">
                    <strong>Destination/s: </strong>
                    <br>
                    {$_SESSION['destination']}
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 60px; border-left: 1px solid black; border-right: 1px solid black;">
                    <strong>Purpose/s of Trip: </strong>
                    <br>
                    {$_SESSION['purpose']}
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 80px; border-left: 1px solid black; border-right: 1px solid black;">
                    <strong>Passenger/s: </strong>
                    <br>
                    {$_SESSION['passenger']}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; border-left: 1px solid black; border-bottom: 1px solid black;">
                    <strong>Requested by: </strong>
                    <br>
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['requester_name']}</p>
                </td>
                <td style="width: 50%; height: 50px; border-right: 1px solid black; border-bottom: 1px solid black;">
                    <strong>Recommending Approval: </strong>
                    <br>
                    <p style="margin: 0; padding: 0; text-align: center;">
                        _____________________________
                        <br>
                        {$_SESSION['division_chief']}<br><strong>Division Chief / Unit Head</strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 50px; border: 1px solid black;">
                    <strong>Received by/Date: </strong>
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['request_date']}</p>
                </td>
                <td style="width: 21%;">
                    <strong>Driver Assigned:</strong>
                </td>
                <td style="width: 29%; border-right: 1px solid black;">
                   {$_SESSION['vehicle_driver']}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; height: 50px; border: 1px solid black;">
                    <strong>Vehicle/Plate No.: </strong>
                    <p style="margin: 0; padding: 0; text-align: center;">{$_SESSION['vehicle_plate_no.']}</p>
                </td>
                <td style="width: 20%; border-bottom: 1px solid black;">
                    <strong>Assigned by: </strong>
                </td>
                <td style="width: 30%; border-right: 1px solid black; border-bottom: 1px solid black; text-align: center;">
                   MARIA LEA F. OLLERES
                   <br>
                   Officer In-Charge,
                   <br>
                   General Services Officer
                </td>
            </tr>
            <tr>
                <td style="width: 100%; height: 90px; border: 1px solid black;">
                    <strong>Approved by: </strong>
                    <br>
                    <p style="margin-bottom: 0; font-family: monospace; font-weight: 400; text-align: center;">
                        {$_SESSION['approved_note']}
                    </p>
                    <p style="margin-bottom: 0; font-weight: bold; text-align: center;">
                        _________________________________________
                        <br>
                        {$approvingOfficer}<br>{$officerPosition}
                    </p>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; font-size: 9.5px;">
                    <i><strong>Note:</strong> Please accomplish properly this form and
                                            submit to the Administration Division-General Services Section upon completion of official travel.</i>
                    <br>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; font-size: 13px;">
                    <i>The departure of the vehicle may not be allowed if it is not approved by the head of the Division</i>
                    <br>
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; font-weight: bold;">
                    ETD-Estimated Time of Departure
                    <br>
                    ETA-Estimated Time of Arrival
                </td>
            </tr>
        </table>
    EOD;

    $pdf->writeHTML($reservationContent, true, false, true, false, '');

    $pdf->Output("{$reservationID}.pdf", 'I');
?>