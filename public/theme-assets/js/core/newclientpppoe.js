// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}
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
// Send data with get
function sendDataGet(method, file, object1, object2) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
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



// get the router interfaces on change of the select button
window.onload = function () {
    // set the select button with a listener
    var router_name = document.getElementById("router_name");
    if (router_name != null) {
        // set the event listener
        router_name.addEventListener("change", getRouterInterfaces);
    } else {
        console.log("Is null");
    }

    // set the client_acc_number with an on key up listener
    var client_acc_number = document.getElementById("client_acc_number");
    client_acc_number.onkeyup = function () {
        var valued = this.value;
        cObj("client_secret_username").value = valued;
        var acc_no = this.value;
        var acc_up = acc_no.toUpperCase();
        if (acc_up.length > 0) {
            var present = 0;
            for (let index = 0; index < client_accounts.length; index++) {
                const element = client_accounts[index].toUpperCase();
                if (element == acc_ups) {
                    present = 1;
                    break;
                }
            }
            if (present == 1) {
                document.getElementById("error_acc_no").innerText = "Account number in use!";
                document.getElementById("client_acc_number").classList.add("border");
                document.getElementById("client_acc_number").classList.add("border-danger");
            } else {
                document.getElementById("error_acc_no").innerText = "";
                document.getElementById("client_acc_number").classList.remove("border");
                document.getElementById("client_acc_number").classList.remove("border-danger");
            }
        } else {
            document.getElementById("error_acc_no").innerText = "";
            document.getElementById("client_acc_number").classList.remove("border");
            document.getElementById("client_acc_number").classList.remove("border-danger");
        }
    }
    var client_use = document.getElementById("client_username");
    client_use.onkeyup = function () {
        var typos = this.value;
        var usernames_up = typos.toUpperCase();
        if (usernames_up.length > 0) {
            var present = 0;
            for (let index = 0; index < client_username.length; index++) {
                const element = client_username[index].toUpperCase();
                if (element == usernames_up) {
                    present = 1;
                    break;
                }
            }
            if (present == 1) {
                document.getElementById("err_username").innerText = "Username already in use!";
                document.getElementById("client_username").classList.add("border");
                document.getElementById("client_username").classList.add("border-danger");
            } else {
                document.getElementById("err_username").innerText = "";
                document.getElementById("client_username").classList.remove("border");
                document.getElementById("client_username").classList.remove("border-danger");
            }
        } else {
            document.getElementById("err_username").innerText = "";
            document.getElementById("client_username").classList.remove("border");
            document.getElementById("client_username").classList.remove("border-danger");
        }
    }
}
function getRouterInterfaces() {
    sendDataGet("GET", "/routerProfile/" + this.value + "", cObj("interface_holder"), cObj("interface_load"));
}
var init_val = "";
function checkOnlyDigits(e, object_id,errhandler) {
    e = e ? e : window.event;
    var charCode = e.which ? e.which : e.keyCode;
    // console.log(charCode);
    var value = cObj(object_id).value;
    var arrays = value.split(".");
    var arr2 = value.split("\/");
    var reject = 0;
    console.log(charCode);
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
    console.log(String.fromCharCode(charCode));
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
