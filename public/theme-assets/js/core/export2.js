// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get the data from the database
var student_data = client_information_static;
var pppoe_data = client_information_pppoe;
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

var rowsColStudents_ppoe = [];
var rowsNCols_original_ppoe = [];
var pagecountTransaction_ppoe = 0; //this are the number of pages for transaction
var pagecounttrans_ppoe = 1; //the current page the user is
var startpage_ppoe = 0; // this is where we start counting the page number

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function() {
    // console.log(student_data.length);
    // get the arrays
    if (student_data.length > 0) {
        var rows = student_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['client_default_gw']);
            col.push(element['client_id']);
            col.push(element['client_name']);
            col.push(element['client_network']);
            col.push(element['gateway_match']);
            col.push(element['max_upload_download']);
            col.push(element['net_address_match']);
            col.push(element['queue_target']);
            col.push(element['speed_status']);
            col.push(element['target_address']);
            col.push(element['client_status']);
            col.push(element['interface_match']);
            col.push(element['client_interface']);
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 25, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 25;
        pagecountTransaction = Math.ceil(counted);

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
    

    // console.log(pppoe_data);
    // get the arrays
    if (pppoe_data.length > 0) {
        var rows = pppoe_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['secret_match']);
            col.push(element['profile_status']);
            col.push(element['client_status']);
            col.push(element['client_name']);
            col.push(element['client_password']);
            col.push(element['client_profile']);
            col.push(element['client_id']);
            col.push(element['client_secret']);
            rowsColStudents_ppoe.push(col);
        }
        rowsNCols_original_ppoe = rowsColStudents_ppoe;
        cObj("tot_records_ppoe").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(0, 25, rowsColStudents_ppoe);

        //show the number of pages for each record
        var counted = rows.length / 25;
        pagecountTransaction_ppoe = Math.ceil(counted);

    } else {
        cObj("transDataReciever_ppoe").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter_ppoe").classList.add("invisible");
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Client Name</th><th>Ip Address </th><th>Default GW/Mask</th><th>Target Address (Queues)</th><th>Internet Speed</th><th>Interface</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 25 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][10] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var gateway_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][4] == 1) {
                    gateway_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var net_address_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][6] == 1) {
                    net_address_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var speed_status = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][8] == 1) {
                    speed_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var target_address = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][9] == 1) {
                    target_address = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var interfaces = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][11] == 1) {
                    interfaces = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-secondary' href='/Clients/View/"+arrays[index][1]+"'>"+arrays[index][2]+" "+status+"</a></td><td>"+arrays[index][3]+" "+net_address_match+"</td><td>"+arrays[index][0]+" "+gateway_match+"</td><td>"+arrays[index][7]+" "+target_address+"</td><td>"+arrays[index][5]+" "+speed_status+"</td><td>"+arrays[index][12]+" "+interfaces+"</td><td><a href='/Client/epxsync/"+arrays[index][1]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='Sync Client Information'><i class='ft-refresh-ccw'></i></a></td></tr>";
                counter++;
        }
    }else{
        //create a table of the 25 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][10] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var gateway_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][4] == 1) {
                    gateway_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var net_address_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][6] == 1) {
                    net_address_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var speed_status = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][8] == 1) {
                    speed_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var target_address = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][9] == 1) {
                    target_address = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var interfaces = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][11] == 1) {
                    interfaces = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-secondary' href='/Clients/View/"+arrays[index][1]+"'>"+arrays[index][2]+" "+status+"</a></td><td>"+arrays[index][3]+" "+net_address_match+"</td><td>"+arrays[index][0]+" "+gateway_match+"</td><td>"+arrays[index][7]+" "+target_address+"</td><td>"+arrays[index][5]+" "+speed_status+"</td><td>"+arrays[index][12]+" "+interfaces+"</td><td><a href='/Client/epxsync/"+arrays[index][1]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='Sync Client Information'><i class='ft-refresh-ccw'></i></a></td></tr>";
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
//add the page by one and the number os rows to dispay by 25
cObj("tonextNav").onclick = function() {
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 25;
            pagecounttrans++;
            var endpage = startpage + 25;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        } else {
            pagecounttrans = pagecountTransaction;
        }
    }
    // end of next records
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 25;
        var endpage = startpage + 25;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 25;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 25) - 25;
        var endpage = startpage + 25;
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
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    var keylower = keyword.toLowerCase();
    var keyUpper = keyword.toUpperCase();
    //row break
    for (let index = 0; index < rowsColStudents.length; index++) {
        const element = rowsColStudents[index];
        //column break
        // console.log(element[3]);
        var present = 0;
        if (element[2].toLowerCase().includes(keylower) || element[2].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[3].includes(keyword)) {
            present++;
        }
        if (element[0].includes(keyword)) {
            present++;
        }
        if (element[5].includes(keyword)) {
            present++;
        }
        if (element[7].includes(keyword)) {
            present++;
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        rowsColStudents = rowsNcol2;
        var counted = rowsNcol2.length / 25;
        pagecountTransaction = Math.ceil(counted);
        cObj("transDataReciever").innerHTML = displayRecord(0, 25, rowsNcol2);
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
