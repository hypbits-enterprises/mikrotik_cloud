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



function displayRecord_ppoe(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<table class='table'><thead><tr><th>#</th><th>Client Name</th><th>Client Secret</th><th>Profile</th><th>Secret Password</th><th>Action</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 25 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
                var status = "<span class='badge badge-success'> </span>";
                if (arrays[index][2] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var secret_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][0] == 1) {
                    secret_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var profile_status = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][1] == 1) {
                    profile_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var client_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-secondary' href='/Clients/View/"+arrays[index][6]+"'>"+arrays[index][3]+" "+status+"</a></td><td>"+arrays[index][7]+" "+secret_match+"</td><td>"+arrays[index][5]+" "+profile_status+"</td><td>"+arrays[index][4]+" "+client_status+"</td><td><a href='/Client/epxsync/"+arrays[index][6]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='Sync Client Information'><i class='ft-refresh-ccw'></i></a></td></tr>";
                counter++;
        }
    }else{
        //create a table of the 25 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
                var status = "<span class='badge badge-success'> </span>";
                console.log(arrays[index][2]);
                if (arrays[index][2] == 0) {
                    // if the user is active
                    status = "<span class='badge badge-danger'> </span>";
                }
                var secret_match = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][0] == 1) {
                    secret_match = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var profile_status = "<span class='text-danger text-bolder text-lg'><i class='ft-x'></i></span>";
                if (arrays[index][1] == 1) {
                    profile_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                }
                var client_status = "<span class='text-success text-bolder text-lg'><i class='ft-check'></i></span>";
                tableData += "<tr><th scope='row'>"+counter+"</th><td><a class='text-secondary' href='/Clients/View/"+arrays[index][6]+"'>"+arrays[index][3]+" "+status+"</a></td><td>"+arrays[index][7]+" "+secret_match+"</td><td>"+arrays[index][5]+" "+profile_status+"</td><td>"+arrays[index][4]+" "+client_status+"</td><td><a href='/Client/epxsync/"+arrays[index][6]+"' class='btn btn-sm btn-primary text-bolder' data-toggle='tooltip' title='Sync Client Information'><i class='ft-refresh-ccw'></i></a></td></tr>";
                counter++;
        }
        fins = total;
    }

    tableData += "</tbody></table>";
    //set the start and the end value
    cObj("startNo_ppoe").innerText = start + 1;
    cObj("finishNo_ppoe").innerText = fins;
    //set the page number
    cObj("pagenumNav_ppoe").innerText = pagecounttrans_ppoe;
    // set tool tip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    return tableData;
}
//next record 
//add the page by one and the number os rows to dispay by 25
cObj("tonextNav_ppoe").onclick = function() {
        if (pagecounttrans_ppoe < pagecountTransaction_ppoe) { // if the current page is less than the total number of pages add a page to go to the next page
            startpage_ppoe += 25;
            pagecounttrans_ppoe++;
            var endpage = startpage_ppoe + 25;
            cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(startpage_ppoe, endpage, rowsColStudents_ppoe);
        } else {
            pagecounttrans_ppoe = pagecountTransaction_ppoe;
        }
    }
    // end of next records
cObj("toprevNac_ppoe").onclick = function() {
    if (pagecounttrans_ppoe > 1) {
        pagecounttrans_ppoe--;
        startpage_ppoe -= 25;
        var endpage = startpage_ppoe + 25;
        cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(startpage_ppoe, endpage, rowsColStudents_ppoe);
    }
}
cObj("tofirstNav_ppoe").onclick = function() {
    if (pagecountTransaction_ppoe > 0) {
        pagecounttrans_ppoe = 1;
        startpage_ppoe = 0;
        var endpage = startpage_ppoe + 25;
        cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(startpage_ppoe, endpage, rowsColStudents_ppoe);
    }
}
cObj("tolastNav_ppoe").onclick = function() {
    if (pagecountTransaction_ppoe > 0) {
        pagecounttrans_ppoe = pagecountTransaction_ppoe;
        startpage_ppoe = (pagecounttrans_ppoe * 25) - 25;
        var endpage = startpage_ppoe + 25;
        cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(startpage_ppoe, endpage, rowsColStudents_ppoe);
    }
}

// seacrh keyword at the table
cObj("searchkey_ppoe").onkeyup = function() {
        checkName_ppoe(this.value);
    }
    //create a function to check if the array has the keyword being searched for
function checkName_ppoe(keyword) {
    rowsColStudents_ppoe = rowsNCols_original_ppoe;
    if (keyword.length > 0) {
        // cObj("tablefooter_ppoe").classList.add("invisible");
    } else {
        // cObj("tablefooter_ppoe").classList.remove("invisible");
    }
    // console.log(keyword.toLowerCase());
    var rowsNcol2 = [];
    var keylower = keyword.toLowerCase();
    var keyUpper = keyword.toUpperCase();
    //row break
    for (let index = 0; index < rowsColStudents_ppoe.length; index++) {
        const element = rowsColStudents_ppoe[index];
        //column break
        // console.log(element[3]);
        var present = 0;
        if (element[2].toString().toLowerCase().includes(keylower) || element[2].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[3].toString().includes(keyword) || element[3].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[0].toString().includes(keyword) || element[0].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[5].toString().includes(keyword) || element[5].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        if (element[7].toString().includes(keyword) || element[7].toString().toUpperCase().includes(keyUpper)) {
            present++;
        }
        //here you can add any other columns to be searched for
        if (present > 0) {
            rowsNcol2.push(element);
        }
    }
    if (rowsNcol2.length > 0) {
        rowsColStudents_ppoe = rowsNcol2;
        var counted = rowsNcol2.length / 25;
        pagecountTransaction_ppoe = Math.ceil(counted);
        cObj("transDataReciever_ppoe").innerHTML = displayRecord_ppoe(0, 25, rowsNcol2);
        cObj("tot_records_ppoe").innerText = rowsNcol2.length;
    } else {
        cObj("transDataReciever_ppoe").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! your search for \"" + keyword + "\" was not found</p>";
        // cObj("tablefooter_ppoe").classList.add("invisible");
        cObj("startNo_ppoe").innerText = 0;
        cObj("finishNo_ppoe").innerText = 0;
        cObj("tot_records_ppoe").innerText = 0;
        pagecountTransaction_ppoe = 1;
    }
}
