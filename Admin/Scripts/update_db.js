function confirmUpdate() {
    let form = document.getElementById("vrf-form");
    
    if (form.checkValidity()) {
        form.submit();
    } else {
        alert("Please fill out all required fields before submitting.");
    }
}

function execute() {
    var id = document.getElementById("req-id");
    var filtered = id.value.toUpperCase();
    console.log(filtered);
}