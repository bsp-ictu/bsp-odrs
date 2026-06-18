function printVRF(requestID, accountRole) {
    window.open(`../Library_tcpdf/process-vehicleReservationPDF.php?request_id=${requestID}&acc_role=${accountRole}`, '_blank');
}

function printNOF(requestID, accountRole) {
    window.open(`../Library_tcpdf/process-nationalOfficeFacilityPDF.php?request_id=${requestID}&acc_role=${accountRole}`, '_blank');
}