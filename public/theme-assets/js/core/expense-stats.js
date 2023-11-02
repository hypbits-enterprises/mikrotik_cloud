
function cObj(id) {
    return document.getElementById(id);
}
function valObj(id) {
    return document.getElementById(id).value;
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]'
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}

// fornat the date we are given
function setDate(string) {
    string = string.toString();
    var year = string.substr(0, 4);
    var month = string.substr(4, 2) - 1;
    var day = string.substr(6, 2);
    var hour = string.substr(8, 2);
    var min = string.substr(10, 2);
    var sec = string.substr(12, 2);
    const d = new Date(year, month, day, hour, min, sec);
    var hours = d.getHours() > 9 ? d.getHours() : "0" + d.getHours();
    var minutes = d.getMinutes() > 9 ? d.getMinutes() : "0" + d.getMinutes();
    var seconds = d.getSeconds() > 9 ? d.getSeconds() : "0" + d.getSeconds();
    return getDays(d.getDay()) + " " + d.getDate() + " " + getMonths(d.getMonth()) + " " + d.getFullYear();
    console.log(year);
}
function getMonths(month) {
    if (month == 0) {
        return "Jan";
    } else if (month == 1) {
        return "Feb";
    } else if (month == 2) {
        return "Mar";
    } else if (month == 3) {
        return "Apr";
    } else if (month == 4) {
        return "May";
    } else if (month == 5) {
        return "Jun";
    } else if (month == 6) {
        return "Jul";
    } else if (month == 7) {
        return "Aug";
    } else if (month == 8) {
        return "Sep";
    } else if (month == 9) {
        return "Oct";
    } else if (month == 10) {
        return "Nov";
    } else if (month == 11) {
        return "Dec";
    }
}
function getDays(days) {
    if (days == 0) {
        return "Sun";
    } else if (days == 1) {
        return "Mon";
    } else if (days == 2) {
        return "Tue";
    } else if (days == 3) {
        return "Wed";
    } else if (days == 4) {
        return "Thur";
    } else if (days == 5) {
        return "Fri";
    } else if (days == 6) {
        return "Sat";
    }
}
function generateRandomNumber(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}
// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

function stopInterval(id) {
    clearInterval(id);
}

var myChart;
var max_number = 0;
var min_number = 0;
window.onload = function () {
    getSelectData();
    data_index = 0;
    plotGraph(data_index);
}

cObj("period_selection_reg").onchange = function () {
    getSelectData();
    data_index = 0;
    plotGraph(data_index);
}
cObj("previous_data").onclick = function () {
    data_index *= 1;
    if (data_index < ((max_count * 1) - 1)) {
        data_index = data_index + 1;
        plotGraph(data_index);
    }
}
cObj("next_data").onclick = function () {
    data_index *= 1;
    if (data_index >= 0) {
        data_index = data_index == 0 ? 0 : (data_index - 1);
        plotGraph(data_index);
    }
}

cObj("show_x_axis").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}
cObj("show_y_axis").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}
cObj("clients_chart_type").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}
function getSelectData() {
    // console.log(expense_stats_weekly);
    if (valObj("period_selection_reg") == "Weekly") {
        var data_to_display = "<select name='select_week_link' id='select_week_link' class='page-item border border-primary bg-white p-1'><option value='' hidden>Select Option</option>";
        for (let index = 0; index < expense_stats_weekly.length; index++) {
            if (index == 0) {
                data_to_display += "<option id='opt_" + (index) + "' value='" + (index) + "' >Current Week.</option>";
            } else {
                data_to_display += "<option id='opt_" + (index) + "' value='" + (index) + "' >" + (index) + " Week(s) Ago</option>";
            }
        }
        data_to_display += "</select>";
        cObj("selective_data").innerHTML = data_to_display;
        cObj("data_navigators").classList.remove("invisible");

        // set listener
        cObj("select_week_link").onchange = function () {
            data_index = valObj("select_week_link");
            plotGraph(data_index);
        }
    } else if (valObj("period_selection_reg") == "Monthly") {
        var data_to_display = "<select name='select_week_link' id='select_week_link' class='page-item border border-primary bg-white p-1'><option value='' hidden>Select Option</option>";
        for (let index = 0; index < expense_records_monthly.length; index++) {
            if (index == 0) {
                data_to_display += "<option id='opt_" + (index) + "' value='" + (index) + "'>Current Year</option>";
            } else {
                data_to_display += "<option id='opt_" + (index) + "' value='" + (index) + "'>" + (index) + " Year(s) Ago</option>";
            }
        }
        data_to_display += "</select>";
        cObj("selective_data").innerHTML = data_to_display;
        cObj("data_navigators").classList.remove("invisible");

        // set listener
        cObj("select_week_link").onchange = function () {
            data_index = valObj("select_week_link");
            plotGraph(data_index);
        }
    } else if (valObj("period_selection_reg") == "Yearly") {
        cObj("data_navigators").classList.add("invisible");
    }
}

var data_index = 0;
var max_count = 0;
function plotGraph(data_index) {
    data_index = this.data_index;
    console.log(data_index);
    if (myChart != null) {
        myChart.destroy();
    }
    cObj("opt_" + data_index).selected = true;
    var data = plotData(data_index);
    // console.log(data);
    getExpenseData(data[1]);
    var show_x_axis = cObj("show_x_axis").checked == true ? true : false;
    var show_y_axis = cObj("show_y_axis").checked == true ? true : false;

    var ctx = cObj("onboarding_canvas");
    var type = cObj("clients_chart_type").value;
    var backgroundColor = ['rgb(55, 61, 125)'];
    if (type == "pie") {
        backgroundColor = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            var rand_red = generateRandomNumber(100, 255);
            var rand_green = generateRandomNumber(100, 255);
            var rand_blue = generateRandomNumber(100, 255);
            var rand_color = 'rgb(' + rand_red + ', ' + rand_green + ', ' + rand_blue + ')';
            backgroundColor.push(rand_color);
        }
    }

    // get the labels
    var labels = [];
    var from = "";
    var to = "";
    for (let index = 0; index < data[0].length; index++) {
        const element = data[0][index];
        if (index == 0) {
            from = "between (" + element.x;
        }
        if (index == data[0].length - 1) {
            to = element.x;
        }
        labels.push(element.x);
    }

    var title = "Expenses " + from + ") to (" + to + ")";
    // chart data
    var chart_data = data[0];
    if (type == "pie") {
        chart_data = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            chart_data.push(element.y);
        }
    }

    myChart = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                tension: 0.4,
                label: 'Expenses',
                data: chart_data,
                borderWidth: 1,
                font: {
                    size: 14
                },
                backgroundColor: backgroundColor,
                borderColor: 'rgb(55, 61, 125)'
            }],
            hoverOffset: 4
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: true
                    },
                    display: show_y_axis
                },
                x: {
                    grid: {
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: true
                    },
                    display: show_x_axis
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 18
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    font: {
                        size: 14
                    }
                }
            }
        }
    });
}
function plotData(data_index) {
    var period_selection_reg = valObj("period_selection_reg");

    var expense_data = [];
    var plot_data = [];
    if (period_selection_reg == "Weekly") {
        max_count = expense_records_weekly.length;
        var trans_info = expense_records_weekly[(expense_records_weekly.length - 1) - data_index];
        var trans_stats = expense_stats_weekly[(expense_records_weekly.length - 1) - data_index];

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                expense_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = { x: element.date, y: element.expense_amount };
            plot_data.push(stats);
        }
    } else if (period_selection_reg == "Monthly") {
        max_count = expense_stats_monthly.length;
        var trans_stats = expense_stats_monthly[(expense_stats_monthly.length - 1) - data_index];
        var trans_info = expense_records_monthly[(expense_stats_monthly.length - 1) - data_index];

        // console.log(expense_stats_monthly);

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                expense_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = { x: element.date, y: element.expense_amount };
            plot_data.push(stats);
        }
    } else if (period_selection_reg == "Yearly") {
        max_count = expense_yearly_records.length;
        var trans_info = expense_yearly_records;
        var trans_stats = expense_yearly_stats;

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                expense_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = { x: element.date, y: element.expense_amount };
            plot_data.push(stats);
        }
    }
    return [plot_data, expense_data];
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number
function getExpenseData(clients_data) {
    // console.log(student_data);
    // get the arrays
    // var clients_data = plotData()[1];
    rowsColStudents = [];
    rowsNCols_original = [];
    pagecountTransaction = 0; //this are the number of pages for transaction
    pagecounttrans = 1; //the current page the user is
    startpage = 0;

    if (clients_data.length > 0) {
        var rows = clients_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['id']);
            col.push(element['name']);
            col.push(element['category']);
            col.push(element['unit_of_measure']);
            col.push(element['unit_price']);
            col.push(element['unit_amount']);
            col.push(element['total_price']);
            col.push(element['date_recorded']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 20;
        pagecountTransaction = Math.ceil(counted);
        cObj("tablefooter").classList.remove("invisible");

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No Expense Records found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    console.log(arrays);
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Expense Names</th><th>Expense Category</th><th>Quantity</th><th>Unit Price</th><th>Total Price</th><th>Date Recorded</th><th>Action</th></tr></thead><tbody>";
    if (finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < finish; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][3] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr><th scope='row'>" + counter + "</th><td>" + arrays[index][1] + "</td><td>" + arrays[index][2] + "</td><td>" + arrays[index][5] + " " + (arrays[index][3] != null ? arrays[index][3] : "Unit(s)") + "</td><td>Kes " + arrays[index][4] + "</td><td>Kes " + arrays[index][6] + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a class='btn btn-primary btn-sm' href='/Expense/View/" + arrays[index][0] + "'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    } else {
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < total; index++) {
            tableData += "<tr><th scope='row'>" + counter + "</th><td>" + arrays[index][1] + "</td><td>" + arrays[index][2] + "</td><td>" + arrays[index][5] + " " + (arrays[index][3] != null ? arrays[index][3] : "Unit(s)") + "</td><td>Kes " + arrays[index][4] + "</td><td>Kes " + arrays[index][6] + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a class='btn btn-primary btn-sm' href='/Expense/View/" + arrays[index][0] + "'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
        fins = total;
    }

    tableData += "</tbody></table>";
    //set the start and the end value
    cObj("startNo").innerText = start + 1;
    cObj("finishNo").innerText = fins;
    //set the page number
    cObj("pagenumNav").innerText = pagecounttrans;
    // set tool tip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    return tableData;
}


//next record 
//add the page by one and the number os rows to dispay by 20
cObj("tonextNav").onclick = function () {
    if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
        startpage += 20;
        pagecounttrans++;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    } else {
        pagecounttrans = pagecountTransaction;
    }
}
// end of next records
cObj("toprevNac").onclick = function () {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 20;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tofirstNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tolastNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 20) - 20;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}

// seacrh keyword at the table
cObj("searchkey").onkeyup = function () {
    checkName(this.value);
}
//create a function to check if the array has the keyword being searched for
function checkName(keyword) {
    rowsColStudents = rowsNCols_original;
    pagecounttrans = 1;
    if (keyword.length > 0) {
        // cObj("tablefooter").classList.add("invisible");
    } else {
        // cObj("tablefooter").classList.remove("invisible");
    }
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    var keylower = keyword.toLowerCase();
    var keyUpper = keyword.toUpperCase();
    //row break
    for (let index = 0; index < rowsColStudents.length; index++) {
        const element = rowsColStudents[index];
        //column break
        var present = 0;
        if (element[0].toString().toLowerCase().includes(keylower) || element[0].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[1].toString().toLowerCase().includes(keylower) || element[1].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[2].toString().toLowerCase().includes(keylower) || element[2].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[4].toString().toLowerCase().includes(keylower) || element[4].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if ((element[5]+" "+element[3]).toString().toLowerCase().includes(keylower) || (element[5]+" "+element[3]).toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[6].toString().toLowerCase().includes(keylower) || element[6].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[7].toString().toLowerCase().includes(keylower) || element[7].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
            router_and_keyword = rowsNcol2;
        }
    }
    if (rowsNcol2.length > 0) {
        rowsColStudents = rowsNcol2;
        var counted = rowsNcol2.length / 50;
        pagecountTransaction = Math.ceil(counted);
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsNcol2);
        cObj("tot_records").innerText = rowsNcol2.length;
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        // cObj("tablefooter").classList.add("invisible");
        cObj("startNo").innerText = 0;
        cObj("finishNo").innerText = 0;
        cObj("tot_records").innerText = 0;
        pagecountTransaction = 1;
    }
}