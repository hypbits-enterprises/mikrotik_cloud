// import chart js
// const { Chart } = await import('chart.js');

// loop through the days of the week
// get an object by id 

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
var myChart;
var max_number = 0;
var min_number = 0;
window.onload = function () {
    // plotGraph();
    cObj("display_clients").click();
    getSelectData();
    data_index = 0;
    plotGraph(data_index);
}

cObj("period_selection_reg").onchange = function () {
    getSelectData();
    data_index = 0;
    plotGraph(data_index);
}
cObj("clients_chart_type").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}
cObj("client_status_reg").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}

cObj("show_x_axis").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}
cObj("show_y_axis").onchange = function () {
    // plotGraph();
    plotGraph(data_index);
}

function getSelectData() {
    // console.log(clients_weekly);
    if(valObj("period_selection_reg") == "Weekly"){
        var data_to_display = "<select name='select_week_link' id='select_week_link' class='page-item border border-primary bg-white p-1'><option value='' hidden>Select Option</option>";
        for (let index = 0; index < clients_weekly.length; index++) {
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
        for (let index = 0; index < clients_monthly.length; index++) {
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

function generateRandomNumber(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

var data_index = 0;
var max_count = 0;
function plotGraph(data_index) {
    data_index = this.data_index;
    console.log(data_index);
    if (myChart != null) {
        myChart.destroy();
    }
    cObj("opt_"+data_index).selected = true;
    var data = plotData(data_index);
    console.log(data);
    getClientData(data[1]);
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

    var title = "Client(s) Registered "+from+") to ("+to+")";
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
                label: 'Client(s) Registered',
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
    var client_status_reg = valObj("client_status_reg");

    var clients_data = [];
    var plot_data = [];
    if (period_selection_reg == "Weekly") {
        max_count = clients_weekly.length;
        if (client_status_reg == "2") {
            var client_infor = clients_weekly[(clients_weekly.length - 1) - data_index];
            var client_stats = client_metrics_weekly[(clients_weekly.length - 1) - data_index];

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    clients_data.push(elem);
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "0" || client_status_reg == "1") {
            var client_infor = clients_weekly[(clients_weekly.length - 1) - data_index];
            var client_stats = client_metrics_weekly[(clients_weekly.length - 1) - data_index];

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.client_status == client_status_reg) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "4" || client_status_reg == "5") {
            var client_infor = clients_weekly[(clients_weekly.length - 1) - data_index];
            var client_stats = client_metrics_weekly[(clients_weekly.length - 1) - data_index];

            var assignment = client_status_reg == "4" ? "static" : "pppoe";
            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.assignment == assignment) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }
    }else if (period_selection_reg == "Monthly") {
        max_count = clients_monthly.length;
        if (client_status_reg == "2") {
            var client_infor = clients_monthly[(clients_monthly.length - 1) - data_index];
            var client_stats = clients_statistics_monthly[(clients_monthly.length - 1) - data_index];

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    clients_data.push(elem);
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "0" || client_status_reg == "1") {
            var client_infor = clients_monthly[(clients_monthly.length - 1) - data_index];
            var client_stats = clients_statistics_monthly[(clients_monthly.length - 1) - data_index];

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.client_status == client_status_reg) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "4" || client_status_reg == "5") {
            var client_infor = clients_monthly[(clients_monthly.length - 1) - data_index];
            var client_stats = clients_statistics_monthly[(clients_monthly.length - 1) - data_index];

            var assignment = client_status_reg == "4" ? "static" : "pppoe";
            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.assignment == assignment) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }
    }else if (period_selection_reg == "Yearly") {
        max_count = clients_data_yearly.length;
        // console.log(clients_data_yearly);
        if (client_status_reg == "2") {
            var client_infor = clients_data_yearly;
            var client_stats = clients_statistics_yearly;

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    clients_data.push(elem);
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "0" || client_status_reg == "1") {
            var client_infor = clients_data_yearly;
            var client_stats = clients_statistics_yearly;

            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.client_status == client_status_reg) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }else if (client_status_reg == "4" || client_status_reg == "5") {
            var client_infor = clients_data_yearly;
            var client_stats = clients_statistics_yearly;

            var assignment = client_status_reg == "4" ? "static" : "pppoe";
            // get all clients
            for (let index = 0; index < client_infor.length; index++) {
                const element = client_infor[index];
                for (let index_1 = 0; index_1 < element.length; index_1++) {
                    const elem = element[index_1];
                    if (elem.assignment == assignment) {
                        clients_data.push(elem);
                    }
                }
            }

            // get statistics
            // console.log(client_stats.length);
            for (let index = 0; index < client_stats.length; index++) {
                const element = client_stats[index];
                var stats = {x:element.date,y:element.number};
                plot_data.push(stats);
            }
        }
    }
    return [plot_data,clients_data];
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number
function getClientData(clients_data) {
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
            col.push(element['client_id']);
            col.push(element['client_name']);
            col.push(element['client_network']);
            col.push(element['client_status']);
            col.push(element['clients_contacts']);
            col.push(element['client_address']);
            col.push(element['monthly_payment']);
            col.push((element['next_expiration_date'] * 1));
            col.push(element['payments_status']);
            col.push(element['router_name']);
            col.push(element['wallet_amount']);
            col.push(element['client_account']);
            col.push(element['reffered_by']);
            col.push(element['comment']);
            col.push(element['location_coordinates']);
            col.push(element['assignment']);
            col.push(element['clients_reg_date']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        // console.log(rowsNCols_original);
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 20;
        pagecountTransaction = Math.ceil(counted);

        if (rowsColStudents.length > 0 ) {
            cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sort_by_name").addEventListener("click",sortByName);
            cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        }
        cObj("tablefooter").classList.remove("invisible");

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No clients found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th><span  title='Sort by date registered' id='sort_by_reg_date' style='cursor:pointer;'># <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_name'   title='Sort by Client Name' style='cursor:pointer;'>Full Names <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_acc_number'   title='Sort by Account Number' style='cursor:pointer;'>Account Number <i class='ft-chevron-down'></i></span></th><th>Registration Date</th><th><span  id ='sort_by_expiration'   title='Sort by Expiration Date' style='cursor:pointer;'>Due Date <i class='ft-chevron-down'></i></span></th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][3] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var reffered = "";
                if (arrays[index][12] != null && arrays[index][12] != "") {
                    var mainData = arrays[index][12];
                    if(arrays[index][12].substr(0,1) == "\""){
                        mainData = mainData.substr(1,mainData.length-2);
                        mainData = mainData.replace(/\\/g, "");
                    }
                    // console.log(mainData);
                    var data = JSON.parse(mainData);
                    // get the client name
                    var fullname = "Null";
                    var id = 0;
                    for (let ind = 0; ind < rowsNCols_original.length; ind++) {
                        const element = rowsNCols_original[ind];
                        if (element[11] == data.client_acc) {
                            fullname = element[1];
                            id = element[0];
                        }
                    }
                    reffered = "<a href='/Clients/View/"+id+"' class='text-secondary'><span data-toggle='tooltip' title='Reffered by "+fullname+" {"+data.client_acc+"} @ Kes "+data.monthly_payment+"' class='badge badge-warning text-dark'>Reffered</span></a>";
                }
                var assignment = "";
                if (arrays[index][15] == "static") {
                    assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
                }else if (arrays[index][15] == "pppoe"){
                    assignment = "<span class='badge text-light' style = 'background:rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
                }
            
            tableData += "<tr><th scope='row'>"+counter+"</th><td>"+assignment+" <a href='/Clients/View/"+arrays[index][0]+"' class='text-secondary'>" + ucwords(arrays[index][1]) +" "+status+"</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) +"</small></td><td>" + arrays[index][11].toUpperCase() + "</td><td>" + setDate(arrays[index][16]) + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> </td></tr>";
            counter++;
        }
    }else{
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][3] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            var assignment = "";
            if (arrays[index][15] == "static") {
                assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
            }else if (arrays[index][15] == "pppoe"){
                assignment = "<span class='badge text-light' style = 'background: rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
            }
            
            // console.log(location);
            tableData += "<tr><th scope='row'>"+counter+"</th><td>"+assignment+" <a href='/Clients/View/"+arrays[index][0]+"' class='text-secondary'>" + ucwords(arrays[index][1]) +" "+status+"</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) +"</small></td><td>" + arrays[index][11].toUpperCase() + "</td><td>" + setDate(arrays[index][16]) + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a></td></tr>";
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
function ucwords(string) {
    var cases = string.toLowerCase().split(" ");
    // split the string to get the number of words present
    var final_word = "";
    for (let index = 0; index < cases.length; index++) {
        const element = cases[index];
        final_word +=element.substr(0,1).toUpperCase()+element.substr(1)+" ";
    }
    return final_word.trim();
}
function ucword(string) {
    if (string != null) {
        var cases = string.toLowerCase();
        // split the string to get the number of words present
        var final_word = cases.substr(0,1).toUpperCase()+cases.substr(1);
        return final_word.trim();
    }
    return "";
}
// fornat the date we are given
function setDate(string) {
    string = string.toString();
    var year = string.substr(0,4);
    var month = string.substr(4,2) - 1;
    var day = string.substr(6,2);
    var hour = string.substr(8,2);
    var min = string.substr(10,2);
    var sec = string.substr(12,2);
    const d = new Date(year,month,day,hour,min,sec);
    var hours = d.getHours()>9 ? d.getHours():"0"+d.getHours();
    var minutes = d.getMinutes()>9 ? d.getMinutes():"0"+d.getMinutes();
    var seconds = d.getSeconds()>9 ? d.getSeconds():"0"+d.getSeconds();
    return getDays(d.getDay())+" "+d.getDate()+" "+getMonths(d.getMonth())+" "+d.getFullYear()+" @ "+hours+":"+minutes+":"+seconds;
}
function getMonths(month) {
    if (month == 0) {
        return "Jan";
    }else if (month == 1) {
        return "Feb";
    }else if (month == 2) {
        return "Mar";
    }else if (month == 3) {
        return "Apr";
    }else if (month == 4) {
        return "May";
    }else if (month == 5) {
        return "Jun";
    }else if (month == 6) {
        return "Jul";
    }else if (month == 7) {
        return "Aug";
    }else if (month == 8) {
        return "Sep";
    }else if (month == 9) {
        return "Oct";
    }else if (month == 10) {
        return "Nov";
    }else if (month == 11) {
        return "Dec";
    }
}
function getDays(days) {
    if (days == 0) {
        return "Sun";
    }else if (days == 1) {
        return "Mon";
    }else if (days == 2) {
        return "Tue";
    }else if (days == 3) {
        return "Wed";
    }else if (days == 4) {
        return "Thur";
    }else if (days == 5) {
        return "Fri";
    }else if (days == 6) {
        return "Sat";
    }
}

//next record 
//add the page by one and the number os rows to dispay by 20
cObj("tonextNav").onclick = function() {
    // console.log(pagecounttrans+" "+pagecountTransaction);
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 20;
            pagecounttrans++;
            var endpage = startpage + 20;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
            if (rowsColStudents.length > 0 ) {
                cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
                cObj("sort_by_name").addEventListener("click",sortByName);
                cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
            }
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
        if (rowsColStudents.length > 0 ) {
            cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sort_by_name").addEventListener("click",sortByName);
            cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        }
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0 ) {
            cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sort_by_name").addEventListener("click",sortByName);
            cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        }
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 20) - 20;
        var endpage = startpage + 20;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0 ) {
            cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sort_by_name").addEventListener("click",sortByName);
            cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        }
    }
}

// seacrh keyword at the table
cObj("searchkey").onkeyup = function() {
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
        if (element[1].toLowerCase().includes(keylower) || element[1].toUpperCase().includes(keyUpper)) {
            present++;
        }
        // console.log(element);
        // if (element[13].toLowerCase().includes(keylower) || element[13].toUpperCase().includes(keyUpper)) {
        //     present++;
        // }
        if (element[4].toLowerCase().includes(keylower) || element[4].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toLowerCase().includes(keylower) || element[5].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[11].toLowerCase().includes(keylower)) {
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
        var counted = rowsNcol2.length / 20;
        pagecountTransaction = Math.ceil(counted);
        cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsNcol2);
        cObj("tot_records").innerText = rowsNcol2.length;
        if (rowsColStudents.length > 0 ) {
            cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sort_by_name").addEventListener("click",sortByName);
            cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        }
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        // cObj("tablefooter").classList.add("invisible");
        cObj("startNo").innerText = 0;
        cObj("finishNo").innerText = 0;
        cObj("tot_records").innerText = 0;
        pagecountTransaction = 1;
    }
}

var sort_by_date = 0;
function sortByRegDate() {
    rowsColStudents = sortAsc(rowsColStudents,0);
    if (sort_by_date == 0) {
        sort_by_date = 1;
        rowsColStudents = sortAsc(rowsColStudents,0);
    }else{
        sort_by_date = 0;
        rowsColStudents = sortDesc(rowsColStudents,0);
    }
    // console.log(sort_by_date);
    cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);
    if (sort_by_date == 0) {
        cObj("sort_by_reg_date").innerHTML = "# <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_reg_date").innerHTML = "# <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sort_by_name").addEventListener("click",sortByName);
        cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
    }
}
/***SOrt by expiration date */

var sort_by_expirations = 0;
function sortByExpDate() {
    rowsColStudents = sortAsc(rowsColStudents,7);
    if (sort_by_expirations == 0) {
        sort_by_expirations = 1;
        rowsColStudents = sortAsc(rowsColStudents,7);
    }else{
        sort_by_expirations = 0;
        rowsColStudents = sortDesc(rowsColStudents,7);
    }
    // console.log(sort_by_expirations);
    cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);
    if (sort_by_expirations == 0) {
        cObj("sort_by_expiration").innerHTML = "Due Date <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_expiration").innerHTML = "Due Date <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sort_by_name").addEventListener("click",sortByName);
        cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
    }
}
/**End of sort by expiration data */
var sortbyname = 0;
function sortByName() {
    rowsColStudents = sortAsc(rowsColStudents,1);
    if (sortbyname == 0) {
        sortbyname = 1;
        rowsColStudents = sortAsc(rowsColStudents,1);
    }else{
        sortbyname = 0;
        rowsColStudents = sortDesc(rowsColStudents,1);
    }
    // console.log(sortbyname);
    cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);
    if (sortbyname == 0) {
        cObj("sort_by_name").innerHTML = "Full Names <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_name").innerHTML = "Full Names <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sort_by_name").addEventListener("click",sortByName);
        cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
    }
}
var sortbyaccno = 0;
function sortByAccNo() {
    rowsColStudents = sortAsc(rowsColStudents,11);
    if (sortbyaccno == 0) {
        sortbyaccno = 1;
        rowsColStudents = sortAsc(rowsColStudents,11);
    }else{
        sortbyaccno = 0;
        rowsColStudents = sortDesc(rowsColStudents,11);
    }
    // console.log(sortbyaccno);
    cObj("transDataReciever").innerHTML = displayRecord(0, 20, rowsColStudents);
    if (sortbyaccno == 0) {
        cObj("sort_by_acc_number").innerHTML = "Account Number <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_acc_number").innerHTML = "Account Number <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sort_by_name").addEventListener("click",sortByName);
        cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
    }
}

function sortDesc(arrays,index){
    arrays = arrays.sort(sortFunction);
    function sortFunction(a, b) {
        if (a[index] === b[index]) {
            return 0;
        }
        else {
            return (a[index] > b[index]) ? -1 : 1;
        }
    }
    return arrays;
}
function sortAsc(arrays,index){
    arrays = arrays.sort(sortFunction);
    function sortFunction(a, b) {
        if (a[index] === b[index]) {
            return 0;
        }
        else {
            return (a[index] < b[index]) ? -1 : 1;
        }
    }
    return arrays;
}

// Send data with get
function sendDataGet(method, file, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("invisible");
        } else if (this.status == 500) {
            object2.classList.add("invisible");
            // cObj("loadings").classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        }
    };
    xml.open(method, file, true);
    xml.send();
}
// Send date with post request
function sendDataPost1(method, file, datapassing, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("invisible");
        } else if (this.status == 500) {
            object2.classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        } else if (this.status == 204) {
            object2.classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Password updated successfully!</p>";
        }
        // console.log(this.status);
    };
    xml.open(method, "" + file, true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send(datapassing);
}

function stopInterval(id) {
    clearInterval(id);
}
function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function checkBlank(id){
    let err = 0;
    if(cObj(id).value.trim().length>0){
        if (cObj(id).value.trim()=='N/A') {
            redBorder(cObj(id));
            err++;
        }else{
            grayBorder(cObj(id));
        }
    }else{
        redBorder(cObj(id));
        err++;
    }
    return err;
}
function valObj(objectid){
    if (document.getElementById(objectid) == null) {
        return "";
    }
    return document.getElementById(objectid).value;
}


var rowsColStudents_2 = [];
var rowsNCols_original_2 = [];
var pagecountTransaction_2 = 0; //this are the number of pages for transaction
var pagecounttrans_2 = 1; //the current page the user is
var startpage_2 = 0; // this is where we start counting the page number
cObj("display_clients").onclick = function () {
    var err = checkBlank("select_dates");
    var from_todays = cObj("from_todays").checked == true ? "true" : "false";
    if (err == 0) {
        if (valObj("select_dates") != "Select date") {
            var datapass = "selected_dates="+valObj("select_dates")+"&from_today="+from_todays;
            sendDataPost1("POST","/Client-due-demographics",datapass,cObj("display_data"),cObj("interface_load"));
            setTimeout(() => {
                var timeout = 0;
                var idss = setInterval(() => {
                    timeout++;
                    //after two minutes of slow connection the next process wont be executed
                    if (timeout==1200) {
                        stopInterval(idss);                        
                    }
                    if (cObj("interface_load").classList.contains("invisible")) {
                        console.log(cObj("display_data").innerText);
                        displayDueDemographics(cObj("display_data").innerText);
                        stopInterval(idss);
                    }
                }, 100);
            }, 200);
        }else{
            err = checkBlank("select_due_dates_demo");
            if (err == 0) {
                var datapass = "selected_dates="+valObj("select_due_dates_demo")+"&from_today="+from_todays;
                sendDataPost1("POST","/Client-due-demographics",datapass,cObj("display_data"),cObj("interface_load"));
                setTimeout(() => {
                    var timeout = 0;
                    var idss = setInterval(() => {
                        timeout++;
                        //after two minutes of slow connection the next process wont be executed
                        if (timeout==1200) {
                            stopInterval(idss);                        
                        }
                        if (cObj("interface_load").classList.contains("invisible")) {
                            console.log(cObj("display_data").innerText);
                            displayDueDemographics(cObj("display_data").innerText);
                            stopInterval(idss);
                        }
                    }, 100);
                }, 200);
            }
        }
    }
}

function displayDueDemographics(data) {
    rowsColStudents_2 = [];
    rowsNCols_original_2 = [];
    pagecountTransaction_2 = 0; //this are the number of pages for transaction
    pagecounttrans_2 = 1; //the current page the user is
    startpage_2 = 0; 
    if (hasJsonStructure(data)) {
        var student_data = JSON.parse(data);
        if (student_data.length > 0) {
            var rows = student_data;
            //create a column now
            for (let index = 0; index < rows.length; index++) {
                const element = rows[index];
                // create the collumn array that will take the row value
                var col = [];
                // console.log(element);
                col.push(element['client_id']);
                col.push(element['client_name']);
                col.push(element['client_network']);
                col.push(element['client_status']);
                col.push(element['clients_contacts']);
                col.push(element['client_address']);
                col.push(element['monthly_payment']);
                col.push((element['next_expiration_date'] * 1));
                col.push(element['payments_status']);
                col.push(element['router_name']);
                col.push(element['wallet_amount']);
                col.push(element['client_account']);
                col.push(element['reffered_by']);
                col.push(element['comment']);
                col.push(element['location_coordinates']);
                col.push(element['assignment']);
                col.push(element['clients_reg_date']);
                // var col = element.split(":");
                rowsColStudents_2.push(col);
                //get the number of pages
                cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsColStudents_2);
        
                //show the number of pages for each record
                var counted = rows.length / 20;
                pagecountTransaction_2 = Math.ceil(counted);
        
                if (rowsColStudents_2.length > 0 ) {
                    cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
                    cObj("sort_by_name_2").addEventListener("click",sortByName_2);
                    cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
                    cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
                }
                cObj("tablefooter_2").classList.remove("invisible");
            }
            rowsNCols_original_2 = rowsColStudents_2;
            cObj("tot_records_2").innerText = rows.length;

        }else {
            cObj("demographics_data").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No clients found!</p>";
            cObj("tablefooter_2").classList.add("invisible");
        }
    }else {
        cObj("demographics_data").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No clients found!</p>";
        cObj("tablefooter_2").classList.add("invisible");
    }
}

cObj("select_dates").onchange = function () {
    if (valObj("select_dates") != "Select date"){
        cObj("specific_dates").classList.add("invisible");
    }else{
        cObj("specific_dates").classList.remove("invisible");
    }
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

/**
 * Start of the due demograohics display
 */

function displayRecord_2(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th><span  title='Sort by date registered' id='sort_by_reg_date_2' style='cursor:pointer;'># <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_name_2'   title='Sort by Client Name' style='cursor:pointer;'>Full Names <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_acc_number_2'   title='Sort by Account Number' style='cursor:pointer;'>Account Number <i class='ft-chevron-down'></i></span></th><th>Registration Date</th><th><span  id ='sort_by_expiration_2'   title='Sort by Expiration Date' style='cursor:pointer;'>Due Date <i class='ft-chevron-down'></i></span></th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][3] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var reffered = "";
                if (arrays[index][12] != null && arrays[index][12] != "") {
                    var mainData = arrays[index][12];
                    if(arrays[index][12].substr(0,1) == "\""){
                        mainData = mainData.substr(1,mainData.length-2);
                        mainData = mainData.replace(/\\/g, "");
                    }
                    // console.log(mainData);
                    var data = JSON.parse(mainData);
                    // get the client name
                    var fullname = "Null";
                    var id = 0;
                    for (let ind = 0; ind < rowsNCols_original_2.length; ind++) {
                        const element = rowsNCols_original_2[ind];
                        if (element[11] == data.client_acc) {
                            fullname = element[1];
                            id = element[0];
                        }
                    }
                    reffered = "<a href='/Clients/View/"+id+"' class='text-secondary'><span data-toggle='tooltip' title='Reffered by "+fullname+" {"+data.client_acc+"} @ Kes "+data.monthly_payment+"' class='badge badge-warning text-dark'>Reffered</span></a>";
                }
                var assignment = "";
                if (arrays[index][15] == "static") {
                    assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
                }else if (arrays[index][15] == "pppoe"){
                    assignment = "<span class='badge text-light' style = 'background:rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
                }
            
            tableData += "<tr><th scope='row'>"+counter+"</th><td>"+assignment+" <a href='/Clients/View/"+arrays[index][0]+"' class='text-secondary'>" + ucwords(arrays[index][1]) +" "+status+"</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) +"</small></td><td>" + arrays[index][11].toUpperCase() + "</td><td>" + setDate(arrays[index][16]) + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> </td></tr>";
            counter++;
        }
    }else{
        //create a table of the 20 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][3] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            var assignment = "";
            if (arrays[index][15] == "static") {
                assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
            }else if (arrays[index][15] == "pppoe"){
                assignment = "<span class='badge text-light' style = 'background: rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
            }
            
            // console.log(location);
            tableData += "<tr><th scope='row'>"+counter+"</th><td>"+assignment+" <a href='/Clients/View/"+arrays[index][0]+"' class='text-secondary'>" + ucwords(arrays[index][1]) +" "+status+"</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) +"</small></td><td>" + arrays[index][11].toUpperCase() + "</td><td>" + setDate(arrays[index][16]) + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a></td></tr>";
            counter++;
        }
        fins = total;
    }

    tableData += "</tbody></table>";
    //set the start and the end value
    cObj("startNo_2").innerText = start + 1;
    cObj("finishNo_2").innerText = fins;
    //set the page number
    cObj("pagenumNav_2").innerText = pagecounttrans_2;
    // set tool tip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    return tableData;
}


//next record 
//add the page by one and the number os rows to dispay by 20
cObj("tonextNav_2").onclick = function() {
        if (pagecounttrans_2 < pagecountTransaction_2) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage_2 += 20;
            pagecounttrans_2++;
            var endpage = startpage_2 + 20;
            cObj("demographics_data").innerHTML = displayRecord_2(startpage_2, endpage, rowsColStudents_2);
            if (rowsColStudents_2.length > 0 ) {
                cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
                cObj("sort_by_name_2").addEventListener("click",sortByName_2);
                cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
                cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
            }
        } else {
            pagecounttrans_2 = pagecountTransaction_2;
        }
    }
    // end of next records
cObj("toprevNac_2").onclick = function() {
    if (pagecounttrans_2 > 1) {
        pagecounttrans_2--;
        startpage_2 -= 20;
        var endpage = startpage_2 + 20;
        cObj("demographics_data").innerHTML = displayRecord_2(startpage_2, endpage, rowsColStudents_2);
        if (rowsColStudents_2.length > 0 ) {
            cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
            cObj("sort_by_name_2").addEventListener("click",sortByName_2);
            cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
            cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
        }
    }
}
cObj("tofirstNav_2").onclick = function() {
    if (pagecountTransaction_2 > 0) {
        pagecounttrans_2 = 1;
        startpage_2 = 0;
        var endpage = startpage_2 + 20;
        cObj("demographics_data").innerHTML = displayRecord_2(startpage_2, endpage, rowsColStudents_2);
        if (rowsColStudents_2.length > 0 ) {
            cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
            cObj("sort_by_name_2").addEventListener("click",sortByName_2);
            cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
            cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
        }
    }
}
cObj("tolastNav_2").onclick = function() {
    if (pagecountTransaction_2 > 0) {
        pagecounttrans_2 = pagecountTransaction_2;
        startpage_2 = (pagecounttrans_2 * 20) - 20;
        var endpage = startpage_2 + 20;
        cObj("demographics_data").innerHTML = displayRecord_2(startpage_2, endpage, rowsColStudents_2);
        if (rowsColStudents_2.length > 0 ) {
            cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
            cObj("sort_by_name_2").addEventListener("click",sortByName_2);
            cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
            cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
        }
    }
}

// seacrh keyword at the table
cObj("searchkey_2").onkeyup = function() {
        checkName_2(this.value);
    }
    //create a function to check if the array has the keyword being searched for
function checkName_2(keyword) {
    rowsColStudents_2 = rowsNCols_original_2;
    pagecounttrans_2 = 1;
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
    for (let index = 0; index < rowsColStudents_2.length; index++) {
        const element = rowsColStudents_2[index];
        //column break
        var present = 0;
        if (element[1].toLowerCase().includes(keylower) || element[1].toUpperCase().includes(keyUpper)) {
            present++;
        }
        // console.log(element);
        // if (element[13].toLowerCase().includes(keylower) || element[13].toUpperCase().includes(keyUpper)) {
        //     present++;
        // }
        if (element[4].toLowerCase().includes(keylower) || element[4].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toLowerCase().includes(keylower) || element[5].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[11].toLowerCase().includes(keylower)) {
            present++;
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
            router_and_keyword = rowsNcol2;
        }
    }
    if (rowsNcol2.length > 0) {
        rowsColStudents_2 = rowsNcol2;
        var counted = rowsNcol2.length / 20;
        pagecountTransaction_2 = Math.ceil(counted);
        cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsNcol2);
        cObj("tot_records_2").innerText = rowsNcol2.length;
        if (rowsColStudents_2.length > 0 ) {
            cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
            cObj("sort_by_name_2").addEventListener("click",sortByName_2);
            cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
            cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
        }
    } else {
        cObj("demographics_data").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        // cObj("tablefooter").classList.add("invisible");
        cObj("startNo").innerText = 0;
        cObj("finishNo").innerText = 0;
        cObj("tot_records_2").innerText = 0;
        pagecountTransaction_2 = 1;
    }
}

var sort_by_date_2 = 0;
function sortByRegDate_2() {
    rowsColStudents_2 = sortAsc_2(rowsColStudents_2,0);
    if (sort_by_date_2 == 0) {
        sort_by_date_2 = 1;
        rowsColStudents_2 = sortAsc_2(rowsColStudents_2,0);
    }else{
        sort_by_date_2 = 0;
        rowsColStudents_2 = sortDesc_2(rowsColStudents_2,0);
    }
    // console.log(sort_by_date_2);
    cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsColStudents_2);
    if (sort_by_date_2 == 0) {
        cObj("sort_by_reg_date_2").innerHTML = "# <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_reg_date_2").innerHTML = "# <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents_2.length > 0 ) {
        cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
        cObj("sort_by_name_2").addEventListener("click",sortByName_2);
        cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
        cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
    }
}
/***SOrt by expiration date */

var sort_by_expirations_2 = 0;
function sortByExpDate_2() {
    rowsColStudents_2 = sortAsc_2(rowsColStudents_2,7);
    if (sort_by_expirations_2 == 0) {
        sort_by_expirations_2 = 1;
        rowsColStudents_2 = sortAsc_2(rowsColStudents_2,7);
    }else{
        sort_by_expirations_2 = 0;
        rowsColStudents_2 = sortDesc_2(rowsColStudents_2,7);
    }
    // console.log(sort_by_expirations_2);
    cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsColStudents_2);
    if (sort_by_expirations_2 == 0) {
        cObj("sort_by_expiration_2").innerHTML = "Due Date <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_expiration_2").innerHTML = "Due Date <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents_2.length > 0 ) {
        cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
        cObj("sort_by_name_2").addEventListener("click",sortByName_2);
        cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
        cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
    }
}
/**End of sort by expiration data */
var sortbyname_2 = 0;
function sortByName_2() {
    rowsColStudents_2 = sortAsc_2(rowsColStudents_2,1);
    if (sortbyname_2 == 0) {
        sortbyname_2 = 1;
        rowsColStudents_2 = sortAsc_2(rowsColStudents_2,1);
    }else{
        sortbyname_2 = 0;
        rowsColStudents_2 = sortDesc_2(rowsColStudents_2,1);
    }
    // console.log(sortbyname_2);
    cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsColStudents_2);
    if (sortbyname_2 == 0) {
        cObj("sort_by_name_2").innerHTML = "Full Names <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_name_2").innerHTML = "Full Names <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents_2.length > 0 ) {
        cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
        cObj("sort_by_name_2").addEventListener("click",sortByName_2);
        cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
        cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
    }
}
var sortbyaccno_2 = 0;
function sortByAccNo_2() {
    rowsColStudents_2 = sortAsc_2(rowsColStudents_2,11);
    if (sortbyaccno_2 == 0) {
        sortbyaccno_2 = 1;
        rowsColStudents_2 = sortAsc_2(rowsColStudents_2,11);
    }else{
        sortbyaccno_2 = 0;
        rowsColStudents_2 = sortDesc_2(rowsColStudents_2,11);
    }
    // console.log(sortbyaccno_2);
    cObj("demographics_data").innerHTML = displayRecord_2(0, 20, rowsColStudents_2);
    if (sortbyaccno_2 == 0) {
        cObj("sort_by_acc_number_2").innerHTML = "Account Number <i class='ft-chevron-down'></i>";
    }else{
        cObj("sort_by_acc_number_2").innerHTML = "Account Number <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents_2.length > 0 ) {
        cObj("sort_by_reg_date_2").addEventListener("click",sortByRegDate_2);
        cObj("sort_by_name_2").addEventListener("click",sortByName_2);
        cObj("sort_by_acc_number_2").addEventListener("click",sortByAccNo_2);
        cObj("sort_by_expiration_2").addEventListener("click",sortByExpDate_2);
    }
}

function sortDesc_2(arrays,index){
    arrays = arrays.sort(sortFunction);
    function sortFunction(a, b) {
        if (a[index] === b[index]) {
            return 0;
        }
        else {
            return (a[index] > b[index]) ? -1 : 1;
        }
    }
    return arrays;
}
