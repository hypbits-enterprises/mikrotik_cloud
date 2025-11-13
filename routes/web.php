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
use App\Http\Controllers\mpesa_api;
use App\Http\Controllers\Router_Cloud;
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
Route::get("/Dashboard", [Transaction::class, "getDashboard"])->middleware(["checkAccount", "validated"]);
// Route::view("/Clients","myclients");
// Route::view("/Transactions","mytransactions");
// Route::view("/Routers","myRouter");
Route::view("/Routers/New", "newrouter");
Route::get('/Login', function () {
    if (session('error')) {
        session()->flash("error", session('error'));
    }
    return redirect('/Hypbits');
});
// Route::view("/sms","adminsms");
// Route::view("/Login","login");
// Special for Hypbits
Route::view("/Hypbits", "login");
Route::view("/verify", "verify");
Route::view("/Forgot-Password", "forget_password");
Route::get("/Reset-Password", [login::class, "reset_password"])->name("reset_password");
// Route::view("/Clients/NewStatic","newClient");

//login controller router
Route::post("/process_login", [login::class, "processLogin"])->name("process_login");
Route::post("/verifycode", [Login::class, "processVerification"])->name("verify_code");//
Route::post("/forgot_password", [login::class, "forgot_password"])->name("forgot_password");
Route::post("/reset_my_password", [login::class, "reset_my_password"])->name("reset_my_password");
Route::get("/No-Change-Password", [login::class, "no_change_password"])->name("no_change_password");

// save client route
Route::post("addClient", [Clients::class, 'processNewClient'])->name("clients.addstatic");
Route::post("/Quick-Register/New-Static-Client", [Clients::class, 'processNewQuickRegisterStaticClient'])->name("clients.quick_register_static");
// save minimum payment
Route::post("/Client/Update/MinimumPay", [Clients::class, "updateMinPay"])->name("client.update.minimum_payment.static");
// save client pppoe
Route::post("addClientPppoe", [Clients::class, 'processClientPPPoE'])->name("clients.addppoe");
Route::post("/Quick-Register/New-PPPoE-Client", [Clients::class, 'processQuickRegisterNewClientPPPoE'])->name("quick_register.new_pppoe_client");
// the clients controller route
Route::get("/Clients", [Clients::class, 'getClientData'])->name("myclients")->middleware(["checkAccount", "validated"]);
Route::get("Clients/search", [Clients::class, 'searchClients'])->name("search_clients")->middleware(["checkAccount", "validated"]);
Route::get("Clients/datatable", [Clients::class, 'getClientsDatatable'])->name("clients_datatable")->middleware(["checkAccount", "validated"]);
// get the router information for the new client
Route::get("/Clients/NewStatic", [Clients::class, "getRouterDataClients"])->middleware(["checkAccount", "validated"]);
Route::get("/Clients/NewPPPoE", [Clients::class, "getRouterDatappoe"])->name("newclient.pppoe")->middleware(["checkAccount", "validated"]);
Route::view("/Quick-Register", "quickregister")->middleware(["checkAccount", "validated"]);
Route::post("/Quick-Register/Validate_User", [Clients::class, "validate_user"])->name("validate_user");
Route::get("/Quick-Register/New-Static", [Clients::class, "newStaticClient"])->middleware(["checkAccount", "validated"]);
Route::get("/Quick-Register/New-PPPoE", [Clients::class, "newPPPOEClient"])->middleware(["checkAccount", "validated"]);
Route::get("/Client-Reports", [Clients::class, "client_issues"])->name("client_issues")->middleware(["checkAccount", "validated"]);
Route::get("/Client-Reports/New", [Clients::class, "newReports"])->name("newReports")->middleware(["checkAccount", "validated"]);
Route::get("/Client-Reports/View/{client_id}", [Clients::class, "viewReports"])->name("viewReports")->middleware(["checkAccount", "validated"]);
Route::post("/Client-Reports/Save-Report", [Clients::class, "saveReports"])->name("saveReports");
Route::post("/Client-Reports/Update-Report", [Clients::class, "updateReports"])->name("updateReports");
Route::post("/Client-Reports/Change-Status", [Clients::class, "changeReportStatus"])->name("changeReportStatus");
Route::get("/Client-Reports/Delete-Report/{report_id}", [Clients::class, "deleteReport"])->name("deleteReport")->middleware(["checkAccount", "validated"]);
Route::post("/update_client_comment", [Clients::class, "update_client_comment"])->name("update_client_comment");

// NEW INVOICES
Route::get("/I/{organization_id}/{invoice_id}", [Clients::class,"print_invoice_external"])->name("print_invoice_external");
Route::post("/New-Invoice", [Clients::class, "new_invoice"])->name("new_invoice");
Route::post("/Send-Invoice", [Clients::class, "send_invoice"])->name("send_invoice");
Route::get("/Delete-Invoice/{invoice_id}", [Clients::class, "delete_invoice"])->name("delete_invoice");
Route::get("/Invoice/Print/{invoice_id}", [Clients::class, "print_invoice"])->name("print_invoice");
Route::post("/Update-Invoice", [Clients::class, "update_invoice"])->name("update_invoice");
// get the router interface
Route::get("/router/{routerid}", [Clients::class, "getRouterInterfaces"])->middleware(["checkAccount", "validated"]);
// get the router profile
Route::get("/routerProfile/{routerid}", [Clients::class, "getRouterProfile"])->middleware(["checkAccount", "validated"]);
// get the clients information interface
Route::get("/Clients/View/{clientid}", [Clients::class, "getClientInformation"])->name("client.viewinformation")->middleware(["checkAccount", "validated"]);
// check the client status in the router
Route::get("/Client/Check-Online/{client_account}", [Clients::class, "checkOnline"])->name("check-online")->middleware(["checkAccount", "validated"]);


// incase the user enters an invalid username
Route::get('/Clients/View', function () {
    return redirect('Clients');
})->middleware(["checkAccount", "validated"]);
Route::post("/Client/Convert", [Clients::class, "convertClient"])->name("convertClient");
// get the refferer details
Route::get("/get_refferal/{client_account}", [Clients::class, "getRefferal"])->middleware(["checkAccount", "validated"]);
// save the refferer data
Route::post("/set_refferal", [Clients::class, "setRefferal"]);
// update clients
Route::post("/updateClients", [Clients::class, 'updateClients']);
// update clients expiration
Route::post("/changeExpDate", [Clients::class, 'updateExpDate']);
// freeze dates set
Route::post("/set_freeze", [Clients::class, 'set_freeze_date']);
// deactivate freeze
Route::get("/Client/deactivate_freeze/{client_id}", [Clients::class, "deactivatefreeze"])->middleware(["checkAccount", "validated"]);
// activate freeze
Route::get("/Client/activate_freeze/{client_id}", [Clients::class, "activatefreeze"])->middleware(["checkAccount", "validated"]);
// client syncs
Route::get("/ClientSync", [Clients::class, "syncclient"])->middleware(["checkAccount", "validated"]);
// sync transactions
Route::get("/TransactionSync", [Clients::class, "synctrans"])->middleware(["checkAccount", "validated"]);
// change wallet balance
Route::post("/changeWallet", [Clients::class, "changeWalletBal"]);
// change the clients phone number
Route::post("/change_client_phone", [Clients::class, "change_phone_number"]);
// cchange monthly payments
Route::post("/change_client_monthly_payment", [Clients::class, "change_client_monthly_payment"]);
//export my clients
Route::get("/Clients/Export", [export_client::class, "exportClients"])->middleware(["checkAccount", "validated"]);
// get detailed router information in order to export
Route::get("/Clients/Export/View/{router_id}", [export_client::class, "router_client_information"])->middleware(["checkAccount", "validated"]);
// sync client information
Route::get("/Client/epxsync/{client_id}", [export_client::class, "sync_client_router"])->middleware(["checkAccount", "validated"]);
// export all from the router
Route::get("/Clients/ExportAll/{router_id}", [export_client::class, "exportall"])->middleware(["checkAccount", "validated"]);
// delete user
Route::get("/delete_user/{user_id}", [Clients::class, 'delete_user'])->middleware(["checkAccount", "validated"]);

// add router
Route::post("addRouter", [Clients::class, 'addRouter']);


// de-activate and activate user the user
Route::get("/deactivate/{userid}", [Clients::class, "deactivate"]);
Route::get("/deactivate/{userid}/{db_name}", [Clients::class, "deactivate"]);
Route::get("/deactivate/{userid}/{db_name}/{subdirectory}", [Clients::class, "deactivate"]);
Route::get("/activate/{userid}", [Clients::class, "activate"]);
Route::get("/activate/{userid}/{db_name}", [Clients::class, "activate"]);
Route::get("/activate/{userid}/{db_name}/{subdirectory}", [Clients::class, "activate"]);


// ROUTER QUERIES CLIENTS
Route::get("/router_clients/{acc_name}/{r_name}", [Clients::class, "getRouterClientInfo"]);
Route::get("/my_global_config", [Clients::class, "getMyGlobalConfig"]);
Route::get("/upload_client_stats", [Clients::class, "upload_client_stats"]);
// Generate reports for data usage and bandwidth
Route::get("/Client/UsageReport", [Clients::class, "generateUsageReports"])->name("generateUsageReports")->middleware(["checkAccount", "validated"]);
Route::get("/Client/UsageReport/Data", [Clients::class, "generateDataReports"])->name("generateDataReports")->middleware(["checkAccount", "validated"]);
Route::post("/migrate_client_data", [Clients::class, "migrate_client_data"])->name("migrate_client_data")->middleware(["checkAccount", "validated"]);
Route::post("/reverse_migration", [Clients::class, "reverse_migration"])->name("reverse_migration")->middleware(["checkAccount", "validated"]);
Route::post("/import_client_data", [Clients::class, "import_client_data"])->name("import_client_data")->middleware(["checkAccount", "validated"]);

// deactivate and activate the user api
// Route::get("/deactivate_user/{userid}",[Clients::class,"deactivate2"]);
// Route::get("/activate_user/{userid}",[Clients::class,"activate2"]);

// deactivate the user payment status
Route::get("/deactivatePayment/{userid}", [Clients::class, "dePay"]);
Route::get("/activatePayment/{userid}", [Clients::class, "actPay"]);




//TRANSACTIONS SECTION
Route::get("/Transactions", [Transaction::class, "getTransactions"])->middleware(["checkAccount", "validated"]);
Route::get("/Transactions/View/{trans_id}", [Transaction::class, "transDetails"])->middleware(["checkAccount", "validated"]);
Route::get("/Assign/Transaction/{trans_id}/Client/{client_id}", [Transaction::class, "assignTransaction"])->middleware(["checkAccount", "validated"]);
Route::get("/confirmTransfer/{user_id}/{transaction_id}", [Transaction::class, "confirmTransfer"])->middleware(["checkAccount", "validated"]);
Route::post("/Transact", [Transaction::class, "mpesaTransactions"]);
Route::post("/Validate", [Transaction::class, "verify_client_transaction"]);
Route::get("/Print-Reciept/{receipt_id}", [Transaction::class, "print_receipt"])->name("print_receipt");

// Router section
// Route::get("/Router/View/{routerid}",[Router::class,"getRouterInfor"]);
// Route::get("/Routers/Delete/{routerid}",[Router::class,"deleteRouter"]);
Route::get("/Router/Reboot/{routerid}", [Router_Cloud::class, "reboot"]);

// cloud router
Route::post("/new_cloud_router", [Router_Cloud::class, "save_cloud_router"])->name("newCloudRouter");
Route::get("/Router/View/{router_id}", [Router_Cloud::class, "view_router_details"])->name("view_router_cloud")->middleware(["checkAccount", "validated"]);
Route::get("/Router/Connect/{router_id}", [Router_Cloud::class, "connect_router"])->name("connect_router")->middleware(["checkAccount", "validated"]);
Route::post("/updateRouter", [Router_Cloud::class, "updateRouter"])->name("update_router");
Route::get("/Routers/Delete/{routerid}", [Router_Cloud::class, "deleteRouter"]);

// get the routers information
Route::get("/Routers", [Router_Cloud::class, 'getRouterData'])->name("my_routers")->middleware(["checkAccount", "validated"]);

// Sms section
Route::get("/sms", [Sms::class, "getSms"])->middleware(["checkAccount", "validated"]);
Route::get("/sms/View/{smsid}", [Sms::class, "getSMSData"])->middleware(["checkAccount", "validated"]);
Route::get("/sms/delete/{smsid}", [Sms::class, "delete"])->middleware(["checkAccount", "validated"]);
Route::get("/sms/compose", [Sms::class, "compose"])->middleware(["checkAccount", "validated"]);
Route::post("/sendsms", [Sms::class, "sendsms"]);
Route::get("/sms/system_sms", [Sms::class, "customsms"])->middleware(["checkAccount", "validated"]);
Route::post("/save_sms_content", [Sms::class, "save_sms_content"]);
Route::get("/sms_balance", [Sms::class, "sms_balance"])->middleware(["checkAccount", "validated"]);
Route::get("/sms/resend/{sms_id}", [Sms::class, "resend_sms"])->middleware(["checkAccount", "validated"]);
Route::post("/sendsms_routers", [Sms::class, "sendsms_routers"]);

// accounts and profile
Route::get("/Accounts", [admin::class, "getAdmin"])->middleware(["checkAccount", "validated"]);
Route::post("/changePasswordAdmin", [admin::class, "updatePassword"]);
Route::get("/Accounts/add", [admin::class, "addAdmin"])->middleware(["checkAccount", "validated"]);
Route::get("/Accounts/delete/{admin_id}", [admin::class, "delete_admin"])->name("delete_admin");
Route::post("/addAdministrator", [admin::class, "addAdministrator"]);
Route::get("/Admin/View/{admin_id}", [admin::class, "viewAdmin"])->middleware(["checkAccount", "validated"]);
Route::post("/updateAdministrator", [admin::class, "updateAdmin"]);
Route::post("/update_dp", [admin::class, "upload_dp"]);
Route::post("/update_company_dp", [admin::class, "update_company_dp"]);
Route::post("/update_admin", [admin::class, "update_admin"]);
Route::post("/update_delete_option", [admin::class, "update_delete_option"]);
Route::get("/delete_pp/{admin_id}", [admin::class, "delete_pp"]);
Route::get("/delete_pp_organization", [admin::class, "delete_pp_organization"]);
Route::post("/update_organization_profile", [admin::class, "update_organization_profile"]);


// routes for the clients information
Route::get("/ClientDashboard", [Clients_data::class, "getClientInfor"]);
Route::get("/Payment", [Clients_data::class, "getTransaction"]);
Route::get("/Payment/View/{paymentid}", [Clients_data::class, 'viewPayment']);
Route::view("/Payment/Confirm", "confirmPay");
Route::get("/Payment/mpesa/{mpesaid}", [Clients_data::class, "confirm_mpesa"]);
Route::view("/Credentials", "credential");
Route::post("/changePassword", [Clients_data::class, "change_password"]);
Route::get("/Payment/stkpush_init", [Transaction::class, "stkpush"]);
Route::post("/Payment/stkpush", [Transaction::class, "initiate_stk"]);

// get ip addresses
Route::get("/ipAddress/{routerid}", [Clients::class, "getIpaddresses"]);

// go and manage the billing sms
Route::get("/BillingSms/Manage", [billsms_manager::class, "getBilledClients"]);
// new sms client
Route::get("/BillingSms/New", [billsms_manager::class, "newClient"]);
// Register new client
Route::post("/register_new", [billsms_manager::class, "registerClient"]);
// VIEW AND UPDATE SMS CLIENT
Route::get("/BillingSms/ViewClient/{clientid}", [billsms_manager::class, "displayClient"]);
// update client sms
Route::post("/update_client_sms", [billsms_manager::class, "updateClient"]);
// delete client sms
Route::get("/delete_user_sms/{client_id}", [billsms_manager::class, "deleteClient"]);
// deactivate a client
Route::get("/deactivate_sms_client/{client_id}", [billsms_manager::class, "deactivateClient"]);
// activate a client
Route::get("/activate_sms_client/{client_id}", [billsms_manager::class, "activateClient"]);
// change sms balance
Route::post("/changeSmsBal", [billsms_manager::class, "changeSmsBal"]);
// view transactions for the transaction manager
Route::get("/BillingSms/Transactions", [billsms_manager::class, "viewTransaction"]);
// view transactions details
Route::get("/BillingSms/Transactions/View/{transaction_id}", [billsms_manager::class, "viewTransactionDetails"]);
// ASSIGNE TRANSACTION
Route::get("/BillingSms/Assign/Transaction/{transaction_id}/Client/{client_id}", [billsms_manager::class, "assignTransaction"]);
// confirm transfer of funnds
Route::get("/BillingSms/confirmTransfer/{client_id}/{transactionid}", [billsms_manager::class, "transferFunds"]);
Route::post("/renew_licence", [billsms_manager::class, "renew_Licence"]);
// manage the packages so that user can know how to pay
Route::get("/BillingSms/Packages", [billsms_manager::class, "myPackages"]);
// set ne package
Route::get("/BillingSms/NewPackage", [billsms_manager::class, "newPackage"]);
// register package
Route::post("/register_package", [billsms_manager::class, "registerPackage"]);
// register package
Route::get("/BillingSms/ViewPackage/{id}", [billsms_manager::class, "viewPackages"]);
// update_package
Route::post("/update_package", [billsms_manager::class, "updatePackage"]);
// delete packages
Route::get("/delete_package/{package_id}", [billsms_manager::class, "deletePackage"]);
// GET PACKAGE LSISTS
Route::get("/getpackages", [billsms_manager::class, "showPackages"]);

// create a new link to set up the router
Route::view("/Clients/NewRouterSetup", "RouterSetup")->middleware(["checkAccount", "validated"]);
Route::post("/connect_router", [Router::class, "test_router"]);
Route::post("/remove_interface_bridge", [Router::class, "remove_interface_bridge"]);
Route::get("/getbridge", [Router::class, "process_interfaces"]);
Route::post("/add_bridge", [Router::class, "add_bridge"]);
Route::post("/remove_bridge", [Router::class, "remove_bridge"]);
Route::post("/change_bridge", [Router::class, "change_bridge"]);
Route::post("/get_setting", [Router::class, "get_setting"]);
Route::post("/set_dynamic", [Router::class, "set_dynamic"]);
Route::post(("/set_static_access"), [Router::class, "set_static_access"]);
Route::post("/set_pppoe_assignment", [Router::class, "set_pppoe_assignment"]);
Route::post("/set_pool", [Router::class, "set_pool"]);
Route::get("/get_pools", [Router::class, "get_pools"]);
Route::post("/add_pppoe_profile", [Router::class, "add_pppoe_profile"]);
Route::get("/get_pppoe_server", [Router::class, "get_pppoe_server"]);
Route::post("/save_ppoe_server", [Router::class, "save_ppoe_server"]);
Route::post("/add_security", [Router::class, "add_security"]);
Route::get("/get_security_profile", [Router::class, "get_security_profile"]);
Route::post("/save_ssid", [Router::class, "save_ssid"]);
Route::post("/get_interface_supply", [Router::class, "get_interface_supply"]);
Route::post("/get_wireless", [Router::class, "get_wireless"]);
Route::get("/getconnection", [Router::class, "getconnection"]);
Route::post("/get_interface_config", [Router::class, "get_interface_config"]);
Route::post("/get_internet_access", [Router::class, "get_internet_access"]);
Route::post("/get_supply_method", [Router::class, "get_supply_method"]);
Route::post("/wireless_settings", [Router::class, "wireless_settings"]);

// statistics
Route::get("/Client-Statistics", [Clients::class, 'getClients_Statistics'])->middleware(["checkAccount", "validated"]);
Route::post("/Client-due-demographics", [Clients::class, 'clientsDemographics']);
Route::get("/Transactions/Statistics", [Transaction::class, 'transactionStatistics'])->middleware(["checkAccount", "validated"]);

// router logs
Route::get("/Router/writeLogs/{router_id}", [Router::class, "writeRouterLogs"]);
Route::get("/Router/Logs/{router_id}", [Router::class, "readLogs"]);

// reports
Route::get("/Clients/generateReports", [Clients::class, "generateReports"])->middleware(["checkAccount", "validated"]);
Route::get("/Transaction/generateReports", [Transaction::class, "generateReports"])->middleware(["checkAccount", "validated"]);
Route::get("/SMS/generateReports", [Sms::class, "generateReports"])->middleware(["checkAccount", "validated"]);

// expenses
Route::get("/Expenses", [Expenses::class, "getExpenses"])->middleware(["checkAccount", "validated"]);
Route::post("/Expense/Category/Add", [Expenses::class, "addExpenseCategory"]);
Route::get("/Expense/Delete/{expense_index}", [Expenses::class, "deleteExpense"]);
Route::post("/Expense/Add", [Expenses::class, "addExpense"]);
Route::post("/Expense/Update", [Expenses::class, "updateExpense"]);
Route::get("/Expense/View/{expense_id}", [Expenses::class, "viewExpense"])->middleware(["checkAccount", "validated"]);
Route::get("/Expense/DeleteRecords/{expense_id}", [Expenses::class, "deleteExpenseRecords"])->middleware(["checkAccount", "validated"]);
Route::get("/Expenses/Generate/Reports", [Expenses::class, "generateReports"])->middleware(["checkAccount", "validated"]);
Route::get("/Expense/Statistics", [Expenses::class, "expenseStatistics"])->middleware(["checkAccount", "validated"]);
Route::get("/Expenses/Generate/FinStats", [Expenses::class, "financeStats"])->middleware(["checkAccount", "validated"]);

// delete users
Route::post("/delete_clients", [Clients::class, "deleteClients"]);
Route::post("/send_sms_clients", [Clients::class, "sendSmsClients"]);
Route::get("/admin/deactivate/{admin_id}", [admin::class, "deactivateAdmin"]);

// bulk sms
Route::post("/Delete_bulk_sms", [Sms::class, "Delete_bulk_sms"]);
Route::post("/Resend_bulk_sms", [Sms::class, "Resend_bulk_sms"]);

Route::get("/SharedTables", [SharedTables::class, "openSharedTables"])->middleware(["checkAccount", "validated"]);
Route::view("/CreateShareTables", "createTable");
Route::post("/SaveTable", [SharedTables::class, "SaveTable"]);
Route::get("SharedTables/View/{table_id}/Name/{table_name}", [SharedTables::class, "getTable"])->middleware(["checkAccount", "validated"]);
Route::get("SharedTables/Edit/{table_id}/Name/{table_name}", [SharedTables::class, "editTable"])->middleware(["checkAccount", "validated"]);
Route::post("/UpdateTableCreated", [SharedTables::class, "UpdateTableCreated"]);
Route::get("/SharedTables/addRecord/{table_id}/Name/{table_name}", [SharedTables::class, "addRecords"])->middleware(["checkAccount", "validated"]);
Route::post("/SharedTables/AddRecords", [SharedTables::class, "saveRecord"]);
Route::get("/SharedTables/Edit/{table_id}/Name/{table_name}/Record/{record_no}", [SharedTables::class, "editRecord"])->middleware(["checkAccount", "validated"]);
Route::post("/SharedTables/UpdateRecords", [SharedTables::class, "UpdateRecords"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{table_name}", [SharedTables::class, "deleteTable"])->middleware(["checkAccount", "validated"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{link_table_name}/Record/{rows_id}", [SharedTables::class, "deleteRecord"])->middleware(["checkAccount", "validated"]);


// MPESA URL REGISTRATION
Route::post("/register_mpesa_url", [mpesa_api::class, "register_url"])->name("register_url");

// EXPORT CLIENT DATA
Route::post("/export_client_data", [Clients::class, "export_client_data"])->name("export_client_data");

// MANAGE CLIENT ROUTER
Route::get("/Router_Bridges/datatable/{router_id}", [Router_Cloud::class, 'get_router_bridge_information'])->name("get_router_bridge_information")->middleware(["checkAccount", "validated"]);
Route::get("/Router_Profile/datatable/{router_id}", [Router_Cloud::class, 'get_router_secret_information'])->name("get_router_secret_information")->middleware(["checkAccount", "validated"]);