function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

window.onload = function () {
    account_options();
    client_options();
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

cObj("clients_issues_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("clients_issues_readonly").checked;
        var your_data = {option:"Clients Issues",view:this.checked,readonly:readonly};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Clients Issues") {
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
        var readonly = cObj("clients_issues_readonly").checked;
        var your_data = {option:"Clients Issues",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("clients_issues_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("clients_issues_view").checked;
        var your_data = {option:"Clients Issues",view:view,readonly:this.checked};
        for (let index = 0; index < privileged.length; index++) {
            const element = privileged[index];
            if (element.option == "Clients Issues") {
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
        var view = cObj("clients_issues_view").checked;
        var your_data = {option:"Clients Issues",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("my_clients_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_clients_option_readonly").checked;
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
        var readonly = cObj("my_clients_option_readonly").checked;
        var your_data = {option:"My Clients",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("my_clients_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_clients_option_view").checked;
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
        var view = cObj("my_clients_option_view").checked;
        var your_data = {option:"My Clients",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("transactions_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("transactions_option_readonly").checked;
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
        var readonly = cObj("transactions_option_readonly").checked;
        var your_data = {option:"Transactions",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("transactions_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("transactions_option_view").checked;
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
        var view = cObj("transactions_option_view").checked;
        var your_data = {option:"Transactions",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("expenses_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("expenses_option_readonly").checked;
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
        var readonly = cObj("expenses_option_readonly").checked;
        var your_data = {option:"Expenses",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("expenses_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("expenses_option_view").checked;
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
        var view = cObj("expenses_option_view").checked;
        var your_data = {option:"Expenses",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("my_routers_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("my_routers_option_readonly").checked;
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
        var readonly = cObj("my_routers_option_readonly").checked;
        var your_data = {option:"My Routers",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("my_routers_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("my_routers_option_view").checked;
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
        var view = cObj("my_routers_option_view").checked;
        var your_data = {option:"My Routers",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("sms_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("sms_option_readonly").checked;
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
        var readonly = cObj("sms_option_readonly").checked;
        var your_data = {option:"SMS",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("sms_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("sms_option_view").checked;
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
        var view = cObj("sms_option_view").checked;
        var your_data = {option:"SMS",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("account_profile_option_view").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var readonly = cObj("account_profile_option_readonly").checked;
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
        var readonly = cObj("account_profile_option_readonly").checked;
        var your_data = {option:"Account and Profile",view:this.checked,readonly:readonly};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("account_profile_option_readonly").onchange = function () {
    var privileged = cObj("privileged").value;
    if (hasJsonStructure(privileged)) {
        privileged = JSON.parse(privileged);

        // loop through the privileged to add the change or change if present
        var present = 0;
        var view = cObj("account_profile_option_view").checked;
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
        var view = cObj("account_profile_option_view").checked;
        var your_data = {option:"Account and Profile",view:view,readonly:this.checked};
        privileges.push(your_data);
        cObj("privileged").value = JSON.stringify(privileges);
    }
    account_options();
    client_options();
    checkChecked();
}

cObj("clients_option_view").onchange = function () {
    cObj("my_clients_option_view").checked = this.checked;
    cObj("clients_issues_view").checked = this.checked;

    cObj("my_clients_option_view").dispatchEvent(new Event("change"));
    cObj("clients_issues_view").dispatchEvent(new Event("change"));

    account_options();
    client_options();
    checkChecked();
}

cObj("clients_option_readonly").onchange = function () {
    cObj("my_clients_option_readonly").checked = this.checked;
    cObj("clients_issues_readonly").checked = this.checked;

    cObj("my_clients_option_readonly").dispatchEvent(new Event("change"));
    cObj("clients_issues_readonly").dispatchEvent(new Event("change"));

    account_options();
    client_options();
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
    client_options();
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
    client_options();
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
    client_options();
    checkChecked();
}

function validateForm() {
    var all_view = document.getElementsByClassName("all_view");
    for (let index = 0; index < all_view.length; index++) {
        all_view[index].dispatchEvent(new Event("change"));
    }

    var all_readonly = document.getElementsByClassName("all_readonly");
    for (let index = 0; index < all_readonly.length; index++) {
        all_readonly[index].dispatchEvent(new Event("change"));
    }

    return true;
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
    client_options();
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

function client_options() {
    var client_options = document.getElementsByClassName("client_options");
    var count = client_options.length;
    var checked = 0;
    for (let index = 0; index < client_options.length; index++) {
        const element = client_options[index];
        if (element.checked) {
            checked++;
        }
    }

    if (checked > 0) {
        if (checked == count) {
            cObj("clients_option_view").checked = true;
            cObj("clients_option_view").indeterminate = false;
        }else{
            cObj("clients_option_view").checked = false;
            cObj("clients_option_view").indeterminate = true;
        }
    }else{
        cObj("clients_option_view").checked = false;
        cObj("clients_option_view").indeterminate = false;
    }


    var client_options_2 = document.getElementsByClassName("client_options_2");
    var count = client_options_2.length;
    var checked = 0;
    for (let index = 0; index < client_options_2.length; index++) {
        const element = client_options_2[index];
        if (element.checked) {
            checked++;
        }
    }

    if (checked > 0) {
        if (checked == count) {
            cObj("clients_option_readonly").checked = true;
            cObj("clients_option_readonly").indeterminate = false;
        }else{
            cObj("clients_option_readonly").checked = false;
            cObj("clients_option_readonly").indeterminate = true;
        }
    }else{
        cObj("clients_option_readonly").checked = false;
        cObj("clients_option_readonly").indeterminate = false;
    }
}