<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords"
        content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/vendors/css/charts/chartist.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/app-lite.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/pages/dashboard-ecommerce.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <!-- END Custom CSS-->
</head>
@php
date_default_timezone_set('Africa/Nairobi');
    $privilleged = session("priviledges");
    $priviledges = ($privilleged);
    function showOption($priviledges,$name){
        if (isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return "";
                    }
                }
            }
        }
        return "hide";
    }
    function readOnly($priviledges,$name){
        if (isJson($priviledges)){
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->readonly) {
                        return "disabled";
                    }
                }
            }
        }
        return "";
    }
    // get the readonly value
    $readonly = readOnly($priviledges,"Account and Profile");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->

    <nav
        class="header-navbar navbar-expand-md navbar  navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light">
        <div class="navbar-wrapper">
            <div class="navbar-container content ">
                <div class="collapse navbar-collapse show" id="navbar-mobile">
                    <ul class="nav navbar-nav mr-auto float-left">
                        <li class="nav-item d-block d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                                href="#"><i class="ft-menu"></i></a></li>
                        <li class="nav-item dropdown navbar-search">
                            <span class="text-light">Hello, {{ session('Usernames') }}</span>
                        </li>
                    </ul>
                    @if (!Session::has('Usernames'))
                        @php
                            header('Location: ' . URL::to('/Login'), true, 302);
                            exit();
                        @endphp
                    @endif
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item"><a
                                class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <span class="avatar avatar-online"> <img style="width: 100px; height: 40px;"
                                        src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                        alt="avatar"><i></i> </span></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="arrow_box_right"><a class="dropdown-item" href="#"><span
                                            class="avatar avatar-online"><img style="width: 100px; height: 30px;"
                                                src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                                alt="avatar"><br><br><span
                                                class="user-name text-bold-700 ml-1">{{ session('Usernames') }}</span></span></a>
                                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/Accounts"><i
                                            class="ft-user"></i>Account & Settings</a>
                                    {{-- <a class="dropdown-item" href="#"><i class="ft-mail"></i> My Inbox</a><a class="dropdown-item" href="#"><i class="ft-check-square"></i> Task</a><a class="dropdown-item" href="#"><i class="ft-message-square"></i> Chats</a> --}}
                                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/Login"><i
                                            class="ft-power"></i> Logout</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true"
        data-img="/theme-assets/images/backgrounds/02.jpg">
        <div class="navbar-header" style="height: 120px">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75 text-center" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard">
                        <img class="w-100 mx-auto" height="100" alt="Your Logo Appear Here"
                            src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                    </a></li>
                <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
            </ul>
        </div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span
                            class="menu-title" data-i18n="">Dashboard</span></a>
                </li>
                <li class="{{showOption($priviledges,"My Clients")}} nav-item"><a href="/Clients"><i class="ft-users"></i><span class="menu-title"
                            data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} nav-item"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                    </ul>
                </li>
                <li class="{{showOption($priviledges,"My Routers")}} nav-item"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title"
                            data-i18n="">My Routers</span></a>
                </li>
                <li class="{{showOption($priviledges,"SMS")}} nav-item"><a href="/sms"><i class="ft-mail"></i><span class="menu-title"
                            data-i18n="">SMS</span></a>
                </li>
                <li class="{{showOption($priviledges,"Account and Profile")}} active"><a href="/Accounts"><i class="ft-lock"></i><span
                            class="menu-title" data-i18n="">Account and Profile</span></a>
                </li>
            </ul>
        </div>
        <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
        <div class="navigation-background">
        </div>
    </div>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">New Client</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Clients">Billing SMS Manager</a>
                                </li>
                                <li class="breadcrumb-item"> New Client
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">New Client</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <a href="/BillingSms/Manage" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                    to list</a>
                                <div class="card-body">
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (session('data_error'))
                                        <p class="text-danger">{{ session('data_error') }}</p>
                                    @endif
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-9">
                                            <p class="card-text">- Make changes accordingly!. <br>- When a user is inactive they wont be able to send messages with the system
                                            <br>- When a licence is renewed a new licence number is generated and the existing licence will be considered invalid.
                                            <br>- When a licence is edited it enables you to lenghthen or shorten the existing licence number expiration date.
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p id="prompt_delete" class="btn btn-secondary float-right btn-sm"><i class="fas fa-trash"></i> Delete</p>
                                            <div class="container d-none" id="prompt_del_window">
                                                <p class="text-primary" ><strong>Are you sure you want to permanently delete this client?</strong></p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <a href="/delete_user_sms/{{$client_id}}" class="btn btn-danger btn-sm" >Yes</a>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="btn btn-secondary btn-sm" id="delet_user_no">No</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 card shadow-lg">
                                            {{-- client active status --}}
                                            @if ($user_data->status == 1)
                                                <div class="row my-1 border-bottom border-light p-1">
                                                    <div class="col-sm-6"><strong>User status:</strong></div>
                                                    <div class="col-sm-6"><a
                                                            href="/deactivate_sms_client/{{ $user_data->client_id }}"
                                                            class="btn btn-sm btn-danger">De-Activate</a><p class="text-success d-none"><b>De-activated</b></p></div>
                                                </div>
                                            @else
                                                <div class="row my-1 border-bottom border-light p-1">
                                                    <div class="col-sm-6"><strong>User status:</strong></div>
                                                    <div class="col-sm-6"><a
                                                            href="/activate_sms_client/{{ $user_data->client_id }}"
                                                            class="btn btn-sm btn-success">Activate</a><p class="text-danger d-none"><b>De-activated</b></p></div>
                                                </div>
                                            @endif
                                            <div class="row my-1 border-bottom border-light py-1">
                                                <div class="col-sm-6"><strong>SMS Balance: </strong><span class="btn btn-infor text-xxs text-primary" style="width: fit-content;" id="edit_sms_balance"><i class="fas fa-pen"></i> Edit</span></div>
                                                <div class="col-sm-6">{{ $user_data->sms_balance }} SMS(es)
                                                </div>
                                            </div>
                                            <div id="change_SMS_balance" class="w-100 d-none">
                                                <hr class="mt-0">
                                                <form action="/changeSmsBal" method="post" class="form-control-group">
                                                    @csrf
                                                    <h6 class="text-center" >Change SMS balance</h6>
                                                    <input type="hidden" name="clients_id"
                                                        value="{{ $user_data->client_id }}">
                                                    <label for="sms_balances" class="form-control-label" id="">New SMS Balance</label>
                                                    <input type="number" required name="sms_balances" id="sms_balances" class="form-control" placeholder="New SMS balance">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="submit" class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-secondary my-1" type="button" id="cancel_sns_updates">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <hr>
                                            </div>
                                            <div class="row border-bottom border-light py-1">
                                                <div class="col-sm-6"><strong>Licence Expiration:</strong><span class="btn btn-infor text-xxs text-primary" style="width: fit-content;" id="edit_licence_expiration"><i class="fas fa-pen"></i> Edit</span></div>
                                                <?php
                                                    $licence_expiry = $user_data->licence_expiry ? date("D, M dS Y",strtotime($user_data->licence_expiry)):"Licence Not Assigned";
                                                ?>
                                                <div class="col-sm-6">{{ $licence_expiry}} <br> { {{$user_data->licence_number}} }
                                                </div>
                                            </div>
                                            <div id="renew_licence" class="w-100 py-1 d-none">
                                                <hr class="mt-0">
                                                <form action="/renew_licence" method="post" class="form-control-group">
                                                    @csrf
                                                    <h6 class="text-center" >Renew Licence</h6>
                                                    <input type="hidden" name="clients_id"
                                                        value="{{ $user_data->client_id }}">
                                                    <label for="lc_actions" class="form-control-label">Select Action</label>
                                                    <select required name="lc_actions" id="lc_actions" class="form-control">
                                                        <option value="" hidden>Select an Action</option>
                                                        <option value="renew">Renew Licence</option>
                                                        <option value="extend">Edit Licence</option>
                                                    </select>
                                                    <label for="lc_expiration_date" class="form-control-label" id="">New Licence Expiration Date</label>
                                                    <input type="date" required name="lc_expiration_date" id="lc_expiration_date" class="form-control">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="submit" class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-secondary my-1" type="button" id="cancel_lc_edit">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                    <h5>Client Details</h5>
                                    <hr>
                                    <form action="/update_client_sms" method="post">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{$client_id ?? ''}}">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label for="client_name" class="form-control-label">Clients / Company
                                                    Fullname <br>{ {{$user_data->client_name}} }</label>
                                                <input type="text" name="client_name" id="client_name"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Clients Fullname .." required
                                                    value="{{ $user_data->client_name ? $user_data->client_name : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_address" class="form-control-label">Clients
                                                    Address { {{$user_data->client_location}} }</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="eg. Kiambu or Mombasa" required
                                                    value="{{ $user_data->client_location ? $user_data->client_location : '' }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="client_phone" class="form-control-label">Phone
                                                    Number { {{$user_data->phone_number}} }</label>
                                                <input type="number" name="client_phone" id="client_phone"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client valid phone number" required
                                                    value="{{ $user_data->phone_number ? $user_data->phone_number : '' }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="package_list" class="form-control-label">Clients Package List: { {{$package_name}} }
                                                    <span class="invisible" id="interface_load"><i
                                                        class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                    <p id="packages_lists"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="client_email" class="form-control-label">Clients
                                                    Email <br> { {{$user_data->email}} }</label>
                                                <input type="text" name="client_email" id="client_email"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Ex. client@email.com"
                                                    value="{{ $user_data->email ? $user_data->email : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_acc_number" class="form-control-label">Clients
                                                    Account Number </label>
                                                <input type="text" name="client_acc_number" id="client_acc_number"
                                                    class="form-control rounded-lg p-1 {{ session('account_number_present') ? 'border border-danger' : '' }}"
                                                    placeholder="Client account no ex HYP001" required readonly
                                                    value="{{ $user_data->account_number ? $user_data->account_number : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_sms_rates" class="form-control-label">Clients
                                                    SMS Buy Rates { {{ $user_data->sms_rate ? $user_data->sms_rate : '' }} } </label>
                                                <input type="number" step="0.05" name="client_sms_rates" id="client_sms_rates"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Ex. Kes 0.7" required
                                                    value="{{ $user_data->sms_rate ? $user_data->sms_rate : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="licence_acc_no" class="form-control-label">Clients
                                                    Licence Acc No Number </label>
                                                <input type="text" name="licence_acc_no" id="licence_acc_no"
                                                    class="form-control rounded-lg p-1 {{ session('account_number_present') ? 'border border-danger' : '' }}"
                                                    placeholder="Client account no ex HYP001" required readonly
                                                    value="{{ $user_data->licence_acc_number ? $user_data->licence_acc_number : '' }}">
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-12">
                                                <label for="comments"
                                                    class="form-control-label">Comments:</label>
                                                <textarea name="comments" id="comments" cols="30" rows="3" class="form-control"
                                                    placeholder="Comment here">{{ $user_data->comments ? $user_data->comments : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-6">
                                                <label for="client_username" class="form-control-label">Client Username
                                                    { {{ $user_data->username ? $user_data->username : '' }} }
                                                </label>
                                                <input type="text" name="client_username" id="client_username"
                                                    class="form-control {{ session('client_username_present') ? 'border border-danger' : '' }}"
                                                    value="{{ $user_data->username ? $user_data->username : '' }}"
                                                    required readonly placeholder="Client`s Username">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_password" class="form-control-label">Client`s
                                                    Password { {{ $user_data->password ? $user_data->password : '' }} }</label>
                                                <input type="password" name="client_password" id="client_password"
                                                    class="form-control"
                                                    value="{{ $user_data->password ? $user_data->password : '' }}"
                                                    required placeholder="Client`s Password">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-success text-dark" {{$readonly}} type="submit"><i
                                                        class="ft-upload"></i> Update User</button>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
    <footer style="margin-bottom: 0% !important" class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span
                class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits
                Enterprises</span>
            <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>


    {{-- START OF THE ROUTER DATA RETRIEVAL --}}
    <script>
        var client_accounts = @json($client_accounts ?? '');
        var client_username = @json($client_username ?? '');
    </script>

    <script src="/theme-assets/js/core/newbillsmsclient.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        var prompt_delete = document.getElementById("prompt_delete");
        prompt_delete.onclick = function () {
            var prompt_del_window = document.getElementById("prompt_del_window");
            prompt_del_window.classList.remove("d-none");
        }
        var delet_user_no = document.getElementById("delet_user_no");
        delet_user_no.onclick = function () {
            var prompt_del_window = document.getElementById("prompt_del_window");
            prompt_del_window.classList.add("d-none");
        }
        // display the edit window for the sms balances
        var edit_sms_balance = document.getElementById("edit_sms_balance");
        edit_sms_balance.onclick = function () {
            var change_SMS_balance = document.getElementById("change_SMS_balance");
            change_SMS_balance.classList.remove("d-none");
        }
        var cancel_sns_updates = document.getElementById("cancel_sns_updates");
        cancel_sns_updates.onclick = function () {
            var change_SMS_balance = document.getElementById("change_SMS_balance");
            change_SMS_balance.classList.add("d-none");
        }
        var edit_licence_expiration = document.getElementById("edit_licence_expiration");
        edit_licence_expiration.onclick = function () {
            var renew_licence = document.getElementById("renew_licence");
            renew_licence.classList.remove("d-none");
        }
        var cancel_lc_edit = document.getElementById("cancel_lc_edit");
        cancel_lc_edit.onclick = function () {
            var renew_licence = document.getElementById("renew_licence");
            renew_licence.classList.add("d-none");
        }
    </script>
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
</body>

</html>
