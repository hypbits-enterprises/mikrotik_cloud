// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

cObj("add_expense_category_btn").onclick = function () {
    cObj("generate_expense_report").classList.add("hide");
    cObj("show_expense_category").classList.add("hide");
    cObj("add_expense_category").classList.toggle("hide");
    cObj("generate_income_statements").classList.add("hide");
}

cObj("view_expense_category_btn").onclick = function () {
    cObj("generate_expense_report").classList.add("hide");
    cObj("show_expense_category").classList.toggle("hide");
    cObj("add_expense_category").classList.add("hide");
    cObj("generate_income_statements").classList.add("hide");
}

cObj("expense_report_btn").onclick = function () {
    cObj("generate_expense_report").classList.toggle("hide");
    cObj("show_expense_category").classList.add("hide");
    cObj("add_expense_category").classList.add("hide");
    cObj("generate_income_statements").classList.add("hide");
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
    return getDays(d.getDay()) + " " + d.getDate() + " " + getMonths(d.getMonth()) + " " + d.getFullYear()+" @ "+hours+":"+minutes+":"+seconds;
    // console.log(year);
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
window.onload = function () {
    if (expenses.length > 0) {
        var rows = expenses;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['id']);
            col.push(element['name']);
            col.push(element['category']);
            col.push(element['unit_of_measure']);
            col.push(element['unit_price']);
            col.push(element['unit_amount']);
            col.push(element['total_price']);
            col.push(element['date_recorded']);
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

    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Expense Names</th><th>Expense Category</th><th>Quantity</th><th>Unit Price</th><th>Total Price</th><th>Date Recorded</th><th>Action</th></tr></thead><tbody>";
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
            tableData += "<tr><th scope='row'>" + counter + "</th><td>" + arrays[index][1] + "</td><td>" + arrays[index][2]+ "</td><td>" + arrays[index][5] +" " + (arrays[index][3] != null ? arrays[index][3] : "Unit(s)") +"</td><td>Kes " + arrays[index][4] + "</td><td>Kes " + arrays[index][6] + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a class='btn btn-primary btn-sm' href='/Expense/View/"+arrays[index][0]+"'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    } else {
        //create a table of the 50 records
        var counter = start + 1;
        for (let index = start; index < total; index++) {
            tableData += "<tr><th scope='row'>" + counter + "</th><td>" + arrays[index][1] + "</td><td>" + arrays[index][2]+ "</td><td>" + arrays[index][5] +" " + (arrays[index][3] != null ? arrays[index][3] : "Unit(s)") +"</td><td>Kes " + arrays[index][4] + "</td><td>Kes " + arrays[index][6] + "</td><td>" + setDate(arrays[index][7]) + "</td><td><a class='btn btn-primary btn-sm' href='/Expense/View/"+arrays[index][0]+"'><i class='ft-eye'></i> View</a></td></tr>";
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
cObj("tonextNav").onclick = function () {
    // console.log(pagecounttrans+" "+pagecountTransaction);
    if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
        startpage += 50;
        pagecounttrans++;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
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
    }
}
cObj("tofirstNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tolastNav").onclick = function () {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
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
        if (element[0].toString().toLowerCase().includes(keylower) || element[0].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[1].toString().toLowerCase().includes(keylower) || element[1].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[2].toString().toLowerCase().includes(keylower) || element[2].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[4].toString().toLowerCase().includes(keylower) || element[4].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if ((element[5]+" "+element[3]).toString().toLowerCase().includes(keylower) || (element[5]+" "+element[3]).toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[6].toString().toLowerCase().includes(keylower) || element[6].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[7].toString().toLowerCase().includes(keylower) || element[7].toString().toUpperCase().includes(keyUpper)) {
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

cObj("addExpenseBtnWindow").onclick = function () {
    cObj("addExpenseWindow").classList.toggle("hide");
}
cObj("expense_quantity").onkeyup = function () {
    doExpenseCalculation();
}
cObj("expense_unit_price").onkeyup = function () {
    doExpenseCalculation();
}
function doExpenseCalculation() {
    var expense_quantity = cObj("expense_quantity").value;
    var expense_unit_price = cObj("expense_unit_price").value;
    if (expense_quantity.length > 0 && expense_unit_price.length > 0) {
        var values = expense_quantity * expense_unit_price;
        cObj("expense_total_price").value = values.toFixed(2);
    }else{
        cObj("expense_total_price").value = 0;
    }
}

// select router to display users
cObj("expense_category_filter").onchange = function () {
    // change client status
    var expenseCategory = this.value;
    rowsColStudents = rowsNCols_original;
    pagecounttrans = 1;
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    //row break
    if (expenseCategory.length > 0) {
        for (let index = 0; index < rowsColStudents.length; index++) {
            const element = rowsColStudents[index];
            //column break
            var present = 0;
            if (element[2] == expenseCategory) {
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

cObj("expense_date_option").onchange = function () {
    var values = this.value;
    if (values == "select date") {
        cObj("single_date_window").classList.remove("hide");
        cObj("from_date_window").classList.add("hide");
        cObj("to_date_window").classList.add("hide");
    }else if (values == "select between date") {
        cObj("single_date_window").classList.add("hide");
        cObj("from_date_window").classList.remove("hide");
        cObj("to_date_window").classList.remove("hide");
    }else{
        cObj("single_date_window").classList.add("hide");
        cObj("from_date_window").classList.add("hide");
        cObj("to_date_window").classList.add("hide");
    }
}

cObj("select_income_statement_period").onchange = function () {
    var option_val = this.value;
    if (option_val == "All") {
        cObj("date_income_statement").classList.add("hide");
        cObj("from_income_statement").classList.add("hide");
        cObj("to_income_statement").classList.add("hide");
        cObj("select_mon_income_statement").classList.add("hide");
        cObj("select_yr_income_statement").classList.add("hide");
    }else if (option_val == "Daily") {
        cObj("date_income_statement").classList.remove("hide");
        cObj("from_income_statement").classList.add("hide");
        cObj("to_income_statement").classList.add("hide");
        cObj("select_mon_income_statement").classList.add("hide");
        cObj("select_yr_income_statement").classList.add("hide");
    }else if (option_val == "Monthly") {
        cObj("date_income_statement").classList.add("hide");
        cObj("from_income_statement").classList.add("hide");
        cObj("to_income_statement").classList.add("hide");
        cObj("select_mon_income_statement").classList.remove("hide");
        cObj("select_yr_income_statement").classList.remove("hide");
    }else if (option_val == "Yearly") {
        cObj("date_income_statement").classList.add("hide");
        cObj("from_income_statement").classList.add("hide");
        cObj("to_income_statement").classList.add("hide");
        cObj("select_mon_income_statement").classList.add("hide");
        cObj("select_yr_income_statement").classList.remove("hide");
    }else if (option_val == "Between Dates") {
        cObj("date_income_statement").classList.add("hide");
        cObj("from_income_statement").classList.remove("hide");
        cObj("to_income_statement").classList.remove("hide");
        cObj("select_mon_income_statement").classList.add("hide");
        cObj("select_yr_income_statement").classList.add("hide");
    }
}

cObj("view_income_statements").onclick = function () {
    cObj("generate_income_statements").classList.toggle("hide");
    cObj("generate_expense_report").classList.add("hide");
    cObj("show_expense_category").classList.add("hide");
    cObj("add_expense_category").classList.add("hide");
}