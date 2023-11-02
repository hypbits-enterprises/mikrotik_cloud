// get the data from the database
var student_data = data;
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

var rowsColStudents = [];
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
            col.push(element['client_id']);
            col.push(element['client_name']);
            col.push(element['client_network']);
            col.push(element['client_status']);
            col.push(element['clients_contacts']);
            col.push(element['client_address']);
            col.push(element['monthly_payment']);
            col.push(element['next_expiration_date']);
            col.push(element['payments_status']);
            col.push(element['router_name']);
            col.push(element['wallet_amount']);
            col.push(element['client_account']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }

        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 10;
        pagecountTransaction = Math.ceil(counted);

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
            
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    var transaction_id = cObj("transaction_id").value;
    var transaction_assign_flag = cObj("transaction_assign_flag").value;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Full Names</th><th>Phone Number</th><th>Account Number</th><th>Location</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][3] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][1] +" "+status+"</td><td>" + arrays[index][4] + "</td><td>" + arrays[index][11] + "</td><td>" + arrays[index][5] + "</td><td><a href='/Assign/Transaction/"+transaction_id+"/Client/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder "+transaction_assign_flag+"' data-toggle='tooltip' title='View this User'><i class='ft-edit'></i> Assign</a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][3] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][1] +" "+status+"</td><td>" + arrays[index][4] + "</td><td>" + arrays[index][11] + "</td><td>" + arrays[index][5] + "</td><td><a href='/Assign/Transaction/"+transaction_id+"/Client/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder "+transaction_assign_flag+"' data-toggle='tooltip' title='View this User'><i class='ft-edit'></i> Assign</a></td></tr>";
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
    return tableData;
}
//next record 
//add the page by one and the number os rows to dispay by 10
cObj("tonextNav").onclick = function() {
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 10;
            pagecounttrans++;
            var endpage = startpage + 10;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        } else {
            pagecounttrans = pagecountTransaction;
        }
    }
    // end of next records
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 10;
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 10) - 10;
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}

// seacrh keyword at the table
cObj("searchkey").onkeyup = function() {
        checkName(this.value);
    }
    //create a function to check if the array has the keyword being searched for
function checkName(keyword) {
    if (keyword.length > 0) {
        cObj("tablefooter").classList.add("invisible");
    } else {
        cObj("tablefooter").classList.remove("invisible");
    }
    var rowsNcol2 = [];
    var keylower = keyword.toLowerCase();
    var keyUpper = keyword.toUpperCase();
    //row break
    for (let index = 0; index < rowsColStudents.length; index++) {
        const element = rowsColStudents[index];
        //column break
        var present = 0;
        if (element[1].toString().toLowerCase().includes(keylower) || element[1].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[4].toString().toLowerCase().includes(keylower) || element[4].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toString().toLowerCase().includes(keylower) || element[5].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[11].toString().toLowerCase().includes(keylower) || element[11].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        // console.log(element[1]);
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsNcol2);
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}