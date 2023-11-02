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

cObj("delete_expense").onclick = function () {
    cObj("delete_expense_window").classList.toggle("hide");
}
