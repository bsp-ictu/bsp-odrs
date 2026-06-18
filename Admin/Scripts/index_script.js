function changeFrame(request_id) {
    if(request_id.includes("VRF")){
        window.location.href = `vrf_Frame.php?request_id=${request_id}`;
    }
    else {
        window.location.href = `nof_Frame.php?request_id=${request_id}`;
    }
}