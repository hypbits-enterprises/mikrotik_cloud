// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}
// Send date with post request
function sendDataPost1(method, file, datapassing, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
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



var mpesa_id_search = document.getElementById("mpesa_id_search");
mpesa_id_search.onclick = function () {
    var mpesa_id = document.getElementById("mpesa_id").value;
    console.log(mpesa_id);
    if (mpesa_id.length > 1) {
        // get the mpesa data
        sendDataGet("GET","/Payment/mpesa/"+mpesa_id,cObj("transaction"),cObj("interface_load"));
        setTimeout(() => {
            var timeout = 0;
            var ids = setInterval(() => {
                timeout++;
                //after two minutes of slow connection the next process wont be executed
                if (timeout==1200) {
                    stopInterval(ids);                        
                }
                if (cObj("interface_load").classList.contains("invisible")) {
                    // get the data that has been returned
                    var transaction_data = JSON.parse(cObj("transaction").innerText);
                    // take the transaction infor and fill the fields and display the fields
                    if (transaction_data.length > 0) {
                        cObj("confirm_pay").classList.remove("hide");
                        cObj("data1").innerText = transaction_data[0].transaction_id;
                        cObj("data2").innerText = transaction_data[0].transaction_mpesa_id;
                        cObj("data3").innerText = transaction_data[0].transaction_date;
                        cObj("data4").innerText = transaction_data[0].transacion_amount;
                        cObj("data5").innerText = transaction_data[0].fullnames;
                        cObj("data6").innerText = transaction_data[0].transaction_short_code;
                        cObj("data7").innerText = transaction_data[0].phone_transacting;
                        cObj("error_handler").innerText = "";
                        cObj("confirm_pay").href = "/confirmTransfer/"+cObj("clientids").innerText+"/"+transaction_data[0].transaction_id;
                    }else{
                        cObj("data1").innerText = "Null";
                        cObj("data2").innerText = "Null";
                        cObj("data3").innerText = "Null";
                        cObj("data4").innerText = "Null";
                        cObj("data5").innerText = "Null";
                        cObj("data6").innerText = "Null";
                        cObj("data7").innerText = "Null";

                        cObj("confirm_pay").classList.add("hide");
                        cObj("error_handler").innerText = "Invalid Mpesa ID or its already used!";
                        setTimeout(() => {
                            cObj("error_handler").innerText = "";
                        }, 3000);
                    }
                }
                stopInterval(ids);
            }, 100);
        }, 200);
    }
}