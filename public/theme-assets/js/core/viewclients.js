
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
    var router_names = clients_data[0]['router_name'];
    cObj("router_interfaced").innerText = clients_data[0]['client_interface'] ? clients_data[0]['client_interface'] : "null";
    // cObj("client_username").value = clients_data[0]['client_username'];
    // cObj("client_password").value = clients_data[0]['client_password'];

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
    cObj("download_speeds").value = down_speed;

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


    // 
    var view_invoice = document.getElementsByClassName("view_invoice");
    for (let index = 0; index < view_invoice.length; index++) {
        const element = view_invoice[index];
        element.addEventListener("click", function () {
            cObj("errors").innerHTML = "";
            // fill the modal with data
            var view_invoice = cObj("invoice_data_holder_"+this.id.substr(13)).value;
            if (hasJsonStructure(view_invoice)) {
                showModal("view_client_invoice");
                var invoice_data = JSON.parse(view_invoice);
                cObj("edit_invoice_id").value = invoice_data.invoice_number;
                cObj("edit_amount_to_pay").value = invoice_data.amount_to_pay;
                cObj("edit_invoice_deadline").value = invoice_data.deadline_duration;
                cObj("edit_period_duration").value = invoice_data.invoice_for_duration.split(" ")[0];
                var children = cObj("edit_period_unit").children;
                console.log(invoice_data.invoice_for_duration.split(" ")[1]);
                for (let index = 0; index < children.length; index++) {
                    const elem = children[index];
                    elem.selected = elem.value == invoice_data.invoice_for_duration.split(" ")[1];
                }

                cObj("edit_payment_from_date").value = invoice_data.payment_from_date;
                cObj("edit_payment_from_time").value = invoice_data.payment_from_time;
                var children2 = cObj("edit_vat_included").children;
                for (let index = 0; index < children2.length; index++) {
                    const elem = children2[index];
                    elem.selected = elem.value == invoice_data.VAT_type;
                }
            }else{
                cObj("errors").innerHTML = "<p class='text-danger'>An error occured!</p>";
            }
        });
    }

    cObj("close_view_client_invoice_1").onclick = function () {
        hideModal("view_client_invoice");
    }
    cObj("close_view_client_invoice_2").onclick = function () {
        hideModal("view_client_invoice");
    }

    // send invoice
    var send_invoice = document.getElementsByClassName("send_invoice");
    for (let index = 0; index < send_invoice.length; index++) {
        const element = send_invoice[index];
        element.addEventListener("click", function () {
            cObj("errors").innerHTML = "";
            cObj("invoice_number_holder").innerText = "NULL";
            // fill the modal with data
            var view_invoice = cObj("invoice_data_holder_"+this.id.substr(13)).value;
            if (hasJsonStructure(view_invoice)) {
                showModal("send_client_invoice");
                var invoice_data = JSON.parse(view_invoice);
                cObj("invoice_number_holder").innerText = invoice_data.invoice_number;
                cObj("send_invoice_id").value = invoice_data.invoice_number;
            }else{
                cObj("errors").innerHTML = "<p class='text-danger'>An error occured!</p>";
            }
        });
    }

    cObj("close_send_client_invoice_1").onclick = function () {
        hideModal("send_client_invoice");
    }

    cObj("close_send_client_invoice_2").onclick = function () {
        hideModal("send_client_invoice");
    }

    var delete_invoice = document.getElementsByClassName("delete_invoice");
    for (let index = 0; index < delete_invoice.length; index++) {
        const element = delete_invoice[index];
        element.addEventListener("click", function () {
            cObj("errors").innerHTML = "";
            cObj("delete_client_invoice_btn").href = "#";
            cObj("delete_invoice_notice").innerHTML = "";
            // fill the modal with data
            var view_invoice = cObj("invoice_data_holder_"+this.id.substr(15)).value;
            if (hasJsonStructure(view_invoice)) {
                showModal("delete_client_invoice");
                var invoice_data = JSON.parse(view_invoice);
                cObj("delete_client_invoice_btn").href = "/Delete-Invoice/"+invoice_data.invoice_number;
                cObj("delete_invoice_notice").innerHTML = "Are you sure you want to delete this invoice with number "+invoice_data.invoice_number;
            }else{
                cObj("errors").innerHTML = "<p class='text-danger'>An error occured!</p>";
            }
        });
    }

    cObj("close_delete_client_invoice_1").onclick = function () {
        hideModal("delete_client_invoice");
    }

    cObj("close_delete_client_invoice_2").onclick = function () {
        hideModal("delete_client_invoice");
    }
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

cObj("confirm_client_convert").onclick = function () {
    cObj("submit_convert").click();
}

function getRouterInterfaces() {
    sendDataGet("GET","/router/"+this.value+"",cObj("interface_holder"),cObj("interface_load"));
}

function getRouterProfiles() {
    sendDataGet("GET","/routerProfile/"+cObj("router_list").value+"",cObj("router_profile_holder"),cObj("secrets_load"));
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

    
function showModal(modal_id) {
    cObj(modal_id).classList.remove("hide");
    cObj(modal_id).classList.add("show");
    cObj(modal_id).classList.add("showBlock");
}

function hideModal(modal_id) {
    cObj(modal_id).classList.add("hide");
    cObj(modal_id).classList.remove("show");
    cObj(modal_id).classList.remove("showBlock");
}

/**DELETE USER MODAL */
cObj("convert_client").onclick = function () {
    showModal("convert_client_modal");
}

cObj("close_convert_client").onclick = function () {
    hideModal("convert_client_modal");
}

cObj("hide_convert_client").onclick = function () {
    hideModal("convert_client_modal");
}

/**DELETE USER MODAL */
cObj("prompt_delete").onclick = function () {
    showModal("delete_client_modal");
}

cObj("close_this_window_delete").onclick = function () {
    hideModal("delete_client_modal");
}

cObj("hide_delete_column").onclick = function () {
    hideModal("delete_client_modal");
}

/** UPDATE USER PHONENUMBER */
cObj("edit_phone_number").onclick = function () {
    showModal("update_phone_modal");
}

cObj("close_update_phone_2").onclick = function () {
    hideModal("update_phone_modal");
}

cObj("close_update_phone_1").onclick = function () {
    hideModal("update_phone_modal");
}

/** UPDATE USER EXPIRATION DATE  */
cObj("edit_expiration_date").onclick = function () {
    showModal("update_expiration_date_modal");
}

cObj("close_update_expiration_date_modal_2").onclick = function () {
    hideModal("update_expiration_date_modal");
}

cObj("close_update_expiration_date_modal_1").onclick = function () {
    hideModal("update_expiration_date_modal");
}

/** UPDATE MONTHLY PAYMENT  */
cObj("edit_monthly_payments").onclick = function () {
    showModal("update_monthly_payment");
}

cObj("close_update_monthly_payment_1").onclick = function () {
    hideModal("update_monthly_payment");
}

cObj("close_update_monthly_payment_2").onclick = function () {
    hideModal("update_monthly_payment");
}

/** UPDATE MONTHLY MINIMUM PAYMENT  */ 
cObj("edit_minimum_amount").onclick = function () {
    showModal("update_monthly_min_pay_modal");
}

cObj("close_update_monthly_min_pay_modal_2").onclick = function () {
    hideModal("update_monthly_min_pay_modal");
}

cObj("close_update_monthly_min_pay_modal_1").onclick = function () {
    hideModal("update_monthly_min_pay_modal");
}

/** UPDATE WALLET AMOUNT  */ 
cObj("edit_wallet").onclick = function () {
    showModal("update_wallet_amount_modal");
}

cObj("close_update_wallet_amount_modal_2").onclick = function () {
    hideModal("update_wallet_amount_modal");
}

cObj("close_update_wallet_amount_modal_1").onclick = function () {
    hideModal("update_wallet_amount_modal");
}

/** UPDATE FREEZE STATUS  */ 
cObj("edit_freeze_client").onclick = function () {
    showModal("update_freeze_status_modal");
}

cObj("close_update_freeze_status_modal_1").onclick = function () {
    hideModal("update_freeze_status_modal");
}

cObj("close_update_freeze_status_modal_2").onclick = function () {
    hideModal("update_freeze_status_modal");
}


/** UPDATE REFEEREE  */ 
cObj("edit_refferal").onclick = function () {
    showModal("update_refferee_by_modal");
}

cObj("close_update_refferee_by_modal_1").onclick = function () {
    hideModal("update_refferee_by_modal");
}

cObj("close_update_refferee_by_modal_2").onclick = function () {
    hideModal("update_refferee_by_modal");
}

/** UPDATE CLIENT COMMENT */
cObj("edit_comments").onclick = function () {
    showModal("update_comments_modal");
}

cObj("close_update_comments_modal_1").onclick = function () {
    hideModal("update_comments_modal");
}

cObj("close_update_comments_modal_2").onclick = function () {
    hideModal("update_comments_modal");
}

// MODAL FOR A NEW INVOICE
cObj("new_invoice").onclick = function() {
    showModal("generate_client_invoice");
}
cObj("close_generate_client_invoice_1").onclick = function() {
    hideModal("generate_client_invoice");
}
cObj("close_generate_client_invoice_2").onclick = function() {
    hideModal("generate_client_invoice");
}

function checkBlank(object_id) {
    if (cObj(object_id).value.trim().length > 0) {
        cObj(object_id).classList.add("border");
        cObj(object_id).classList.add("border-secondary");
        cObj(object_id).classList.add("border-danger");
        return 0;
    }else{
        cObj(object_id).classList.remove("border");
        cObj(object_id).classList.remove("border-danger");
        cObj(object_id).classList.add("border-secondary");
        return 1;
    }
}

// cObj("generate_invoice").onclick = function () {
//     var err = checkBlank("invoice_id");
//     err += checkBlank("amount_to_pay");
//     err += checkBlank("period_duration");
//     err += checkBlank("period_unit");
//     err += checkBlank("payment_from_date");
//     err += checkBlank("payment_from_time");
//     err += checkBlank("invoice_deadline");
//     err += checkBlank("vat_included");
    
//     if (err == 0) {
//         cObj("reponse_holder_invoices").innerHTML = "";
//         var datapass = "invoice_number="+cObj("invoice_id").value+"&amount_to_pay="+cObj("amount_to_pay").value+"&period_duration="+cObj("period_duration").value+"&period_unit="+cObj("period_unit").value+"&payment_from_date="+cObj("payment_from_date").value;
//         datapass+="&payment_from_time="+cObj("payment_from_time").value+"&invoice_deadline="+cObj("invoice_deadline").value+"&vat_included="+cObj("vat_included").value+"&client_id="+cObj("client_id_invoice").value;
//         sendDataPost1("POST", "/New-Invoice", datapass, cObj("reponse_holder_invoices"), cObj("invoice_loader"));
//     }else{
//         cObj("reponse_holder_invoices").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
//     }
// }

// Send date with post request
function sendDataPost1(method, file, datapassing, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("invisible");
        } else if (this.status == 500) {
            object2.classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        } else if (this.status == 204) {
            object2.classList.add("invisible");
            object1.innerHTML = "<p class='red_notice'>Password updated successfully!</p>";
        }
        // console.log(this.status);
    };
    xml.open(method, "" + file, true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send(datapassing);
}