function ucwords(string) {
    var cases = string.toLowerCase().split(" ");
    // split the string to get the number of words present
    var final_word = "";
    for (let index = 0; index < cases.length; index++) {
        const element = cases[index];
        final_word +=element.substr(0,1).toUpperCase()+element.substr(1)+" ";
    }
    return final_word.trim();
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
function ucword(string) {
    if (string != null) {
        var cases = string.toLowerCase();
        // split the string to get the number of words present
        var final_word = cases.substr(0,1).toUpperCase()+cases.substr(1);
        return final_word.trim();
    }
    return "";
}
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}
// Send date with post request
function sendDataPost1(method, file, datapassing, object1, object2) {
    //make the loading window show
    object2.classList.remove("d-none");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("d-none");
        } else if (this.status == 500) {
            object2.classList.add("d-none");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        } else if (this.status == 204) {
            object2.classList.add("d-none");
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
    object2.classList.remove("d-none");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("d-none");
        } else if (this.status == 500) {
            object2.classList.add("d-none");
            // cObj("loadings").classList.add("d-none");
            object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
        }
    };
    xml.open(method, file, true);
    xml.send();
}

// next button
cObj("next_button").onclick = function () {
    var steppers = document.getElementsByClassName("steppers");
    for (let index = 0; index < steppers.length; index++) {
        const element = steppers[index];
        if (!element.classList.contains("d-none")) {
            var ids = element.id.substring(4);
            // console.log(ids);
            if (ids == 6) {
                window.location.href = "/Routers";
                // cObj("step1").classList.remove("d-none");
                // element.classList.add("d-none");
                // cObj("step"+ids+"-tab").classList.remove("active");
                // // steps button
                // cObj("step1-tab").classList.add("active");
                // cObj("prev_button").disabled = true;
                // redirect
            }else{
                if (ids == 1) {
                    var status = step1();
                }
                if (ids == 2) {
                    step3();
                }
                if (ids == 5) {
                    this.innerText = "Finish";
                    getAllConfiguration();
                }
                if (ids == 2 || ids == 3 || ids == 4 || ids == 5) {
                    // continue
                    cObj("prev_button").disabled = false;
                    cObj("step"+ids+"-tab").classList.remove("active");
                    element.classList.add("d-none");
                    var news = ((ids*1)+1);
                    cObj("step"+news).classList.remove("d-none");
                    // steps button
                    cObj("step"+news+"-tab").classList.add("active");
                    // end of continue
                }
            }
            break
        }
    }
}

cObj("prev_button").onclick = function () {
    var steppers = document.getElementsByClassName("steppers");
    for (let index = 0; index < steppers.length; index++) {
        const element = steppers[index];
        if (!element.classList.contains("d-none")) {
            var ids = element.id.substring(4);
            element.classList.add("d-none");
            cObj("step"+((ids * 1) - 1)).classList.remove("d-none");
            // steps buttons
            cObj("step"+((ids * 1) - 1)+"-tab").classList.add("active");
            cObj("step"+ids+"-tab").classList.remove("active");
            if (((ids * 1) - 1) == 1) {
                cObj("prev_button").disabled = true;
            }
            cObj("next_button").innerText = "Next & Save";
            break
        }
    }
}

function step1() {
    var err = checkBlank("router_name");
    err+=checkBlank("router_ip_address");
    err+=checkBlank("router_username");
    err+=checkBlank("router_password");
    err+=checkBlank("router_api_port");
    if (err == 0) {
        cObj("error_handlers").innerHTML = "";
        var datapass = "router_name="+valObj("router_name")+"&router_ip_address="+valObj("router_ip_address")+"&router_username="+valObj("router_username")+"&router_password="+valObj("router_password")+"&router_api_port="+valObj("router_api_port");
        sendDataPost1("POST","/connect_router",datapass,cObj("error_handlers"),cObj("loader"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("loader").classList.contains("d-none")) {
                    stopInterval(ids);
                    // console.log(ids);
                    getBridges();

                    // if the connection is not established dont go to the next window
                    if ((cObj("errors_connection") == null || cObj("errors_connection") == undefined) && cObj("error_handlers").innerText.length == 0) {
                        ids = 1;
                        // continue to next window
                        cObj("prev_button").disabled = false;
                        cObj("step"+ids+"-tab").classList.remove("active");
                        cObj("step1").classList.add("d-none");
                        var news = ((ids*1)+1);
                        cObj("step"+news).classList.remove("d-none");
                        // steps button
                        cObj("step"+news+"-tab").classList.add("active");
                        // end of continue
                    }
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_handlers").innerHTML = "<p class='text-danger'>Fill all fields with the red border</p>";
    }
}
// step3();
function step3() {
    // check setting if they are dynamic static or dynamic
    var datapass = "get_setting=true";
    sendDataPost1("POST","/get_setting",datapass,cObj("internet_access_modes"),cObj("load_internet_access"));
}

cObj("internet_access_methods").onchange = function () {
    var my_value = this.value;
    var s_win = document.getElementsByClassName("s_win");
    for (let index = 0; index < s_win.length; index++) {
        const element = s_win[index];
        element.classList.add("d-none");
    }
    if (my_value == "dynamic") {
        cObj("win_dynamic").classList.remove("d-none");
    }else if (my_value == "static") {
        cObj("static_window").classList.remove("d-none");
    }else if (my_value == "ppp") {
        cObj("pppoe_window").classList.remove("d-none");
    }
}
function valObj(objectid){
    if (document.getElementById(objectid) == null) {
        return "";
    }
    return document.getElementById(objectid).value;
}

function getBridges() {
    var datapass = "?get_bridges=true";
    sendDataGet("GET","/getbridge",cObj("interface_lists"),cObj("loading"));
    setTimeout(() => {
        var ids = setInterval(() => {
            if (cObj("loading").classList.contains("d-none")) {
                stopInterval(ids);
                // get the data 
                var un_used_ports = document.getElementsByClassName("un_used_ports");
                var data = "<ul>";
                var data2 = "<ul>";
                var counter = 0;
                for (let index = 0; index < un_used_ports.length; index++) {
                    const element = un_used_ports[index];
                    if (element.id == "ether1") {
                        // data+="<li><label for='interface_names"+element.id+"' class='form-contol-label'>"+element.id+"</label><input data-toggle='tooltip' title='This interface will be used as your WAN gateway' type='checkbox' disabled value='"+element.id+"' name='interface_names"+element.id+"' id='interface_names"+element.id+"' class='interfaces_present ml-1'></li>";
                    }else{
                        counter++;
                        data+="<li><label for='interface_names"+element.id+"' class='form-contol-label'>"+element.id+"</label><input type='checkbox' checked value='"+element.id+"' name='interface_names"+element.id+"' id='interface_names"+element.id+"' class='interfaces_present ml-1'></li>";
                    }
                    if (element.id == "ether1") {
                        // data2+="<li><label for='interface_unused"+element.id+"' class='form-contol-label'>"+element.id+"</label><input data-toggle='tooltip' title='This interface will be used as your WAN gateway' type='checkbox' disabled value='"+element.id+"' name='interface_unused"+element.id+"' id='interface_unused"+element.id+"' class='interfaces_present ml-1'></li>";
                    }else{
                        data2+="<li><label for='interface_unused"+element.id+"' class='form-contol-label'>"+element.id+"</label><input type='checkbox' checked value='"+element.id+"' name='interface_unused"+element.id+"' id='interface_unused"+element.id+"' class='interface_unused ml-1'></li>";
                    }
                }
                data+="</ul>";
                data2+="</ul>";
                if (counter > 0) {
                    cObj("interface_list").innerHTML = data;
                    cObj("bridge-adding").innerHTML = data2;
                }else{
                    cObj("interface_list").innerHTML = "<p class='text-danger'>No interfaces present</p>";
                    cObj("bridge-adding").innerHTML = "<p class='text-danger'>No interfaces present</p>";
                }
                // end of displaying all bridge interfaces
                var bridges = document.getElementsByClassName("bridge-ports");
                data = "<select class='form-control' name='bridge_list' id='bridge_list'><option value='' hidden>Select Bridge</option>";
                for (let index = 0; index < bridges.length; index++) {
                    const element = bridges[index];
                    data+="<option value='"+element.innerText+"'>"+ucwords(element.innerText)+"</option>";
                }
                data+="</select>";
                cObj("bridges").innerHTML = data;

                // set the listeners to remove the interfaces from the bridge
                var funga = document.getElementsByClassName("funga");
                for (let index = 0; index < funga.length; index++) {
                    const element = funga[index];
                    element.addEventListener("click",remove_interface);
                }
                // set listener to remove bridges
                var my_funga = document.getElementsByClassName("my_funga");
                for (let index = 0; index < my_funga.length; index++) {
                    const element = my_funga[index];
                    element.addEventListener("click",removeBridge);
                }
            }
        }, 100);
    }, 200);
}
window.onload = function loadingWin() {
    // enable tooltips every where
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
}

function removeBridge() {
    var datapass = "remove_bridge="+this.id.substr(8)+"&bridge_name="+cObj("br_name"+this.id.substr(8)).innerText;
    // console.log(datapass);
    sendDataPost1("POST","/remove_bridge",datapass,cObj("error_handlers"),cObj("loading"));
    setTimeout(() => {
        var ids = setInterval(() => {
            if (cObj("loading").classList.contains("d-none")) {
                stopInterval(ids);
                getBridges();
            }
        }, 100);
    }, 200);
}

function remove_interface() {
    var datapass = "router_id="+this.id.substr(5);
    sendDataPost1("POST","/remove_interface_bridge",datapass,cObj("error_handlers"),cObj("loading"));
    setTimeout(() => {
        var ids = setInterval(() => {
            if (cObj("loading").classList.contains("d-none")) {
                stopInterval(ids);
                getBridges();
            }
        }, 100);
    }, 200);
}

cObj("add_bridges").onclick = function () {
    cObj("add_bridge_window").classList.remove("d-none");
    cObj("edit_bridge_window").classList.add("d-none");
}

cObj("edit_bridges").onclick = function () {
    cObj("add_bridge_window").classList.add("d-none");
    cObj("edit_bridge_window").classList.remove("d-none");
}

cObj("add_bridge").onclick = function () {
    var err = checkBlank("bridge_name");
    var interfaces_present = document.getElementsByClassName("interfaces_present");
    var selected = 0;
    var int_selected = "";
    for (let index = 0; index < interfaces_present.length; index++) {
        const element = interfaces_present[index];
        if (element.checked == true) {
            selected++;
            int_selected+=element.value+",";
        }
    }
    int_selected = int_selected.length>0 ? int_selected.substring(0,int_selected.length-1):"";
    if (selected == 0) {
        err++;
        alert("Please select atleast one interface so that the bridge can be created!");
    }

    if (err == 0) {
        // proceed and sent the interface list
        var datapass = "bridge_name="+cObj("bridge_name").value+"&bridge_ports="+int_selected;
        sendDataPost1("POST","/add_bridge",datapass,cObj("error_handling"),cObj("bridge_create_loader"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("bridge_create_loader").classList.contains("d-none")) {
                    stopInterval(ids);
                    getBridges();
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_handling").innerHTML = "<p class='text-danger'>Please fill all the fields covered with red border</p>";
    }
}

cObj("change_bridge").onclick = function () {
    var err = checkBlank("bridge_list");
    var interface_unused = document.getElementsByClassName("interface_unused");
    var counter = 0;
    var interface_list = "";
    for (let index = 0; index < interface_unused.length; index++) {
        const element = interface_unused[index];
        if (element.checked == true) {
            counter++;
            interface_list+=element.value+",";
        }
    }
    if (counter == 0) {
        err++;
        alert("Select atleast one interface before proceeding!")
    }
    if (err == 0) {
        interface_list = interface_list.length>0 ? interface_list.substring(0,interface_list.length-1) : "";
        var datapass = "bridge_name="+valObj("bridge_list")+"&interface_name="+interface_list;
        sendDataPost1("POST","/change_bridge",datapass,cObj("error_handling"),cObj("bridge_edit_loader"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("bridge_create_loader").classList.contains("d-none")) {
                    stopInterval(ids);
                    getBridges();
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_handling").innerHTML = "<p class='text-danger'>Fill all field covered with red borders.</p>";
    }
}

cObj("set_dynamic").onclick = function () {
    var datapass = "";
    sendDataPost1("POST","/set_dynamic",datapass,cObj("dynamic_set_err"),cObj("spinner_dynamic"));
    setTimeout(() => {
        var ids = setInterval(() => {
            if (cObj("spinner_dynamic").classList.contains("d-none")) {
                stopInterval(ids);
                step3();
            }
        }, 100);
    }, 200);
}

cObj("set_static_assignment").onclick = function () {
    var err = 0;
    err+=checkBlank("ip_address_assigned");
    err+=checkBlank("default_gw");
    err+=checkBlank("default_dns_server");
    if (err == 0) {
        cObj("error_static_window").innerHTML = "";
        // get the ip address
        var ip_address = cObj("ip_address_assigned").value;
        cObj("error_static_window").innerHTML = check_ip_subnet(ip_address);
        if (check_ip_subnet(ip_address).length == 0) {
            grayBorder(cObj("ip_address_assigned"));
            // check for the gateway
            var gateway = cObj("default_gw").value;
            cObj("error_static_window").innerHTML = check_ip_addr(gateway);
            if (check_ip_addr(gateway).length == 0) {
                grayBorder(cObj("default_gw"));
                var dns = cObj("default_dns_server").value;
                cObj("error_static_window").innerHTML = check_ip_addr(dns);
                if (check_ip_addr(dns) == 0) {
                    grayBorder(cObj("default_dns_server"));
                    // get the data and save in the database
                    var datapass = "ipaddress="+valObj("ip_address_assigned")+"&gateway="+valObj("default_gw")+"&dns="+valObj("default_dns_server");
                    sendDataPost1("POST","/set_static_access",datapass,cObj("error_static_window"),cObj("load_static_access"));
                    setTimeout(() => {
                        var ids = setInterval(() => {
                            if (cObj("load_static_access").classList.contains("d-none")) {
                                stopInterval(ids);
                                step3();
                            }
                        }, 100);
                    }, 200);
                }else{
                    redBorder(cObj("default_dns_server"));
                }
            }else{
                redBorder(cObj("default_gw"));
            }
        }else{
            redBorder(cObj("ip_address_assigned"));
        }

    } else {
        cObj("error_static_window").innerHTML = "<p class='text-danger'>Please fill all fields with red border!</p>";
    }
}

function check_ip_subnet(ip_address) {
    // checks for the ip address with a subnet mask
    if (ip_address.length > 0) {
        // split to check subnet mask
        var subnet_split = ip_address.split("\/");
        if (subnet_split.length == 2) {
            // check if the subnet mask is above 30
            if ((subnet_split[1]*1) <= 30) {
                // check the ip address split and check if they are all.
                ip_addresses = subnet_split[0].split(".");
                if (ip_addresses.length == 4) {
                    var errors = 0;
                    for (let index = 0; index < ip_addresses.length; index++) {
                        const element = ip_addresses[index]*1;
                        if (element >255) {
                            errors++;
                        }
                    }
                    if (errors == 0) {
                        return "";
                    }else{
                        return "<p class='text-danger'>Your IP address is invalid!</p>";
                    }
                }else{
                    return "<p class='text-danger'>Your IP address is invalid!</p>";
                }
            }else{
                return "<p class='text-danger'>Your subnet mask should not be greater than 30</p>";
            }
        }else{
            return "<p class='text-danger'>Please provide your subnet mask</p>";
        }
    }else{
        return "<p class='text-danger'>Please provide a valid ip address</p>";
    }
}

function check_ip_addr(ip_address) {
    var ip_addresses = ip_address.split(".");
    // console.log(ip_addresses);
    if (ip_addresses.length == 4) {
        var errors = 0;
        for (let index = 0; index < ip_addresses.length; index++) {
            const element = ip_addresses[index]*1;
            if (element >255 || element.length < 1) {
                errors++;
            }
        }
        if (errors == 0) {
            return "";
        }else{
            return "<p class='text-danger'>Your IP address is invalid!</p>";
        }
    }else{
        return "<p class='text-danger'>Your IP address is invalid!</p>";
    }
}

cObj("set_pppoe_connection").onclick = function () {
    var err = 0;
    err+=checkBlank("pppoer_username");
    err+=checkBlank("pppoe_password");
    if (err == 0) {
        cObj("error_pppoe_window").innerHTML = "";
        var datapass = "username="+valObj("pppoer_username")+"&password="+valObj("pppoe_password");
        sendDataPost1("POST","/set_pppoe_assignment",datapass,cObj("error_pppoe_window"),cObj("load_pppoe_access"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("load_pppoe_access").classList.contains("d-none")) {
                    stopInterval(ids);
                    step3();
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_pppoe_window").innerHTML = "<p class='text-danger'>Please fill all fields covered with red border!</p>";
    }
}
cObj("internet_distro_methods").onchange = function () {
    var my_value = this.value;
    var give_net = document.getElementsByClassName("give_net");
    for (let index = 0; index < give_net.length; index++) {
        const element = give_net[index];
        element.classList.add("d-none");
    }
    if (my_value == "static") {
        cObj("static_setup").classList.remove("d-none");
    }else if (my_value == "ppp") {
        cObj("pppoe_set_up").classList.remove("d-none");
    }
}

cObj("skip_create_pool").onclick = function skip_step1() {
    displayPools();
    cObj("pppoe_set_1").classList.add("d-none");
    cObj("pppoe_set_2").classList.remove("d-none");
}

cObj("add_pool").onclick = function () {
    // get the data
    var err = 0;
    err+=checkBlank("pool_names");
    err+=checkBlank("pppoe_pool");
    if (err == 0) {
        cObj("err_handler_pools").innerHTML = "";
        // check the pool address
        $pool = valObj("pppoe_pool");
        $split_pool = $pool.split("-");
        if ($split_pool.length == 2) {
            cObj("err_handler_pools").innerHTML = "";
            // check the ip addresses if they are legit
            var first_addr = $split_pool[0].trim();
            var second_addr = $split_pool[1].trim();
            var err1 = check_ip_addr(first_addr);
            var err2 = check_ip_addr(second_addr);
            if (err1.length == 0 && err2.length == 0) {
                grayBorder(cObj("pppoe_pool"));
                // set up pool
                var datapass = "pool_name="+valObj("pool_names")+"&pool_address="+first_addr+"-"+second_addr;
                sendDataPost1("POST","/set_pool",datapass,cObj("err_handler_pools"),cObj("load_add_pool_access"));
                setTimeout(() => {
                    var ids = setInterval(() => {
                        if (cObj("load_pppoe_access").classList.contains("d-none")) {
                            stopInterval(ids);
                            // check if the object is valid or not
                            if (cObj("added_pool_success") != undefined && cObj("added_pool_success") != null) {
                                // proceed to the next page and display pools
                                displayPools();
                                cObj("pppoe_set_1").classList.add("d-none");
                                cObj("pppoe_set_2").classList.remove("d-none");
                            }
                        }
                    }, 100);
                }, 200);
            }else{
                redBorder(cObj("pppoe_pool"));
                cObj("err_handler_pools").innerHTML = "<p class='text-danger'>The IP address you have provided are invalid!</p>";
            }
        }else{
            cObj("err_handler_pools").innerHTML = "<p class='text-danger'>You have provide an invalid address pool separate with hyphen and try again!</p>";
        }
    }else{
        cObj("err_handler_pools").innerHTML = "<p class='text-danger'>Please fill all fields covered with red border!</p>";
    }
}
// displayPools();
function displayPools() {
    sendDataGet("GET","/get_pools",cObj("pool_lists"),cObj("load_create_profile"));
    setTimeout(() => {
        var ids = setInterval(() => {
            if (cObj("load_create_profile").classList.contains("d-none")) {
                stopInterval(ids);
                // check if the object is valid or not
                if (cObj("pool_name") != undefined && cObj("pool_name") != null) {
                    // check if the pool name is present
                    cObj("pool_name").addEventListener("click",processGW);
                }
            }
        }, 100);
    }, 200);
}

function processGW() {
    var my_value = this.value;
    if (my_value.length > 0) {
        var gw = my_value.split("|")[1].split("-")[0].trim().split(".");
        var final_gw = gw[0]+"."+gw[1]+"."+gw[2]+".1";
        cObj("gateway_address").value = final_gw;
    }else{
        cObj("gateway_address").value = "";
    }
}

cObj("save_profiles").onclick = function () {
    var err = 0;
    err+=checkBlank("profile_name");
    err+=checkBlank("pool_name");
    err+=checkBlank("gateway_address");
    err+=checkBlank("upload_units1");
    err+=checkBlank("upload_limit1");
    err+=checkBlank("upload_units2");
    err+=checkBlank("upload_limit2");
    err+=checkBlank("only_one");
    err+=checkBlank("bridge_pppoe_list");
    if (err == 0) {
        cObj("error_handling_ppoe").innerHTML = "";
        var datapass = "profile_name="+valObj("profile_name")+"&pool_name="+valObj("pool_name")+"&gateway_address="+valObj("gateway_address")+"&upload="+valObj("upload_units1")+valObj("upload_limit1")+"&download="+valObj("upload_units2")+valObj("upload_limit2");
        datapass+="&only_one="+valObj("only_one")+"&bridge="+valObj("bridge_pppoe_list");
        sendDataPost1("POST","/add_pppoe_profile",datapass,cObj("error_handling_ppoe"),cObj("load_save_pppoe_profiles"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("load_create_profile").classList.contains("d-none")) {
                    stopInterval(ids);
                    if (cObj("profile_done") != null && cObj("profile_done") != undefined) {
                        getProfileInterface();
                        cObj("pppoe_set_3").classList.remove("d-none");
                        cObj("pppoe_set_2").classList.add("d-none");
                    }
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_handling_ppoe").innerHTML = "<p class='text-danger'>Please fill all fields with red borders before proceeding!</p>";
    }
}
// getProfileInterface();
cObj("skip_step2").onclick = function () {
    getProfileInterface();
    cObj("pppoe_set_3").classList.remove("d-none");
    cObj("pppoe_set_2").classList.add("d-none");
}
function getProfileInterface() {
    sendDataGet("GET","/get_pppoe_server",cObj("interface_pppoe"),cObj("create_server"));
}

cObj("back_step2").onclick = function () {
    cObj("pppoe_set_2").classList.remove("d-none");
    cObj("pppoe_set_3").classList.add("d-none");
}

cObj("back_to_step1").onclick = function () {
    cObj("pppoe_set_1").classList.remove("d-none");
    cObj("pppoe_set_2").classList.add("d-none");
}

cObj("save_ppoe_server").onclick = function () {
    if (cObj("pppoe_server_bridges") != null && cObj("pppoe_server_bridges") != undefined && cObj("profile_pppoe_server") != null && cObj("profile_pppoe_server") != undefined) {
        var err = 0;
        err+=checkBlank("pppoe_server_bridges");
        err+=checkBlank("profile_pppoe_server");
        err+=checkBlank("server_names");

        if (err == 0) {
            cObj("error_handling_pppoe_server").innerHTML = "";
            var datapass = "server_name="+valObj("server_names")+"&profile_name="+valObj("profile_pppoe_server")+"&bridge_name="+valObj("pppoe_server_bridges");
            sendDataPost1("POST","/save_ppoe_server",datapass,cObj("error_handling_pppoe_server"),cObj("load_save_pppoe_server"));
        }else{
            cObj("error_handling_pppoe_server").innerHTML = "<p class='text-danger'>Please all fields covered with red border!</p>";
        }
    }
}

cObj("save_wireless_profile").onclick = function () {
    var err = 0;
    err+=checkBlank("security_profile_name");
    err+=checkBlank("profile_password");
    
    if (err == 0) {
        cObj("error_security_prof").innerHTML = "";
        var datapass = "profile_name="+valObj("security_profile_name")+"&profile_password="+valObj("profile_password");
        sendDataPost1("POST","/add_security",datapass,cObj("error_security_prof"),cObj("load_save_profiles"));
        setTimeout(() => {
            var ids = setInterval(() => {
                if (cObj("load_save_profiles").classList.contains("d-none")) {
                    stopInterval(ids);
                    if (cObj("security_prof_added") != null && cObj("security_prof_added") != undefined) {
                        // move to the next profile
                        cObj("wifi_profile").classList.remove("d-none");
                        cObj("security_window").classList.add("d-none");
                        getSecurityProf();
                    }
                }
            }, 100);
        }, 200);
    }else{
        cObj("error_security_prof").innerHTML = "<p class='text-danger'>Please fill all fields covered with red border</p>";
    }
}

function getSecurityProf() {
    sendDataGet("GET","/get_security_profile",cObj("security_holder"),cObj("wifi_profiles"));
}

cObj("save_wifi").onclick = function () {
    var err = 0;
    err+=checkBlank("security_profile");
    err+=checkBlank("wifi_name");
    if (err == 0) {
        cObj("profile_errors").innerHTML = "";
        var datapass = "ssid="+valObj("wifi_name")+"&security_profile="+valObj("security_profile");
        sendDataPost1("POST","/save_ssid",datapass,cObj("profile_errors"),cObj("load_save_ssid"));
    }else{
        cObj("profile_errors").innerHTML = "<p class='text-danger'>Please fill all fields covered with red border</p>";
    }
}


cObj("back_to_profile").onclick = function () {
    cObj("security_window").classList.remove("d-none");
    cObj("wifi_profile").classList.add("d-none");
}


cObj("skip_adding_prof").onclick = function () {
    cObj("wifi_profile").classList.remove("d-none");
    cObj("security_window").classList.add("d-none");
    getSecurityProf();
}


function getAllConfiguration() {
    // connect router
    sendDataGet("GET","/getconnection",cObj("step_one_conf"),cObj("step_one_load"));
    // step one getting a bridge
    sendDataGet("GET","/getbridge",cObj("step_two_conf"),cObj("step_two_load"));
    // step two 
    sendDataPost1("POST","/get_setting","",cObj("step_three_conf"),cObj("step_three_load"));
    // step three
    sendDataPost1("POST","/get_interface_supply","",cObj("step_four_conf"),cObj("step_four_load"));
    sendDataPost1("POST","/get_wireless","",cObj("step_five_conf"),cObj("step_five_load"));
}