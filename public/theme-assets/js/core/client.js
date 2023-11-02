// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get the data from the database
var student_data = data;
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

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function () {
    // console.log(student_data);
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
            col.push((element['next_expiration_date'] * 1));
            col.push(element['payments_status']);
            col.push(element['router_name']);
            col.push(element['wallet_amount']);
            col.push(element['client_account']);
            col.push(element['reffered_by']);
            col.push(element['comment']);
            col.push(element['location_coordinates']);
            col.push(element['assignment']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        // console.log(rowsNCols_original);
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 50;
        pagecountTransaction = Math.ceil(counted);

        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

            // check and uncheck all fields that have been selected
            checkedUnchecked();
        }

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function checkedUnchecked() {
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        // check if values of the selected client is included if included check the boxes
        for (let index = 0; index < hold_user_id_data.length; index++) {
            const element = hold_user_id_data[index];
            if (cObj("actions_id_"+element) != null) {
                cObj("actions_id_"+element).checked = true;
            }
        }
    }
    // check box event listener
    var actions_id = document.getElementsByClassName("actions_id");
    for (let index = 0; index < actions_id.length; index++) {
        const element = actions_id[index];
        element.addEventListener("change",addSelectedClients);
    }


}
var minium_client_lists = "";
function getNames() {
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        var client_lists = "";
        // check if values of the selected client is included if included check the boxes
        for (let index = (hold_user_id_data.length - 1); index >= 0; index--) {
            const element = hold_user_id_data[index];
            var client_name = (hold_user_id_data.length - index) + ". Null {"+element+"}";
            for (let inds = 0; inds < student_data.length; inds++) {
                const elems = student_data[inds];
                if (elems['client_account'] == element) {
                    client_name = elems['client_name']+" {"+element+"}";
                    break;
                }
            }
            client_lists+="<div class='badge border-info primary badge-border'>"+(hold_user_id_data.length - index) +". "+client_name+"</div>";
            if ((hold_user_id_data.length - index) == 20) {
                break;
            }
        }
        // client_lists = client_lists.substring(0,client_lists.length-3);
        minium_client_lists = client_lists;
        // console.log(minium_client_lists);

        if (hold_user_id_data.length > 20) {
            cObj("clients_selected").innerHTML = client_lists+".. <b class='text-primary' style='cursor:pointer;' id='show_more'>Show More</b>";

            // get the other selected users
            var client_lists = "";
            // check if values of the selected client is included if included check the boxes
            for (let index = (hold_user_id_data.length - 1); index >= 0; index--) {
                const element = hold_user_id_data[index];
                var client_name = (hold_user_id_data.length - index) + ". Null {"+element+"}";
                for (let inds = 0; inds < student_data.length; inds++) {
                    const elems = student_data[inds];
                    if (elems['client_account'] == element) {
                        client_name = elems['client_name']+" {"+element+"}";
                        break;
                    }
                }
                client_lists+="<div class='badge border-info primary badge-border'>"+(hold_user_id_data.length - index) +". "+client_name+""+"</div>";
            }
            // client_lists = client_lists.substring(0,client_lists.length-3);
            cObj("clients_list_selected").value = client_lists;

            // add the eventlistener for the show more button
            cObj("show_more").addEventListener("click",showMoreFunc);
        }else{
            cObj("clients_selected").innerHTML = client_lists;
        }

        if (hold_user_id_data.length == 0) {
            cObj("action_for_selected_window").classList.add("hide");
        }else{
            cObj("action_for_selected_window").classList.remove("hide");
        }
        cObj("delete_number_clients").innerText = hold_user_id_data.length;

        // set the value for the second data holder
        cObj("hold_user_id_data_2").value = cObj("hold_user_id_data").value;

        if (hold_user_id_data.length == student_data.length) {
            cObj("select_all_clients").indeterminate = false;
            cObj("select_all_clients").checked = true;
        }else{
            cObj("select_all_clients").indeterminate = true;
            cObj("select_all_clients").checked = false;
        }
    }
}

cObj("delete_clients_id").onclick = function () {
    cObj("delete_clients_window").classList.toggle("hide");
}
cObj("no_dont_delete_selected").onclick = function () {
    cObj("delete_clients_window").classList.add("hide");
}

function showMoreFunc() {
    cObj("clients_selected").innerHTML = cObj("clients_list_selected").value+".. <b class='text-primary' style='cursor:pointer;' id='show_less'>Show Less</b>";
    cObj("show_less").addEventListener("click",showLessFunc);
}

function showLessFunc() {
    cObj("clients_selected").innerHTML = minium_client_lists+".. <b class='text-primary' style='cursor:pointer;' id='show_more'>Show More</b>";
    cObj("show_more").addEventListener("click",showMoreFunc);
}

cObj("select_all_clients").onchange = function () {
    if (this.checked) {
        var new_data = [];
        for (let inds = 0; inds < student_data.length; inds++) {
            const elems = student_data[inds];
            new_data.push(elems['client_account']);
        }
    
        cObj("hold_user_id_data").value = JSON.stringify(new_data);
        // uncheck 
        var actions_id = document.getElementsByClassName("actions_id");
        for (let index = 0; index < actions_id.length; index++) {
            const element = actions_id[index];
            element.checked = true;
        }
    
    }else{
        cObj("hold_user_id_data").value = "[]";
        // uncheck 
        var actions_id = document.getElementsByClassName("actions_id");
        for (let index = 0; index < actions_id.length; index++) {
            const element = actions_id[index];
            element.checked = false;
        }
    }

    // names
    getNames();

    // check the unchecked
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        // check if values of the selected client is included if included check the boxes
        for (let index = 0; index < hold_user_id_data.length; index++) {
            const element = hold_user_id_data[index];
            if (cObj("actions_id_"+element) != null) {
                cObj("actions_id_"+element).checked = true;
            }
        }
    }

    // count selected clients
    var clients_selected_count = cObj("hold_user_id_data").value;
    clients_selected_count = JSON.parse(clients_selected_count);
    cObj("client_select_counts").innerText = clients_selected_count.length+" Client(s) Selected";

}

function addSelectedClients() {
    var this_ids = this.id.substr(11);
    if (this.checked) {
        var hold_user_id_data = cObj("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);

            // is present
            if (!isPresent(hold_user_id_data,cObj("actions_value_"+this_ids).value)) {
                hold_user_id_data.push(cObj("actions_value_"+this_ids).value);
            }
            cObj("hold_user_id_data").value = JSON.stringify(hold_user_id_data);
        }else{
            cObj("hold_user_id_data").value = JSON.stringify([cObj("actions_value_"+this_ids).value]);
        }
    }else{
        var hold_user_id_data = cObj("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);
            var new_data = [];
            for (let index = 0; index < hold_user_id_data.length; index++) {
                const element = hold_user_id_data[index];
                if (element == cObj("actions_value_"+this_ids).value) {
                    continue;
                }
                new_data.push(element);
            }
            cObj("hold_user_id_data").value = JSON.stringify(new_data);
        }
    }

    var clients_selected_count = cObj("hold_user_id_data").value;
    clients_selected_count = JSON.parse(clients_selected_count);
    cObj("client_select_counts").innerText = clients_selected_count.length+" Client(s) Selected";

    // display all the clients that have been selected
    getNames();
}

function isPresent(array,object) {
    for (let index = 0; index < array.length; index++) {
        const element = array[index];
        if (element == object) {
            return true;
        }
    }
    return false;
}

var sort_by_date = 0;
function sortByRegDate() {
    rowsColStudents = sortAsc(rowsColStudents, 0);
    if (sort_by_date == 0) {
        sort_by_date = 1;
        rowsColStudents = sortAsc(rowsColStudents, 0);
    } else {
        sort_by_date = 0;
        rowsColStudents = sortDesc(rowsColStudents, 0);
    }
    // console.log(sort_by_date);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sort_by_date == 0) {
        cObj("sort_by_reg_date").innerHTML = "# <i class='ft-chevron-down'></i>";
    } else {
        cObj("sort_by_reg_date").innerHTML = "# <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0) {
        cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
        cObj("sort_by_name").addEventListener("click", sortByName);
        cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
        // check and uncheck all fields that have been selected
        checkedUnchecked();
    }
}
/***SOrt by expiration date */

var sort_by_expirations = 0;
function sortByExpDate() {
    rowsColStudents = sortAsc(rowsColStudents, 7);
    if (sort_by_expirations == 0) {
        sort_by_expirations = 1;
        rowsColStudents = sortAsc(rowsColStudents, 7);
    } else {
        sort_by_expirations = 0;
        rowsColStudents = sortDesc(rowsColStudents, 7);
    }
    // console.log(sort_by_expirations);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sort_by_expirations == 0) {
        cObj("sort_by_expiration").innerHTML = "Due Date <i class='ft-chevron-down'></i>";
    } else {
        cObj("sort_by_expiration").innerHTML = "Due Date <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0) {
        cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
        cObj("sort_by_name").addEventListener("click", sortByName);
        cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
        // check and uncheck all fields that have been selected
        checkedUnchecked();
    }
}
/**End of sort by expiration data */
var sortbyname = 0;
function sortByName() {
    rowsColStudents = sortAsc(rowsColStudents, 1);
    if (sortbyname == 0) {
        sortbyname = 1;
        rowsColStudents = sortAsc(rowsColStudents, 1);
    } else {
        sortbyname = 0;
        rowsColStudents = sortDesc(rowsColStudents, 1);
    }
    // console.log(sortbyname);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sortbyname == 0) {
        cObj("sort_by_name").innerHTML = "Full Names <i class='ft-chevron-down'></i>";
    } else {
        cObj("sort_by_name").innerHTML = "Full Names <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0) {
        cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
        cObj("sort_by_name").addEventListener("click", sortByName);
        cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
        // check and uncheck all fields that have been selected
        checkedUnchecked();
    }
}
var sortbyaccno = 0;
function sortByAccNo() {
    rowsColStudents = sortAsc(rowsColStudents, 11);
    if (sortbyaccno == 0) {
        sortbyaccno = 1;
        rowsColStudents = sortAsc(rowsColStudents, 11);
    } else {
        sortbyaccno = 0;
        rowsColStudents = sortDesc(rowsColStudents, 11);
    }
    // console.log(sortbyaccno);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sortbyaccno == 0) {
        cObj("sort_by_acc_number").innerHTML = "Account Number <i class='ft-chevron-down'></i>";
    } else {
        cObj("sort_by_acc_number").innerHTML = "Account Number <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0) {
        cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
        cObj("sort_by_name").addEventListener("click", sortByName);
        cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
        cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
        // check and uncheck all fields that have been selected
        checkedUnchecked();
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var readonly_flag = cObj("readonly_flag").value;
    var tableData = "<table class='table'><thead><tr><th><span  title='Sort by date registered' id='sort_by_reg_date' style='cursor:pointer;'># <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_name'   title='Sort by Client Name' style='cursor:pointer;'>Full Names <i class='ft-chevron-down'></i></span></th><th><span id ='sort_by_acc_number'   title='Sort by Account Number' style='cursor:pointer;'>Account Number <i class='ft-chevron-down'></i></span></th><th>Location</th><th><span  id ='sort_by_expiration'   title='Sort by Expiration Date' style='cursor:pointer;'>Due Date <i class='ft-chevron-down'></i></span></th><th>Action</th></tr></thead><tbody>";
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
            var reffered = "";
            if (arrays[index][12] != null && arrays[index][12] != "") {
                var mainData = arrays[index][12];
                if (arrays[index][12].substr(0, 1) == "\"") {
                    mainData = mainData.substr(1, mainData.length - 2);
                    mainData = mainData.replace(/\\/g, "");
                    mainData = mainData.replace(/'/g, "\"");
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
                reffered = (data.monthly_payment > 0 && fullname != "Null") ?"<a href='/Clients/View/" + id + "' class='text-secondary'><span data-toggle='tooltip' title='Reffered by " + fullname + " {" + data.client_acc + "} @ Kes " + data.monthly_payment + "' class='badge badge-warning text-dark'>Reffered</span></a>":"";
            }
            var assignment = "";
            if (arrays[index][15] == "static") {
                assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
            } else if (arrays[index][15] == "pppoe") {
                assignment = "<span class='badge text-light' style = 'background:rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
            }
            var location = (arrays[index][14] != null && arrays[index][14].length > 0) ? "<a class='text-danger' href = 'https://www.google.com/maps/place/" + arrays[index][14] + "' target = '_blank'><u>Locate Client</u> </a>" : "";
            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][11]+"'><input type='hidden' id='actions_value_"+arrays[index][11]+"' value='"+arrays[index][11]+"'> " + counter + "</th><td>" + assignment + " <a href='/Clients/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) + "</small></td><td>" + arrays[index][11].toUpperCase() + " " + reffered + "</td><td>" + ucwords(arrays[index][5]) + "<br><small class='d-none d-md-block'>" + location + "</small></td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> <a href='/deactivate/" + arrays[index][0] + "' class='btn btn-sm btn-danger text-dark text-bolder "+readonly_flag+"'  data-toggle='tooltip' title='Disable this User'><i class='ft-x'></i></a></td></tr>";
            counter++;
        }
    } else {
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][3] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            var reffered = "";
            if (arrays[index][12] != null && arrays[index][12] != "") {
                var mainData = arrays[index][12];
                if (arrays[index][12].substr(0, 1) == "\"") {
                    mainData = mainData.substr(1, mainData.length - 2);
                    mainData = mainData.replace(/\\/g, "");
                }
                console.log(mainData);
                if (hasJsonStructure(mainData)) {
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
                    reffered = (data.monthly_payment > 0 && fullname != "Null") ?"<a href='/Clients/View/" + id + "' class='text-secondary'><span data-toggle='tooltip' title='Reffered by " + fullname + " {" + data.client_acc + "} @ Kes " + data.monthly_payment + "' class='badge badge-warning text-dark'>Reffered</span></a>":"";
                }
            }
            var assignment = "";
            if (arrays[index][15] == "static") {
                assignment = "<span class='badge text-light' style='background: rgb(141, 110, 99);' data-toggle='tooltip' title='Static Assigned'>S</span>";
            } else if (arrays[index][15] == "pppoe") {
                assignment = "<span class='badge text-light' style = 'background: rgb(119, 105, 183);' data-toggle='tooltip' title='PPPoE Assigned'>P</span>";
            }
            var location = (arrays[index][14] != null && arrays[index][14].length > 0) ? "<a class='text-danger' href = 'https://www.google.com/maps/place/" + arrays[index][14] + "' target = '_blank'><u>Locate Client</u> </a>" : "";
            // console.log(location);
            tableData += "<tr><th scope='row'><input type='checkbox' class='actions_id' id='actions_id_"+arrays[index][11]+"'><input type='hidden' id='actions_value_"+arrays[index][11]+"' value='"+arrays[index][11]+"'> " + counter + "</th><td>" + assignment + " <a href='/Clients/View/" + arrays[index][0] + "' class='text-secondary'>" + ucwords(arrays[index][1]) + " " + status + "</a><br><small class='text-gray d-none d-xl-block'>" + ucword(arrays[index][13]) + "</small></td><td>" + arrays[index][11].toUpperCase() + " " + reffered + "</td><td>" + ucwords(arrays[index][5]) + "<br><small class='d-none d-md-block'>" + location + "</small></td><td>" + setDate(arrays[index][7]) + "</td><td><a href='/Clients/View/" + arrays[index][0] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> <a href='/deactivate/" + arrays[index][0] + "' class='btn btn-sm btn-danger text-dark text-bolder "+readonly_flag+"'  data-toggle='tooltip' title='Disable this User'><i class='ft-x'></i></a></td></tr>";
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
        final_word += element.substr(0, 1).toUpperCase() + element.substr(1) + " ";
    }
    return final_word.trim();
}
function ucword(string) {
    if (string != null) {
        var cases = string.toLowerCase();
        // split the string to get the number of words present
        var final_word = cases.substr(0, 1).toUpperCase() + cases.substr(1);
        return final_word.trim();
    }
    return "";
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
    return getDays(d.getDay()) + " " + d.getDate() + " " + getMonths(d.getMonth()) + " " + d.getFullYear() + " @ " + hours + ":" + minutes + ":" + seconds;
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
//next record 
//add the page by one and the number os rows to dispay by 50
cObj("tonextNav").onclick = function () {
    // console.log(pagecounttrans+" "+pagecountTransaction);
    if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
        startpage += 50;
        pagecounttrans++;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
            // check and uncheck all fields that have been selected
            checkedUnchecked();
        }
    } else {
        pagecounttrans = pagecountTransaction;
    }
}
// end of next records
cObj("toprevNac").onclick = function () {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

            // check and uncheck all fields that have been selected
            checkedUnchecked();
        }
    }
}
cObj("tofirstNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
            // check and uncheck all fields that have been selected
            checkedUnchecked();
        }
    }
}
cObj("tolastNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

            // check and uncheck all fields that have been selected
            checkedUnchecked();
        }
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
        rowsColStudents = rowsNCols_original;
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
        var counted = rowsNcol2.length / 50;
        pagecountTransaction = Math.ceil(counted);
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsNcol2);
        cObj("tot_records").innerText = rowsNcol2.length;
        if (rowsColStudents.length > 0) {
            cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            cObj("sort_by_name").addEventListener("click", sortByName);
            cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

            // check and uncheck all fields that have been selected
            checkedUnchecked();
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
var router_and_keyword = [];
cObj("client_status").onchange = function () {
    // change client status
    var status = this.value;
    rowsColStudents = router_and_keyword.length > 0 ? router_and_keyword : rowsNCols_original;
    // router_and_keyword = rowsNcol2;
    pagecounttrans = 1;
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    //row break
    if (status == "1" || status == "0") {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[3] == status) {
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
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = status == 0 ? "In-Active users" : "Active users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    } else if (status == "2") {
        // rowsNcol2 = rowsNCols_original;
        rowsNcol2 = router_and_keyword.length > 0 ? router_and_keyword : rowsNCols_original;
        // console.log(rowsNcol2);
        if (rowsNcol2.length > 0) {
            rowsColStudents = rowsNcol2;
            var counted = rowsNcol2.length / 50;
            pagecountTransaction = Math.ceil(counted);
            cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsNcol2);
            cObj("tot_records").innerText = rowsNcol2.length;
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = "Users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    } else if (status == "3") {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[12] != null && element[12] != "") {
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
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = "Reffered users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    } else if (status == "4") {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[15] != null && element[15] == "static") {
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
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = "Static assigned users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    } else if (status == "5") {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[15] != null && element[15] == "pppoe") {
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
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = "PPPoE assigned users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    }
}
// select router to display users
cObj("select_router").onchange = function () {
    // change client status
    var router_id = this.value;
    rowsColStudents = rowsNCols_original;
    pagecounttrans = 1;
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    //row break
    if (router_id != "all") {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[9] == router_id) {
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
            router_and_keyword = rowsNcol2;
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! no client found on the selected router not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    } else {
        rowsNcol2 = rowsNCols_original;
        // console.log(rowsNcol2);
        if (rowsNcol2.length > 0) {
            rowsColStudents = rowsNcol2;
            var counted = rowsNcol2.length / 50;
            pagecountTransaction = Math.ceil(counted);
            cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsNcol2);
            cObj("tot_records").innerText = rowsNcol2.length;
            router_and_keyword = rowsNcol2;
            if (rowsColStudents.length > 0) {
                cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
                cObj("sort_by_name").addEventListener("click", sortByName);
                cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
                cObj("sort_by_expiration").addEventListener("click", sortByExpDate);

                // check and uncheck all fields that have been selected
                checkedUnchecked();
            }
        } else {
            var keyword = "Users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    }
}
function sortDesc(arrays, index) {
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
function sortAsc(arrays, index) {
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

cObj("client_reports_btn").onclick = function () {
    cObj("show_generate_reports_window").classList.toggle("hide");
    if (cObj("show_generate_reports_window").classList.contains("hide")) {
        var datapass = "?showRouters=true";
    }
}

cObj("client_report_option").onchange = function () {
    var option = this.value;
    if (option == "client registration") {
        cObj("date_option").classList.remove("hide");
        cObj("select_router_window").classList.remove("hide");
        cObj("client_status_opt").classList.remove("hide");
    } else if (option == "client information" || option == "client router information") {
        cObj("date_option").classList.add("hide");
        cObj("select_date_win").classList.add("hide");
        cObj("select_router_window").classList.remove("hide");
        cObj("client_status_opt").classList.remove("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
        cObj("default_reg_date").selected = true;
    }
}
cObj("client_registration_date_option").onchange = function () {
    var option = this.value;
    if (option == "select date") {
        cObj("select_date_win").classList.remove("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    } else if (option == "between dates") {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.remove("hide");
        cObj("select_to_date_win").classList.remove("hide");
    } else {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    }
}