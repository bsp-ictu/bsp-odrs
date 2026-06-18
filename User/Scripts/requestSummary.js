function openVehicleRequestSummary(requestId) {
    fetch(`../User/Database/process-vehicleReservationSummary.php?request_id=${requestId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("summaryStatus").textContent = data.status;
            document.getElementById("summaryRequestID").textContent = requestId;
            document.getElementById("cancelBtnVehicle").dataset.requestId = requestId;

            if (data.status == 'Pending'){
                document.getElementById("summaryStatus").style.color = "rgb(1, 4, 143)";
            } else if (data.status == 'Approved'){
                document.getElementById("summaryStatus").style.color = "rgb(4,131,4)";
            } else {
                document.getElementById("summaryStatus").style.color = "#A50003";
            }

            document.getElementById("summaryDivision").textContent = data.requester_division;
            document.getElementById("summaryChief").textContent = data.division_chief;

            let reservedAndRequestDate = [data.reservation_date, data.request_date];
            let newFormattedDate = [];

            const dateFormat = new Intl.DateTimeFormat('en-US', { day: '2-digit', month: '2-digit', year: 'numeric' });

            for (let i=0; i<2; i++){
                newFormattedDate[i] = dateFormat.format(new Date(reservedAndRequestDate[i])).replace(/\//g, '-');
            }

            document.getElementById("summaryReservedDate").textContent = newFormattedDate[0];
            document.getElementById("summaryRequestDate").textContent = newFormattedDate[1];

            let etdAndEta = [data.etd_from_station, data.etd_from_destination, data.eta_to_station, data.eta_to_destination];
            let dummyDateTime = [];
            let newReservedTime = [];
        
            const dummyDate = "2025-01-01";
            const timeFormat = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit'});

            for (let j=0; j<4; j++ ){
                dummyDateTime [j] = new Date(`${dummyDate}T${etdAndEta[j]}`);
                newReservedTime[j] = timeFormat.format(dummyDateTime[j]);
            }

            document.getElementById("summaryETDfrStation").textContent = newReservedTime[0];
            document.getElementById("summaryETDfrDestination").textContent = newReservedTime[1];
            document.getElementById("summaryETAtoStation").textContent = newReservedTime[2];
            document.getElementById("summaryETAtoDestination").textContent = newReservedTime[3];

            document.getElementById("summaryDestination").textContent = data.destination;
            document.getElementById("summaryPassenger").textContent = data.passenger;
            document.getElementById("summaryRequester").textContent = data.requester_name;
            document.getElementById("summaryPurpose").textContent = data.purpose;

            document.getElementById("cancelBtnVehicle").onclick = () => cancelReservationVehicle(requestId);

            document.getElementById("summaryContainer").style.display = "flex";
            document.getElementById("vehicleReservation").style.display = "block";
        })
        .catch(error => console.error("Error fetching request summary:", error)); 
}

function openUseofNationalOfficeFacilitySummary(requestId) {
    fetch(`../User/Database/process-nationalOfficeFacilitySummary.php?request_id=${requestId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("summaryStatus2").textContent = data.status;
            document.getElementById("summaryRequestID2").textContent = requestId;
            document.getElementById("cancelBtnFacility").dataset.requestId = requestId;

            if (data.status == 'Pending'){
                document.getElementById("summaryStatus2").style.color = "rgb(1, 4, 143)";
            } else if (data.status == 'Approved'){
                document.getElementById("summaryStatus2").style.color = "rgb(4,131,4)";
            } else {
                document.getElementById("summaryStatus2").style.color = "#A50003";
            }

            document.getElementById("summaryRequester2").textContent = data.requester_name;
            document.getElementById("summaryDivision2").textContent = data.requester_division;
            document.getElementById("summaryChief2").textContent = data.division_chief;

            document.getElementById("summaryReservedFacility").textContent = data.reserved_facility;
            
            let reservedAndRequestDate = [data.reservation_date, data.request_date];
            let newFormattedDate = [];

            const dateFormat = new Intl.DateTimeFormat('en-US', { day: '2-digit', month: '2-digit', year: 'numeric' });

            for (let i=0; i<2; i++){
                newFormattedDate[i] = dateFormat.format(new Date(reservedAndRequestDate[i])).replace(/\//g, '-');
            }

            let startAndEndTime = [data.reservation_time_start, data.reservation_time_end];
            let dummyDateTime = [];
            let newReservedTime = [];
            const dummyDate = "2025-01-01";
            const timeFormat = new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit'});

            for (let j=0; j<2; j++ ){
                dummyDateTime [j] = new Date(`${dummyDate}T${startAndEndTime[j]}`);
                newReservedTime[j] = timeFormat.format(dummyDateTime[j]);
            }

            document.getElementById("summaryReservedDate2").textContent = newFormattedDate[0];
            document.getElementById("summaryRequestDate2").textContent = newFormattedDate[1];

            document.getElementById("summaryReservedStartTime").textContent = newReservedTime[0];
            document.getElementById("summaryReservedEndTime").textContent = newReservedTime[1];

            document.getElementById("summaryNumOfPerson").textContent = data.number_of_person;
            document.getElementById("summaryPurpose2").textContent = data.purpose;

            document.getElementById("cancelBtnFacility").onclick = () => cancelReservationFacility(requestId);
            
            document.getElementById("summaryContainer").style.display = "flex";
            document.getElementById("useOfNationalOffice").style.display = "block";
        })
        .catch(error => console.error("Error fetching request summary:", error)); 
}

function closeVehicleRequestSummary() {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("vehicleReservation").style.display = "none";
}

function closeUseofNationalOfficeFacilitySummary() {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("useOfNationalOffice").style.display = "none";
}

function pdfVehicleReservation() {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("vehicleReservation").style.display = "none";

    window.open('../Library_tcpdf/process-vehicleReservationPDF.php', '_blank');
}

function pdfFacilityReservation() {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("useOfNationalOffice").style.display = "none";

    window.open('../Library_tcpdf/process-nationalOfficeFacilityPDF.php', '_blank');
}

function cancelReservationVehicle(requestId) {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("vehicleReservation").style.display = "none";

    fetch(`Database/process-cancelReservationVehicle.php?request_id=${requestId}`, {
        method: "GET"
    })
    .then(response => response.text())
    .then(data => {
        console.log("PHP Response:", data);

        if (data.includes("success")) {
            // if PHP sent success message
            window.location.href = "clientIndex.php?cancellation-success";
        } else {
            alert("Cancellation failed: " + data);
        }
    })
    .catch(error => {
        console.error("Error cancelling reservation:", error);
        alert("An error occurred while cancelling the reservation.");
    });
}

function cancelReservationFacility(requestId) {
    document.getElementById("summaryContainer").style.display = "none";
    document.getElementById("useOfNationalOffice").style.display = "none";

    fetch(`Database/process-cancelReservationFacility.php?request_id=${requestId}`, {
        method: "GET"
    })
    .then(response => response.text())
    .then(data => {
        console.log("PHP Response:", data);

        if (data.includes("success")) {
            // if PHP sent success message
            window.location.href = "clientIndex.php?cancellation-success";
        } else {
            alert("Cancellation failed: " + data);
        }
    })
    .catch(error => {
        console.error("Error cancelling reservation:", error);
        alert("An error occurred while cancelling the reservation.");
    });
}

const urlParams = new URLSearchParams(window.location.search);
const isCancelled = urlParams.get('cancellation');
if(isCancelled == "success"){

}