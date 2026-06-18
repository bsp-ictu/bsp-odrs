let passengerCount = 0;
let destinationCount = 0;

function dateNow() {
  const date = new Date();

  const dateFormat = new Intl.DateTimeFormat("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  });
  const newDate = dateFormat.format(date);

  return newDate;
}

function timeNow() {
  const date = new Date();

  const timeFormat = new Intl.DateTimeFormat("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
  const newTime = timeFormat.format(date);

  return newTime;
}

function openChooseForms() {
  document.getElementById("requestModal").style.display = "flex";
  document.getElementById("chooseForm").style.display = "flex";
}

function closeChooseForms() {
  document.getElementById("requestModal").style.display = "none";
  document.getElementById("chooseForm").style.display = "none";
}

function openVehicleReservationForm() {
  document.getElementById("date").textContent = dateNow();
  document.getElementById("time").textContent = timeNow();

  document.getElementById("vehicleReservationForm").style.display = "block";
  document.getElementById("chooseForm").style.display = "none";
}

function closeVehicleReservationForm() {
  document.getElementById("requestModal").style.display = "none";
  document.getElementById("vehicleReservationForm").style.display = "none";
}

function openUseOfNationalOfficeForm() {
  document.getElementById("date2").textContent = dateNow();
  document.getElementById("time2").textContent = timeNow();

  document.getElementById("officeRequestMainContainer").style.display = "flex";
  document.getElementById("chooseForm").style.display = "none";
}

function closeUseOfNationalOfficeForm() {
  document.getElementById("officeRequestMainContainer").style.display = "none";
  document.getElementById("requestModal").style.display = "none";
}

function updateDivisionChiefPlaceholder() {
  fetch(`../User/Database/process-divisionChief.php`)
    .then((response) => response.json())
    .then((data) => {
      var division = document.getElementById("division").value;
      var chiefInput = document.getElementById("chief");

      var divisionChiefs = {};

      data.forEach((item) => {
        divisionChiefs[item.division_name] = item.division_chief;
      });

      chiefInput.value = divisionChiefs[division];
    })
    .catch((error) => console.error("Error fetching division array:", error));
}

function updateDivisionChiefPlaceholder_2() {
  fetch(`../User/Database/process-divisionChief.php`)
    .then((response) => response.json())
    .then((data) => {
      var division = document.getElementById("division_2").value;
      var chiefInput = document.getElementById("chief_2");

      var divisionChiefs = {};

      data.forEach((item) => {
        divisionChiefs[item.division_name] = item.division_chief;
      });

      chiefInput.value = divisionChiefs[division];
    })
    .catch((error) => console.error("Error fetching division array:", error));
}

function addDestination() {
  if (destinationCount < 2) {
    destinationCount++;

    let div = document.createElement("div");
    div.classList.add("inputDestinationName");
    div.setAttribute("id", "destinationNumber" + destinationCount);
    div.innerHTML = `
            <input type="text" name="destination[]" placeholder="Enter destination" required>
            <button type="button" class="removeDestinationButton" onclick="removeDestination(${destinationCount})">-</button>
        `;

    let container = document.getElementById("destinationNameContainer");
    container.insertBefore(div, container.lastElementChild);

    if (destinationCount == 2) {
      document.getElementById("addDestinationButton").style.display = "none";
    }
  }
}

function removeDestination(destinationNumber) {
  let destinationDiv = document.getElementById(
    "destinationNumber" + destinationNumber
  );

  if (destinationDiv) {
    destinationDiv.remove();
    destinationCount--;

    if (destinationCount < 2) {
      document.getElementById("addDestinationButton").style.display = "block";
    }
  }
}

function adjustPassengerPlaceholder() {
  var passengerNumber = document.getElementById("passengerCount").value;

  if (0 < passengerNumber && passengerNumber < 20) {
    if (passengerNumber == passengerCount + 1) {
      console.log("equal");
      return;
    } else if (passengerNumber > passengerCount + 1) {
      console.log("ADD (Current Total)", passengerCount + 1);
      let totalPassengerCount = passengerCount + 1;
      for (let i = totalPassengerCount; i < passengerNumber; i++) {
        console.log("add");
        addPassengerName();
      }
    } else if (passengerNumber < passengerCount + 1) {
      if (passengerNumber > 0) {
        console.log("REMOVE (Current Total)", passengerCount + 1);
        let totalPassengerCount = passengerCount + 1;
        for (let i = totalPassengerCount; i > passengerNumber; i--) {
          console.log("remove");
          removePassengerName(passengerCount);
        }
      }
    }
  } else {
    return;
  }
}

function addPassengerName() {
  if (passengerCount < 18) {
    passengerCount++;

    let div = document.createElement("div");
    div.classList.add("inputPassengerName");
    div.setAttribute("id", "passengerNumber" + passengerCount);
    div.innerHTML = `
            <input type="text" name="passengers[]" placeholder="Enter name" required>
            <button type="button" class="removePassengerButton" onclick="removePassengerName(${passengerCount})">-</button>
        `;

    let container = document.getElementById("passengerNameContainer");
    container.insertBefore(div, container.lastElementChild);

    if (passengerCount == 18) {
      document.getElementById("addNameButton").style.display = "none";
    }

    console.log(passengerCount + 1);
    let currentPassengerCount = document.getElementById("passengerCount").value;

    if (currentPassengerCount != passengerCount + 1) {
      document.getElementById("passengerCount").value = passengerCount + 1;
    }
  }
}

function removePassengerName(passengerNumber) {
  let passengerDiv = document.getElementById(
    "passengerNumber" + passengerNumber
  );

  if (passengerDiv) {
    passengerDiv.remove();
    passengerCount--;

    if (passengerCount < 18) {
      document.getElementById("addNameButton").style.display = "block";
    }

    console.log(passengerCount + 1);
    let currentPassengerCount = document.getElementById("passengerCount").value;

    if (currentPassengerCount != passengerCount + 1) {
      document.getElementById("passengerCount").value = passengerCount + 1;
    }
  }
}

function selectRadio(id) {
  let radio = document.getElementById(id);
  if (radio) {
    radio.checked = true;
    radio.dispatchEvent(new Event("change"));
  }
}

document.getElementById("etdFrStation").addEventListener("change", function () {
  let etdFrStation = this.value;
  document.getElementById("etaToDestination").min = etdFrStation;
});

document.getElementById("etaToDestination").addEventListener("input", function () {
  let etdFrStation = document.getElementById("etdFrStation").value;
  let etaToDestination = this.value;

  if (etaToDestination < etdFrStation) {
    this.value = null;
    alert("Invalid time input!");
  }
});

document.getElementById("etaToDestination").addEventListener("change", function () {
  let etaToDestination = this.value;
  document.getElementById("etdFrDestination").min = etaToDestination;
});

document.getElementById("etdFrDestination").addEventListener("input", function () {
    let etaToDestination = document.getElementById("etaToDestination").value;
    let etdFrDestination = this.value;

    if (etdFrDestination < etaToDestination) {
      this.value = null;
      alert("Invalid time input!");
    }
  });

document.getElementById("etdFrDestination").addEventListener("change", function () {
    let etdFrDestination = this.value;
    document.getElementById("etaToStation").min = etdFrDestination;
  });

document.getElementById("etaToStation").addEventListener("input", function () {
  let etdFrDestination = document.getElementById("etdFrDestination").value;
  let etaToStation = this.value;

  if (etaToStation < etdFrDestination) {
    this.value = null;
    alert("Invalid time input!");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  let today = new Date().toISOString().split("T")[0];
  console.log(today);
  document.querySelectorAll("input[type='date']").forEach((input) => {
    input.min = today;
  });

  const chosenFacility = document.querySelectorAll(
    'input[name="chosenFacility"]'
  );
  const chosenOtherFacility = document.getElementById("chosenOtherFacility");

  chosenFacility.forEach((radio) => {
    radio.addEventListener("change", function () {
      if (this.value === "Others") {
        chosenOtherFacility.removeAttribute("readonly");
        chosenOtherFacility.setAttribute("required", "true");
        chosenOtherFacility.style.borderBottom = "2px solid #044f12";
      } else {
        chosenOtherFacility.setAttribute("readonly", "true");
        chosenOtherFacility.removeAttribute("required");
        chosenOtherFacility.style.borderBottom = "2px solid #928E8E";
        chosenOtherFacility.value = "";
      }
    });
  });
});
