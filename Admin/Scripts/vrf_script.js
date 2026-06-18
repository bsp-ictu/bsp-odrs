document.addEventListener('DOMContentLoaded', function() {
    var driver = document.getElementById("driver-value").value;
    var vehicle = document.getElementById("vehicle-value").value;
    var status = document.getElementById("status-value").value;
    console.log(status);
    if(driver!="" && vehicle!=""){
        document.getElementById("driver-value").setAttribute("disabled", "true");
        document.getElementById("vehicle-value").setAttribute("disabled", "true");
    }
    if(status=="Approved" || status=="No Vehicle Available" || status=="Lapsed"){
        document.getElementById("status-value").setAttribute("disabled", "true");
        document.getElementById("driver-value").setAttribute("disabled", "true");
        document.getElementById("vehicle-value").setAttribute("disabled", "true");
    }
});

document.getElementById("status-value").addEventListener('change', function () {
    var statusRequest = document.getElementById("status-value").value;
    console.log(statusRequest);
    if(statusRequest=="No Vehicle Available"){
        document.getElementById("driver-value").setAttribute("disabled", "true");
        document.getElementById("vehicle-value").setAttribute("disabled", "true");
        document.getElementById("vehicle-value").selectedIndex = 0;
        document.getElementById("driver-value").selectedIndex = 0;
    } else {
        document.getElementById("driver-value").removeAttribute("disabled");
        document.getElementById("vehicle-value").removeAttribute("disabled"); 
    }

})



const urlParams = new URLSearchParams(window.location.search);
const conflictType = urlParams.get('conflict');

if(conflictType == "driver"){
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "Driver is already booked at the selected time and date.";
    document.getElementById("messageContentConflictHeader").innerHTML = "Oops...Something went wrong!";
    document.getElementById("messageContentConflictHeader").style.color = "#FF0000";
} else if(conflictType == "vehicle") {
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "Vehicle is already booked at the selected time and date.";
    document.getElementById("messageContentConflictHeader").innerHTML = "Oops... Something went wrong!";
    document.getElementById("messageContentConflictHeader").style.color = "#FF0000";
}else if(urlParams.get('update') == "success") {
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "Data successfully updated!";
    document.getElementById("messageContentConflictHeader").innerHTML = "Success...";
    document.getElementById("messageContentConflictHeader").style.color = "#4CAF50";
}

function removeQueryParams(params) {
    const url = new URL(window.location);
    params.forEach(param => url.searchParams.delete(param));
    history.replaceState({}, document.title, url.toString());
}

function closeConflictMsg() {
    document.getElementById("notificationModal").style.display = "none";
    document.getElementById("conflictMsg").style.display = "none";
    removeQueryParams(["update", "conflict"]);
}

function openUpdateVRF() {
    var statusForm = document.getElementById("status-value").value;

    if(statusForm == "Not Available"){
        document.getElementById("driver-value").removeAttribute("required");
        document.getElementById("vehicle-value").removeAttribute("required");
        document.getElementById("driver-value").value = "";
        document.getElementById("vehicle-value").value = "";
    }

    let form = document.getElementById("vrf-form");
    if (form.checkValidity()) {
        document.getElementById("updateModal").style.display = "block";
        document.getElementById("notificationModal").style.display = "block";
    } else {
        alert("Please fill out all required fields before submitting.");
    }
}

function closeUpdateVRF() {
    document.getElementById("updateModal").style.display = "none";
    document.getElementById("notificationModal").style.display = "none";
}

function submitUpdateForm() {
    let form = document.getElementById("vrf-form");
    form.submit();
}

function selectValue(){
    var status = document.getElementById("status-value").value;

    if(status == "Not Available"){
        document.getElementById("driver-value").value = "";
        document.getElementById("vehicle-value").value = "";
    }
}

function openCancelRequestVRF() {
    document.getElementById("cancellationModal").style.display = "block";
    document.getElementById("notificationModal").style.display = "block";
}

function closeCancelRequestVRF() {
    document.getElementById("cancellationModal").style.display = "none";
    document.getElementById("notificationModal").style.display = "none";
}

function openCancelRequestVRF() {
    document.getElementById("cancellationModal").style.display = "block";
    document.getElementById("notificationModal").style.display = "block"
}

function cancelRequestVRF(requestID){
    const formData = new FormData();
    formData.append("requestID", requestID);
    fetch("Database/vrf_cancelReservation.php", {
        method: "POST",
        body: formData
    })
    .then((response) => response.json())
    .then((data) => {
        console.log("Cancellation Response: ", data);

        if(data.status === "success"){
            document.getElementById("cancellationModal").style.display = "none";
            document.getElementById("conflictMsg").style.display = "flex";
            document.getElementById("notificationModal").style.display = "block";
            document.getElementById("messageContentConflict").innerHTML = "Reservation cancelled successfully.";
            document.getElementById("messageContentConflictHeader").innerHTML = "Success";
            document.getElementById("messageContentConflictHeader").style.color = "#044f12";
        } else {
            document.getElementById("cancellationModal").style.display = "none";
            document.getElementById("conflictMsg").style.display = "flex";
            document.getElementById("notificationModal").style.display = "block";
            document.getElementById("messageContentConflict").innerHTML = "An error occured.";
            document.getElementById("messageContentConflictHeader").innerHTML = "Error";
            document.getElementById("messageContentConflictHeader").style.color = "#FF0000";
        }
    })
    .catch((error) => console.error("Reservation cancellation error: ", error));
}