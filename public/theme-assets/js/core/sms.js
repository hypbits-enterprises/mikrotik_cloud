// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get the data from the database
// var sms_data = sms_data;
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

// Send data with get
function sendDataGet(method, file, object1, object2) {
    //make the loading window show
    object2.classList.remove("d-none");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("d-none");
        } else if (this.status == 500) {
            object2.classList.add("d-none");
            // cObj("loadings").classList.add("d-none");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        }
    };
    xml.open(method, file, true);
    xml.send();
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function() {
    // console.log(sms_data.length);
    // get the arrays
    if (sms_data.length > 0) {
        var rows = sms_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['account_id']);
            col.push(element['date_sent']);
            col.push(element['recipient_phone']);
            col.push(element['sms_content']);
            col.push(element['sms_id']);
            col.push(element['sms_status']);
            col.push(element['sms_type']);

            // add the clientname in the row list
            col.push(client_names[index]);
            console.log(client_names.length);
            // add the dates also
            col.push(dates[index]);
            // var col = element.split(":");
            col.push(element['account_id']);
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);

        checkedUnchecked();
        //show the number of pages for each record
        var counted = rows.length / 50;
        pagecountTransaction = Math.ceil(counted);

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
            
}

function checkedUnchecked() {
    var actions_id = document.getElementsByClassName("actions_id");
    for (let index = 0; index < actions_id.length; index++) {
        const element = actions_id[index];
        element.addEventListener("change",addChecked);
    }
}

function addChecked() {
    var this_value = this.id.substr(11);
    if (this.checked) {
        var hold_user_id_data = document.getElementById("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);
            hold_user_id_data.push(this_value);
            cObj("hold_user_id_data").value = JSON.stringify(hold_user_id_data);
        }else{
            var data = [];
            data.push(this_value);

            cObj("hold_user_id_data").value = JSON.stringify(data);
        }
    }else{
        // remove the unchecked checkbox
        var hold_user_id_data = cObj("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);

            var new_data = [];
            for (let index = 0; index < hold_user_id_data.length; index++) {
                const element = hold_user_id_data[index];
                if (element == this_value) {
                    continue;
                }
                new_data.push(element);
            }
            cObj("hold_user_id_data").value = JSON.stringify(new_data);
        }else{
            cObj("hold_user_id_data").value = "[]";
        }
    }

    // get the count data of the
    var hold_user_id_data = cObj("hold_user_id_data").value;
    var count = hasJsonStructure(hold_user_id_data) ? JSON.parse(hold_user_id_data).length : 0;
    cObj("client_select_counts").innerText = count + " SMS(s) Selected";
    cObj("delete_number_clients").innerText = count;
    
    // checkbox indeterminate
    if (count > 0) {
        // display the stats window
        cObj("action_for_selected_window").classList.remove("hide");
        if (count == sms_data.length) {
            cObj("select_all_clients").checked = true;
            cObj("select_all_clients").indeterminate = false;
        }else{
            cObj("select_all_clients").checked = false;
            cObj("select_all_clients").indeterminate = true;
        }
    }else{
        // hide the stats window
        cObj("action_for_selected_window").classList.add("hide");
        
        cObj("select_all_clients").checked = false;
        cObj("select_all_clients").indeterminate = false;
    }

    // set the next value holder the same
    cObj("hold_user_id_data_2").value = cObj("hold_user_id_data").value;
}

cObj("select_all_clients").onchange = function () {
    if (this.checked) {
        var ids = [];
        for (let index = 0; index < sms_data.length; index++) {
            const element = sms_data[index];
            ids.push(element.sms_id+"");
        }
        
        cObj("hold_user_id_data_2").value = JSON.stringify(ids);
        cObj("hold_user_id_data").value = JSON.stringify(ids);
    }else{
        cObj("hold_user_id_data_2").value = "[]";
        cObj("hold_user_id_data").value = "[]";
    }

    // check all checked
    checkUnchecked();
}

function checkUnchecked() {
    // uncheck all
    var actions_id = document.getElementsByClassName("actions_id");
    for (let index = 0; index < actions_id.length; index++) {
        const element = actions_id[index];
        element.checked = false;
    }

    // check the neccessary ones
    var data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(data)) {
        data = JSON.parse(data);
        for (let index = 0; index < data.length; index++) {
            const element = data[index];
            if (cObj("actions_id_"+element) != null) {
                cObj("actions_id_"+element).checked = true;
            }
        }
    }

    // number of checked sms
    var hold_user_id_data = cObj("hold_user_id_data").value;
    var count = hasJsonStructure(hold_user_id_data) ? JSON.parse(hold_user_id_data).length : 0;
    cObj("client_select_counts").innerText = count + " SMS(s) Selected";
    cObj("delete_number_clients").innerText = count;

    if (count > 0) {
        // display the stats window
        cObj("action_for_selected_window").classList.remove("hide");
    }else{
        // display the stats window
        cObj("action_for_selected_window").classList.add("hide");
    }
}

cObj("delete_clients_id").onclick = function () {
    cObj("delete_clients_window").classList.toggle("hide");
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

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Date Sent</th><th>Message Body</th><th>Message Type</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {

            var message = (arrays[index][3].length > 100) ? arrays[index][3].substr(0,100)+". . .": arrays[index][3];
            var transactiontype = (arrays[index][6] == 1) ? "Transaction" : "Notification";
            var status = (arrays[index][5] == "1") ? "<span class='badge badge-success'> </span>":"<span class='badge badge-danger'> </span>";

            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][4]+"'><input type='hidden' id='actions_value_"+arrays[index][4]+"' value='"+arrays[index][4]+"'> "+counter+"  <a href='/sms/resend/"+arrays[index][4]+"' class='text-bolder' data-toggle='tooltip' title='Re-send this Message'><i class='ft-refresh-ccw'></i></a></th><td>"+arrays[index][8] +" "+status+"<br><small><a class='text-secondary' href='/Clients/View/"+arrays[index][0]+"'>{"+arrays[index][7] +"}</a></small></td><td><span data-toggle='tooltip' data-html='true' title='"+arrays[index][3]+"'>"+message+"</span></td><td>"+transactiontype+"</td><td><a href='/sms/View/"+arrays[index][4]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this Message'><i class='ft-eye'></i></a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var message = (arrays[index][3].length > 100) ? arrays[index][3].substr(0,100)+". . .": arrays[index][3];
            var transactiontype = (arrays[index][6] == 1) ? "Transaction" : "Notification";
            var status = (arrays[index][5] == "1") ? "<span class='badge badge-success'> </span>":"<span class='badge badge-danger'> </span>";

            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][4]+"'><input type='hidden' id='actions_value_"+arrays[index][4]+"' value='"+arrays[index][4]+"'> "+counter+"  <a href='/sms/resend/"+arrays[index][4]+"' class='text-bolder' data-toggle='tooltip' title='Re-send this Message'><i class='ft-refresh-ccw'></i></a></th><td>"+arrays[index][8] +" "+status+"<br><small><a class='text-secondary' href='/Clients/View/"+arrays[index][0]+"'>{"+arrays[index][7] +"}</a></small></td><td><span data-toggle='tooltip' data-html='true' title='"+arrays[index][3]+"'>"+message+"</span></td><td>"+transactiontype+"</td><td><a href='/sms/View/"+arrays[index][4]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this Message'><i class='ft-eye'></i></a></td></tr>";
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
//add the page by one and the number os rows to dispay by 50
cObj("tonextNav").onclick = function() {
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 50;
            pagecounttrans++;
            var endpage = startpage + 50;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        } else {
            pagecounttrans = pagecountTransaction;
        }
        checkUnchecked();
        checkedUnchecked()
    }
    // end of next records
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
    checkUnchecked();
    checkedUnchecked()
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
    checkUnchecked();
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
    checkUnchecked();
    checkedUnchecked()
}

// seacrh keyword at the table
cObj("searchkey").onkeyup = function() {
    // console.log(cObj("searchkey").value);
        checkName(this.value);
        checkUnchecked();
        checkedUnchecked()
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
        if (element[7].toLowerCase().includes(keylower) || element[7].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[8].toLowerCase().includes(keylower) || element[8].toUpperCase().includes(keyUpper)) {
            present++;
        }
        var word = element[2]+"";
        if (word.toLowerCase().includes(keylower)) {
            present++;
        }
        if (element[3].toLowerCase().includes(keylower) || element[3].toUpperCase().includes(keyUpper)) {
            present++;
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
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

// get the time

// get the sms balance
cObj("show_sms_balance_btn").onclick = function () {
    sendDataGet("GET","/sms_balance/",cObj("show_sms_balance"),cObj("show_sms_loader"));
}

cObj("sms_reports_btn").onclick = function () {
    cObj("show_generate_reports_window").classList.toggle("hide");
}

cObj("sms_date_option").onchange = function () {
    var option = this.value;
    if (option == "select date") {
        cObj("select_date_win").classList.remove("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    }else if (option == "all dates") {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    }else if (option == "between dates") {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.remove("hide");
        cObj("select_to_date_win").classList.remove("hide");
    }
}

cObj("select_user_option").onchange = function () {
    var option = this.value;
    if (option == "All") {
        cObj("client_status_opt").classList.add("hide");
        cObj("client_status_phone_no").classList.add("hide");
    }else if (option == "specific_user") {
        cObj("client_status_opt").classList.remove("hide");
        cObj("client_status_phone_no").classList.add("hide");
    }else if (option == "specific_user_phone") {
        cObj("client_status_phone_no").classList.remove("hide");
        cObj("client_status_opt").classList.add("hide");
    }
}

cObj("contain_text_option").onchange =  function () {
    var this_value = this.value;
    if (this_value == "text_containing") {
        cObj("text_containing_window").classList.remove("hide");
    }else{
        cObj("text_containing_window").classList.add("hide");
    }
}