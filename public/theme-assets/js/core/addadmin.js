
// get the data from the database
var student_data = admin_data;
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
    // show that the username is already used
    var admin_username = document.getElementById("admin_username");
    admin_username.onkeyup = function () {
        var values = this.value.toUpperCase();
        if (values.length > 0){
            var present = 0;
            for (let index = 0; index < username.length; index++) {
                const element = username[index].toUpperCase();
                if (element.includes(values)) {
                    present = 1;
                    break;
                }
            }
            if (present == 1) {
                document.getElementById("error_acc_no").innerText = "Account number in use!";
                document.getElementById("admin_username").classList.add("border");
                document.getElementById("admin_username").classList.add("border-danger");
            }else{
                document.getElementById("error_acc_no").innerText = "";
                document.getElementById("admin_username").classList.remove("border");
                document.getElementById("admin_username").classList.remove("border-danger");
            }
        }else{
            document.getElementById("error_acc_no").innerText = "";
            document.getElementById("admin_username").classList.remove("border");
            document.getElementById("admin_username").classList.remove("border-danger");
        }
    }

    // get the arrays
    if (student_data.length > 0) {
        var rows = student_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['admin_id']);
            col.push(element['admin_fullname']);
            col.push(element['admin_username']);
            col.push(element['admin_password']);
            col.push(element['last_time_login']);
            col.push(element['organization_id']);
            col.push(element['contacts']);
            col.push(element['user_status']);
            col.push(dates[index]);
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
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='fas fa-exclamation-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
            
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    var readonly_flag = cObj("readonly_flag").value;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Full Names</th><th>Username</th><th>Last Time Login</th><th>Contact</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][7] == "0") {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][1] +" "+status+"</td><td>" + arrays[index][2] + "</td><td>" + arrays[index][8] + "</td><td>" + arrays[index][6] + "</td><td><a href='/Admin/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> <a href='/admin/deactivate/"+arrays[index][0]+"' class='btn btn-sm btn-warning text-bolder "+readonly_flag+"'  data-toggle='tooltip' title='Disable this User'><i class='ft-alert-octagon'></i></a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][7] == "0") {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            console.log(arrays[index][7]);
            tableData += "<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][1] +" "+status+"</td><td>" + arrays[index][2] + "</td><td>" + arrays[index][8] + "</td><td>" + arrays[index][6] + "</td><td><a href='/Admin/View/"+arrays[index][0]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a> <a href='/admin/deactivate/"+arrays[index][0]+"' class='btn btn-sm btn-warning text-bolder "+readonly_flag+"'  data-toggle='tooltip' title='Disable this User'><i class='ft-alert-octagon'></i></a></td></tr>";
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
        if (element[1].toLowerCase().includes(keylower) || element[1].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[2].toLowerCase().includes(keylower) || element[2].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[4].toLowerCase().includes(keylower) || element[4].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toLowerCase().includes(keylower) || element[5].toUpperCase().includes(keyUpper)) {
            present++;
        }
        console.log(element[1]);
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsNcol2);
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='fas fa-exclamation-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function checkChecked() {
    var all_readonly = document.getElementsByClassName("all_readonly");
    var total = all_readonly.length;
    var checked = 0;
    for (let index = 0; index < all_readonly.length; index++) {
        const element = all_readonly[index];
        if (element.checked == true) {
            checked++;
        }
    }
    if (checked > 0){
        if (checked == total) {
            cObj("all_readonly").checked = true;
            cObj("all_readonly").indeterminate  = false;
        }else{
            cObj("all_readonly").checked = false;
            cObj("all_readonly").indeterminate  = true;
        }
    }else{
        cObj("all_readonly").checked = false;
        cObj("all_readonly").indeterminate  = false;
    }


    var all_view = document.getElementsByClassName("all_view");
    var total = all_view.length;
    var checked = 0;
    for (let index = 0; index < all_view.length; index++) {
        const element = all_view[index];
        if (element.checked == true) {
            checked++;
        }
    }
    if (checked > 0) {
        if (checked == total) {
            cObj("all_view").checked = true;
            cObj("all_view").indeterminate  = false;
        }else{
            cObj("all_view").checked = false;
            cObj("all_view").indeterminate  = true;
        }
    }else{
        cObj("all_view").checked = false;
        cObj("all_view").indeterminate  = false;
    }
}

cObj("my_clients_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_clients_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Clients",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Clients") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("my_clients_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Clients",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("my_clients_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_clients_option_view").checked == true ? true : false;
        var your_data = {option:"My Clients",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Clients") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("my_clients_option_view").checked == true ? true : false;
        var your_data = {option:"My Clients",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("transactions_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("transactions_option_readonly").checked == true ? true : false;
        var your_data = {option:"Transactions",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Transactions") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("transactions_option_readonly").checked == true ? true : false;
        var your_data = {option:"Transactions",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    checkChecked();
}

cObj("transactions_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("transactions_option_view").checked == true ? true : false;
        var your_data = {option:"Transactions",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Transactions") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("transactions_option_view").checked == true ? true : false;
        var your_data = {option:"Transactions",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options_2();
    checkChecked();
}

cObj("expenses_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("expenses_option_readonly").checked == true ? true : false;
        var your_data = {option:"Expenses",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Expenses") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("expenses_option_readonly").checked == true ? true : false;
        var your_data = {option:"Expenses",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    checkChecked();
}

cObj("expenses_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("expenses_option_view").checked == true ? true : false;
        var your_data = {option:"Expenses",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Expenses") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("expenses_option_view").checked == true ? true : false;
        var your_data = {option:"Expenses",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options_2();
    checkChecked();
}

cObj("my_routers_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_routers_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Routers",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Routers") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("my_routers_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Routers",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("my_routers_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_routers_option_view").checked == true ? true : false;
        var your_data = {option:"My Routers",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Routers") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("my_routers_option_view").checked == true ? true : false;
        var your_data = {option:"My Routers",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("sms_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("sms_option_readonly").checked == true ? true : false;
        var your_data = {option:"SMS",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "SMS") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("sms_option_readonly").checked == true ? true : false;
        var your_data = {option:"SMS",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("sms_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("sms_option_view").checked == true ? true : false;
        var your_data = {option:"SMS",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "SMS") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("sms_option_view").checked == true ? true : false;
        var your_data = {option:"SMS",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("account_profile_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("account_profile_option_readonly").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Account and Profile") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("account_profile_option_readonly").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("account_profile_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("account_profile_option_view").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Account and Profile") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("account_profile_option_view").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    checkChecked();
}

cObj("accounts_option_view").onchange = function () {
    cObj("transactions_option_view").checked = this.checked;
    cObj("expenses_option_view").checked = this.checked;

    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            if (element.option == "Transactions" || element.option == "Expenses") {
                element.view = this.checked;
            }
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    checkChecked();
}
cObj("all_view").onchange = function () {
    var all_view = document.getElementsByClassName("all_view");
    for (let index = 0; index < all_view.length; index++) {
        const elem = all_view[index];
        elem.checked = this.checked;
    }
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            element.view = this.checked;
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    checkChecked();
}

cObj("all_readonly").onchange = function () {
    var all_readonly = document.getElementsByClassName("all_readonly");
    for (let index = 0; index < all_readonly.length; index++) {
        const element = all_readonly[index];
        element.checked = this.checked;
    }


    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            element.readonly = this.checked;
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
}

cObj("accounts_option_readonly").onchange = function () {
    var account_options_2 = document.getElementsByClassName("account_options_2");
    for (let index = 0; index < account_options_2.length; index++) {
        const element = account_options_2[index];
        element.checked = this.checked;
    }
    
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            if (element.option == "Transactions" || element.option == "Expenses") {
                element.readonly = this.checked;
            }
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    checkChecked();
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

function account_options() {
    var account_options = document.getElementsByClassName("account_options");
    var count = account_options.length;
    var checked = 0;
    for (let index = 0; index < account_options.length; index++) {
        const element = account_options[index];
        if (element.checked) {
            checked++;
        }
    }

    if (checked > 0) {
        if (checked == count) {
            cObj("accounts_option_view").checked = true;
            cObj("accounts_option_view").indeterminate = false;
        }else{
            cObj("accounts_option_view").checked = false;
            cObj("accounts_option_view").indeterminate = true;
        }
    }else{
        cObj("accounts_option_view").checked = false;
        cObj("accounts_option_view").indeterminate = false;
    }
}

function account_options_2() {
    var account_options = document.getElementsByClassName("account_options_2");
    var count = account_options.length;
    var checked = 0;
    for (let index = 0; index < account_options.length; index++) {
        const element = account_options[index];
        if (element.checked) {
            checked++;
        }
    }

    if (checked > 0) {
        if (checked == count) {
            cObj("accounts_option_readonly").checked = true;
            cObj("accounts_option_readonly").indeterminate = false;
        }else{
            cObj("accounts_option_readonly").checked = false;
            cObj("accounts_option_readonly").indeterminate = true;
        }
    }else{
        cObj("accounts_option_readonly").checked = false;
        cObj("accounts_option_readonly").indeterminate = false;
    }
}