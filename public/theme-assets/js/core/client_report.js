
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
            col.push(element['report_id']);
            col.push(element['report_title']);
            col.push(element['report_description']);
            col.push(element['client_id']);
            col.push(element['admin_reporter']);
            col.push(element['admin_attender']);
            col.push(element['report_date']);
            col.push((element['resolve_time'] * 1));
            col.push(element['client_name']);
            col.push(element['client_account']);
            col.push(element['admin_reporter_fullname']);
            col.push(element['admin_attender_fullname']);
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
            // cObj("sort_by_reg_date").addEventListener("click", sortByRegDate);
            // cObj("sort_by_name").addEventListener("click", sortByName);
            // cObj("sort_by_acc_number").addEventListener("click", sortByAccNo);
            // cObj("sort_by_expiration").addEventListener("click", sortByExpDate);
            // cObj("sort_by_location").addEventListener("click", sort_by_location);
            // cObj("sort_by_network_gateway").addEventListener("click",sort_by_network);

            // check and uncheck all fields that have been selected
            // checkedUnchecked();
        }

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}