
let reportChart; // global variable
$(document).ready(function () {
    // code here
    cObj("client_usage_report_btn").onclick = function () {
        var err = checkBlank("report_type");
        cObj("report_error_holder").innerHTML = "";
        if (err == 0) {
            fetchGraphData();
        }else{
            cObj("report_error_holder").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
        }
    }
    cObj("select_the_next_date").onchange = function () {
        var select = cObj("select_the_next_date").options;
        var selected = 0;
        for (let id = select.length-1; id > 0 ; id--) {
            const element = select[id];
            selected = element.selected ? id : selected;
        }

        if (selected > 0) {
            // fetch_graph
            fetchGraphData(this.value);
        }
    }

    cObj("report_type").onchange = function () {
        var select = cObj("select_the_next_date").options;
        var selected = 0;
        for (let id = select.length-1; id > 0 ; id--) {
            const element = select[id];
            selected = element.selected ? id : selected;
        }

        if (selected > 0) {
            // fetch graph
            fetchGraphData();
        }
    }

    cObj("report_on").onchange = function () {
        cObj("report_type").options[0].selected = true;
        if (this.value == "data") {
            cObj("report_type").options[1].hidden = true;
        }else{
            cObj("report_type").options[1].hidden = false;
        }
    }
});

function nextDate() {
    var select = cObj("select_the_next_date");
    var selected_index = 0;
    cObj("report_error_holder").innerHTML = "";
    for (let index = 0; index < select.options.length; index++) {
        const element = select.options[index];
        if (element.selected) {
            selected_index = index;
        }
    }

    selected_index = selected_index > 1 ? selected_index - 1 : 1;
    select.options[selected_index].selected = true;

    // get the data
    if(checkBlank("report_type") == 0){
        fetchGraphData(select.options[selected_index].value);
    }else{
        cObj("report_error_holder").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
    }
}

function prevDate() {
    var select = cObj("select_the_next_date");
    var selected_index = select.options.length;
    cObj("report_error_holder").innerHTML = "";
    for (let index = 0; index < select.options.length; index++) {
        const element = select.options[index];
        if (element.selected) {
            selected_index = index;
        }
    }

    // selected_index

    selected_index = select.options.length > (selected_index+1) ? selected_index + 1 : selected_index;
    select.options[selected_index].selected = true;

    // get the data
    if(checkBlank("report_type") == 0){
        fetchGraphData(select.options[selected_index].value);
    }else{
        cObj("report_error_holder").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
    }
}


function fetchGraphData(next_date = null) {
    if (cObj("report_on").value == "bandwidth" && cObj("report_type").value == "Yearly") {
        cObj("navigate_reports").classList.add("d-none");
    }else{
        cObj("navigate_reports").classList.remove("d-none");
    }
    
    cObj("client_usage_report_btn").disabled = true;
        cObj("client_usage_report_btn").classList.add("disabled");
        cObj("report_error_holder").innerHTML = "";
        var datapass = next_date != null ? "&next_date="+next_date : "";

        if (cObj("report_on").value == "bandwidth") {
            var urls = "";
            if(cObj("isTabElem") != undefined){
                urls = "/Client/UsageReport?report_type="+cObj("report_type").value+datapass;
            }else{
                urls = "/Client/UsageReport?report_type="+cObj("report_type").value+"&client_account="+cObj("client_account_number").value+datapass;
            }
            sendDataGet("GET", urls, cObj("report_error_holder"), cObj("client_report_loader"), function () {
                cObj("client_usage_report_btn").disabled = false;
                cObj("client_usage_report_btn").classList.remove("disabled");
                console.log("We are here");

                var report_data = cObj("report_error_holder").innerText;
                if (hasJsonStructure(report_data)) {
                    createChart(report_data);

                    report_data = JSON.parse(report_data);


                    // add the select option
                    var select = cObj("select_the_next_date");
                    // first remove whats there
                    for (let id = select.options.length-1; id > 0 ; id--) {
                        const element = select.options[id];
                        select.remove(id);
                    }
                    for (let index = 0; index < report_data.length; index++) {
                        const element = report_data[index];
                        let option = document.createElement("option");
                        option.value = element.day;
                        option.text = element.y;
                        option.selected = element.selected;
                        select.appendChild(option);
                    }
                }else{
                    cObj("report_error_holder").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
                }
            });
        }else{
            var urls = "";
            if(cObj("isTabElem") != undefined){
                urls = "/Client/UsageReport/Data?report_type="+cObj("report_type").value+datapass;
            }else{
                urls = "/Client/UsageReport/Data?report_type="+cObj("report_type").value+"&client_account="+cObj("client_account_number").value+datapass;
            }
            sendDataGet("GET", urls, cObj("report_error_holder"), cObj("client_report_loader"), function () {
                cObj("client_usage_report_btn").disabled = false;
                cObj("client_usage_report_btn").classList.remove("disabled");

                var report_data = cObj("report_error_holder").innerText;
                if (hasJsonStructure(report_data)) {
                    createChart(report_data);

                    report_data = JSON.parse(report_data);


                    // add the select option
                    var select = cObj("select_the_next_date");
                    // first remove whats there
                    for (let id = select.options.length-1; id > 0 ; id--) {
                        const element = select.options[id];
                        select.remove(id);
                    }
                    for (let index = 0; index < report_data.length; index++) {
                        const element = report_data[index];
                        let option = document.createElement("option");
                        option.value = element.day;
                        option.text = element.y;
                        option.selected = element.selected;
                        select.appendChild(option);
                    }
                }
            });
        }
}
function createChart(report_data) {
    var label = [];
    var upload = [];
    var download = [];

    // check if is tab or not
    if(cObj("isTabElem") != undefined){
        var clients_data = null;
    }

    report_data = JSON.parse(report_data);
    for (let index = 0; index < report_data.length; index++) {
        const element = report_data[index];
        if (element.selected || cObj("report_type").value == "Yearly") {
            for (let index_2 = 0; index_2 < element.report.length; index_2++) {
                const element_2 = element.report[index_2];
                label.push(element_2.x);
                upload.push(element_2.upload);
                download.push(element_2.download);
            }
        }
    }
    upload = cObj("report_on").value == "bandwidth" ? normalizeBitsToUnit(upload) : normalizeBytesToUnit(upload);
    download = cObj("report_on").value == "bandwidth" ? normalizeBitsToUnit(download) : normalizeBytesToUnit(download);
    // console.log(upload);
    // console.log(download);
    // destroy if chart already exists
    if (reportChart) {
        reportChart.destroy();
    }

    // prepare chart
    const ctx = document.getElementById('report-charts').getContext('2d');
    reportChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: label,  // your x-axis labels
        datasets: [
            {
                label: 'Upload',
                data: upload.values,  // your upload values
                borderColor: 'rgba(250, 98, 107, 1)',
                backgroundColor: 'rgba(250, 98, 107, 0.3)', // 👈 area fill
                fill: true,
                borderWidth: 1,
                tension: 0.3  // smooth curves
            },
            {
                label: 'Download',
                data: download.values,  // your download values
                borderColor: 'rgba(94, 216, 79, 1)',
                backgroundColor: 'rgba(94, 216, 79, 0.3)',
                fill: true,
                borderWidth: 1,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom', // 👈 legend below chart
                labels: {
                    font: {
                        family: 'Comfortaa, sans-serif',
                        size: 10,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                enabled: true,
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || "";
                        if (label) {
                        label += ": ";
                        }
                        label += context.parsed.y + " " + upload.unit+(cObj("report_on").value == "bandwidth" ? "ps" : ""); // tooltip suffix
                        return label;
                    }
                },
                titleFont: { family: 'Comfortaa, sans-serif', size: 14, weight: 'bold' },
                bodyFont: { family: 'Comfortaa, sans-serif', size: 12 }
            },
            title: {
                display: true,
                text: cObj("report_on").value == "bandwidth" ? '"'+(clients_data != null ? clients_data[0].client_name : "All Clients")+'" Bandwidth Usage Statistics' : "\""+(clients_data != null ? clients_data[0].client_name : "All Clients")+"\" Data Usage Statistics",
                font: {
                    family: 'Comfortaa, sans-serif',
                    size: 16,
                    weight: 'bold',
                    style: 'normal'
                },
                color: '#333',
                padding: {
                    top: 10,
                    bottom: 20
                },
                align: 'center' // "start", "center", "end"
            },
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        family: 'Comfortaa, sans-serif',
                        size: 10
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function (value) {
                        return value + " " + upload.unit+(cObj("report_on").value == "bandwidth" ? "ps" : ""); // add suffix
                    },
                    font: {
                        family: 'Comfortaa, sans-serif',
                        size: 10
                    }
                }
            }
        }
    }
    });

}
function normalizeBytesToUnit(values) {
  if (!Array.isArray(values) || values.length === 0) return [];

  const units = ["KB", "MB", "GB", "TB"]; // baseline is KB
  let unitIndex = 0;

  // Step 1: convert bits → bytes
  const valuesInBytes = values.map(v => v / 8);

  // Step 2: find smallest non-zero candidate
  let minVal = valuesInBytes.filter(v => v > 0);
  if (minVal.length === 0) {
    // all are zero
    return { values: values.map(() => 0), unit: "KB" };
  }
  minVal = Math.min(...minVal);

  // Step 3: determine the best unit (start at KB = divide by 1024 once)
  let minInKB = minVal / 1024;
  while (minInKB >= 1024 && unitIndex < units.length - 1) {
    minInKB /= 1024;
    unitIndex++;
  }

  // Step 4: normalize all values to that unit
  const converted = valuesInBytes.map(v =>
    parseFloat((v / Math.pow(1024, unitIndex + 1)).toFixed(2)) // +1 since baseline = KB
  );

  return {
    values: converted,
    unit: units[unitIndex]
  };
}
function normalizeBitsToUnit(values) {
  if (!Array.isArray(values) || values.length === 0) return [];

  const units = ["Kb", "Mb", "Gb", "Tb"]; // baseline is Kb (kilobits)
  let unitIndex = 0;

  // Step 1: find smallest non-zero candidate
  let minVal = values.filter(v => v > 0);
  if (minVal.length === 0) {
    // all are zero
    return { values: values.map(() => 0), unit: "Kb" };
  }
  minVal = Math.min(...minVal);

  // Step 2: determine the best unit (start at Kb = divide by 1024 once)
  let minInKb = minVal / 1024;
  while (minInKb >= 1024 && unitIndex < units.length - 1) {
    minInKb /= 1024;
    unitIndex++;
  }

  // Step 3: normalize all values to that unit
  const converted = values.map(v =>
    parseFloat((v / Math.pow(1024, unitIndex + 1)).toFixed(2)) // +1 since baseline = Kb
  );

  return {
    values: converted,
    unit: units[unitIndex]
  };
}
