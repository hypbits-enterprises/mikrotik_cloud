var select_recipient = document.getElementById("select_recipient");
select_recipient.onchange = function () {
    // change the value of the written phone
    if (this.value == "1") {
        // get the value
        var number_lists = document.getElementById("number_lists");
        number_lists.classList.remove("d-none");
    }else{
        // get the value
        var number_lists = document.getElementById("number_lists");
        number_lists.classList.add("d-none");
    }
    if (this.value == "5") {
        // get the value
        var number_lists = document.getElementById("select_clients");
        number_lists.classList.remove("d-none");
    }else{
        // get the value
        var number_lists = document.getElementById("select_clients");
        number_lists.classList.add("d-none");
    }
    
}
