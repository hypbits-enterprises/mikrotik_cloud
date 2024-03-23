function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

window.onload = function () {
    account_options();
    account_options_2();
    checkChecked();
}

cObj("delete_user").onclick = function () {
    cObj("prompt_del_window").classList.toggle("d-none");
}

function checkChecked() {
    var all_readonly = document.getElementsByClassName("all_readonly");
    var total = all_readonly.length;
    var checked = 0;
    for (let index = 0; index < all_readonly.length; index++) {
        const element = all_readonly[index];
        if (element.checked == true) {
            checked++;
        }
    }
    if (checked > 0){
        if (checked == total) {
            cObj("all_readonly").checked = true;
            cObj("all_readonly").indeterminate  = false;
        }else{
            cObj("all_readonly").checked = false;
            cObj("all_readonly").indeterminate  = true;
        }
    }else{
        cObj("all_readonly").checked = false;
        cObj("all_readonly").indeterminate  = false;
    }


    var all_view = document.getElementsByClassName("all_view");
    var total = all_view.length;
    var checked = 0;
    for (let index = 0; index < all_view.length; index++) {
        const element = all_view[index];
        if (element.checked == true) {
            checked++;
        }
    }
    if (checked > 0) {
        if (checked == total) {
            cObj("all_view").checked = true;
            cObj("all_view").indeterminate  = false;
        }else{
            cObj("all_view").checked = false;
            cObj("all_view").indeterminate  = true;
        }
    }else{
        cObj("all_view").checked = false;
        cObj("all_view").indeterminate  = false;
    }
}

cObj("my_clients_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_clients_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Clients",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Clients") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("my_clients_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Clients",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("my_clients_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_clients_option_view").checked == true ? true : false;
        var your_data = {option:"My Clients",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Clients") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("my_clients_option_view").checked == true ? true : false;
        var your_data = {option:"My Clients",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("transactions_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("transactions_option_readonly").checked == true ? true : false;
        var your_data = {option:"Transactions",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Transactions") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("transactions_option_readonly").checked == true ? true : false;
        var your_data = {option:"Transactions",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("transactions_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("transactions_option_view").checked == true ? true : false;
        var your_data = {option:"Transactions",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Transactions") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("transactions_option_view").checked == true ? true : false;
        var your_data = {option:"Transactions",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("expenses_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("expenses_option_readonly").checked == true ? true : false;
        var your_data = {option:"Expenses",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Expenses") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("expenses_option_readonly").checked == true ? true : false;
        var your_data = {option:"Expenses",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("expenses_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("expenses_option_view").checked == true ? true : false;
        var your_data = {option:"Expenses",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Expenses") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("expenses_option_view").checked == true ? true : false;
        var your_data = {option:"Expenses",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("my_routers_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_routers_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Routers",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Routers") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("my_routers_option_readonly").checked == true ? true : false;
        var your_data = {option:"My Routers",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("my_routers_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_routers_option_view").checked == true ? true : false;
        var your_data = {option:"My Routers",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "My Routers") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("my_routers_option_view").checked == true ? true : false;
        var your_data = {option:"My Routers",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("sms_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("sms_option_readonly").checked == true ? true : false;
        var your_data = {option:"SMS",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "SMS") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("sms_option_readonly").checked == true ? true : false;
        var your_data = {option:"SMS",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("sms_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("sms_option_view").checked == true ? true : false;
        var your_data = {option:"SMS",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "SMS") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("sms_option_view").checked == true ? true : false;
        var your_data = {option:"SMS",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("account_profile_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("account_profile_option_readonly").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Account and Profile") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var readonly = cObj("account_profile_option_readonly").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("account_profile_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("account_profile_option_view").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Account and Profile") {
                privileged[index] = your_data;
                present=1;
            }
        }
        if (present == 0) {
            privileged.push(your_data);
        }
        cObj("privileged").value = JSON.stringify(privileged);
    }else{
        var privileges = [];
        var view = cObj("account_profile_option_view").checked == true ? true : false;
        var your_data = {option:"Account and Profile",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    account_options_2();
    checkChecked();
}

cObj("accounts_option_view").onchange = function () {
    cObj("transactions_option_view").checked = this.checked;
    cObj("expenses_option_view").checked = this.checked;

    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            if (element.option == "Transactions" || element.option == "Expenses") {
                element.view = this.checked;
            }
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    account_options();
    account_options_2();
    checkChecked();
}
cObj("all_view").onchange = function () {
    var all_view = document.getElementsByClassName("all_view");
    for (let index = 0; index < all_view.length; index++) {
        const elem = all_view[index];
        elem.checked = this.checked;
    }
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            element.view = this.checked;
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    account_options();
    account_options_2();
    checkChecked();
    
}

cObj("all_readonly").onchange = function () {
    var all_readonly = document.getElementsByClassName("all_readonly");
    for (let index = 0; index < all_readonly.length; index++) {
        const element = all_readonly[index];
        element.checked = this.checked;
    }


    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            element.readonly = this.checked;
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }

    account_options();
    account_options_2();
    checkChecked();
}

cObj("accounts_option_readonly").onchange = function () {
    var account_optioned = document.getElementsByClassName("account_options_2");
    for (let index = 0; index < account_optioned.length; index++) {
        const element = account_optioned[index];
        element.checked = this.checked;
    }
    
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);
        privileged.forEach(element => {
            if (element.option == "Transactions" || element.option == "Expenses") {
                element.readonly = this.checked;
            }
        });
        cObj("privileged").value = JSON.stringify(privileged);
    }
    account_options();
    account_options_2();
    checkChecked();
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

function account_options() {
    var account_options = document.getElementsByClassName("account_options");
    var count = account_options.length;
    var checked = 0;
    for (let index = 0; index < account_options.length; index++) {
        const element = account_options[index];
        if (element.checked) {
            checked++;
        }
    }

    if (checked > 0) {
        if (checked == count) {
            cObj("accounts_option_view").checked = true;
            cObj("accounts_option_view").indeterminate = false;
        }else{
            cObj("accounts_option_view").checked = false;
            cObj("accounts_option_view").indeterminate = true;
        }
    }else{
        cObj("accounts_option_view").checked = false;
        cObj("accounts_option_view").indeterminate = false;
    }
}

function account_options_2() {
    var account_options = document.getElementsByClassName("account_options_2");
    var count = account_options.length;
    var checked = 0;
    for (let index = 0; index < account_options.length; index++) {
        const element = account_options[index];
        if (element.checked) {
            checked++;
        }
    }
    if (checked > 0) {
        if (checked == count) {
            cObj("accounts_option_readonly").checked = true;
            cObj("accounts_option_readonly").indeterminate = false;
        }else{
            cObj("accounts_option_readonly").checked = false;
            cObj("accounts_option_readonly").indeterminate = true;
        }
    }else{
        cObj("accounts_option_readonly").checked = false;
        cObj("accounts_option_readonly").indeterminate = false;
    }
}