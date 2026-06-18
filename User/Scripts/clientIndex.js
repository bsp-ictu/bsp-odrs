
document.addEventListener('wheel', function(event) {
    if (event.ctrlKey) {
        event.preventDefault();
    }
}, { passive: false });

document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && (event.key === '+' || event.key === '-' || event.key === '0')) {
        event.preventDefault();
    }
});

window.onload = function(){
    const urlParameter = new URLSearchParams(window.location.search);

    if (urlParameter.has('request-submitted-successfully')){
        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('successfulRequest').style.display = 'block';
    } else if (urlParameter.has('request-submitted-unsuccessfully')){
        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('unsuccessfulRequest').style.display = 'block';
    } else if (urlParameter.has('cancellation-success')){
        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('successfulCancellation').style.display = 'block';
    } else if (urlParameter.has('cancellation-failed')){
        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('usuccessfulCancellation').style.display = 'block';
    } else if (urlParameter.has('facility-not-available')){
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const purpose = urlParams.get('purpose');
        const numberOfPersons = urlParams.get('numberOfPersons');
        const requesterDivision = urlParams.get('division');
        const requesterName = urlParams.get('requesterName');
        const requesterChief = urlParams.get('divisionChief');
    
        document.getElementById('facilityPurpose').value =purpose;
        document.getElementById('numOfPerson').value =numberOfPersons;
        document.getElementById('division_2').value =requesterDivision;
        document.getElementById('facilityRequester').value =requesterName;
        document.getElementById('chief_2').value =requesterChief;

        openUseOfNationalOfficeForm();
        document.getElementById('notificationModal').style.display = 'block';
        document.getElementById('unavailableFacility').style.display = 'block';
    }
}