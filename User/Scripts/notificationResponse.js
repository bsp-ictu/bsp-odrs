function closeSuccessfulNotification() {
    document.getElementById("notificationModal").style.display = "none";
    document.getElementById("successfulRequest").style.display = "none";

    const currentUrl = window.location.href; 
    const newUrl = currentUrl.split('?')[0]; 
    window.history.replaceState({}, document.title, newUrl); 
}

function closeUnsuccessfulNotification() {
    document.getElementById("notificationModal").style.display = "none";
    document.getElementById("unsuccessfulRequest").style.display = "none";

    const currentUrl = window.location.href; 
    const newUrl = currentUrl.split('?')[0]; 
    window.history.replaceState({}, document.title, newUrl); 
}

function closeUnavailableNotification() {
    document.getElementById("notificationModal").style.display = "none";
    document.getElementById("unavailableFacility").style.display = "none";
    document.getElementById("chooseForm").style.display = "none";
    document.getElementById("vehicleReservationForm").style.display = "none";
    document.getElementById("useOfNationalOfficeForm").style.display = "none";

    const currentUrl = window.location.href; 
    const newUrl = currentUrl.split('?')[0]; 
    window.history.replaceState({}, document.title, newUrl); 
}

function cancelUpdatePassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('changePassword').style.display = 'none';
}

function closeSamePassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('sameOldNewPassword').style.display = 'none';
}

function closeDifferentPassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('differentConfirmationPassword').style.display = 'none';
}

function closeIncorrectPassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('incorrectPassword').style.display = 'none';
}

function closeSuccessfulUpdatePassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('successfulUpdatePassword').style.display = 'none';
}

function openLogoutConfirmation() {
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("logOutConfirmation").style.display = "block";
}

function closeLogoutConfirmation() {
    document.getElementById("notificationModal").style.display = "none";
    document.getElementById("logOutConfirmation").style.display = "none";
}