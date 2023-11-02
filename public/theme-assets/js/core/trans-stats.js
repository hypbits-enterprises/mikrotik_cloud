
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
    data_index*=1;
    if (data_index < ((max_count*1) - 1)) {
        data_index = data_index + 1;
        plotGraph(data_index);
    }
}
cObj("next_data").onclick = function () {
    data_index*=1;
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
    // console.log(transaction_stats_weekly);
    if(valObj("period_selection_reg") == "Weekly"){
        var data_to_display = "<select name='select_week_link' id='select_week_link' class='page-item border border-primary bg-white p-1'><option value='' hidden>Select Option</option>";
        for (let index = 0; index < transaction_stats_weekly.length; index++) {
            if (index == 0) {
                data_to_display+="<option id='opt_"+(index)+"' value='"+(index)+"' >Current Week.</option>";
            }else{
                data_to_display+="<option id='opt_"+(index)+"' value='"+(index)+"' >"+(index)+" Week(s) Ago</option>";
            }
        }
        data_to_display+="</select>";
        cObj("selective_data").innerHTML = data_to_display;
        cObj("data_navigators").classList.remove("invisible");

        // set listener
        cObj("select_week_link").onchange = function () {
            data_index = valObj("select_week_link");
            plotGraph(data_index);
        }
    }else if(valObj("period_selection_reg") == "Monthly"){
        var data_to_display = "<select name='select_week_link' id='select_week_link' class='page-item border border-primary bg-white p-1'><option value='' hidden>Select Option</option>";
        for (let index = 0; index < transaction_records_monthly.length; index++) {
            if (index == 0) {
                data_to_display+="<option id='opt_"+(index)+"' value='"+(index)+"'>Current Year</option>";
            }else{
                data_to_display+="<option id='opt_"+(index)+"' value='"+(index)+"'>"+(index)+" Year(s) Ago</option>";
            }
        }
        data_to_display+="</select>";
        cObj("selective_data").innerHTML = data_to_display;
        cObj("data_navigators").classList.remove("invisible");

        // set listener
        cObj("select_week_link").onchange = function () {
            data_index = valObj("select_week_link");
            plotGraph(data_index);
        }
    }else if(valObj("period_selection_reg") == "Yearly"){
        cObj("data_navigators").classList.add("invisible");
    }
}

var data_index = 0;
var max_count = 0;
function plotGraph(data_index) {
    data_index = this.data_index;
    // console.log(data_index);
    if (myChart != null) {
        myChart.destroy();
    }
    cObj("opt_"+data_index).selected = true;
    var data = plotData(data_index);
    // console.log(data);
    getTransactionData(data[1]);
    var show_x_axis = cObj("show_x_axis").checked == true ? true : false;
    var show_y_axis = cObj("show_y_axis").checked == true ? true : false;
    
    var ctx = cObj("onboarding_canvas");
    var type = cObj("clients_chart_type").value;
    var backgroundColor = ['rgb(55, 61, 125)'];
    if (type == "pie") {
        backgroundColor = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            var rand_red = generateRandomNumber(100,255);
            var rand_green = generateRandomNumber(100,255);
            var rand_blue = generateRandomNumber(100,255);
            var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
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
            from = "between ("+element.x;
        }
        if (index == data[0].length-1) {
            to = element.x;
        }
        labels.push(element.x);
    }

    var title = "Amount Received "+from+") to ("+to+")";
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
            labels:labels,
            datasets: [{
                tension: 0.4,
                label: 'Amount Received',
                data: chart_data,
                borderWidth: 1,
                font: {
                    size: 14
                },
                backgroundColor: backgroundColor,
                borderColor:'rgb(55, 61, 125)'
            }],
            hoverOffset: 4
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks:{
                        stepSize: 1
                    },
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_y_axis
                },
                x:{
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_x_axis
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

    var transaction_data = [];
    var plot_data = [];
    if (period_selection_reg == "Weekly") {
        max_count = transaction_records_weekly.length;
        var trans_info = transaction_records_weekly[(transaction_records_weekly.length - 1) - data_index];
        var trans_stats = transaction_stats_weekly[(transaction_records_weekly.length - 1) - data_index];

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                transaction_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = {x:element.date,y:element.trans_amount};
            plot_data.push(stats);
        }
    }else if (period_selection_reg == "Monthly") {
        max_count = transaction_stats_monthly.length;
        var trans_stats = transaction_stats_monthly[(transaction_stats_monthly.length - 1) - data_index];
        var trans_info = transaction_records_monthly[(transaction_stats_monthly.length - 1) - data_index];

        // console.log(transaction_stats_monthly);

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                transaction_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = {x:element.date,y:element.trans_amount};
            plot_data.push(stats);
        }
    }else if (period_selection_reg == "Yearly") {
        max_count = transaction_yearly_records.length;
        var trans_info = transaction_yearly_records;
        var trans_stats = transaction_yearly_stats;

        // get all clients
        for (let index = 0; index < trans_info.length; index++) {
            const element = trans_info[index];
            for (let index_1 = 0; index_1 < element.length; index_1++) {
                const elem = element[index_1];
                transaction_data.push(elem);
            }
        }

        // get statistics
        // console.log(trans_stats.length);
        for (let index = 0; index < trans_stats.length; index++) {
            const element = trans_stats[index];
            var stats = {x:element.date,y:element.trans_amount};
            plot_data.push(stats);
        }
    }
    return [plot_data,transaction_data];
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number
function getTransactionData(clients_data) {
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
            col.push(element['fullnames']);
            col.push(element['phone_transacting']);
            col.push(element['transacion_amount']);
            col.push(element['transaction_acc_id']);
            col.push(element['transaction_account']);
            col.push(element['transaction_date']);
            col.push(element['transaction_id']);
            col.push(element['transaction_mpesa_id']);
            col.push(element['transaction_short_code']);
            col.push(element['transaction_status']);
            col.push("Null");
            col.push(element['phone_transacting']);
            col.push("Null");
            col.push(element['transaction_acc_id']);
            col.push(element['account_names']);
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
    var fins = 0;
    //this is the table header to the start of the tbody
    // var tableData = "<table class='table'><thead><tr><th>#</th><th>Full Names</th><th>Account Number</th><th>Location</th><th>Action</th></tr></thead><tbody>";
    var tableData = "<table class='table'><thead><tr><th><span id='sortbydate' title='Sort by date registered' style='cursor:pointer;'># </span></th><th><span id='sortTransCode'   title='Sort by date registered' style='cursor:pointer;'>Transaction ID</span></th><th><span id='trans_account_number'   title='Sort by date registered' style='cursor:pointer;'>Account Number</span></th><th><span id='transaction_amount_id' title='Sort by Amount' style='cursor:pointer;'>Amount</span></th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][9] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][14] +" "+status+"<small></small></td><td>" + arrays[index][4] + " <small></small></td><td>Kes " + arrays[index][2] + "</td><td><a href='/Transactions/View/" + arrays[index][6] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this transaction'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][9] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][14] +" "+status+"<small></small></td><td>" + arrays[index][4] + " <small></small></td><td>Kes " + arrays[index][2] + "</td><td><a href='/Transactions/View/" + arrays[index][6] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this transaction'><i class='ft-eye'></i> View</a></td></tr>";
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
cObj("tonextNav").onclick = function() {
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
cObj("toprevNac").onclick = function() {
if (pagecounttrans > 1) {
    pagecounttrans--;
    startpage -= 20;
    var endpage = startpage + 20;
    cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
}
}
cObj("tofirstNav").onclick = function() {
if (pagecountTransaction > 0) {
    pagecounttrans = 1;
    startpage = 0;
    var endpage = startpage + 20;
    cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
}
}
cObj("tolastNav").onclick = function() {
if (pagecountTransaction > 0) {
    pagecounttrans = pagecountTransaction;
    startpage = (pagecounttrans * 20) - 20;
    var endpage = startpage + 20;
    cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
}
}

// seacrh keyword at the table
cObj("searchkey").onkeyup = function() {
    checkName(this.value);
}
//create a function to check if the array has the keyword being searched for
function checkName(keyword) {
rowsColStudents = rowsNCols_original;
if (keyword.length > 0) {
    // cObj("tablefooter").classList.add("invisible");
} else {
    // cObj("tablefooter").classList.remove("invisible");
}
var rowsNcol2 = [];
var keylower = keyword.toLowerCase();
var keyUpper = keyword.toUpperCase();
//row break
for (let index = 0; index < rowsColStudents.length; index++) {
    const element = rowsColStudents[index];
    //column break
    var present = 0;
    if (element[0].toString().includes(keylower) || element[0].toString().includes(keyUpper)) {
        present++;
    }
    if (element[2].toString().includes(keylower) || element[2].toString().includes(keyUpper)) {
        present++;
    }
    if (element[4].toString().includes(keylower) || element[4].toString().includes(keyUpper)) {
        present++;
    }
    if (element[7].toString().includes(keylower) || element[7].toString().includes(keyUpper)) {
        present++;
    }
    if (element[1].toString().includes(keylower) || element[1].toString().includes(keyUpper)) {
        present++;
    }
    if (element[8].toString().includes(keylower) || element[8].toString().includes(keyUpper)) {
        present++;
    }
    if (element[12].toString().includes(keylower) || element[12].toString().includes(keyUpper)) {
        present++;
    }
    if (element[10].toString().includes(keylower) || element[10].toString().includes(keyUpper)) {
        present++;
    }
    if (element[14].toString().includes(keylower) || element[14].toString().includes(keyUpper)) {
        present++;
    }
    //here you can add any other columns to be searched for
    if (present > 0) {
        rowsNcol2.push(element);
    }
}
if (rowsNcol2.length > 0) {
    rowsColStudents = rowsNcol2;
    var counted = rowsNcol2.length / 20;
    pagecountTransaction = Math.ceil(counted);
    cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsNcol2);
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