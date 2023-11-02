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
    // set the client_acc_number with an on key up listener
    var client_acc_number = document.getElementById("client_acc_number");
    client_acc_number.onkeyup = function () {
        var acc_no = this.value;
        // console.log(client_accounts);
        var acc_up = acc_no.toUpperCase();
        if (acc_up.length > 0) {
            var present = 0;
            for (let index = 0; index < client_accounts.length; index++) {
                const element = client_accounts[index].toUpperCase();
                if (element == acc_up) {
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
        console.log(typos);
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
    // client licence account number
    var client_lc = document.getElementById("licence_acc_no");
    client_lc.onkeyup = function () {
        var typos = this.value;
        console.log(typos);
        var usernames_up = typos.toUpperCase();
        if (usernames_up.length > 0) {
            var present = 0;
            for (let index = 0; index < client_lc_acc.length; index++) {
                const element = client_lc_acc[index].toUpperCase();
                if (element == usernames_up) {
                    present = 1;
                    break;
                }
            }
            if (present == 1) {
                document.getElementById("error_lc_acc_no").innerText = "Licence Number already in use!";
                document.getElementById("licence_acc_no").classList.add("border");
                document.getElementById("licence_acc_no").classList.add("border-danger");
            } else {
                document.getElementById("error_lc_acc_no").innerText = "";
                document.getElementById("licence_acc_no").classList.remove("border");
                document.getElementById("licence_acc_no").classList.remove("border-danger");
            }
        } else {
            document.getElementById("error_lc_acc_no").innerText = "";
            document.getElementById("licence_acc_no").classList.remove("border");
            document.getElementById("licence_acc_no").classList.remove("border-danger");
        }
    }
    sendDataGet("GET","/getpackages",cObj("packages_lists"),cObj("interface_load"));
}

