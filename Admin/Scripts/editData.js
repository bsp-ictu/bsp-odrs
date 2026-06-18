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
        
        if (editingData === "divisionOffice" && (i === 2 || i === 3)) {
            cell.style.textAlign = "center";
            cell.innerHTML = `<input type="checkbox">`;
        } else {
            let inputType = (editingData === "accountInfo" && i === 1) ? "email" : "text";
            cell.innerHTML = `<input type="${inputType}" placeholder="Enter Details" 
                style="border: 2px solid #044f12; border-radius: 4px; font-size: 18px; outline: none; width: 100%; background: transparent;">`;
        }       
    }
    
    let editCell = newRow.insertCell(colCount);
    editCell.innerHTML = `<span class="edit-icon tooltip" onclick="editRow(this)"><span class="tooltiptext">Edit</span></span>`;

    let saveCell = newRow.insertCell(colCount + 1);
    let deleteCell = newRow.insertCell(colCount + 2);

    if(editingData == "divisionOffice"){
        saveCell.innerHTML = `<span class="save-icon tooltip" onclick="addtoDB('divisionOffice',this)"><span class="tooltiptext">Save</span></span>`;
        deleteCell.innerHTML = `<span class="delete-icon tooltip" onclick="deleteNewRow(this)"><span class="tooltiptext">Delete</span></span>`;
    }
    else if(editingData == "vehicleInfo"){
        saveCell.innerHTML = `<span class="save-icon tooltip" onclick="addtoDB('vehicleInfo', this)"><span class="tooltiptext">Save</span></span>`;
        deleteCell.innerHTML = `<span class="delete-icon tooltip" onclick="deleteNewRow(this)"><span class="tooltiptext">Delete</span></span>`;
    }
    else if(editingData == "accountInfo"){
        let resetCell = newRow.insertCell(colCount + 2);
        deleteCell = newRow.insertCell(colCount + 3)

        editCell.innerHTML = `<span class="edit-icon tooltip"><span class="tooltiptext">Edit</span></span>`;
        saveCell.innerHTML = `<span class="save-icon tooltip" onclick="validateAndSave(this, 'accountInfo')"><span class="tooltiptext">Save</span></span>`;
        resetCell.innerHTML = `<span class="reset-icon tooltip"><span class="tooltiptext">Reset</span></span>`;
        deleteCell.innerHTML = `<span class="delete-icon tooltip" onclick="deleteNewRow(this)"><span class="tooltiptext">Delete</span></span>`;
    }
    else{
        saveCell.innerHTML = `<span class="save-icon tooltip" onclick="addtoDB('driverInfo', this)"><span class="tooltiptext">Save</span></span>`;
        deleteCell.innerHTML = `<span class="delete-icon tooltip" onclick="deleteNewRow(this)"><span class="tooltiptext">Delete</span></span>`;
    }
    newRow.scrollIntoView({ behavior: "smooth", block: "start" });
}

function editRow(span) {
    let row = span.parentElement.parentElement;
    let cells = row.querySelectorAll("td");

    for (let i = 0; i < 2; i++) {
        let input = cells[i].querySelector("input");
        if (input) {
            input.disabled = false;
            input.style.border = "2px solid #044f12";
            input.style.borderRadius = "4px";
        }
    }
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

        if (editingData == "divisionOffice") {
            window.location.href = `../Admin/Database/editData_db.php?type=divOffice&action=save&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}&editingData4=${values[3]}&editingData5=${values[4]}`;
        } else if (editingData == "vehicleInfo") {
            window.location.href = `../Admin/Database/editData_db.php?type=vehicleInfo&action=save&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}`;
        } else if (editingData == "accountInfo") {
            window.location.href = `../Admin/Database/editData_db.php?type=accountInfo&action=save&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}`;
        } else {
            window.location.href = `../Admin/Database/editData_db.php?type=driverInfo&action=save&editingData1=${values[0]}&editingData2=${values[1]}`;
        }

        console.log("Row values:", values);
    } 
    catch (error) {
        console.log("User canceled action.");
    }
}

async function resetRow(editingData, span) {
    try {
        await openConfirmMsg("reset");
        
        let row = span.parentElement.parentElement;
        let values = getRowValues(row);

        let inputCells = row.querySelectorAll("td input");
        inputCells.forEach(input => {
            input.disabled = true;
            input.style.border = "none";
        });

        if (editingData == "accountInfo") {
            window.location.href = `../Admin/Database/editData_db.php?type=accountInfo&action=reset&editingData1=${values[0]}&editingData2=${values[1]}&editingData3=${values[2]}`;
        }

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
        if(editingData == "divisionOffice"){
            row.remove();
            window.location.href= "../Admin/Database/editData_db.php?type=divOffice&action=delete&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData3="+values[2];
        }
        else if(editingData == "vehicleInfo"){
            window.location.href= "../Admin/Database/editData_db.php?type=vehicleInfo&action=delete&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData3="+values[2];
        }
        else if(editingData == "accountInfo"){
            window.location.href= "../Admin/Database/editData_db.php?type=accountInfo&action=delete&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData3="+values[2]+"&editingData4="+values[3];
        }
        else{
            window.location.href= "../Admin/Database/editData_db.php?type=driverInfo&action=delete&editingData1="+values[0]+"&editingData2="+values[1];
        }
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
            if (input.type === "checkbox") {
                values.push(input.checked);
            } else {
                values.push(input.value.trim());
            }
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

        if(editingData == "divisionOffice"){
            window.location.href= "../Admin/Database/editData_db.php?type=divOffice&action=add&editingData1="+values[0]+"&editingData2="+values[1];
        }
        else if(editingData == "vehicleInfo"){
            window.location.href= "../Admin/Database/editData_db.php?type=vehicleInfo&action=add&editingData1="+values[0]+"&editingData2="+values[1];
        }
        else if(editingData == "accountInfo"){
            window.location.href= "../Admin/Database/editData_db.php?type=accountInfo&action=add&editingData1="+values[0]+"&editingData2="+values[1]+"&editingData4="+values[2];
        }
        else if(editingData == "driverInfo"){
            window.location.href= "../Admin/Database/editData_db.php?type=driverInfo&action=add&editingData1="+values[0];
        }
    }
    catch(error){
        console.log("User canceled action.");
    }
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
    window.location.href="editData.php";
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

function setupSingleColumnCheckbox(groupClass) {
      const checkboxes = document.querySelectorAll('.' + groupClass);

      checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
          if (this.checked) {
            // Uncheck others in the same column
            checkboxes.forEach(other => {
              if (other !== this) {
                other.checked = false;
              }
            });
          }
        });
      });
    }
setupSingleColumnCheckbox('vrfSelected');
setupSingleColumnCheckbox('facilitiesSelected');