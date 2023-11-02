<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\login;
use App\Http\Controllers\Clients;
use App\Http\Controllers\Transaction;
use App\Http\Controllers\Router;
use App\Http\Controllers\Sms;
use App\Http\Controllers\admin;
use App\Http\Controllers\Clients_data;
use App\Http\Controllers\export_client;
use App\Http\Controllers\billsms_manager;
use App\Http\Controllers\Expenses;
use App\Http\Controllers\SharedTables;
use Symfony\Component\Mime\Crypto\SMime;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::view("/","mainpage");
Route::get("/", function () {
    return redirect('/Hypbits');
});
// Route::view("/Dashboard","index");
Route::get("/Dashboard",[Transaction::class,"getDashboard"]);
// Route::view("/Clients","myclients");
// Route::view("/Transactions","mytransactions");
// Route::view("/Routers","myRouter");
Route::view("/Routers/New","newrouter");
Route::get('/Login', function () {
    if (session('error')) {
        session()->flash("error",session('error'));
    }
    return redirect('/Hypbits');
});
// Route::view("/sms","adminsms");
// Route::view("/Login","login");
// Special for Hypbits
Route::view("/Hypbits","login");
Route::view("/verify","verify");
// Route::view("/Clients/NewStatic","newClient");

//login controller router
Route::post("/proc_login",[login::class,"processLogin"]);
Route::post("/verifycode",[Login::class,"processVerification"]);

// save client route
Route::post("addClient",[Clients::class,'processNewClient'])->name("clients.addstatic");
// save minimum payment
Route::post("/Client/Update/MinimumPay",[Clients::class,"updateMinPay"])->name("client.update.minimum_payment.static");
// save client pppoe
Route::post("addClientPppoe",[Clients::class,'processClientPPPoE'])->name("clients.addppoe");
// the clients controller route
Route::get("/Clients",[Clients::class,'getClientData']);
// get the routers information
Route::get("/Routers",[Clients::class,'getRouterData']);
// get the router information for the new client
Route::get("/Clients/NewStatic",[Clients::class,"getRouterDataClients"]);
Route::get("/Clients/NewPPPoE",[Clients::class,"getRouterDatappoe"])->name("newclient.pppoe");
// get the router interface
Route::get("/router/{routerid}",[Clients::class,"getRouterInterfaces"]);
// get the router profile
Route::get("/routerProfile/{routerid}",[Clients::class,"getRouterProfile"]);
// get the clients information interface
Route::get("/Clients/View/{clientid}",[Clients::class,"getClientInformation"])->name("client.viewinformation");
// incase the user enters an invalid username
Route::get('/Clients/View', function () {
    return redirect('Clients');
});
// get the refferer details
Route::get("/get_refferal/{client_account}",[Clients::class,"getRefferal"]);
// save the refferer data
Route::post("/set_refferal",[Clients::class,"setRefferal"]);
// update clients
Route::post("/updateClients",[Clients::class,'updateClients']);
// update clients expiration
Route::post("/changeExpDate",[Clients::class,'updateExpDate']);
// freeze dates set
Route::post("/set_freeze",[Clients::class,'set_freeze_date']);
// deactivate freeze
Route::get("/Client/deactivate_freeze/{client_id}",[Clients::class,"deactivatefreeze"]);
// activate freeze
Route::get("/Client/activate_freeze/{client_id}",[Clients::class,"activatefreeze"]);
// client syncs
Route::get("/ClientSync",[Clients::class,"syncclient"]);
// sync transactions
Route::get("/TransactionSync",[Clients::class,"synctrans"]);
// change wallet balance
Route::post("/changeWallet",[Clients::class,"changeWalletBal"]);
//export my clients
Route::get("/Clients/Export",[export_client::class,"exportClients"]);
// get detailed router information in order to export
Route::get("/Clients/Export/View/{router_id}",[export_client::class,"router_client_information"]);
// sync client information
Route::get("/Client/epxsync/{client_id}",[export_client::class,"sync_client_router"]);
// export all from the router
Route::get("/Clients/ExportAll/{router_id}",[export_client::class,"exportall"]);
// delete user
Route::get("/delete_user/{user_id}",[Clients::class,'delete_user']);

// add router
Route::post("addRouter",[Clients::class,'addRouter']);


// de-activate and activate user the user
Route::get("/deactivate/{userid}",[Clients::class,"deactivate"]);
Route::get("/activate/{userid}",[Clients::class,"activate"]);

// deactivate and activate the user api
Route::get("/deactivate_user/{userid}",[Clients::class,"deactivate2"]);
Route::get("/activate_user/{userid}",[Clients::class,"activate2"]);

// deactivate the user payment status
Route::get("/deactivatePayment/{userid}", [Clients::class,"dePay"]);
Route::get("/activatePayment/{userid}", [Clients::class,"actPay"]);




//TRANSACTIONS SECTION
Route::get("/Transactions",[Transaction::class,"getTransactions"]);
Route::get("/Transactions/View/{trans_id}",[Transaction::class,"transDetails"]);
Route::get("/Assign/Transaction/{trans_id}/Client/{client_id}",[Transaction::class,"assignTransaction"]);
Route::get("/confirmTransfer/{user_id}/{transaction_id}",[Transaction::class,"confirmTransfer"]);
Route::post("/Transact",[Transaction::class,"mpesaTransactions"]);

// Router section
Route::get("/Router/View/{routerid}",[Router::class,"getRouterInfor"]);
Route::get("/Routers/Delete/{routerid}",[Router::class,"deleteRouter"]);
Route::get("/Router/Reboot/{routerid}",[Router::class,"reboot"]);
Route::post("/updateRouter",[Router::class,"updateRoute"]);

// Sms section
Route::get("/sms",[Sms::class,"getSms"]);
Route::get("/sms/View/{smsid}", [Sms::class,"getSMSData"]);
Route::get("/sms/delete/{smsid}", [Sms::class,"delete"]);
Route::get("/sms/compose",[Sms::class,"compose"]);
Route::post("/sendsms",[Sms::class,"sendsms"]);
Route::get("/sms/system_sms",[Sms::class,"customsms"]);
Route::post("/save_sms_content",[Sms::class,"save_sms_content"]);
Route::get("/sms_balance",[Sms::class,"sms_balance"]);
Route::get("/sms/resend/{sms_id}",[Sms::class,"resend_sms"]);
Route::post("/sendsms_routers",[Sms::class,"sendsms_routers"]);

// accounts and profile
Route::get("/Accounts",[admin::class,"getAdmin"]);
Route::post("/changePasswordAdmin", [admin::class,"updatePassword"]);
Route::get("/Accounts/add",[admin::class,"addAdmin"]);
Route::post("/addAdministrator", [admin::class,"addAdministrator"]);
Route::get("/Admin/View/{admin_id}", [admin::class,"viewAdmin"]);
Route::post("/updateAdministrator", [admin::class,"updateAdmin"]);
Route::post("/update_dp", [admin::class,"upload_dp"]);
Route::post("/update_admin", [admin::class,"update_admin"]);
Route::post("/update_delete_option", [admin::class,"update_delete_option"]);
Route::get("/delete_pp/{admin_id}",[admin::class,"delete_pp"]);

// routes for the clients information
Route::get("/ClientDashboard",[Clients_data::class,"getClientInfor"]);
Route::get("/Payment",[Clients_data::class,"getTransaction"]);
Route::get("/Payment/View/{paymentid}",[Clients_data::class,'viewPayment']);
Route::view("/Payment/Confirm","confirmPay");
Route::get("/Payment/mpesa/{mpesaid}",[Clients_data::class,"confirm_mpesa"]);
Route::view("/Credentials","credential");
Route::post("/changePassword",[Clients_data::class,"change_password"]);
Route::get("/Payment/stkpush",[Transaction::class,"stkpush"]);

// get ip addresses
Route::get("/ipAddress/{routerid}",[Clients::class,"getIpaddresses"]);

// go and manage the billing sms
Route::get("/BillingSms/Manage",[billsms_manager::class,"getBilledClients"]);
// new sms client
Route::get("/BillingSms/New",[billsms_manager::class,"newClient"]);
// Register new client
Route::post("/register_new",[billsms_manager::class,"registerClient"]);
// VIEW AND UPDATE SMS CLIENT
Route::get("/BillingSms/ViewClient/{clientid}",[billsms_manager::class,"displayClient"]);
// update client sms
Route::post("/update_client_sms",[billsms_manager::class,"updateClient"]);
// delete client sms
Route::get("/delete_user_sms/{client_id}",[billsms_manager::class,"deleteClient"]);
// deactivate a client
Route::get("/deactivate_sms_client/{client_id}",[billsms_manager::class,"deactivateClient"]);
// activate a client
Route::get("/activate_sms_client/{client_id}",[billsms_manager::class,"activateClient"]);
// change sms balance
Route::post("/changeSmsBal",[billsms_manager::class,"changeSmsBal"]);
// view transactions for the transaction manager
Route::get("/BillingSms/Transactions",[billsms_manager::class,"viewTransaction"]);
// view transactions details
Route::get("/BillingSms/Transactions/View/{transaction_id}",[billsms_manager::class,"viewTransactionDetails"]);
// ASSIGNE TRANSACTION
Route::get("/BillingSms/Assign/Transaction/{transaction_id}/Client/{client_id}",[billsms_manager::class,"assignTransaction"]);
// confirm transfer of funnds
Route::get("/BillingSms/confirmTransfer/{client_id}/{transactionid}",[billsms_manager::class,"transferFunds"]);
Route::post("/renew_licence",[billsms_manager::class,"renew_Licence"]);
// manage the packages so that user can know how to pay
Route::get("/BillingSms/Packages",[billsms_manager::class,"myPackages"]);
// set ne package
Route::get("/BillingSms/NewPackage",[billsms_manager::class,"newPackage"]);
// register package
Route::post("/register_package",[billsms_manager::class,"registerPackage"]);
// register package
Route::get("/BillingSms/ViewPackage/{id}",[billsms_manager::class,"viewPackages"]);
// update_package
Route::post("/update_package",[billsms_manager::class,"updatePackage"]);
// delete packages
Route::get("/delete_package/{package_id}",[billsms_manager::class,"deletePackage"]);
// GET PACKAGE LSISTS
Route::get("/getpackages",[billsms_manager::class,"showPackages"]);

// create a new link to set up the router
Route::view("/Clients/NewRouterSetup","RouterSetup");
Route::post("/connect_router",[Router::class,"test_router"]);
Route::post("/remove_interface_bridge",[Router::class,"remove_interface_bridge"]);
Route::get("/getbridge",[Router::class,"process_interfaces"]);
Route::post("/add_bridge",[Router::class,"add_bridge"]);
Route::post("/remove_bridge",[Router::class,"remove_bridge"]);
Route::post("/change_bridge",[Router::class,"change_bridge"]);
Route::post("/get_setting",[Router::class,"get_setting"]);
Route::post("/set_dynamic",[Router::class,"set_dynamic"]);
Route::post(("/set_static_access"),[Router::class,"set_static_access"]);
Route::post("/set_pppoe_assignment",[Router::class,"set_pppoe_assignment"]);
Route::post("/set_pool",[Router::class,"set_pool"]);
Route::get("/get_pools",[Router::class,"get_pools"]);
Route::post("/add_pppoe_profile",[Router::class,"add_pppoe_profile"]);
Route::get("/get_pppoe_server",[Router::class,"get_pppoe_server"]);
Route::post("/save_ppoe_server",[Router::class,"save_ppoe_server"]);
Route::post("/add_security",[Router::class,"add_security"]);
Route::get("/get_security_profile",[Router::class,"get_security_profile"]);
Route::post("/save_ssid",[Router::class,"save_ssid"]);
Route::post("/get_interface_supply",[Router::class,"get_interface_supply"]);
Route::post("/get_wireless",[Router::class,"get_wireless"]);
Route::get("/getconnection",[Router::class,"getconnection"]);
Route::post("/get_interface_config",[Router::class,"get_interface_config"]);
Route::post("/get_internet_access",[Router::class,"get_internet_access"]);
Route::post("/get_supply_method",[Router::class,"get_supply_method"]);
Route::post("/wireless_settings",[Router::class,"wireless_settings"]);

// statistics
Route::get("/Client-Statistics",[Clients::class,'getClients_Statistics']);
Route::post("/Client-due-demographics",[Clients::class,'clientsDemographics']);
Route::get("/Transactions/Statistics",[Transaction::class,'transactionStatistics']);

// router logs
Route::get("/Router/writeLogs/{router_id}",[Router::class,"writeRouterLogs"]);
Route::get("/Router/Logs/{router_id}",[Router::class,"readLogs"]);

// reports
Route::get("/Clients/generateReports",[Clients::class,"generateReports"]);
Route::get("/Transaction/generateReports",[Transaction::class,"generateReports"]);
Route::get("/SMS/generateReports",[Sms::class,"generateReports"]);

// expenses
Route::get("/Expenses",[Expenses::class,"getExpenses"]);
Route::post("/Expense/Category/Add",[Expenses::class,"addExpenseCategory"]);
Route::get("/Expense/Delete/{expense_index}",[Expenses::class,"deleteExpense"]);
Route::post("/Expense/Add",[Expenses::class,"addExpense"]);
Route::post("/Expense/Update",[Expenses::class,"updateExpense"]);
Route::get("/Expense/View/{expense_id}",[Expenses::class,"viewExpense"]);
Route::get("/Expense/DeleteRecords/{expense_id}",[Expenses::class,"deleteExpenseRecords"]);
Route::get("/Expenses/Generate/Reports",[Expenses::class,"generateReports"]);
Route::get("/Expense/Statistics",[Expenses::class,"expenseStatistics"]);
Route::get("/Expenses/Generate/FinStats",[Expenses::class,"financeStats"]);

// delete users
Route::post("/delete_clients",[Clients::class,"deleteClients"]);
Route::post("/send_sms_clients",[Clients::class,"sendSmsClients"]);
Route::get("/admin/deactivate/{admin_id}",[admin::class,"deactivateAdmin"]);

// bulk sms
Route::post("/Delete_bulk_sms",[Sms::class,"Delete_bulk_sms"]);
Route::post("/Resend_bulk_sms",[Sms::class,"Resend_bulk_sms"]);

Route::get("/SharedTables",[SharedTables::class,"openSharedTables"]);
Route::view("/CreateShareTables","createTable");
Route::post("/SaveTable",[SharedTables::class,"SaveTable"]);
Route::get("SharedTables/View/{table_id}/Name/{table_name}",[SharedTables::class,"getTable"]);
Route::get("SharedTables/Edit/{table_id}/Name/{table_name}",[SharedTables::class,"editTable"]);
Route::post("/UpdateTableCreated",[SharedTables::class,"UpdateTableCreated"]);
Route::get("/SharedTables/addRecord/{table_id}/Name/{table_name}",[SharedTables::class,"addRecords"]);
Route::post("/SharedTables/AddRecords",[SharedTables::class,"saveRecord"]);
Route::get("/SharedTables/Edit/{table_id}/Name/{table_name}/Record/{record_no}",[SharedTables::class,"editRecord"]);
Route::post("/SharedTables/UpdateRecords",[SharedTables::class,"UpdateRecords"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{table_name}",[SharedTables::class,"deleteTable"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{link_table_name}/Record/{rows_id}",[SharedTables::class,"deleteRecord"]);