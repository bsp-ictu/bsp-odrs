function openStatusModal() {
    toggleModal("updateModal", true);
}

function openStatusConfirmationModal() {
    toggleModal("statusConfirmationModal", true);
}

function closeStatusConfirmationModal() {
    toggleModal("statusConfirmationModal", false);
}

function closeModal() {
    toggleModal("updateModal", false);
}

function openVehicleSchedModal() {
    toggleModal("vehicleSchedModal", true);
}

function closeVehicleSchedModal() {
    toggleModal("vehicleSchedModal", false);
}

function openFacilitySchedModal() {
    toggleModal("facilitySchedModal", true);
}

function closeFacilitySchedModal() {
    toggleModal("facilitySchedModal", false);
}

function closeSuccessfulNotification() {
    toggleModal("successfulRequest", false);
}

function closeUnsuccessfulNotification() {
    toggleModal("unsuccessfulRequest", false);
}

function closeLogoutConfirmation() {
    toggleModal("logOutConfirmation", false);
}

function toggleModal(modalId, show) {
    let modal = document.getElementById(modalId);
    let overlay = document.getElementById("modalOverlay");

    if (modal) modal.style.display = show ? "block" : "none";
    if (overlay) overlay.style.display = show ? "block" : "none";
}

function openLogoutConfirmation() {
    document.getElementById("notificationModal").style.display ="block";
    document.getElementById("logOutConfirmation").style.display ="block";
}

function closeLogoutConfirmation() {
    document.getElementById("notificationModal").style.display ="none";
    document.getElementById("logOutConfirmation").style.display ="none";
}

function openStatusUpdate() {
    document.getElementById("notificationModal").style.display ="block";
    document.getElementById("statusModalForm").style.display ="block";
}

function closeStatusUpdate() {
    document.getElementById("notificationModal").style.display ="none";
    document.getElementById("statusModalForm").style.display ="none";
}