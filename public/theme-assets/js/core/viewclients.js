
// Send data with get
function sendDataGet(method, file, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
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

function stopInterval(id) {
    clearInterval(id);
}
function cObj(id) {
    return document.getElementById(id);
}

var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number
// populate the client data
window.onload = function () {
    cObj("client_name").value = clients_data[0]['client_name'];
    cObj("client_address").value = clients_data[0]['client_address'];
    cObj("client_phone").value = clients_data[0]['clients_contacts'];
    cObj("client_monthly_pay").value = clients_data[0]['monthly_payment'];
    cObj("client_network").value = clients_data[0]['client_network'];
    cObj("networks").innerText = clients_data[0]['client_network'];
    cObj("addresses").innerText = clients_data[0]['client_default_gw'];
    cObj("client_gw").value = clients_data[0]['client_default_gw'];
    cObj("comments").value = clients_data[0]['comment'];
    var router_names = clients_data[0]['router_name'];
    cObj("router_interfaced").innerText = clients_data[0]['client_interface'] ? clients_data[0]['client_interface'] : "null";
    cObj("client_username").value = clients_data[0]['client_username'];
    cObj("client_password").value = clients_data[0]['client_password'];

    // edit upload and download of the user
    var upload_download = clients_data[0]['max_upload_download'];
    // split the upload and download
    var up_down = upload_download.split("/");
    // splits speeds
    var up = up_down[0];
    var down = up_down[1];
    // split speed up
    var up_speed = up.substr(0,(up.length-1));
    var up_unit = up.substr((up.length-1),(up.length));
    // split speed down
    var down_speed = down.substr(0,(down.length-1));
    var down_unit = down.substr((down.length-1),(down.length));
    // assigne the different speeds to the correct input box
    cObj("upload_speed").value = up_speed;
    cObj("download_speed").value = down_speed;

    // assigne the selected unit
    // start with upload
    var innit = document.getElementsByClassName("innit");
    for (let index = 0; index < innit.length; index++) {
        const element = innit[index];
        // if the value of the element is equal to the value assigned to the user select it
        if (element.value == up_unit) {
            element.selected = true;
        }
    }
    // download speed
    var downinit = document.getElementsByClassName("downinit");
    for (let index = 0; index < downinit.length; index++) {
        const element = downinit[index];
        // if the value of the element is equal to the value assigned to the user select it
        if (element.value == down_unit) {
            element.selected = true;
        }
    }

    // add an event listener to the router name list to change
    var router_name = document.getElementById("router_name");
    if (router_name != null) {
        // get the router values to tell the routers id
        var router_id_infor = document.getElementsByClassName("router_id_infor");
        for (let index = 0; index < router_id_infor.length; index++) {
            const element = router_id_infor[index];
            if (element.value == router_names) {
                // assign the name of the default router to the router name
                cObj("router_named").innerText = element.innerText+" ("+element.value+")";
            }
        }
        // set the event listener
        router_name.addEventListener("change", getRouterInterfaces);
    }else{
        // console.log("Is null");
    }
    // populate the table below
    // var payment_infor = [];
    // var payinfor = refferal_payment.payment_history;
    // for (let index = 0; index < payinfor.length; index++) {
    //     const element = payinfor[index];
    //     var infor = [element.amount,element.date];
    //     payment_infor.push(infor);
    // }
    rowsColStudents = refferal_payment;
    // console.log(refferal_payment);
    cObj("transDataReciever").innerHTML = displayRecord(0, 50, rowsColStudents);
}

function displayRecord(start, finish, arrays) {
    var total = arrays.length;
    //the finish value
    var fins = 0;
    //this is the table header to the start of the tbody
    var tableData = "<div class='table-responsive' id='transDataReciever'><table class='table'><thead><tr><th>#</th><th>Amount</th><th>Date</th></tr></thead><tbody>";
    if(finish < total) {
        fins = finish;
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < finish; index++) {
            tableData += "<tr><th scope='row'>"+counter+"</th><td>Kes "+arrays[index][0]+"</td><td>"+arrays[index][1]+"</td></tr>";
            counter++;
        }
    }else{
        //create a table of the 50 records
        var counter = start+1;
        for (let index = start; index < total; index++) {
            tableData += "<tr><th scope='row'>"+counter+"</th><td>Kes "+arrays[index][0]+"</td><td>"+arrays[index][1]+"</td></tr>";
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
cObj("toprevNac").onclick = function() {
    if (pagecounttrans > 1) {
        pagecounttrans--;
        startpage -= 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tofirstNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = 1;
        startpage = 0;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}
cObj("tolastNav").onclick = function() {
    if (pagecountTransaction > 0) {
        pagecounttrans = pagecountTransaction;
        startpage = (pagecounttrans * 50) - 50;
        var endpage = startpage + 50;
        cObj("transDataReciever").innerHTML = displayRecord(startpage, endpage, rowsColStudents);
    }
}

function getRouterInterfaces() {
    sendDataGet("GET","/router/"+this.value+"",cObj("interface_holder"),cObj("interface_load"));
}
cObj("edit_epiration").onclick = function () {
    cObj("change_exp_date_windoe").classList.toggle("d-none");
}
cObj("cancel_exp_update").onclick = function () {
    cObj("change_exp_date_windoe").classList.add("d-none");
}

cObj("prompt_delete").onclick = function () {
    cObj("prompt_del_window").classList.remove("d-none");
}
cObj("delet_user_no").onclick = function () {
    cObj("prompt_del_window").classList.add("d-none");
}

cObj("edit_freeze_client").onclick = function () {
    cObj("change_freeze_date_window").classList.toggle("d-none");
}
cObj("cancel_freeze_dates").onclick = function () {
    cObj("change_freeze_date_window").classList.add("d-none");
}
cObj("edit_wallet").onclick = function () {
    cObj("change_wallet_window").classList.toggle("d-none");
}
cObj("cancel_wallet_updates").onclick = function () {
    cObj("change_wallet_window").classList.add("d-none");
}

cObj("client_network").onkeyup = function (event) {
    var network = this.value;
    // checkOnlyDigits(event, "client_network","errorMsg");
};
cObj("client_gw").onkeyup = function (event) {
    // checkOnlyDigits(event, "client_gw","errorMsg1");
};
var init_val = "";
function checkOnlyDigits(e, object_id,errhandler) {
    e = e ? e : window.event;
    var charCode = e.which ? e.which : e.keyCode;
    // // console.log(charCode);
    var value = cObj(object_id).value;
    var arrays = value.split(".");
    var arr2 = value.split("\/");
    var reject = 0;
    if (arrays.length > 4) {
        reject++;
    } else {
        if (arrays.length == 1) {
            // value*=1;
            if (value > 255) {
                reject++;
            }
        }
        for (let index = 0; index < arrays.length; index++) {
            const element = arrays[index];
            if (element > 255) {
                reject++;
            } else {
                var valueds = value;
                valueds *= 1;
                if (typeof valueds === 'string') {
                    reject++;
                }
            }
        }
    }
    if (arr2.length > 1 && arrays.length < 4) {
        reject++;
    }
    if (arr2.length > 1) {
        if (arr2[1] > 30 || arr2.length > 2) {
            reject++;
        }
    }
    if (charCode == 188 || charCode == 32) {
        reject++;
    }
    if ((charCode > 106 && charCode != 110 && charCode != 111) || (charCode > 64 && charCode < 91)) {
        reject++;
    }
    if (charCode > 31 && (charCode < 45 || charCode > 57) && reject > 0) {
        cObj(object_id).value = init_val;
        // return false;
    } else {
        init_val = value;
        // return true;
    }
    if (arrays.length != 4) {
        cObj(object_id).classList.add("border");
        cObj(object_id).classList.add("border-danger");
        document.getElementById(errhandler).style.color = 'red';
    } else {
        if (arrays[3].length > 0) {
            cObj(object_id).classList.remove("border");
            cObj(object_id).classList.remove("border-danger");
            document.getElementById(errhandler).style.color = 'black';
        }else{
            cObj(object_id).classList.add("border");
            cObj(object_id).classList.add("border-danger");
            document.getElementById(errhandler).style.color = 'red';
        }
    }
}

cObj("freeze_date").onchange = function () {
    if (this.value == "set_freeze") {
        cObj("setFreezeDate").classList.remove("d-none");
    }else{
        cObj("setFreezeDate").classList.add("d-none");
    }
}

cObj("edit_minimum_amount").onclick = function () {
    cObj("hide_min_pay_window").classList.toggle("d-none");
}