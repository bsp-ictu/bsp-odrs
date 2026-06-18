function validateAndSave(saveIcon, editingData) {
    let row = saveIcon.closest("tr");
    let emailInput = row.cells[1].querySelector("input");
    let roleInput = row.cells[2].querySelector("select");

    if (editingData === "accountInfo") {
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(emailInput.value)) {
            alert("Please enter a valid email address.");
            emailInput.focus();
            return;
        }
        else if(roleInput.value === ""){
            alert("Please enter an account role.");
            roleInput.focus();
            return;
        }
    }

    addtoDB(editingData, saveIcon);
}

function addRow(tableId, editingData, colCount) {
    let table = document.getElementById(tableId).querySelector("tbody");
    let newRow = table.insertRow();

    for (let i = 0; i < colCount; i++) {
        let cell = newRow.insertCell(i);
        
        if (editingData === "accountInfo" && i === 2) {
            cell.innerHTML = `
                <select style="border: 2px solid #044f12; border-radius: 4px; font-size: 14px; outline: none; width: 100%; background: transparent;">
                    <option value="" disabled selected>Role</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                    <option value="GuestUser">Guest User</option>
                    <option value="Superadmin">Superadmin</option>
                </select>`;
        }else if(editingData==="accountInfo" && i === 3){
            cell.innerHTML = `
                <select style="border: 2px solid #044f12; border-radius: 4px; font-size: 14px; outline: none; width: 100%; background: transparent;">
                    <option value="" disabled selected>Division</option>
                    <option value="Administration"}>Administration</option>
                    <option value="CPSMO"}>CPSMO</option>
                    <option value="ONP"}>ONP</option>
                    <option value="Finance">Finance</option>
                    <option value="FOD">FOD</option>
                    <option value="IAO">IAO</option>
                    <option value="Legal Services Office">Legal Services Office</option>
                    <option value="PMDD">PMDD</option>
                    <option value="OSG">OSG</option>
                </select>`;
        } 
        else {
            let inputType = (editingData === "accountInfo" && i === 1) ? "email" : "text";
            cell.innerHTML = `<input type="${inputType}" placeholder="Enter Details" 
                style="border: 2px solid #044f12; border-radius: 4px; font-size: 14px; outline: none; width: 100%; background: transparent;">`;
        }
    }
    
    let editCell = newRow.insertCell(colCount);
    editCell.innerHTML = `<span class="edit-icon tooltip" onclick="editRow(this)"><span class="tooltiptext">Edit</span></span>`;

    let saveCell = newRow.insertCell(colCount + 1);
    let deleteCell = newRow.insertCell(colCount + 2);

    let resetCell = newRow.insertCell(colCount + 2);
    deleteCell = newRow.insertCell(colCount + 3)

    editCell.innerHTML = `<span class="edit-icon tooltip"><span class="tooltiptext">Edit</span></span>`;
    saveCell.innerHTML = `<span class="save-icon tooltip" onclick="validateAndSave(this, 'accountInfo')"><span class="tooltiptext">Save</span></span>`;
    resetCell.innerHTML = `<span class="reset-icon tooltip"><span class="tooltiptext">Reset</span></span>`;
    deleteCell.innerHTML = `<span class="delete-icon tooltip" onclick="deleteNewRow(this)"><span class="tooltiptext">Delete</span></span>`;
    newRow.scrollIntoView({ behavior: "smooth", block: "start" });
}

function editRow(span) {
    let row = span.closest("tr"); 
    let cells = row.querySelectorAll("td"); 

    for (let i = 0; i < 5; i++) {
        let input = cells[i].querySelector("input");
        if (input) {
            input.disabled = false;
            input.style.border = "2px solid #044f12";
            input.style.borderRadius = "4px";
        }
    }

    let roleCell = cells[3];
    let currentRole = roleCell.querySelector("input").value;

    roleCell.innerHTML = `
        <select style="border: 2px solid #044f12; border-radius: 4px; font-size: 14px; outline: none; width: 100%; background: transparent;">
            <option value="Admin" ${currentRole === "Admin" ? "selected" : ""}>Admin</option>
            <option value="User" ${currentRole === "User" ? "selected" : ""}>User</option>
            <option value="GuestUser" ${currentRole === "GuestUser" ? "selected" : ""}>Guest User</option>
            <option value="Superadmin" ${currentRole === "Superadmin" ? "selected" : ""}>Superadmin</option>
        </select>
    `;

    let divCell = cells[4];
    let currentDiv = divCell.querySelector("input").value;

    divCell.innerHTML = `
        <select style="border: 2px solid #044f12; border-radius: 4px; font-size: 14px; outline: none; width: 100%; background: transparent;">
            <option value="Administration" ${currentDiv === "Administration" ? "selected" : ""}>Administration</option>
            <option value="CPSMO" ${currentDiv === "CPSMO" ? "selected" : ""}>CPSMO</option>
            <option value="ONP" ${currentDiv === "ONP" ? "selected" : ""}>ONP</option>
            <option value="Finance" ${currentDiv === "Finance" ? "selected" : ""}>Finance</option>
            <option value="FOD" ${currentDiv === "FOD" ? "selected" : ""}>FOD</option>
            <option value="IAO" ${currentDiv === "IAO" ? "selected" : ""}>IAO</option>
            <option value="Legal Services Office" ${currentDiv === "Legal Services Office" ? "selected" : ""}>Legal Services Office</option>
            <option value="PMDD" ${currentDiv === "PMDD" ? "selected" : ""}>PMDD</option>
            <option value="OSG" ${currentDiv === "OSG" ? "selected" : ""}>OSG</option>
        </select>
    `;
}

async function saveRow(editingData, span) {
    try {
        await openConfirmMsg("save");
        
        let row = span.parentElement.parentElement;
        let values = getRowValues(row);

        let inputCells = row.querySelectorAll("td input");
        inputCells.forEach(input => {
            input.disabled = true;
            input.style.border = "none";
        });
        window.location.href = `../Admin/Database/editData_db.php?type=accountInfo&action=save&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}&editingData4=${values[3]}&editingData5=${values[4]}`;
        console.log("Row values:", values);
    } 
    catch (error) {
        console.log("User canceled action.");
    }
}

async function resetRow(editingData, span) {
    try {
        await openResetPassword();
        
        let row = span.parentElement.parentElement;
        let values = getRowValues(row);

        let inputCells = row.querySelectorAll("td input");
        inputCells.forEach(input => {
            input.disabled = true;
            input.style.border = "none";
        });
        window.location.href = `../Admin/Database/editData_db.php?type=accountInfo&action=reset&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}`;
        console.log("Row values:", values);
    } 
    catch (error) {
        console.log("User canceled action.");
    }
}

async function deleteRow(editingData, span) {
    try{
        await openConfirmMsg("delete");
        let row = span.parentElement.parentElement;
        let values = getRowValues(row);
        window.location.href= "../Admin/Database/editData_db.php?type=accountInfo&action=delete&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData3="+values[2]+"&editingData4="+values[3];
    }
    catch(error) {
        console.log("User canceled action.");
    }
}

function deleteNewRow(span) {
    let row = span.parentElement.parentElement;
    row.remove();
}

function getRowValues(row) {
    let cells = row.querySelectorAll("td");
    let values = [];

    cells.forEach(cell => {
        let input = cell.querySelector("input");
        let select = cell.querySelector("select");

        if (input) {
            values.push(input.value.trim());
        } else if (select) {
            values.push(select.value.trim());
        }
    });
    return values;
}

  

async function addtoDB(editingData, span) {
    try{
        await openConfirmMsg("add");

        let row = span.parentElement.parentElement;
        let values = getRowValues(row);
 
        let inputCells = row.querySelectorAll("td input, td select");
        inputCells.forEach(input => {
            input.disabled = true; 
            input.style.border = "none";
        });
        window.location.href= "../Admin/Database/editData_db.php?type=accountInfo&action=add&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData4="+values[2]+"&editingData5="+values[3];
    }
    catch(error){
        console.log("User canceled action.");
    }
}

function openResetPassword(){
    return new Promise((resolve, reject) => {
        document.getElementById("notificationModal").style.display = "flex";
        document.getElementById("resetPasswordModal").style.display = "block";

        document.getElementById("actionBtn").onclick = function () {
            closeConfirmMsg();
            resolve(true);
        };

        document.getElementById("cancelBtn").onclick = function () {
            closeConfirmMsg();
            reject(false);
        };
    });
}

function openConfirmMsg(action) {
    return new Promise((resolve, reject) => {
        document.getElementById("confirmationModal").style.display = "block";
        document.getElementById("notificationModal").style.display = "block";

        if (action === "save") {
            document.getElementById("actionConfirmation").innerText = "save this entry?";
            document.getElementById("actionBtn").innerText = "Save";
        } 
        else if (action === "delete") {
            document.getElementById("actionConfirmation").innerText = "delete this entry?";
            document.getElementById("actionBtn").innerText = "Delete";
        } 
        else if (action === "add") {
            document.getElementById("actionConfirmation").innerText = "add an entry?";
            document.getElementById("actionBtn").innerText = "Add";
        }
        else if (action === "reset") {
            document.getElementById("actionConfirmation").innerText = "reset the selected user's password?";
            document.getElementById("actionBtn").innerText = "Reset";
        }

        document.getElementById("actionBtn").onclick = function () {
            closeConfirmMsg();
            resolve(true);
        };

        document.getElementById("cancelBtn").onclick = function () {
            closeConfirmMsg();
            reject(false);
        };
    });
}

function closeConfirmMsg() {
    document.getElementById("confirmationModal").style.display = "none";
    document.getElementById("notificationModal").style.display = "none";
    window.location.href="editAccount.php";
}

function removeQueryParams(params) {
    const url = new URL(window.location);
    params.forEach(param => url.searchParams.delete(param));
    history.replaceState({}, document.title, url.toString());
}

const urlParams = new URLSearchParams(window.location.search);
const isSaved = urlParams.get('save');
const isDeleted = urlParams.get('delete');
const isAdded = urlParams.get('add');
const isReset = urlParams.get('reset');

if(isSaved == "success"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/check.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Your changes have been saved successfully.";
}
else if(isSaved == "failed"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/remove.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Failed to save the entry. Please try again.";
}
else if(isDeleted == "success"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/check.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Selected data has been removed.";
}
else if(isDeleted == "failed"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/remove.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Failed to delete the entry. Please try again.";
}
else if(isAdded == "success"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/check.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "A new record has been successfully created!";
}
else if(isAdded == "failed"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/check.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Account password has been successfully reset!";
}
else if(isReset == "success"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/check.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Password has been successfully reset.";
}
else if(isReset == "failed"){
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("statusModal").style.display = "block";
    document.getElementById("statusModalIcon").style.background = "url('../Images/remove.png')";
    document.getElementById("statusModalIcon").style.backgroundRepeat = "no-repeat";
    document.getElementById("statusModalIcon").style.backgroundPosition = "center";
    document.getElementById("statusModalIcon").style.backgroundSize = "contain";
    document.getElementById("contentStatusModal").innerHTML = "Failed to reset account password. Please try again.";
}

function openUploadPhotoForm(){
    document.getElementById('uploadPhoto').style.display = 'block';
}

function closeUploadPhotoForm(){
    document.getElementById('uploadPhoto').style.display = 'none';
}

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

function closeResetPassword(){
    document.getElementById('notificationModal').style.display = 'none';
    document.getElementById("resetPasswordModal").style.display = "none";
}

document.getElementById('imageFileInput').addEventListener("change", function(){
    let fileInput = document.getElementById('imageFileInput').files[0]; 

    if ((fileInput.type == "image/jpeg") || (fileInput.type == "image/jpg") || (fileInput.type == "image/png")){
        let fileName = fileInput.name;

        let fileSize = "";
        if (fileInput.size < 1e3 ){
            fileSize = `${fileInput.size} bytes`;
        } else if ((fileInput.size >= 1e3) && (fileInput.size < 1e6)){
            fileSize = `${(fileInput.size / 1e3).toFixed(1)} KB`;
        } else {
            fileSize = `${(fileInput.size / 1e6).toFixed(1)} MB`;
        }

        document.getElementById('fileName').value = fileName;
        document.getElementById('fileSize').value = fileSize;
    } else {
        document.getElementById('fileName').value = "Invalid file type input";
        document.getElementById('fileSize').value = "Invalid file type input";
    }
});

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
    
    fetch("../Admin/Database/update_password.php", {
        method: "POST",
        body: updatePasswordData
    })
    .then(async response => {
        const text = await response.text();
        console.log("Response Text:", text);

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("JSON parse error:", e.message);
            return {};
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

function limitInputLength(el) {
  if (el.value.length > 2) {
    el.value = el.value.slice(0, 2);
  }
}