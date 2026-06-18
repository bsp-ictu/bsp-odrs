let verificationCode = "";
let userEmailForReset = "";

function showPassword(showPassIcon, hidePassIcon, passPlaceholder) {
  document.getElementById(showPassIcon).style.display = "none";
  document.getElementById(hidePassIcon).style.display = "block";
  document.getElementById(passPlaceholder).type = "text";
}

function hidePassword(hidePassIcon, showPassIcon, passPlaceholder) {
  document.getElementById(hidePassIcon).style.display = "none";
  document.getElementById(showPassIcon).style.display = "block";
  document.getElementById(passPlaceholder).type = "password";
}

function openForgotPassword() {
  document.getElementById("forgotPassword").style.display = "flex";
  document.getElementById("verificationContainer").style.display = "block";
}

function moveToNextInput(input, index) {
  const inputs = document.querySelectorAll(".codeInput");
  if (input.value.length === 1 && index < 3) {
    inputs[index + 1].focus();
  }
}

function moveToPreviousInput(event, index) {
  const inputs = document.querySelectorAll(".codeInput");
  if (event.key === "Backspace" && inputs[index].value === "" && index > 0) {
    event.preventDefault();
    inputs[index - 1].focus();
  }
}

function cancelForgotPassword(modalId) {
  document.getElementById("currentVerifyEmail").value = "";
  document.getElementById(modalId).style.display = "none";
  document.getElementById("forgotPassword").style.display = "none";
}

function closeErrorModal(errorId) {
  document.getElementById(errorId).style.display = "none";
}

document.getElementById("verifyAccount").addEventListener("submit", function (event) {
  event.preventDefault();
  let currentEmail = document.getElementById("currentVerifyEmail").value;
  userEmailForReset = currentEmail;

  console.log("Verifying email:", currentEmail);

  let verifyAccount = new FormData();
  verifyAccount.append("verifyEmail", currentEmail);

  fetch("Database/process-verifyEmail.php", {
    method: "POST",
    body: verifyAccount,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Verify Email Response:", data);

      if (data.status === "accountDoesNotExists") {
        document.getElementById("noAccountFound").style.display = "flex";
      } else if (data.status === "success") {
        verificationCode = data.code;
        userEmailForReset = data.email;
        document.getElementById("verificationContainer").style.display = "none";
        document.getElementById("uniqueCode").style.display = "block";
      } else {
        console.warn("Unexpected response:", data.message || data);
      }
    })
    .catch((error) => console.error("Verify email fetch error:", error));
});

document.getElementById("verifyCode").addEventListener("submit", function (event) {
  event.preventDefault();

  let codeInput = "";
  const inputs = document.querySelectorAll(".codeInput");

  inputs.forEach((input) => {
    codeInput += input.value;
  });

  if (codeInput === verificationCode) {
    document.getElementById("uniqueCode").style.display = "none";
    document.getElementById("newPassword").style.display = "block";
  } else {
    document.getElementById("incorrectCode").style.display = "flex";
  }
});

document.getElementById("createPassword").addEventListener("submit", function (event) {
  event.preventDefault();

  let newPass = document.getElementById("createNewPassword").value;
  let confirmPass = document.getElementById("confirmNewPassword").value;

  if (newPass !== confirmPass) {
    document.getElementById("passwordDoesNotMatch").style.display = "flex";
    return;
  }

  let changePassword = new FormData();
  changePassword.append("newPassword", confirmPass);
  changePassword.append("verifyEmail", userEmailForReset);

  fetch("Database/process-forgotPassword.php", {
    method: "POST",
    body: changePassword,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Create Password Response:", data);

      switch (data.status) {
        case "updateSuccessful":
          document.getElementById("newPassword").style.display = "none";
          document.getElementById("newPasswordSuccessful").style.display = "flex";
          break;

        case "accountDoesNotExists":
        case "missingData":
        case "updateFailed":
          console.error("Password update failed:", data);
          alert("Password update failed: " + (data.debug || data.error || data.status));
          break;

        default:
          console.warn("Unhandled response:", data);
          break;
      }
    })
    .catch((error) => {
      console.error("Create password fetch error:", error);
      alert("Error submitting password update. Check your internet or server.");
    });
});

window.onload = function () {
  const urlParameter = new URLSearchParams(window.location.search);

  if (urlParameter.has("incorrect-email-or-password")) {
    document.getElementById("errorLogin").style.display = "flex";
  } else if (urlParameter.has("account-does-not-exist")) {
    document.getElementById("errorAccount").style.display = "flex";
  } else if (urlParameter.has("login-attempts-exceeded")) {
    console.log("imcALLED");
    document.getElementById("exceedLoginAttempt").style.display = "flex";
  }

  document.getElementById("confirmNewPassword").type = "password";
  document.getElementById("showConfirmPasswordIcon").style.display = "block";
  document.getElementById("hideConfirmPasswordIcon").style.display = "none";
};
