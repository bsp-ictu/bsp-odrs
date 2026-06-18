const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

const weekdayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

let currentDate = new Date();
let today = new Date(); 

let approvedReservations = [];

async function fetchReservations() {
    try {
        const response = await fetch(`../Admin/Database/admin_calendarMarkings.php`); 
        const data = await response.json();
        console.log(data);
        
        approvedReservations = data.map(res => ({
            date: new Date(res.date),
            type: res.type
        }));

        console.log(approvedReservations);
        
        renderCalendar();
    } catch (error) {
        renderCalendar()
    }
}

function renderCalendar() {
    const monthYearText = document.getElementById("currentMonthYear");
    const daysContainer = document.querySelector(".calendarDays");
    const weekdaysContainer = document.querySelector(".calendarWeekdays");

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    monthYearText.textContent = `${monthNames[month]} ${year}`;

    daysContainer.innerHTML = "";
    weekdaysContainer.innerHTML = "";

    weekdayNames.forEach(day => {
        const dayDiv = document.createElement("div");
        dayDiv.textContent = day;
        weekdaysContainer.appendChild(dayDiv);
    });

    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const emptyDiv = document.createElement("div");
        daysContainer.appendChild(emptyDiv);
    }

    for (let day = 1; day <= lastDate; day++) {
        const dayDiv = document.createElement("div");
        dayDiv.textContent = day;
        dayDiv.classList.add("day");

        let currentDay = new Date(year, month, day);

        let reservation = approvedReservations.filter(res => 
            res.date.getFullYear() === currentDay.getFullYear() &&
            res.date.getMonth() === currentDay.getMonth() &&
            res.date.getDate() === currentDay.getDate()
        );
        
        let hasVehicle = reservation.some(res => res.type === "Vehicle Reservation");
        let hasFacility = reservation.some(res => res.type === "Facility Reservation");

        if (hasVehicle && hasFacility) {
            dayDiv.classList.add("both");
            console.log('both');
        } else if (hasVehicle) {
            dayDiv.classList.add("vrf");
            console.log('vrf');
        } else if (hasFacility) {
            dayDiv.classList.add("nof");
            console.log('nof');
        }

        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
            dayDiv.classList.add("today"); 
        }

        daysContainer.appendChild(dayDiv);
    }
}

document.getElementById("previousMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

fetchReservations();
