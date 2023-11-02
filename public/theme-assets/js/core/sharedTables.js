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

// load the user data
window.onload = function() {
    // console.log(tables_data);
    // get the arrays
    if (tables_data.length > 0) {
        var rows = tables_data;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['name']);
            col.push(element['date_created']);
            col.push(element['date_modified']);
            col.push(element['creator']);
            col.push(element['comment']);
            col.push(element['id']);
            rowsColStudents.push(col);
        }

        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsColStudents);

        //show the number of pages for each record
        var counted = rows.length / 10;
        pagecountTransaction = Math.ceil(counted);

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
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
    var tableData = "<table class='table table-bordered mb-0'><thead class='thead-dark'><tr><th>#</th><th>Table Name</th><th>Date Created</th><th>Last Modified</th><th>Creator</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
            tableData+="<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][0] +" </td><td>" + formatDate(arrays[index][1]) + "</td><td>" + formatDate(arrays[index][2]) + "</td><td>"+arrays[index][3]+"</td><td><a href='SharedTables/View/" + arrays[index][5] + "/Name/"+replacePunctuationWithUnderscore(arrays[index][0])+"' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Shared Table'><i class='ft-eye'></i> View</a></td></tr>";
            counter++;
        }
    }else{
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            tableData+="<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][0] +" </td><td>" + formatDate(arrays[index][1]) + "</td><td>" + formatDate(arrays[index][2]) + "</td><td>"+arrays[index][3]+"</td><td><a href='SharedTables/View/" + arrays[index][5] + "/Name/"+replacePunctuationWithUnderscore(arrays[index][0])+"' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Shared Table'><i class='ft-eye'></i> View</a></td></tr>";
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
        startpage -= 10;
        var endpage = startpage + 10;
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
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 10) - 10;
        var endpage = startpage + 10;
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
        for (let index_1 = 0; index_1 < element.length; index_1++) {
            const elems = element[index_1];
            if (elems == null || elems == undefined) {
                continue;
            }
            if (elems.toString().toLowerCase().includes(keylower) || elems.toString().toUpperCase().includes(keyUpper)) {
                present++;
            }
        }
        // console.log(element[2]);
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsNcol2);
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}