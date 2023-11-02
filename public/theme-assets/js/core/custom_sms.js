// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}
cObj("view_sample_daybefore").onclick = function () {
    // switch between windwos
    cObj("daybefore_win_sample").classList.remove("d-none");
    cObj("daybefore_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents").value;
    var newdata = replace_text(olddata);
    cObj("daybefore_contents").innerText = newdata;
}
cObj("backto_daybefore").onclick = function () {
    // switch between windwos
    cObj("daybefore_win_sample").classList.add("d-none");
    cObj("daybefore_win").classList.remove("d-none");
}
// deday
cObj("view_sample_deday").onclick = function () {
    // switch between windwos
    cObj("deday_win_sample").classList.remove("d-none");
    cObj("deday_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_2").value;
    var newdata = replace_text(olddata);
    cObj("deday_contents").innerText = newdata;
}
cObj("backto_deday").onclick = function () {
    // switch between windwos
    cObj("deday_win_sample").classList.add("d-none");
    cObj("deday_win").classList.remove("d-none");
}
// after due date
cObj("view_sample_after_due_date").onclick = function () {
    // switch between windwos
    cObj("after_due_date_win_sample").classList.remove("d-none");
    cObj("after_due_date_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_3").value;
    var newdata = replace_text(olddata);
    cObj("after_due_date_contents").innerText = newdata;
}
cObj("backto_after_due_date").onclick = function () {
    // switch between windwos
    cObj("after_due_date_win_sample").classList.add("d-none");
    cObj("after_due_date_win").classList.remove("d-none");
}
// paid to correct account number
cObj("view_sample_correct_acc_no").onclick = function () {
    // switch between windwos
    cObj("correct_acc_no_win_sample").classList.remove("d-none");
    cObj("correct_acc_no_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_4").value;
    var newdata = replace_text(olddata);
    cObj("correct_acc_no_contents").innerText = newdata;
}
cObj("backto_correct_acc_no").onclick = function () {
    // switch between windwos
    cObj("correct_acc_no_win_sample").classList.add("d-none");
    cObj("correct_acc_no_win").classList.remove("d-none");
}

// paid to incorrect account number
cObj("view_sample_incorrect_acc_no").onclick = function () {
    // switch between windwos
    cObj("incorrect_acc_no_win_sample").classList.remove("d-none");
    cObj("incorrect_acc_no_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_5").value;
    var newdata = replace_text(olddata);
    cObj("incorrect_acc_no_contents").innerText = newdata;
}
cObj("backto_incorrect_acc_no").onclick = function () {
    // switch between windwos
    cObj("incorrect_acc_no_win_sample").classList.add("d-none");
    cObj("incorrect_acc_no_win").classList.remove("d-none");
}
// renewed an account
cObj("view_sample_account_renewed").onclick = function () {
    // switch between windwos
    cObj("account_renewed_win_sample").classList.remove("d-none");
    cObj("account_renewed_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_6").value;
    var newdata = replace_text(olddata);
    cObj("account_renewed_contents").innerText = newdata;
}
cObj("backto_account_renewed").onclick = function () {
    // switch between windwos
    cObj("account_renewed_win_sample").classList.add("d-none");
    cObj("account_renewed_win").classList.remove("d-none");
}
// extend an account
cObj("view_sample_account_extended").onclick = function () {
    // switch between windwos
    cObj("account_extended_win_sample").classList.remove("d-none");
    cObj("account_extended_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_7").value;
    var newdata = replace_text(olddata);
    cObj("account_extended_contents").innerText = newdata;
}
cObj("backto_account_extended").onclick = function () {
    // switch between windwos
    cObj("account_extended_win_sample").classList.add("d-none");
    cObj("account_extended_win").classList.remove("d-none");
}
// welcome new sms
cObj("view_sample_welcome_sms").onclick = function () {
    // switch between windwos
    cObj("welcome_sms_win_sample").classList.remove("d-none");
    cObj("welcome_sms_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_8").value;
    var newdata = replace_text(olddata);
    cObj("welcome_sms_contents").innerText = newdata;
}
cObj("backto_welcome_sms").onclick = function () {
    // switch between windwos
    cObj("welcome_sms_win_sample").classList.add("d-none");
    cObj("welcome_sms_win").classList.remove("d-none");
}
// account deactivated
cObj("view_sample_account_deactivated").onclick = function () {
    // switch between windwos
    cObj("account_deactivated_win_sample").classList.remove("d-none");
    cObj("account_deactivated_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_9").value;
    var newdata = replace_text(olddata);
    cObj("account_deactivated_contents").innerText = newdata;
}
cObj("backto_account_deactivated").onclick = function () {
    // switch between windwos
    cObj("account_deactivated_win_sample").classList.add("d-none");
    cObj("account_deactivated_win").classList.remove("d-none");
}
// refferer account number
cObj("view_sample_refferer_msg").onclick = function () {
    // switch between windwos
    cObj("refferer_msg_win_sample").classList.remove("d-none");
    cObj("refferer_message_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_10").value;
    var newdata = replace_text(olddata);
    cObj("refferer_msg_contents").innerText = newdata;
}
cObj("backto_refferer_msg").onclick = function () {
    // switch between windwos
    cObj("refferer_msg_win_sample").classList.add("d-none");
    cObj("refferer_message_win").classList.remove("d-none");
}
// refferer account number
cObj("view_sample_below_min_amnt").onclick = function () {
    // switch between windwos
    cObj("below_min_amnt_win_sample").classList.remove("d-none");
    cObj("below_min_amnt_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_11").value;
    var newdata = replace_text(olddata);
    cObj("below_min_amnt_contents").innerText = newdata;
}
cObj("backto_below_min_amnt").onclick = function () {
    // switch between windwos
    cObj("below_min_amnt_win_sample").classList.add("d-none");
    cObj("below_min_amnt_win").classList.remove("d-none");
}

// welcome new sms client sms
cObj("view_sample_welcome_client_sms").onclick = function () {
    // switch between windwos
    cObj("welcome_client_sms_win_sample").classList.remove("d-none");
    cObj("welcome_client_sms_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_12").value;
    var newdata = replace_text(olddata);
    cObj("welcome_client_sms_contents").innerText = newdata;
}
cObj("backto_welcome_client_sms").onclick = function () {
    // switch between windwos
    cObj("welcome_client_sms_win_sample").classList.add("d-none");
    cObj("welcome_client_sms_win").classList.remove("d-none");
}

// Correct Account number sms client sms
cObj("view_sample_rcv_coracc_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_coracc_billsms_win_sample").classList.remove("d-none");
    cObj("rcv_coracc_billsms_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_13").value;
    var newdata = replace_text(olddata);
    cObj("rcv_coracc_billsms_contents").innerText = newdata;
}
cObj("backto_rcv_coracc_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_coracc_billsms_win_sample").classList.add("d-none");
    cObj("rcv_coracc_billsms_win").classList.remove("d-none");
}

// Incorrect Account number sms client sms
cObj("view_sample_rcv_incoracc_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_incoracc_billsms_win_sample").classList.remove("d-none");
    cObj("rcv_incoracc_billsms_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_14").value;
    var newdata = replace_text(olddata);
    cObj("rcv_incoracc_billsms_contents").innerText = newdata;
}
cObj("backto_rcv_incoracc_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_incoracc_billsms_win_sample").classList.add("d-none");
    cObj("rcv_incoracc_billsms_win").classList.remove("d-none");
}

// Incorrect Account number but below minimum amount sms client sms
cObj("view_sample_rcv_belowmin_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_belowmin_billsms_win_sample").classList.remove("d-none");
    cObj("rcv_belowmin_billsms_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_15").value;
    var newdata = replace_text(olddata);
    cObj("rcv_belowmin_billsms_contents").innerText = newdata;
}
cObj("backto_rcv_belowmin_billsms").onclick = function () {
    // switch between windwos
    cObj("rcv_belowmin_billsms_win_sample").classList.add("d-none");
    cObj("rcv_belowmin_billsms_win").classList.remove("d-none");
}
// Message reminder when low
cObj("view_sample_msg_reminder_bal").onclick = function () {
    // switch between windwos
    cObj("msg_reminder_bal_win_sample").classList.remove("d-none");
    cObj("msg_reminder_bal_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_16").value;
    var newdata = replace_text(olddata);
    cObj("msg_reminder_bal_contents").innerText = newdata;
}
cObj("backto_msg_reminder_bal").onclick = function () {
    // switch between windwos
    cObj("msg_reminder_bal_win_sample").classList.add("d-none");
    cObj("msg_reminder_bal_win").classList.remove("d-none");
}
// Message reminder when client is frozen?
cObj("view_sample_account_frozen").onclick = function () {
    // switch between windwos
    cObj("account_frozen_win_sample").classList.remove("d-none");
    cObj("account_frozen_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_17").value;
    var newdata = replace_text(olddata);
    cObj("account_frozen_contents").innerText = newdata;
}
cObj("backto_account_frozen").onclick = function () {
    // switch between windwos
    cObj("account_frozen_win_sample").classList.add("d-none");
    cObj("account_frozen_win").classList.remove("d-none");
}
// 
// Message reminder when client is frozen?
cObj("view_sample_account_unfrozen").onclick = function () {
    // switch between windwos
    cObj("account_unfrozen_win_sample").classList.remove("d-none");
    cObj("account_unfrozen_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_18").value;
    var newdata = replace_text(olddata);
    cObj("account_unfrozen_contents").innerText = newdata;
}
cObj("backto_account_unfrozen").onclick = function () {
    // switch between windwos
    cObj("account_unfrozen_win_sample").classList.add("d-none");
    cObj("account_unfrozen_win").classList.remove("d-none");
}
// Message reminder when client is going to be frozen in the future?
cObj("view_sample_future_account_freeze").onclick = function () {
    // switch between windwos
    cObj("future_account_freeze_win_sample").classList.remove("d-none");
    cObj("future_account_freeze_win").classList.add("d-none");
    // replace text with data
    var olddata = cObj("message_contents_19").value;
    var newdata = replace_text(olddata);
    cObj("future_account_freeze_contents").innerText = newdata;
}
cObj("backto_future_account_freeze").onclick = function () {
    // switch between windwos
    cObj("future_account_freeze_win_sample").classList.add("d-none");
    cObj("future_account_freeze_win").classList.remove("d-none");
}
function replace_text(data) {
    var date = new Date();
    data = data.replace(/\[client_name\]/g, "Juma Jux");
    data = data.replace(/\[client_f_name\]/g, "Juma");
    data = data.replace(/\[client_addr\]/g, "Kisumu Town");
    data = data.replace(/\[exp_date\]/g, "12th Dec 2022 at 12:00:00am");
    data = data.replace(/\[reg_date\]/g, "12th Jan 2022");
    data = data.replace(/\[int_speeds\]/g, "3M/4M");
    data = data.replace(/\[monthly_fees\]/g, "Ksh 2000");
    data = data.replace(/\[client_phone\]/g, "0743551250");
    data = data.replace(/\[acc_no\]/g, "HYP001");
    data = data.replace(/\[client_wallet\]/g, "Ksh 1000");
    data = data.replace(/\[username\]/g, "juma");
    data = data.replace(/\[password\]/g, "Kiganjo16");
    data = data.replace(/\[trans_amnt\]/,"Ksh 1000");
    data = data.replace(/\[min_amnt\]/,"Ksh 300");
    data = data.replace(/\[refferer_trans_amount\]/,"Ksh 150");
    data = data.replace(/\[refferer_name\]/,"Owen Malingu");
    data = data.replace(/\[refferer_f_name\]/,"Owen");
    data = data.replace(/\[sms_rate\]/,"Ksh 0.7");
    data = data.replace(/\[sms_balance\]/,"100");
    data = data.replace(/\[unfreeze_date\]/,"13th Nov 2023 at 12:00:00am");
    data = data.replace(/\[days_frozen\]/,"2 Day(s)");
    data = data.replace(/\[frozen_date\]/,"16th Nov 2023");
    data = data.replace(/\[today\]/g, ""+(date.getDate()< 10 ? "0"+date.getDate() : date.getDate())+"-"+((date.getMonth()+1)< 10 ? "0"+(date.getMonth()+1) : (date.getMonth()+1))+"-"+(date.getFullYear()< 10 ? "0"+date.getFullYear() : date.getFullYear()));
    data = data.replace(/\[now\]/g, ""+(date.getHours()< 10 ? "0"+date.getHours() : date.getHours())+":"+(date.getMinutes()<10 ? "0"+date.getMinutes() : date.getMinutes())+":"+(date.getSeconds()<10 ? "0"+date.getSeconds() : date.getSeconds()));
    return data;
}
window.onload = function () {
    var data1 = cObj("message_contents").value;
    var newdata = replace_text(data1);
    cObj("daybefore_contents").innerText = newdata;
    // two
    var data1 = cObj("message_contents_2").value;
    var newdata = replace_text(data1);
    cObj("deday_contents").innerText = newdata;
    // three
    var data1 = cObj("message_contents_3").value;
    var newdata = replace_text(data1);
    cObj("after_due_date_contents").innerText = newdata;
    // four
    var data1 = cObj("message_contents_4").value;
    var newdata = replace_text(data1);
    cObj("correct_acc_no_contents").innerText = newdata;
    // five
    var data1 = cObj("message_contents_5").value;
    var newdata = replace_text(data1);
    cObj("incorrect_acc_no_contents").innerText = newdata;
    // six
    var data1 = cObj("message_contents_6").value;
    var newdata = replace_text(data1);
    cObj("account_renewed_contents").innerText = newdata;
    // seven
    var data1 = cObj("message_contents_7").value;
    var newdata = replace_text(data1);
    cObj("account_extended_contents").innerText = newdata;
    // eight
    var data1 = cObj("message_contents_8").value;
    var newdata = replace_text(data1);
    cObj("welcome_sms_contents").innerText = newdata;
    // nine
    var data1 = cObj("message_contents_9").value;
    var newdata = replace_text(data1);
    cObj("account_deactivated_contents").innerText = newdata;
    // ten 
    var data1 = cObj("message_contents_10").value;
    var newdata = replace_text(data1);
    cObj("refferer_msg_contents").innerText = newdata;
    // eleven
    var data1 = cObj("message_contents_11").value;
    var newdata = replace_text(data1);
    cObj("below_min_amnt_contents").innerText = newdata;
    // twelve
    var data1 = cObj("message_contents_12").value;
    var newdata = replace_text(data1);
    cObj("welcome_client_sms_contents").innerText = newdata;
    // thirteen
    var data1 = cObj("message_contents_13").value;
    var newdata = replace_text(data1);
    cObj("rcv_coracc_billsms_contents").innerText = newdata;
    // fourteen
    var data1 = cObj("message_contents_14").value;
    var newdata = replace_text(data1);
    cObj("rcv_incoracc_billsms_contents").innerText = newdata;
    // fifteen
    var data1 = cObj("message_contents_15").value;
    var newdata = replace_text(data1);
    cObj("rcv_belowmin_billsms_contents").innerText = newdata;
    // sixteen
    var data1 = cObj("message_contents_16").value;
    var newdata = replace_text(data1);
    cObj("msg_reminder_bal_contents").innerText = newdata;
    // seventeen
    var data1 = cObj("message_contents_17").value;
    var newdata = replace_text(data1);
    cObj("account_frozen_contents").innerText = newdata;
    // eighteen
    var data1 = cObj("message_contents_18").value;
    var newdata = replace_text(data1);
    cObj("account_unfrozen_contents").innerText = newdata;
    // nineteen
    var data1 = cObj("message_contents_19").value;
    var newdata = replace_text(data1);
    cObj("future_account_freeze_contents").innerText = newdata;
}