function tableFilter() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("pendingSearch");
    filter = input.value.toUpperCase();
    table = document.getElementById("pendingTable");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        let rowmatch = false;
      td = tr[i].getElementsByTagName("td");
      for (j = 0; j < td.length; j++) {
      if (td[j]) {
        txtValue = td[j].textContent || td[j].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          rowmatch = true;
          break;
        } 
      }
      }
      tr[i].style.display = rowmatch ? "" : "none";      
    }
};

function yearByFilter() {   
    let input = document.getElementById("yearFilter");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("pendingTable");
    let tr = table.getElementsByTagName("tr");
    console.log(input);
    for (let i = 1; i < tr.length; i++) {
        let rowmatch = false;
        let td = tr[i].getElementsByTagName("td")[2];
        console.log(td);      
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().includes(filter)) {
                rowmatch = true;
                console.log(td);
            }
        }
        tr[i].style.display = rowmatch ? "" : "none";
    }
  }