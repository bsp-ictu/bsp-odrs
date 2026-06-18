var currentPass = "";
var newPass = "";
var confirmPass = "";

function showPasswordInput(showPassIcon, hidePassIcon, passInputPlaceholder){
    document.getElementById(showPassIcon).style.display = 'none';
    document.getElementById(hidePassIcon).style.display = 'block';

    document.getElementById(passInputPlaceholder).type = 'text';
}

function hidePasswordInput(hidePassIcon, showPassIcon, passInputPlaceholder){
    document.getElementById(hidePassIcon).style.display = 'none';
    document.getElementById(showPassIcon).style.display = 'block';

    document.getElementById(passInputPlaceholder).type = 'password';
}

function proceedUpdatePassword(){
    let updatePasswordData = new FormData();

    updatePasswordData.append("currentPass", currentPass);
    updatePasswordData.append("newPass", newPass);
    updatePasswordData.append("confirmPass", confirmPass);

    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById('changePassword').style.display = 'none';
    
    fetch("../User/Database/process-updatePassword.php", {
        method: "POST",
        body: updatePasswordData
    })
    .then(async response => {
        const text = await response.text();
        console.log("Response Text:", text);  // debug output

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("JSON parse error:", e.message);
            return {}; // safely fallback
        }
    })
    .then(data => {
        if (data.status === "incorrectPassword"){
            document.getElementById('notificationModal').style.display = 'block';
            document.getElementById('incorrectPassword').style.display = "block";
        } else if (data.status === "updateSuccessful"){
            document.getElementById('notificationModal').style.display = 'block';
            document.getElementById('successfulUpdatePassword').style.display = "block";
        }

        document.getElementById('currentPass').value = "";
        document.getElementById('newPass').value = "";
        document.getElementById('confirmPass').value = "";
    })
    .catch(error => console.error("Error:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("accountFormSettings").addEventListener("submit", function (event) {
        event.preventDefault(); 

        currentPass = document.getElementById('currentPass').value;
        newPass = document.getElementById('newPass').value;
        confirmPass = document.getElementById('confirmPass').value;

        if (currentPass === "" || newPass === "" || confirmPass === "") {
            return;
        } else if (currentPass === newPass) {
            document.getElementById('notificationModal').style.display = 'block';
            document.getElementById('sameOldNewPassword').style.display = 'block';

            return;
        } else if (newPass !== confirmPass) {
            document.getElementById('notificationModal').style.display = 'block';
            document.getElementById('differentConfirmationPassword').style.display = 'block';

            return;
        }

        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('changePassword').style.display = 'block';
    });
});