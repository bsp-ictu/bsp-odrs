let previousRequests = {
    requestSummaryID: [],
    forCancellationSummaryID: []
};

function playPing() {
    let audio = document.getElementById("pingSound");
    audio.play();
}

function loadRequests(url, tbodyId) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById(tbodyId);
            tbody.innerHTML = "";

            let currentIds = data.map(row => row.request_id);
            let oldIds = previousRequests[tbodyId] || [];

            let newOnes = currentIds.filter(id => !oldIds.includes(id));

            if (newOnes.length > 0 && oldIds.length > 0) {
                playPing();
            }

            previousRequests[tbodyId] = currentIds;

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr style="height: 215px; background-color: unset !important; cursor: default">
                        <td colspan="5" style="text-align: center;">
                            <div class="noRecordsFound">
                                <h4>No records found</h4>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(row => {
                const requestDate = new Date(row.request_date).toLocaleDateString("en-US");
                const reservationDate = new Date(row.reservation_date).toLocaleDateString("en-US");

                const tr = document.createElement("tr");
                tr.setAttribute("onclick", `changeFrame('${row.request_id}')`);
                tr.innerHTML = `
                    <td>${row.request_id}</td>
                    <td>${row.requester_division}</td>
                    <td>${requestDate}</td>
                    <td>${reservationDate}</td>
                `;

                if (newOnes.includes(row.request_id)) {
                    tr.classList.add("new-row");
                }

                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error("Failed to fetch requests:", error);
        });
}


setInterval(() => loadRequests('fetch_pending_requests.php', 'requestSummaryID'), 5000);
setInterval(() => loadRequests('fetch_forCancellation_requests.php', 'forCancellationSummaryID'), 5000);

window.onload = function () {
    loadRequests('fetch_pending_requests.php', 'requestSummaryID');
    loadRequests('fetch_forCancellation_requests.php', 'forCancellationSummaryID');
};