// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get the data from the database
var student_data = data;
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

async function sendDataGetAsync(method, url, object1 = null, object2 = null) {
    if (object2) object2.classList.remove("invisible");

    try {
        const response = await fetch(url, { method });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const text = await response.text(); // You can change this to response.json() if the response is JSON

        if (object1) object1.innerHTML = "";
        if (object2) object2.classList.add("invisible");

        // Try converting to array if it's JSON
        try {
            return JSON.parse(text); // return as array/object if response is JSON
        } catch (e) {
            return []; // fallback to empty array if not JSON
        }

    } catch (error) {
        if (object2) object2.classList.add("invisible");
        if (object1) object1.innerHTML = `<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>`;
        console.error("Fetch error:", error);
        return [];
    }
}


var rowsColStudents = [];
var rowsNCols_original = [];
var pagecountTransaction = 0; //this are the number of pages for transaction
var pagecounttrans = 1; //the current page the user is
var startpage = 0; // this is where we start counting the page number

// load the user data
window.onload = function () {
    // clients_table
    cObj("clients_table").classList.remove("d-none");
    cObj("loading_clients_data").classList.add("d-none");
    let table = $('#clients_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/Clients/datatable", // route to controller
            type: "GET",
            data: function (d) {
                d.router_name = $('#select_router').val(); // send dropdown value
                let client_status = $('#client_status').val();
                if (client_status == "inactive") {
                    d.client_status = "0"
                }
                if (client_status == "active") {
                    d.client_status = "1"
                }
                if (client_status == "pppoe") {
                    d.assignment = "pppoe"
                }
                if (client_status == "static") {
                    d.assignment = "static"
                }
            }
        },
        order: [[0, 'desc']],
        dom: '<"bottom"l>t<"bottom"ip>', // hide search, put length menu bottom-left
        pageLength: 50,  // default rows per page
        lengthMenu: [10, 25, 50, 100], // available options
        columns: [
            { data: 'rownum' },
            { data: 'client_name' },
            { data: 'client_account' },
            { data: 'client_address' },
            { data: 'next_expiration_date' },
            { data: 'client_default_gw' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // Hook up external search
    $('#searchkey').on('keyup', function () {
        table.search(this.value).draw();
    });

    // reload table when dropdown changes
    $('#select_router').change(function () {
        table.ajax.reload();
    });

    // reload table when dropdown changes
    $('#client_status').change(function () {
        table.ajax.reload();
    });

    // ✅ Reinitialize tooltips after table redraw
    table.on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
        checkedUnchecked();
    });

    // plot graph
    plotGraph(added_last_week);
}

function checkBlank(object_id) {
    if (cObj(object_id).value.trim().length > 0) {
        cObj(object_id).classList.add("border");
        cObj(object_id).classList.add("border-secondary");
        cObj(object_id).classList.remove("border-danger");
        return 0;
    }else{
        cObj(object_id).classList.add("border");
        cObj(object_id).classList.add("border-danger");
        cObj(object_id).classList.remove("border-secondary");
        return 1;
    }
}

var myChart;
function plotGraph(client_data) {
    if (myChart != null) {
        myChart.destroy();
    }
    var data = [client_data];
    console.log(data);
    var show_x_axis = true;
    var show_y_axis = true;
    
    var ctx = cObj("onboarding_canvas");
    var type = "line" //line, pie, bar, doughnut, polarArea, radar;
    var backgroundColor = ['rgb(48, 182, 215)'];
    if (type == "pie") {
        backgroundColor = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            var rand_red = generateRandomNumber(100,255);
            var rand_green = generateRandomNumber(100,255);
            var rand_blue = generateRandomNumber(100,255);
            var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
            backgroundColor.push(rand_color);
        }
    }

    // get the labels
    var labels = [];
    var from = "";
    var to = "";
    for (let index = 0; index < data[0].length; index++) {
        const element = data[0][index];
        if (index == 0) {
            from = "between ("+element.x;
        }
        if (index == data[0].length-1) {
            to = element.x;
        }
        labels.push(element.x);
    }

    var title = "Clients registered in the last 7 days";
    // chart data
    var chart_data = data[0];
    if (type == "pie") {
        chart_data = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            chart_data.push(element.y);
        }
    }

    myChart = new Chart(ctx, {
        type: type,
        data: {
            labels:labels,
            datasets: [{
                tension: 0.4,
                label: 'Client(s) Registered',
                data: chart_data,
                borderWidth: 1,
                font: {
                    size: 14
                },
                backgroundColor: 'rgba(55, 61, 125,0.3)',
                borderColor:'rgb(55, 61, 125)',
                fill: true
            }],
            hoverOffset: 4
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks:{
                        stepSize: 1
                    },
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_y_axis
                },
                x:{
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_x_axis
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 14
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    font: {
                        size: 14
                    }
                }
            }
        }
    });
}

function checkedUnchecked() {
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        // check if values of the selected client is included if included check the boxes
        for (let index = 0; index < hold_user_id_data.length; index++) {
            const element = hold_user_id_data[index];
            if (cObj("actions_id_"+element) != null) {
                cObj("actions_id_"+element).checked = true;
            }
        }
    }
    // check box event listener
    var actions_id = document.getElementsByClassName("actions_id");
    for (let index = 0; index < actions_id.length; index++) {
        const element = actions_id[index];
        element.addEventListener("change",addSelectedClients);
    }


}

var minium_client_lists = "";
function getNames() {
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        var client_lists = "";
        // check if values of the selected client is included if included check the boxes
        for (let index = (hold_user_id_data.length - 1); index >= 0; index--) {
            const element = hold_user_id_data[index];
            var client_name = (hold_user_id_data.length - index) + ". Null {"+element+"}";
            for (let inds = 0; inds < student_data.length; inds++) {
                const elems = student_data[inds];
                if (elems['client_account'] == element) {
                    client_name = elems['client_name']+" {"+element+"}";
                    break;
                }
            }
            client_lists+="<div class='badge border-info primary badge-border'>"+(hold_user_id_data.length - index) +". "+client_name+"</div>";
            if ((hold_user_id_data.length - index) == 20) {
                break;
            }
        }
        // client_lists = client_lists.substring(0,client_lists.length-3);
        minium_client_lists = client_lists;
        // console.log(minium_client_lists);

        if (hold_user_id_data.length > 20) {
            cObj("clients_selected").innerHTML = client_lists+".. <b class='text-primary' style='cursor:pointer;' id='show_more'>Show More</b>";

            // get the other selected users
            var client_lists = "";
            // check if values of the selected client is included if included check the boxes
            for (let index = (hold_user_id_data.length - 1); index >= 0; index--) {
                const element = hold_user_id_data[index];
                var client_name = (hold_user_id_data.length - index) + ". Null {"+element+"}";
                for (let inds = 0; inds < student_data.length; inds++) {
                    const elems = student_data[inds];
                    if (elems['client_account'] == element) {
                        client_name = elems['client_name']+" {"+element+"}";
                        break;
                    }
                }
                client_lists+="<div class='badge border-info primary badge-border'>"+(hold_user_id_data.length - index) +". "+client_name+""+"</div>";
            }
            // client_lists = client_lists.substring(0,client_lists.length-3);
            cObj("clients_list_selected").value = client_lists;

            // add the eventlistener for the show more button
            cObj("show_more").addEventListener("click",showMoreFunc);
        }else{
            cObj("clients_selected").innerHTML = client_lists;
        }

        if (hold_user_id_data.length == 0) {
            cObj("action_for_selected_window").classList.add("hide");
        }else{
            cObj("action_for_selected_window").classList.remove("hide");
        }
        cObj("delete_number_clients").innerText = hold_user_id_data.length;

        // set the value for the second data holder
        cObj("hold_user_id_data_2").value = cObj("hold_user_id_data").value;

        if (hold_user_id_data.length == student_data.length) {
            cObj("select_all_clients").indeterminate = false;
            cObj("select_all_clients").checked = true;
        }else{
            cObj("select_all_clients").indeterminate = true;
            cObj("select_all_clients").checked = false;
        }
    }
}

cObj("delete_clients_id").onclick = function () {
    cObj("delete_clients_window").classList.toggle("hide");
}
cObj("no_dont_delete_selected").onclick = function () {
    cObj("delete_clients_window").classList.add("hide");
}

function showMoreFunc() {
    cObj("clients_selected").innerHTML = cObj("clients_list_selected").value+".. <b class='text-primary' style='cursor:pointer;' id='show_less'>Show Less</b>";
    cObj("show_less").addEventListener("click",showLessFunc);
}

function showLessFunc() {
    cObj("clients_selected").innerHTML = minium_client_lists+".. <b class='text-primary' style='cursor:pointer;' id='show_more'>Show More</b>";
    cObj("show_more").addEventListener("click",showMoreFunc);
}

cObj("select_all_clients").onchange = function () {
    if (this.checked) {
        var new_data = [];
        for (let inds = 0; inds < student_data.length; inds++) {
            const elems = student_data[inds];
            new_data.push(elems['client_account']);
        }
    
        cObj("hold_user_id_data").value = JSON.stringify(new_data);
        // uncheck 
        var actions_id = document.getElementsByClassName("actions_id");
        for (let index = 0; index < actions_id.length; index++) {
            const element = actions_id[index];
            element.checked = true;
        }
    
    }else{
        cObj("hold_user_id_data").value = "[]";
        // uncheck 
        var actions_id = document.getElementsByClassName("actions_id");
        for (let index = 0; index < actions_id.length; index++) {
            const element = actions_id[index];
            element.checked = false;
        }
    }

    // names
    getNames();

    // check the unchecked
    var hold_user_id_data = cObj("hold_user_id_data").value;
    if (hasJsonStructure(hold_user_id_data)) {
        hold_user_id_data = JSON.parse(hold_user_id_data);

        // check if values of the selected client is included if included check the boxes
        for (let index = 0; index < hold_user_id_data.length; index++) {
            const element = hold_user_id_data[index];
            if (cObj("actions_id_"+element) != null) {
                cObj("actions_id_"+element).checked = true;
            }
        }
    }

    // count selected clients
    var clients_selected_count = cObj("hold_user_id_data").value;
    clients_selected_count = JSON.parse(clients_selected_count);
    cObj("client_select_counts").innerText = clients_selected_count.length+" Client(s) Selected";

}

function addSelectedClients() {
    var this_ids = this.id.substr(11);
    if (this.checked) {
        var hold_user_id_data = cObj("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);

            // is present
            if (!isPresent(hold_user_id_data,cObj("actions_value_"+this_ids).value)) {
                hold_user_id_data.push(cObj("actions_value_"+this_ids).value);
            }
            cObj("hold_user_id_data").value = JSON.stringify(hold_user_id_data);
        }else{
            cObj("hold_user_id_data").value = JSON.stringify([cObj("actions_value_"+this_ids).value]);
        }
    }else{
        var hold_user_id_data = cObj("hold_user_id_data").value;
        if (hasJsonStructure(hold_user_id_data)) {
            hold_user_id_data = JSON.parse(hold_user_id_data);
            var new_data = [];
            for (let index = 0; index < hold_user_id_data.length; index++) {
                const element = hold_user_id_data[index];
                if (element == cObj("actions_value_"+this_ids).value) {
                    continue;
                }
                new_data.push(element);
            }
            cObj("hold_user_id_data").value = JSON.stringify(new_data);
        }
    }

    var clients_selected_count = cObj("hold_user_id_data").value;
    clients_selected_count = JSON.parse(clients_selected_count);
    cObj("client_select_counts").innerText = clients_selected_count.length+" Client(s) Selected";

    // display all the clients that have been selected
    getNames();
}

function isPresent(array,object) {
    for (let index = 0; index < array.length; index++) {
        const element = array[index];
        if (element == object) {
            return true;
        }
    }
    return false;
}

function ucwords(string) {
    var cases = string.toLowerCase().split(" ");
    // split the string to get the number of words present
    var final_word = "";
    for (let index = 0; index < cases.length; index++) {
        const element = cases[index];
        final_word += element.substr(0, 1).toUpperCase() + element.substr(1) + " ";
    }
    return final_word.trim();
}
function ucword(string) {
    if (string != null) {
        var cases = string.toLowerCase();
        // split the string to get the number of words present
        var final_word = cases.substr(0, 1).toUpperCase() + cases.substr(1);
        return final_word.trim();
    }
    return "";
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
// fornat the date we are given
function setDate(string) {
    string = string.toString();
    var year = string.substr(0, 4);
    var month = string.substr(4, 2) - 1;
    var day = string.substr(6, 2);
    var hour = string.substr(8, 2);
    var min = string.substr(10, 2);
    var sec = string.substr(12, 2);
    const d = new Date(year, month, day, hour, min, sec);
    var hours = d.getHours() > 9 ? d.getHours() : "0" + d.getHours();
    var minutes = d.getMinutes() > 9 ? d.getMinutes() : "0" + d.getMinutes();
    var seconds = d.getSeconds() > 9 ? d.getSeconds() : "0" + d.getSeconds();
    return getDays(d.getDay()) + " " + d.getDate() + " " + getMonths(d.getMonth()) + " " + d.getFullYear() + " @ " + hours + ":" + minutes + ":" + seconds;
}
function getMonths(month) {
    if (month == 0) {
        return "Jan";
    } else if (month == 1) {
        return "Feb";
    } else if (month == 2) {
        return "Mar";
    } else if (month == 3) {
        return "Apr";
    } else if (month == 4) {
        return "May";
    } else if (month == 5) {
        return "Jun";
    } else if (month == 6) {
        return "Jul";
    } else if (month == 7) {
        return "Aug";
    } else if (month == 8) {
        return "Sep";
    } else if (month == 9) {
        return "Oct";
    } else if (month == 10) {
        return "Nov";
    } else if (month == 11) {
        return "Dec";
    }
}
function getDays(days) {
    if (days == 0) {
        return "Sun";
    } else if (days == 1) {
        return "Mon";
    } else if (days == 2) {
        return "Tue";
    } else if (days == 3) {
        return "Wed";
    } else if (days == 4) {
        return "Thur";
    } else if (days == 5) {
        return "Fri";
    } else if (days == 6) {
        return "Sat";
    }
}


cObj("client_reports_btn").onclick = function () {
    cObj("show_generate_reports_window").classList.toggle("hide");
    if (cObj("show_generate_reports_window").classList.contains("hide")) {
        var datapass = "?showRouters=true";
    }
}

cObj("client_report_option").onchange = function () {
    var option = this.value;
    if (option == "client registration") {
        cObj("date_option").classList.remove("hide");
        cObj("select_router_window").classList.remove("hide");
        cObj("client_status_opt").classList.remove("hide");
    } else if (option == "client information" || option == "client router information") {
        cObj("date_option").classList.add("hide");
        cObj("select_date_win").classList.add("hide");
        cObj("select_router_window").classList.remove("hide");
        cObj("client_status_opt").classList.remove("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
        cObj("default_reg_date").selected = true;
    }
}
cObj("client_registration_date_option").onchange = function () {
    var option = this.value;
    if (option == "select date") {
        cObj("select_date_win").classList.remove("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    } else if (option == "between dates") {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.remove("hide");
        cObj("select_to_date_win").classList.remove("hide");
    } else {
        cObj("select_date_win").classList.add("hide");
        cObj("select_from_date_win").classList.add("hide");
        cObj("select_to_date_win").classList.add("hide");
    }
}

cObj("export_client_data_btn").onclick = function () {
    cObj("export_client_data").classList.remove("hide");
    cObj("export_client_data").classList.add("show");
    cObj("export_client_data").classList.add("showBlock");
}

cObj("close_export_client_data_1").onclick = function () {
    cObj("export_client_data").classList.add("hide");
    cObj("export_client_data").classList.remove("show");
    cObj("export_client_data").classList.remove("showBlock");
}

cObj("close_export_client_data_2").onclick = function () {
    cObj("export_client_data").classList.add("hide");
    cObj("export_client_data").classList.remove("show");
    cObj("export_client_data").classList.remove("showBlock");
}

// initiate autocomplete for the search input
// autocomplete(cObj("searchkey"));
function autocomplete(inp) {
    let arr,arr2,arr3,arr4,arr5,arr6 = [];
    /*the autocomplete function takes an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", async function(e) {
        var a, b, i, val = this.value;
        var search_results = await searchClients(this.value);
        arr = search_results[0];
        arr2 = search_results[1];
        arr3 = search_results[2];
        arr4 = search_results[3];
        arr5 = search_results[4];
        arr6 = search_results[5];
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        a.style.maxHeight = "250px";
        a.style.overflowY = "auto";
        a.style.overflowX = "hidden";
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        var counter = 1;
        for (i = 0; i < arr.length; i++) {
            if (counter > 10) {
                break;
            }
            
            /*check if the item starts with the same letters as the text field value:*/
            if ((arr2[i]+"").toUpperCase().includes(val.toUpperCase()) ||
                (arr3[i]+"").toUpperCase().includes(val.toUpperCase()) ||
                (arr4[i]+"").toUpperCase().includes(val.toUpperCase()) ||
                (arr5[i]+"").toUpperCase().includes(val.toUpperCase()) ||
                (arr6[i]+"").toUpperCase().includes(val.toUpperCase())
            ) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                var display_text = arr2[i] + " (" + arr3[i] + ") - " + arr4[i]+" - " + arr5[i] +" | "+arr6[i];
                b.innerHTML = (counter)+".) "+highlightNeedleInHaystack(display_text, val);
                // b.innerHTML += display_text.substring(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                    /*insert the value for the autocomplete text field:*/
                    window.location.href = "/Clients/View/"+this.getElementsByTagName("input")[0].value;
                    // inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
                counter++;
            }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function(e) {
        closeAllLists(e.target);
    });
}

function highlightNeedleInHaystack(haystack, needle) {
    if (!needle) return haystack;

    const escapedNeedle = needle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // Escape regex special chars
    const regex = new RegExp(escapedNeedle, 'gi'); // Case-insensitive global match

    return haystack.replace(regex, (match) => `<b class='text-danger'>${match}</b>`);
}

async function searchClients(keyword){
    var client_list = await sendDataGetAsync("GET", "/Clients/search?keyword=" + keyword, cObj("clients_search_loader"), cObj("clients_search_loader"));
    let data = [];
    data[0] = [];
    data[1] = [];
    data[2] = [];
    data[3] = [];
    data[4] = [];
    data[5] = [];
    for (let index = 0; index < client_list.length; index++) {
        const element = client_list[index];
        data[0].push(element.client_id);
        data[1].push(element.client_name);
        data[2].push(element.client_account);
        data[3].push(element.clients_contacts);
        data[4].push(element.client_address);
        data[5].push(element.client_network_address);
    }
    return data;
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
                callback();
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