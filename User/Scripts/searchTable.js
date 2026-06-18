function searchTableContent(){
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchTableSummary");
    filter = input.value.toUpperCase();
    table = document.getElementById("requestTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {  
        let rowMatch = false; 

        td = tr[i].getElementsByTagName("td");  

        for (j = 0; j < td.length; j++) {  
        if (td[j]) {
            txtValue = td[j].textContent || td[j].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
            rowMatch = true;  
            break;  
            }
        }
        }

        tr[i].style.display = rowMatch ? "" : "none";
    }
}

function filterByYear(value) { 
    let filter = value.toUpperCase(); 
    let table = document.getElementById("requestTable");
    let tr = table.getElementsByTagName("tr");
    
    console.log(filter);
    for (let i = 0; i < tr.length; i++) { 
        let rowmatch = false;
        let td = tr[i].getElementsByTagName("td")[0]; 
    
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().includes(filter)) {
                rowmatch = true;
            }
        }
        tr[i].style.display = rowmatch ? "" : "none";
    }    
}