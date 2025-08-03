// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}


cObj("expense_quantity").onkeyup = function () {
    doExpenseCalculation();
}
cObj("expense_unit_price").onkeyup = function () {
    doExpenseCalculation();
}
function doExpenseCalculation() {
    var expense_quantity = cObj("expense_quantity").value;
    var expense_unit_price = cObj("expense_unit_price").value;
    if (expense_quantity.length > 0 && expense_unit_price.length > 0) {
        var values = expense_quantity * expense_unit_price;
        cObj("expense_total_price").value = values.toFixed(2);
    }else{
        cObj("expense_total_price").value = 0;
    }
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
cObj("delete_expense").onclick = function () {
    showModal("delete_expense_window");
}

cObj("hide_delete_expense").onclick = function () {
    hideModal("delete_expense_window");
}

cObj("close_this_window_delete").onclick = function () {
    hideModal("delete_expense_window");
}
