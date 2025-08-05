// console.log(router_data);
// get the data from the database

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
var routersData = router_data;
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
    // console.log(routersData);
    // get the arrays
    if (routersData.length > 0) {
        var rows = routersData;
        //create a column now
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            // create the collumn array that will take the row value
            var col = [];
            // console.log(element);
            col.push(element['sstp_password']);
            col.push(element['api_port']);
            col.push(element['sstp_username']);
            col.push(element['router_id']);
            col.push(element['router_location']);
            col.push(element['router_name']);
            col.push(element['router_status']);
            col.push(element['activated']);
            col.push(element['user_count']);
            col.push(element['router_coordinates']);
            // var col = element.split(":");
            rowsColStudents.push(col);
        }

        cObj("tot_records").innerText = rows.length;
        //create the display table
        //get the number of pages
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsColStudents);
        hoverEffect();

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
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Router Name</th><th>Location</th><th># of Clients</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][7] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var actions = "<a href='Router/View/" + arrays[index][3] + "' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Router' style=\"padding: 3px;\"><span class=\"d-inline-block border border-white w-100 text-center\" style=\"border-radius: 2px; padding: 5px;\"><i class='ft-eye'></i> View</span></a>";
                tableData+="<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][5] +" "+status+"</td><td>" + arrays[index][4] +" "+ (arrays[index][9] != null ? " <a class='text-danger' href = 'https://www.google.com/maps/place/"+arrays[index][9]+"' target = '_blank'><u>Locate</u> </a>": "")+"</td><td>" + arrays[index][8]+" Client(s)" + "</td><td>"+actions+"</td></tr>";
            counter++;
        }
    }else{
        //create a table of the 10 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][7] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var actions = "<a href='Router/View/" + arrays[index][3] + "' class='btn btn-sm btn-primary text-bolder ' data-toggle='tooltip' title='View this Router' style=\"padding: 3px;\"><span class=\"d-inline-block border border-white w-100 text-center\" style=\"border-radius: 2px; padding: 5px;\"><i class='ft-eye'></i> View</span></a>";
                tableData+="<tr><th scope='row'>"+counter+"</th><td>" + arrays[index][5] +" "+status+"</td><td>" + arrays[index][4] +" "+ (arrays[index][9] != null ? " <a class='text-danger' href = 'https://www.google.com/maps/place/"+arrays[index][9]+"' target = '_blank'><u>Locate</u> </a>": "")+"</td><td>" + arrays[index][8]+" Client(s)" + "</td><td>"+actions+"</td></tr>";
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
            hoverEffect();
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
        hoverEffect();
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        hoverEffect();
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 10) - 10;
        var endpage = startpage + 10;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
        hoverEffect();
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
        if (element[2].toLowerCase().includes(keylower) || element[2].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[4].toLowerCase().includes(keylower) || element[4].toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toLowerCase().includes(keylower) || element[5].toUpperCase().includes(keyUpper)) {
            present++;
        }
        // console.log(element[2]);
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        cObj("transDataReciever").innerHTML = displayRecord(0, 10, rowsNcol2);
        hoverEffect();
    } else {
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        cObj("tablefooter").classList.add("invisible");
    }
}