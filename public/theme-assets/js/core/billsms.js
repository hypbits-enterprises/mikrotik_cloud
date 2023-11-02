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

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function() {
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
            col.push(element['client_location']);
            col.push(element['phone_number']);
            col.push(element['email']);
            col.push(element['sms_rate']);
            col.push(element['sms_balance']);
            col.push(element['account_number']);
            col.push(element['status']);
            col.push(element['comments']);
            col.push(element['username']);
            col.push(element['password']);
            col.push(package_name[index]);
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
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Company Name</th><th>SMS Balance</th><th>SMS Buy Rates</th><th>Package Name</th><th>Acc No</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][8] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-dark' href='/BillingSms/ViewClient/"+arrays[index][0] +"'>"+ucwords(arrays[index][1]) +"</a> "+status+"</td><td>"+arrays[index][6] +" sms</td><td>"+arrays[index][5] +" <small>per</small> SMS</td><td>"+arrays[index][12] +"</td><td>"+arrays[index][7] +"</td><td><a href='/BillingSms/ViewClient/"+arrays[index][0] +"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][8] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-dark' href='/BillingSms/ViewClient/"+arrays[index][0] +"'>"+ucwords(arrays[index][1]) +"</a> "+status+"</td><td>"+arrays[index][6] +" sms</td><td>"+arrays[index][5] +" <small>per</small> SMS</td><td>"+arrays[index][12] +"</td><td>"+arrays[index][7] +"</td><td><a href='/BillingSms/ViewClient/"+arrays[index][0] +"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this User'><i class='ft-eye'></i></a></td></tr>";
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
    // console.log(element[3]);
    if (element[3] != null) {
        if (element[3].toString().toLowerCase().includes(keylower) || element[3].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
    }
    // console.log(element[6]);
    if (element[6].toString().toLowerCase().includes(keylower) || element[6].toString().toUpperCase().includes(keyUpper)) {
        present++;
    }
    if (element[7].toLowerCase().includes(keylower) || element[7].toUpperCase().includes(keyUpper)) {
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
//add the page by one and the number os rows to dispay by 50
cObj("tonextNav").onclick = function() {
    // console.log(pagecounttrans+" "+pagecountTransaction);
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 50;
            pagecounttrans++;
            var endpage = startpage + 50;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
            // if (rowsColStudents.length > 0 ) {
            //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            //     cObj("sort_by_name").addEventListener("click",sortByName);
            //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
            // }
        } else {
            pagecounttrans = pagecountTransaction;
        }
    }
    // end of next records
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        // if (rowsColStudents.length > 0 ) {
        //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        //     cObj("sort_by_name").addEventListener("click",sortByName);
        //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        // }
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        // if (rowsColStudents.length > 0 ) {
        //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        //     cObj("sort_by_name").addEventListener("click",sortByName);
        //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        // }
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        // if (rowsColStudents.length > 0 ) {
        //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        //     cObj("sort_by_name").addEventListener("click",sortByName);
        //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
        //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
        // }
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
var router_and_keyword = [];
cObj("client_status").onchange = function () {
    // change client status
    var status = this.value;
    rowsColStudents = router_and_keyword.length > 0 ? router_and_keyword:rowsNCols_original;
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
            if (element[8] == status) {
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
            // if (rowsColStudents.length > 0 ) {
            //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            //     cObj("sort_by_name").addEventListener("click",sortByName);
            //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
            // }
        } else {
            var keyword = status == 0?"In-Active users" : "Active users";
            cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! \"" + keyword + "\" not found</p>";
            // cObj("tablefooter").classList.add("invisible");
            cObj("startNo").innerText = 0;
            cObj("finishNo").innerText = 0;
            cObj("tot_records").innerText = 0;
            pagecountTransaction = 1;
        }
    }else if (status == "2"){
        // rowsNcol2 = rowsNCols_original;
        rowsNcol2 = router_and_keyword.length > 0 ? router_and_keyword:rowsNCols_original;
        // console.log(rowsNcol2);
        if (rowsNcol2.length > 0) {
            rowsColStudents = rowsNcol2;
            var counted = rowsNcol2.length / 50;
            pagecountTransaction = Math.ceil(counted);
            cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsNcol2);
            cObj("tot_records").innerText = rowsNcol2.length;
            // if (rowsColStudents.length > 0 ) {
            //     cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            //     cObj("sort_by_name").addEventListener("click",sortByName);
            //     cObj("sort_by_acc_number").addEventListener("click",sortByAccNo);
            //     cObj("sort_by_expiration").addEventListener("click",sortByExpDate);
            // }
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