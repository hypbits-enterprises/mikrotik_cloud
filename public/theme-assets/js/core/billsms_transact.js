// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// console.log(router_data);

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
    // get the arrays
    if (router_data.length > 0) {
        var rows = router_data;
        var client_name = account_names;
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
            col.push(account_names[index]);
            col.push(element['phone_transacting']);
            col.push(transaction_date[index]);
            col.push(element['transaction_acc_id']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }
        rowsNCols_original = rowsColStudents;
        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 50;
        pagecountTransaction = Math.ceil(counted);
        if (rowsColStudents.length > 0 ) {
            cObj("sortbydate").addEventListener("click",sortbydates);
            cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
            cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
            cObj("transaction_amount_id").addEventListener("click",sortByAmount);
        }

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No transactions records found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }           
}
var sortbydatess = 0;
function sortbydates() {
    rowsColStudents = sortAsc(rowsColStudents,6);
    if (sortbydatess == 0) {
        sortbydatess = 1;
        rowsColStudents = sortAsc(rowsColStudents,6);
    }else{
        sortbydatess = 0;
        rowsColStudents = sortDesc(rowsColStudents,6);
    }
    // console.log(sortbydatess);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sortbydatess == 0) {
        cObj("sortbydate").innerHTML = "# <i class='ft-chevron-down'></i>";
    }else{
        cObj("sortbydate").innerHTML = "# <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
        cObj("sortbydate").addEventListener("click",sortbydates);
        cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
        cObj("transaction_amount_id").addEventListener("click",sortByAmount);
    }
}
var sort_transcode = 0;
function sort_by_transaction_code() {
    rowsColStudents = sortAsc(rowsColStudents,7);
    if (sort_transcode == 0) {
        sort_transcode = 1;
        rowsColStudents = sortAsc(rowsColStudents,7);
    }else{
        sort_transcode = 0;
        rowsColStudents = sortDesc(rowsColStudents,7);
    }
    // console.log(sort_transcode);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sort_transcode == 0) {
        cObj("sortTransCode").innerHTML = "Transaction ID <i class='ft-chevron-down'></i>";
    }else{
        cObj("sortTransCode").innerHTML = "Transaction ID <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
        cObj("sortbydate").addEventListener("click",sortbydates);
        cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
        cObj("transaction_amount_id").addEventListener("click",sortByAmount);
    }
}

var sort_accno = 0;
function sort_by_acc_no() {
    rowsColStudents = sortAsc(rowsColStudents,4);
    if (sort_accno == 0) {
        sort_accno = 1;
        rowsColStudents = sortAsc(rowsColStudents,4);
    }else{
        sort_accno = 0;
        rowsColStudents = sortDesc(rowsColStudents,4);
    }
    // console.log(sort_accno);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sort_accno == 0) {
        cObj("trans_account_number").innerHTML = "Account Number <i class='ft-chevron-down'></i>";
    }else{
        cObj("trans_account_number").innerHTML = "Account Number <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
        cObj("sortbydate").addEventListener("click",sortbydates);
        cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
        cObj("transaction_amount_id").addEventListener("click",sortByAmount);
    }
}
var sortAmount = 0;
function sortByAmount() {
    rowsColStudents = sortAsc(rowsColStudents,2);
    if (sortAmount == 0) {
        sortAmount = 1;
        rowsColStudents = sortAsc(rowsColStudents,2);
    }else{
        sortAmount = 0;
        rowsColStudents = sortDesc(rowsColStudents,2);
    }
    // console.log(sortAmount);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
    if (sortAmount == 0) {
        cObj("transaction_amount_id").innerHTML = "Amount <i class='ft-chevron-down'></i>";
    }else{
        cObj("transaction_amount_id").innerHTML = "Amount <i class='ft-chevron-up'></i>";
    }
    if (rowsColStudents.length > 0 ) {
        // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
        cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
        cObj("sortbydate").addEventListener("click",sortbydates);
        cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
        cObj("transaction_amount_id").addEventListener("click",sortByAmount);
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
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][9] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
            tableData += "<tr data-toggle='tooltip' title='Paid Ksh "+arrays[index][2]+" to "+arrays[index][4]+" {"+arrays[index][10]+"} using "+arrays[index][11]+" {"+arrays[index][0]+"} on "+arrays[index][12]+".'><th scope='row'>"+counter+"</th><td>" + arrays[index][7] +" "+status+"<small>{"+arrays[index][12]+"}</small></td><td>" + arrays[index][4] + " <small><a href='/Clients/View/"+arrays[index][13]+"' class='text-secondary'>{"+arrays[index][10]+"}</a></small></td><td>Kes " + arrays[index][2] + "</td><td><a href='/BillingSms/Transactions/View/" + arrays[index][6] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this transaction'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            var status = "<span class='badge badge-success'> </span>";
            if (arrays[index][9] == 0) {
                // if the user is active
                status = "<span class='badge badge-danger'> </span>";
            }
            tableData += "<tr data-toggle='tooltip' title='Paid Ksh "+arrays[index][2]+" to "+arrays[index][4]+" {"+arrays[index][10]+"} using "+arrays[index][11]+" {"+arrays[index][0]+"} on "+arrays[index][12]+".'><th scope='row'>"+counter+"</th><td>" + arrays[index][7] +" "+status+"<small>{"+arrays[index][12]+"}</small></td><td>" + arrays[index][4] + " <small><a href='/Clients/View/"+arrays[index][13]+"' class='text-secondary'>{"+arrays[index][10]+"}</a></small></td><td>Kes " + arrays[index][2] + "</td><td><a href='/BillingSms/Transactions/View/" + arrays[index][6] + "' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='View this transaction'><i class='ft-eye'></i> View</a></td></tr>";
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
            if (rowsColStudents.length > 0 ) {
                // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
                cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
                cObj("sortbydate").addEventListener("click",sortbydates);
                cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
                cObj("transaction_amount_id").addEventListener("click",sortByAmount);
            }
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
        if (rowsColStudents.length > 0 ) {
            // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
            cObj("sortbydate").addEventListener("click",sortbydates);
            cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
            cObj("transaction_amount_id").addEventListener("click",sortByAmount);
        }
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0 ) {
            // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
            cObj("sortbydate").addEventListener("click",sortbydates);
            cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
            cObj("transaction_amount_id").addEventListener("click",sortByAmount);
        }
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        if (rowsColStudents.length > 0 ) {
            // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
            cObj("sortbydate").addEventListener("click",sortbydates);
            cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
            cObj("transaction_amount_id").addEventListener("click",sortByAmount);
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
        if (element[0].includes(keylower) || element[0].includes(keyUpper)) {
            present++;
        }
        if (element[4].includes(keylower) || element[4].includes(keyUpper)) {
            present++;
        }
        if (element[7].includes(keylower) || element[7].includes(keyUpper)) {
            present++;
        }
        if (element[1].includes(keylower) || element[1].includes(keyUpper)) {
            present++;
        }
        if (element[8].includes(keylower) || element[8].includes(keyUpper)) {
            present++;
        }
        if (element[12].includes(keylower) || element[12].includes(keyUpper)) {
            present++;
        }
        if (element[10].includes(keylower) || element[10].includes(keyUpper)) {
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
        if (rowsColStudents.length > 0 ) {
            // cObj("sort_by_reg_date").addEventListener("click",sortByRegDate);
            cObj("sortTransCode").addEventListener("click",sort_by_transaction_code);
            cObj("sortbydate").addEventListener("click",sortbydates);
            cObj("trans_account_number").addEventListener("click",sort_by_acc_no);
            cObj("transaction_amount_id").addEventListener("click",sortByAmount);
        }
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
        // console.log(b[index]+" "+a[index]);
        if (a[index] === b[index]) {
            return 0;
        }
        else {
            return (a[index] < b[index]) ? -1 : 1;
        }
    }
    return arrays;
}
var closedWin = 0;
cObj("show_totals").onclick = function () {
    if (closedWin == 0) {
        cObj("totals_window").classList.remove("d-none");
        closedWin = 1;
        this.innerHTML = "<i class='ft-eye-off'></i> Hide Totals";
    }else{
        cObj("totals_window").classList.add("d-none");
        closedWin = 0;
        this.innerHTML = "<i class='ft-eye'></i> Show Totals";
    }
}