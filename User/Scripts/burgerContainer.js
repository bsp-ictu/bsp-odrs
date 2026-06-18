function openBurgerMenu() {
    document.getElementById("burgerMenuModal").classList.add("show");
    document.getElementById("burgerMenuModal").classList.remove("hide");
}

function closeBurgerMenu() {
    document.getElementById("burgerMenuModal").classList.add("hide");
    document.getElementById("burgerMenuModal").classList.remove("show");
}

function burgerMainHome(){
    closeBurgerMenu();
    document.getElementById("settingsMainContainer").style.display = "none";
    document.getElementById("clientCalendarMainContainer").style.display = "flex";
    document.getElementById("clientTableContainer").style.display = "block";
    document.getElementById("reservedDatesContainer").style.display = "block";

    document.getElementById("burgerAccount").classList.remove("toggle");
    document.getElementById("burgerAccount").classList.add("untoggle");
    
    document.getElementById("burgerHomeMain").classList.remove("untoggle");
    document.getElementById("burgerHomeMain").classList.add("toggle");
}

function burgerHome(){
    closeBurgerMenu();
    document.getElementById("settingsMainContainer").style.display = "none";
    document.getElementById("clientCalendarMainContainer").style.display = "flex";

    document.getElementById("reservedDatesContainer").style.display = "none";
    document.getElementById("clientTableContainer").style.display = "block";

    document.getElementById("burgerCalendar").classList.remove("toggle");
    document.getElementById("burgerCalendar").classList.add("untoggle");

    document.getElementById("burgerAccount").classList.remove("toggle");
    document.getElementById("burgerAccount").classList.add("untoggle");

    document.getElementById("burgerHome").classList.remove("untoggle");
    document.getElementById("burgerHome").classList.add("toggle");
}

function burgerCalendar(){
    closeBurgerMenu();
    document.getElementById("settingsMainContainer").style.display = "none";
    document.getElementById("clientCalendarMainContainer").style.display = "flex";

    document.getElementById("clientTableContainer").style.display = "none";
    document.getElementById("reservedDatesContainer").style.display = "block";

    document.getElementById("burgerHome").classList.remove("toggle");
    document.getElementById("burgerHome").classList.add("untoggle");

    document.getElementById("burgerAccount").classList.remove("toggle");
    document.getElementById("burgerAccount").classList.add("untoggle");

    document.getElementById("burgerCalendar").classList.remove("untoggle");
    document.getElementById("burgerCalendar").classList.add("toggle");
}

function burgerSettings(){
    closeBurgerMenu();
    document.getElementById("clientCalendarMainContainer").style.display = "none";
    document.getElementById("settingsMainContainer").style.display = "block";

    document.getElementById("burgerHomeMain").classList.remove("toggle");
    document.getElementById("burgerHomeMain").classList.add("untoggle");

    document.getElementById("burgerHome").classList.remove("toggle");
    document.getElementById("burgerHome").classList.add("untoggle");

    document.getElementById("burgerCalendar").classList.remove("toggle");
    document.getElementById("burgerCalendar").classList.add("untoggle");

    document.getElementById("burgerAccount").classList.remove("untoggle");
    document.getElementById("burgerAccount").classList.add("toggle");
}

function burgerLogout(){
    closeBurgerMenu();
    document.getElementById("notificationModal").style.display = "block";
    document.getElementById("logOutConfirmation").style.display = "block";
}

document.addEventListener("DOMContentLoaded", function (){
    var clientTable = document.getElementById("clientTableContainer").style.display;

    if (clientTable == "block"){
        document.getElementById("burgerHome").classList.add("toggle");
        document.getElementById("burgerHomeMain").classList.add("toggle");
    }
});