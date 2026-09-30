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

    var audienceRow = document.getElementById("audience_filter_row");
    if (audienceRow) {
        if (this.value == "filtered") {
            audienceRow.classList.remove("d-none");
            var panel = document.getElementById("audience_builder");
            if (panel && panel.refreshAudienceCount) panel.refreshAudienceCount();
        } else {
            audienceRow.classList.add("d-none");
        }
    }
}
