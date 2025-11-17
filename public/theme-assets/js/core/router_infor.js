
var load_router_infor = "0";
if (router_detail.length > 0) {
    load_router_infor = "1";
}

var milli_seconds = 1200;
setInterval(() => {
    if (milli_seconds == 0) {
        window.location.href = "/";
    }
    milli_seconds--;
}, 1000);
function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
    .then(() => {
        console.log('Text successfully copied to clipboard:', text);
    })
    .catch(err => {
        console.error('Unable to copy text to clipboard', err);
    });
}
var send_to_clipboard = document.getElementById("send_to_clipboard");
send_to_clipboard.addEventListener("click", function () {
    var this_inner_text = document.getElementById("command_holder").innerText;
    var child = this.children;
    if (child[0]!=undefined) {
        child[0].innerHTML = "<i class='ft-check-circle'></i> Copied!";
    }
    setTimeout(() => {
        if (child[0]!=undefined) {
            child[0].innerHTML = "<i class='ft-copy'></i> Copy";
        }
    }, 2000);
    copyToClipboard(this_inner_text);
});

document.getElementById("configuration_show_button").onclick = function () {
    document.getElementById("configuration_window").classList.toggle("d-none");
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

/**DELETE EXPENSE MODAL */
cObj("delete_user").onclick = function () {
    showModal("delete_router_modal");
}

cObj("hide_delete_expense").onclick = function () {
    hideModal("delete_router_modal");
}

cObj("close_this_window_delete").onclick = function () {
    hideModal("delete_router_modal");
}
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}


cObj("tab2-tab").addEventListener("click", function () {
    cObj("add_bridges_btn").disabled = true;
    cObj("sync_bridges_btn").disabled = true;
    cObj("add_bridges_btn").classList.add("disabled");
    cObj("sync_bridges_btn").classList.add("disabled");
    if ($.fn.DataTable.isDataTable('#router_table_data')) {
        // just reload data
        $('#router_table_data').DataTable().ajax.reload();
    } else {
        let table = $('#router_table_data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/Router_Bridges/datatable/"+router_data[0].router_id, // route to controller
                type: "GET",
                data: function (d) {
                }
            },
            order: [[0, 'desc']],
            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
            pageLength: 5,  // default rows per page
            lengthMenu: [5, 10, 20], // available options
            columns: [
                { data: 'rownum' },
                { data: 'bridge_name' },
                { data: 'bridge_status' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

        // reinitialize tooltips after table data is drawn/refreshed
        table.on('draw.dt', function () {
            cObj("add_bridges_btn").disabled = false;
            cObj("sync_bridges_btn").disabled = false;
            cObj("add_bridges_btn").classList.remove("disabled");
            cObj("sync_bridges_btn").classList.remove("disabled");
            $('[data-toggle="tooltip"]').tooltip(); // Bootstrap 4
            var missing_bridge = document.getElementsByClassName("missing_bridge");
            if(missing_bridge.length > 0){
                cObj("sync_bridges_btn").classList.remove("d-none");
            }else{
                cObj("sync_bridges_btn").classList.add("d-none");
            }

            var bridge_del_btn = document.getElementsByClassName("bridge_del_btn");
            for (let index = 0; index < bridge_del_btn.length; index++) {
                const element = bridge_del_btn[index];
                element.addEventListener("click", function () {
                    showModal("delete_bridge_data_modal");
                    cObj("bridge_name_heading").innerText = this.getAttribute("data-bridge-name");
                    cObj("delete_bridge_url_holder").href = "/Router_Bridge/delete/"+router_data[0].router_id+"/"+this.getAttribute("data-bridge-name");
                })
            }

            var bridge_view_btn  = document.getElementsByClassName("bridge_view_btn");
            for (let index = 0; index < bridge_view_btn.length; index++) {
                const element = bridge_view_btn[index];
                element.addEventListener("click", function () {
                    showModal("edit_bridge_modal");
                    cObj("heading_1").innerText = "Edit Bridges Details";
                    cObj("heading_2").innerText = "Edit Bridges Details";
                    cObj("heading_3").innerHTML = "<b>Edit Bridge Name</b>";
                    cObj("edit_bridge_name").value = this.getAttribute("data-bridge-name");
                    cObj("edit_bridge_name_2").value = this.getAttribute("data-bridge-name");
                    cObj("submit_bridge_details").disabled = true;
                    cObj("submit_bridge_details").classList.add("disabled");
                    if ($.fn.DataTable.isDataTable('#router_table_data_interfaces')) {
                        // just reload data
                        $('#router_table_data_interfaces').DataTable().ajax.reload();
                    } else {
                        let table = $('#router_table_data_interfaces').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "/Router_Bridge_Interfaces/datatable/"+router_data[0].router_id, // route to controller
                                type: "GET",
                                data: function (d) {
                                    d.bridge_name = cObj("edit_bridge_name_2").value;
                                }
                            },
                            order: [[0, 'desc']],
                            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
                            pageLength: 20,  // default rows per page
                            lengthMenu: [5, 10, 20, 50], // available options
                            columns: [
                                { data: 'rownum' },
                                { data: 'interface_name' },
                                { data: 'interface_status' },
                                { data: 'actions', orderable: false, searchable: false }
                            ]
                        });
                        // reinitialize tooltips after table data is drawn/refreshed
                        table.on('draw.dt', function () {
                            $('[data-toggle="tooltip"]').tooltip(); // Bootstrap 4
                            cObj("submit_bridge_details").disabled = false;
                            cObj("submit_bridge_details").classList.remove("disabled");
                        });
                    }
                });
            }
        });
    }
});

cObj("close_edit_bridge_modal_1").onclick = function () {
    hideModal("edit_bridge_modal");
}
cObj("close_edit_bridge_modal_2").onclick = function () {
    hideModal("edit_bridge_modal");
}


cObj("tab3-tab").addEventListener("click", function () {
    cObj("add_profiles_btn").disabled = true;
    cObj("sync_profiles_btn").disabled = true;
    cObj("add_profiles_btn").classList.add("disabled");
    cObj("sync_profiles_btn").classList.add("disabled");
    if ($.fn.DataTable.isDataTable('#router_profile_table')) {
        // just reload data
        $('#router_profile_table').DataTable().ajax.reload();
    } else {
        let table = $('#router_profile_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/Router_Profile/datatable/"+router_data[0].router_id, // route to controller
                type: "GET",
                data: function (d) {
                }
            },
            order: [[0, 'desc']],
            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
            pageLength: 20,  // default rows per page
            lengthMenu: [5, 10, 20, 50], // available options
            columns: [
                { data: 'rownum' },
                { data: 'profile_name' },
                { data: 'profile_status' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
        // reinitialize tooltips after table data is drawn/refreshed
        table.on('draw.dt', function () {
            cObj("add_profiles_btn").disabled = false;
            cObj("sync_profiles_btn").disabled = false;
            cObj("add_profiles_btn").classList.remove("disabled");
            cObj("sync_profiles_btn").classList.remove("disabled");
            $('[data-toggle="tooltip"]').tooltip(); // Bootstrap 4
            var missing_profiles = document.getElementsByClassName("missing_profiles");
            if (missing_profiles.length > 0) {
                cObj("sync_profiles_btn").classList.remove("d-none");
            }else{
                cObj("sync_profiles_btn").classList.add("d-none");
            }

            var profile_edit_btn = document.getElementsByClassName("profile_edit_btn");
            for (let index = 0; index < profile_edit_btn.length; index++) {
                const element = profile_edit_btn[index];
                element.addEventListener("click", function () {
                    // show modal
                    showModal("edit_profile_modal");
                    cObj("edit_profile_name").value = this.getAttribute("data-profile-name");
                    cObj("edit_profile_name_2").value = this.getAttribute("data-profile-name");
                    
                    // edit profile
                    cObj("heading_profile_1").innerText = "Edit Profile Details";
                    cObj("heading_profile_2").innerHTML = 'Edit Profile Details <small id="loading_profile_details" class="text-small text-primary invisible"><i class="fas fa-refresh fa-spin"></i> Loading... </small>';
                    cObj("heading_profile_3").innerHTML = "<b>Edit Profile Name</b>";
                    // send data
                    display_pool_list(this.getAttribute("data-profile-name"));
                });
            }

            var profile_del_btn = document.getElementsByClassName("profile_del_btn");
            for (let index_2 = 0; index_2 < profile_del_btn.length; index_2++) {
                const element = profile_del_btn[index_2];
                element.addEventListener("click", function () {
                    showModal("delete_profile_data_modal");
                    cObj("accept_delete_pool").checked = false;
                    // set the url
                    cObj("delete_profile_url_holder").href = "/Router_Profile/delete/"+router_data[0].router_id+"/"+element.getAttribute("data-profile-name");
                });
            }
        });
    }
});

cObj("accept_delete_pool").onchange = function () {
    if (this.checked) {
        cObj("delete_profile_url_holder").href = cObj("delete_profile_url_holder").href+"?delete_pool=true"
    }else{
        cObj("delete_profile_url_holder").href = cObj("delete_profile_url_holder").href.slice(0, -17);
    }
}

cObj("local_address").onchange = function () {
    if (this.value == "ip_address") {
        this.classList.add("d-none");
        cObj("local_ip_address").classList.remove("d-none");
        cObj("back_to_ippools_list").classList.remove("d-none");
    }
}

cObj("close_delete_profile_data_modal_1").onclick = function () {
    hideModal("delete_profile_data_modal")
}
cObj("close_delete_profile_data_modal_2").onclick = function () {
    hideModal("delete_profile_data_modal")
}

cObj("back_to_ippools_list").onclick = function () {
    cObj("local_address").classList.remove("d-none");
    cObj("local_ip_address").classList.add("d-none");
    cObj("back_to_ippools_list").classList.add("d-none");
    cObj("local_address").children[0].selected = true;
}

cObj("new_pool").onchange = function(){
    if (this.checked) {
        cObj("existing_pool").classList.add("d-none");
        cObj("new_pool_address").classList.remove("d-none");
    }else{
        cObj("existing_pool").classList.remove("d-none");
        cObj("new_pool_address").classList.add("d-none");
    }
}

cObj("sync_bridges_btn").onclick = function () {
    showModal("sync_bridge_modal");
    cObj("selected_bridges").classList.add("d-none");
    if ($.fn.DataTable.isDataTable('#router_table_data_bridge')) {
        // just reload data
        $('#router_table_data_bridge').DataTable().ajax.reload();
    } else {
        let table = $('#router_table_data_bridge').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/Router_Bridges/datatable/"+router_data[0].router_id+"?only_misconfigured=true", // route to controller
                type: "GET",
                data: function (d) {
                }
            },
            order: [[0, 'desc']],
            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
            pageLength: 5,  // default rows per page
            lengthMenu: [5, 10, 20], // available options
            columns: [
                { data: 'rownum' },
                { data: 'bridge_name' },
                { data: 'bridge_status' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
        // reinitialize tooltips after table data is drawn/refreshed
        table.on('draw.dt', function () {
            $('[data-toggle="tooltip"]').tooltip();
            var select_bridge_checkbox  = document.getElementsByClassName("select_bridge_checkbox");
            for (let index = 0; index < select_bridge_checkbox.length; index++) {
                const element = select_bridge_checkbox[index];
                element.addEventListener("change", function () {
                    var select_bridge_checkbox_inside  = document.getElementsByClassName("select_bridge_checkbox");
                    var counter = 0;
                    for (let index = 0; index < select_bridge_checkbox_inside.length; index++) {
                        const element_1 = select_bridge_checkbox_inside[index];
                        counter += element_1.checked ? 1 : 0;
                    }
                    
                    cObj("selected_bridges").innerHTML = ""+counter+" bridge(s) selected";
                    if (counter > 0) {
                        cObj("selected_bridges").classList.remove("d-none");
                    } else {
                        cObj("selected_bridges").classList.add("d-none");
                    }
                });
            }
        });
    }
}

cObj("close_sync_bridge_modal_1").onclick = function () {
    hideModal("sync_bridge_modal");
}

cObj("close_sync_bridge_modal_2").onclick = function () {
    hideModal("sync_bridge_modal");
}

// display the table of the bridges that are not synced
cObj("add_bridges_btn").onclick = function () {
    showModal("edit_bridge_modal");
    cObj("edit_bridge_name").value = "";
    cObj("edit_bridge_name_2").value = "";
    cObj("heading_1").innerText = "Add Bridges Details";
    cObj("heading_2").innerText = "Add Bridges Details";
    cObj("heading_3").innerHTML = "<b>Bridge Name</b>";
    cObj("submit_bridge_details").disabled = true;
    cObj("submit_bridge_details").classList.add("disabled");
    if ($.fn.DataTable.isDataTable('#router_table_data_interfaces')) {
        // just reload data
        $('#router_table_data_interfaces').DataTable().ajax.reload();
    } else {
        let table = $('#router_table_data_interfaces').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/Router_Bridge_Interfaces/datatable/"+router_data[0].router_id, // route to controller
                type: "GET",
                data: function (d) {
                    d.bridge_name = cObj("edit_bridge_name_2").value;
                }
            },
            order: [[0, 'desc']],
            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
            pageLength: 20,  // default rows per page
            lengthMenu: [5, 10, 20, 50], // available options
            columns: [
                { data: 'rownum' },
                { data: 'interface_name' },
                { data: 'interface_status' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
        // reinitialize tooltips after table data is drawn/refreshed
        table.on('draw.dt', function () {
            $('[data-toggle="tooltip"]').tooltip(); // Bootstrap 4
            cObj("submit_bridge_details").disabled = false;
            cObj("submit_bridge_details").classList.remove("disabled");
        });
    }
}

cObj("close_delete_bridge_data_modal_1").onclick = function () {
    hideModal("delete_bridge_data_modal");
}

cObj("close_delete_bridge_data_modal_2").onclick = function () {
    hideModal("delete_bridge_data_modal");
}

cObj("sync_profiles_btn").onclick = function () {
    showModal("sync_profiles_modal");
    if ($.fn.DataTable.isDataTable('#router_table_data_profiles')) {
        // just reload data
        $('#router_table_data_profiles').DataTable().ajax.reload();
    } else {
        let table = $('#router_table_data_profiles').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/Router_Profile/datatable/"+router_data[0].router_id, // route to controller
                type: "GET",
                data: function (d) {
                    d.missing_account = true;
                }
            },
            order: [[0, 'desc']],
            dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
            pageLength: 20,  // default rows per page
            lengthMenu: [5, 10, 20, 50], // available options
            columns: [
                { data: 'rownum' },
                { data: 'profile_name' },
                { data: 'profile_status' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });
        // reinitialize tooltips after table data is drawn/refreshed
        table.on('draw.dt', function () {
            $('[data-toggle="tooltip"]').tooltip(); // Bootstrap 4
            var select_profile_checkbox = document.getElementsByClassName("select_profile_checkbox");
            for (let index = 0; index < select_profile_checkbox.length; index++) {
                const element = select_profile_checkbox[index];
                element.addEventListener("change", function () {
                    var inside = document.getElementsByClassName("select_profile_checkbox");
                    var count = 0;
                    for (let index = 0; index < inside.length; index++) {
                        const element2 = inside[index];
                        count += element2.checked ? 1 : 0;
                    }
                    
                    cObj("selected_profiles").innerHTML = ""+count+" profile(s) selected";
                    if (count > 0) {
                        cObj("selected_profiles").classList.remove("d-none");
                    } else {
                        cObj("selected_profiles").classList.add("d-none");
                    }
                });
            }
        });
    }
}
cObj("close_sync_profile_modal_1").onclick = function () {
    hideModal("sync_profiles_modal");
}
cObj("close_sync_profile_modal_2").onclick = function () {
    hideModal("sync_profiles_modal");
}


cObj("close_edit_profile_modal_1").onclick = function () {
    hideModal("edit_profile_modal");
}
cObj("close_edit_profile_modal_2").onclick = function () {
    hideModal("edit_profile_modal");
}

cObj("add_profiles_btn").onclick = function () {
    cObj("heading_profile_1").innerText = "Add New Profile";
    cObj("heading_profile_2").innerHTML = 'Add New Profile <small id="loading_profile_details" class="text-small text-primary invisible"><i class="fas fa-refresh fa-spin"></i> Loading... </small>';
    cObj("heading_profile_3").innerHTML = "<b>New Profile Name</b>";
    cObj("existing_pool").classList.remove("d-none");
    cObj("new_pool_address").classList.add("d-none");
    cObj("local_address").children[0].selected = true;
    cObj("remote_address").children[0].selected = true;
    cObj("back_to_ippools_list").click();

    showModal("edit_profile_modal");
    display_pool_list("invalid");

    cObj("edit_profile_name").value = ""
    cObj("upload_speed_value").value = ""
    cObj("download_speed_value").value = ""
}

function display_pool_list(profile_name = "null") {
    cObj("save_router_profile").disabled = true;
    cObj("save_router_profile").classList.add("disabled");
    sendDataGet("GET","/Router_Pool/print/"+router_data[0].router_id+"/"+profile_name, cObj("profile_details"), cObj("loading_profile_details"), function (response) {
        if (hasJsonStructure(response)) {
            cObj("back_to_ippools_list").click();
            cObj("save_router_profile").disabled = false;
            cObj("save_router_profile").classList.remove("disabled");
            var poolData = JSON.parse(response);

            // remove any existing children
            cObj("local_address").children[0].selected = true;
            var local_address_children = cObj("local_address").children;
            for (let index_2 = local_address_children.length-1; index_2 > 1; index_2--) {
                const element_2 = local_address_children[index_2];
                if (index_2 > 1) {
                    cObj("local_address").removeChild(element_2);
                }
            }

            // add new children
            for (let index_3 = 0; index_3 < poolData.bridge_port.length; index_3++) {
                const element_3 = poolData.bridge_port[index_3];
                let opt = document.createElement("option");
                opt.value = element_3.name;
                opt.textContent = element_3.name+" ("+element_3.ranges+")";
                cObj("local_address").appendChild(opt);
            }


            // remove any existing children
            cObj("remote_address").children[0].selected = true;
            var local_address_children = cObj("remote_address").children;
            for (let index_2 = local_address_children.length-1; index_2 > 1; index_2--) {
                const element_2 = local_address_children[index_2];
                if (index_2 > 1) {
                    cObj("remote_address").removeChild(element_2);
                }
            }

            // add new children
            for (let index_3 = 0; index_3 < poolData.bridge_port.length; index_3++) {
                const element_3 = poolData.bridge_port[index_3];
                let opt = document.createElement("option");
                opt.value = element_3.name;
                opt.textContent = element_3.name+" ("+element_3.ranges+")";
                cObj("remote_address").appendChild(opt);
            }

            // speed
            if (poolData.profile_details.length > 0) {
                if(poolData.profile_details[0]['rate-limit'] != undefined){
                    var rate = poolData.profile_details[0]['rate-limit'];
                    var upload_download = rate.split("/");

                    // Extract speeds and units
                    var upload_speed = upload_download[0].slice(0, -1);
                    var upload_unit  = upload_download[0].slice(-1);

                    var download_speed = upload_download[1].slice(0, -1);
                    var download_unit  = upload_download[1].slice(-1);

                    cObj("upload_speed_value").value = upload_speed;
                    cObj("download_speed_value").value = download_speed;

                    // Select UPLOAD unit
                    var upload_speed_unit = cObj("upload_speed_unit").children;
                    for (let i = 0; i < upload_speed_unit.length; i++) {
                        if (upload_speed_unit[i].value == upload_unit) {
                            upload_speed_unit[i].selected = true;
                            break;
                        }
                    }

                    // Select DOWNLOAD unit
                    var download_speed_unit = cObj("download_speed_unit").children;
                    for (let i = 0; i < download_speed_unit.length; i++) {
                        if (download_speed_unit[i].value == download_unit) {
                            download_speed_unit[i].selected = true;
                            break;
                        }
                    }
                }

                // set the pool selected
                var local_address_pool = poolData.profile_details[0]['local-address'] ? poolData.profile_details[0]['local-address'] : "";
                var remote_address_pool = poolData.profile_details[0]['remote-address'] ? poolData.profile_details[0]['remote-address'] : "";

                // assigned the dropdowns the value
                for (let index_6 = 0; index_6 < cObj("local_address").children.length; index_6++) {
                    const element_6 = cObj("local_address").children[index_6];
                    if (element_6.value == local_address_pool) {
                        element_6.selected = true;
                    }
                }

                // assigned the dropdowns the value
                for (let index_7 = 0; index_7 < cObj("remote_address").children.length; index_7++) {
                    const element_7 = cObj("remote_address").children[index_7];
                    if (element_7.value == remote_address_pool) {
                        element_7.selected = true;
                    }
                }
                if (isValidIPv4(local_address_pool)) {
                    // display the ip address area
                    cObj("local_address").classList.add("d-none");
                    cObj("local_ip_address").classList.remove("d-none");
                    cObj("back_to_ippools_list").classList.remove("d-none");
                    cObj("local_ip_address").value = local_address_pool;
                    cObj("local_address").children[1].selected = true;
                }
            }
        }
    });
}

// Send data with get
function sendDataGet(method, file, object1, object2, callback = null) {
    //make the loading window show
    object2.classList.remove("invisible");
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            object1.innerHTML = this.responseText;
            object2.classList.add("invisible");

            // ✅ Run the callback after updating DOM
            if (typeof callback === "function") {
                callback(this.responseText);
            }
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

function isValidIPv4(ip) {
    const regex = /^(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)){3}$/;
    return regex.test(ip);
}

function validateForm() {
    var error = checkBlank("edit_profile_name");
    if(cObj("new_pool").checked){
        error+=checkBlank("new_pool_name");
        error+=checkBlank("pool_range_start");
        error+=checkBlank("pool_range_end");
    }else{
        error += checkBlank("local_address");
        if(cObj("local_address").value == ""){
            error += checkBlank("local_ip_address");
        }
        error += checkBlank("remote_address");
    }
    error += checkBlank("upload_speed_value");
    error += checkBlank("upload_speed_unit");
    error += checkBlank("download_speed_value");
    error += checkBlank("download_speed_unit");
    // check error
    if(error == 0){
        if(cObj("new_pool").checked){
            // check the validity of the ipaddresses
            error += isValidIPv4(cObj("pool_range_start").value) ? 0 : 1;
            if(isValidIPv4(cObj("pool_range_start").value)){
                cObj("pool_range_start").classList.remove("border");
                cObj("pool_range_start").classList.remove("border-danger");
            }else{
                cObj("pool_range_start").classList.add("border");
                cObj("pool_range_start").classList.add("border-danger");
            }

            // is valid ipv4 address
            error += isValidIPv4(cObj("pool_range_end").value) ? 0 : 1;
            if(isValidIPv4(cObj("pool_range_end").value)){
                cObj("pool_range_end").classList.remove("border");
                cObj("pool_range_end").classList.remove("border-danger");
            }else{
                cObj("pool_range_end").classList.add("border");
                cObj("pool_range_end").classList.add("border-danger");
            }
            if(error > 0){
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }
    return false;
}

function checkBlank(id) {
    let err = 0;
    if (cObj(id).value.trim().length > 0) {
        if (cObj(id).value.trim() == "N/A") {
        //   redBorder(cObj(id));
        cObj(id).classList.add("border");
        cObj(id).classList.add("border-danger");
        err++;
        } else {
        //   grayBorder(cObj(id));
        cObj(id).classList.remove("border");
        cObj(id).classList.remove("border-danger");
        }
    } else {
        cObj(id).classList.add("border");
        cObj(id).classList.add("border-danger");
        // redBorder(cObj(id));
        err++;
    }
    return err;
}