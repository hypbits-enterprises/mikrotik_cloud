
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
function getUser() {
    var acc_no = cObj("search_refferer_keyword").value;
    var err = checkBlank("search_refferer_keyword");
    if (err == 0) {
        var datapass = "/get_refferal/"+acc_no;
        sendDataGet("GET", datapass, cObj("refferer_data"), cObj("search_referer_loader"));
        setTimeout(() => {
            var timeout = 0;
            var ids = setInterval(() => {
                timeout++;
                //after two minutes of slow connection the next process wont be executed
                if (timeout==1200) {
                    stopInterval(ids);                        
                }
                if (cObj("search_referer_loader").classList.contains("invisible")) {
                    var refferer_data = cObj("refferer_data").innerText;
                    var user_data = refferer_data.split(":");
                    if (user_data.length == 4) {
                        cObj("show_data_inside").innerHTML = "";
                        // this is the user data length anything else is an error
                        cObj("refferer_name").innerText = user_data[0];
                        cObj("refferer_acc_no").innerText = user_data[1];
                        cObj("reffer_wallet").innerText = user_data[2];
                        cObj("refferer_location").innerText = user_data[3];
                        cObj("refferer_acc_no2").value = user_data[1];
                        cObj("save_data_inside").disabled = false;
                    }else{
                        cObj("show_data_inside").innerHTML = "<p class='text-danger'>Invalid User</p>";
                        var user_data = document.getElementsByClassName("user_data");
                        for (let index = 0; index < user_data.length; index++) {
                            const element = user_data[index];
                            element.innerText = "Unknown";
                        }
                        cObj("refferer_acc_no2").value = "";
                        cObj("save_data_inside").disabled = true;
                    }
                    stopInterval(ids);
                }
            }, 100);
        }, 200);
    }
}

cObj("find_user_refferal").addEventListener("click",getUser);
cObj("edit_refferal").onclick = function () {
    cObj("set_refferal_window").classList.remove("d-none");
}
cObj("cancel_refferer_updates").onclick = function () {
    cObj("set_refferal_window").classList.add("d-none");
}