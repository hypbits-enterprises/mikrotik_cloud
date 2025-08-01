
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
    cObj("client_secret_username").value = clients_data[0]['client_secret'];
    cObj("secret_username").innerText = clients_data[0]['client_secret'];
    cObj("addresses").innerText = "********";
    cObj("secret_holder").innerText = clients_data[0]['client_secret_password'];
    cObj("client_secret_password").value = clients_data[0]['client_secret_password'];
    // cObj("comments").value = clients_data[0]['comment'];
    var router_names = clients_data[0]['router_name'];
    cObj("router_profiles").innerText = clients_data[0]['client_profile'] ? clients_data[0]['client_profile'] : "null";

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
        router_name.addEventListener("change", getRouterProfiles);
    }else{
        // console.log("Is null");
    }
    rowsColStudents = refferal_payment;
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

function getRouterProfiles() {
    sendDataGet("GET","/routerProfile/"+this.value+"",cObj("profile_holder"),cObj("interface_load"));
}


function getRouterInterfaces() {
    sendDataGet("GET","/router/"+cObj("router_list").value+"",cObj("interface_holder"),cObj("interface_loader"));
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


var notseen = 1;
cObj("display_secret").onclick = function () {
    var pass = cObj("secret_holder").innerText;
    if (notseen == 1) {
        cObj("addresses").innerText = pass;
        notseen = 0;
        this.innerHTML = "<span class='text-secondary'><i class='fas fa-eye-slash'></i></span>";
    }else{
        cObj("addresses").innerText = "********";
        notseen = 1;
        this.innerHTML = "<span class='text-secondary'><i class='fas fa-eye'></i></span>";
    }
}

cObj("freeze_date").onchange = function () {
    if (this.value == "set_freeze") {
        cObj("setFreezeDate").classList.remove("d-none");
    }else{
        cObj("setFreezeDate").classList.add("d-none");
    }
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

/**CONVERT USER MODAL */
cObj("convert_client").onclick = function () {
    showModal("convert_client_modal");
}

cObj("close_convert_client").onclick = function () {
    hideModal("convert_client_modal");
}

cObj("hide_convert_client").onclick = function () {
    hideModal("convert_client_modal");
}
cObj("confirm_client_convert").onclick = function () {
    cObj("submit_convert").click();
}