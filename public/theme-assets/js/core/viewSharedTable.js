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

function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

function formatDate(timestamp) {
    const year = timestamp.toString().slice(0, 4);
    const month = parseInt(timestamp.toString().slice(4, 6), 10) - 1;
    const date = timestamp.toString().slice(6, 8);
    const hour = timestamp.toString().slice(8, 10);
    const minute = timestamp.toString().slice(10, 12);
    const second = timestamp.toString().slice(12, 14);
    
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    const dateObj = new Date(year, month, date, hour, minute, second);
    const weekday = weekdays[dateObj.getDay()];
    const monthName = months[dateObj.getMonth()];
  
    return `${weekday}, ${monthName} ${date} ${year}`;
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
function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function replaceSpacesWithUnderscore(string) {
    const pattern = /\s+/g;
    const replacement = '_';
    return string.replace(pattern, replacement);
  }
function replacePunctuationWithUnderscore(string) {
    const pattern = /[^\w\s]/g;
    const replacement = '_';
    return replaceSpacesWithUnderscore(string.replace(pattern, replacement));
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

var rowsColStudents = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number
var column_data = [];

// load the user data
window.onload = function() {
    // console.log(tables_data);
    var columns = tables_data.columns;
    var row_data = tables_data.row_data;
    var table_name = tables_data.table_name;
    var table_id = tables_data.table_id;


    // get the columns 
    for (let index = 0; index < columns.length; index++) {
        const element = columns[index];
        column_data.push(element.column_name);
    }

    // // get the arrays
    if (row_data.length > 0) {
        rowsColStudents = row_data;

        cObj("tot_records").innerText = row_data.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 40, rowsColStudents);

        //show the number of pages for each record
        var counted = row_data.length / 40;
        pagecountTransaction = Math.ceil(counted);

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

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
    var header = "";
    for (let index = 0; index < column_data.length; index++) {
        const element = column_data[index];
        header+="<th>"+element+"</th>";
    }
    console.log(arrays);
    var tableData = "<table class='table'><thead><tr><th>#</th>"+header+"<th>Actions</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 40 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
            tableData+="<tr><th scope='row'>"+counter+"</th>";
            for (let ind = 0; ind < arrays[index].length; ind++) {
                const element = arrays[index][ind];

                // skip the last row
                if (arrays[index].length == ind+1) {
                    continue;
                }
                tableData+="<td>"+element.col_value+"</td>";

            }
            tableData+="<td><a href='/SharedTables/Edit/" + tables_data.table_id + "/Name/"+replacePunctuationWithUnderscore(tables_data.table_name)+"/Record/"+arrays[index][(arrays[index].length - 1)]+"' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Shared Table'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 40 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            tableData+="<tr><th scope='row'>"+counter+"</th>";
            for (let ind = 0; ind < arrays[index].length; ind++) {
                const element = arrays[index][ind];
                // skip the last row it has the id
                if (arrays[index].length == ind+1) {
                    continue;
                }
                tableData+="<td>"+element.col_value+"</td>";
            }
            tableData+="<td><a href='/SharedTables/Edit/" + tables_data.table_id + "/Name/"+replacePunctuationWithUnderscore(tables_data.table_name)+"/Record/"+arrays[index][(arrays[index].length - 1)].row_id+"' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Shared Table'><i class='ft-eye'></i> View</a></td></tr>";
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
//add the page by one and the number os rows to dispay by 40
cObj("tonextNav").onclick = function() {
        if (pagecounttrans < pagecountTransaction) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage += 40;
            pagecounttrans++;
            var endpage = startpage + 40;
            cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
        } else {
            pagecounttrans = pagecountTransaction;
        }
    }
    // end of next records
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 40;
        var endpage = startpage + 40;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 40;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 40) - 40;
        var endpage = startpage + 40;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
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
        for (let index = 0; index < element.length; index++) {
            const elems = element[index];
            if (elems.toString().toLowerCase().includes(keylower) || elems.toString().toUpperCase().includes(keylower)) {
                present++;
            }
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        cObj("transDataReciever").innerHTML = displayRecord(0, 40, rowsNcol2);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}

cObj("close_this_window_delete").onclick = function () {
    cObj("delete_column_details").classList.add("hide");
    cObj("delete_column_details").classList.remove("show");
    cObj("delete_column_details").classList.remove("showBlock");
}

cObj("DeleteTable").onclick = function () {
    cObj("delete_column_details").classList.remove("hide");
    cObj("delete_column_details").classList.add("show");
    cObj("delete_column_details").classList.add("showBlock");
}