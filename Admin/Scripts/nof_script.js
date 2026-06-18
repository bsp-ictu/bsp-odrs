const urlParams = new URLSearchParams(window.location.search);
if(urlParams.get("update") == "success"){
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "Entry successfully updated.";
    document.getElementById("messageContentConflictHeader").innerHTML = "Success";
    document.getElementById("messageContentConflictHeader").style.color = "#4CAF50";
} else if(urlParams.get("update") == "failed") {
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "An error unexpectedly occurred.";
    document.getElementById("messageContentConflictHeader").innerHTML = "Error";
    document.getElementById("messageContentConflictHeader").style.color = "#FF0000";
} else if(urlParams.get("conflict") == "facility") {
    document.getElementById("conflictMsg").style.display = "flex";
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("messageContentConflict").innerHTML = "The facility is already reserved at this time.";
    document.getElementById("messageContentConflictHeader").innerHTML = "Conflict Detected";
    document.getElementById("messageContentConflictHeader").style.color = "#FF0000";
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
    window.location.href = window.location.href;

}

function openUpdateNOF() {
    let form = document.getElementById("updateStatusForm");
    if (form.checkValidity()) {
        document.getElementById("updateModal").style.display = "block";
        document.getElementById("notificationModal").style.display = "block";
    } else {
        alert("Please fill out all required fields before submitting.");
    }
}

function closeUpdateNOF() {
    document.getElementById("updateModal").style.display = "none";
    document.getElementById("notificationModal").style.display = "none";
}

function submitUpdateForm() {
    let form = document.getElementById("updateStatusForm");
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    var status = document.getElementById("status-value").value;
    if(status=="Approved" || status=="Not Available" || status=="Lapsed"){
        document.getElementById("status-value").setAttribute("disabled", "true");
        document.getElementById("facility-value").setAttribute("disabled", "true");
    }
});

function openCancelRequestNOF() {
    document.getElementById("cancellationModal").style.display = "block";
    document.getElementById("notificationModal").style.display = "block"
}

function closeCancelRequestNOF() {
    document.getElementById("cancellationModal").style.display = "none";
    document.getElementById("notificationModal").style.display = "none"
}

function cancelRequestNOF(requestID){
    const formData = new FormData();
    formData.append("requestID", requestID);
    fetch("Database/nof_cancelReservation.php", {
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