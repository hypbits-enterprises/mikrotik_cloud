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
    <title>Hypbits - View Admin Details</title>
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
<style>
    .hide{
        display: none;
    }
</style>
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
        return "d-none";
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
                        <li>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="arrow_box_right"><a class="dropdown-item" href="#"><i
                                            class="ft-book"></i> Read Notices</a><a class="dropdown-item"
                                        href="#"><i class="ft-check-square"></i> Mark all Read </a></div>
                            </div>
                        </li>
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
                <li class="{{(showOption($priviledges,"Transactions") == "d-none" && showOption($priviledges,"Expenses") == "d-none") ? "d-none" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
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
                    <h3 class="content-header-title">Add Administrator</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Clients">Account And Profile</a>
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
                                <h4 class="card-title">Account and Profile</h4>
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
                                <div class="card-body">
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (session('network_presence'))
                                        <p class="text-danger">{{ session('network_presence') }}</p>
                                    @endif
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/Accounts/add" class="btn btn-infor"><i class="ft-arrow-left"></i>
                                                Back to list</a>
                                        </div>
                                        <div class="col-md-6">
                                            <button id="delete_user" class="btn btn-danger text-lg float-right"><i class="ft-trash-2"> Delete</i></button>
                                            <div class="container d-none mt-4" id="prompt_del_window">
                                                <p class="text-secondary"><strong>Are you sure you want to permanently delete this user?</strong></p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <a href="{{url()->route("delete_admin",[$admin_data[0]->admin_id])}}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h6><strong>Update Administrator</strong></h6>
                                    <p class="card-text">Fill all fields to add the Administrator.</p>
                                    <form action="/updateAdministrator" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="admin_name" class="form-control-label">Fullname</label>
                                                <input type="text" name="admin_name" id="admin_name"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $admin_data[0]->admin_fullname }}"
                                                    placeholder="Admin Fullname .." required>
                                                <input type="hidden" name="admin_id"
                                                    value="{{ $admin_data[0]->admin_id }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_address" class="form-control-label">Contacts</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    value="{{ $admin_data[0]->contacts }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator contacts" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="admin_username" class="form-control-label">Username <span
                                                        class="text-danger" id="error_acc_no"></span></label>
                                                <input type="text" name="admin_username" id="admin_username"
                                                    value="{{ $admin_data[0]->admin_username }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator Username" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Password</label>
                                                <input type="password" name="admin_password" id="admin_password"
                                                    value="{{ $admin_data[0]->admin_password }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Status</label>
                                                <select class="form-control" name="status" id="status" required>
                                                    <option value="" hidden>Select an option</option>
                                                    <option {{ $admin_data[0]->activated == "0" ? "selected" : ""}} value="0">In-Active</option>
                                                    <option {{$admin_data[0]->activated == "1" ? "selected" : ""}} value="1">Active</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="privileges" id="privileged" value="{{$admin_data[0]->priviledges}}">
                                        {{-- <input type="hidden" name="privileges" id="privileged"> --}}
                                        <div class="container my-2">
                                            @php
                                                // check json structure
                                                function isJson_report($string) {
                                                    return ((is_string($string) &&
                                                            (is_object(json_decode($string)) ||
                                                            is_array(json_decode($string))))) ? true : false;
                                                }
                                                $privileged = [];
                                                if (isJson_report($admin_data[0]->priviledges)) {
                                                    $privileged = json_decode($admin_data[0]->priviledges);
                                                }
                                                function getChecked($privileged,$name,$option){
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->option == $name) {
                                                            if ($option == "view") {
                                                                if ($privileged[$ind]->view) {
                                                                    return "checked";
                                                                }
                                                            }
                                                            if ($option == "readonly") {
                                                                if ($privileged[$ind]->readonly) {
                                                                    return "checked";
                                                                }
                                                            }
                                                        }
                                                    }
                                                    return "";
                                                }
                                                function checkAllView($privileged){
                                                    $counter = 0;
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->view) {
                                                            $counter++;
                                                        }
                                                    }
                                                    if ($counter == count($privileged)) {
                                                        return "checked";
                                                    }else{
                                                        return "";
                                                    }
                                                }
                                                function checkAllReadonly($privileged){
                                                    $counter = 0;
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->readonly) {
                                                            $counter++;
                                                        }
                                                    }
                                                    if ($counter == count($privileged)) {
                                                        return "checked";
                                                    }else{
                                                        return "";
                                                    }
                                                }
                                            @endphp
                                            <h6 class="text-center"><u>Assign Administrator Privileges</u></h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Menu</th>
                                                            <th>View <input type="checkbox" {{checkAllView($privileged)}} id="all_view"></th>
                                                            <th>Read-only <input {{checkAllReadonly($privileged)}} type="checkbox" id="all_readonly"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td><label for="my_clients_option" class="form-label"><b>My Clients</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"My Clients","view")}}  type="checkbox" id="my_clients_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"My Clients","readonly")}} type="checkbox" id="my_clients_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th rowspan="3" scope="row">2</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>Accounts</b></label></td>
                                                            <td><input class="all_view" type="checkbox" id="accounts_option_view"></td>
                                                            <td><input class="all_readonly" type="checkbox" id="accounts_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><label for="my_clients_option" class="form-label"><b><i>Transactions</i></b></label></td>
                                                            <td><input class="all_view account_options" {{getChecked($privileged,"Transactions","view")}} type="checkbox" id="transactions_option_view"></td>
                                                            <td><input class="all_readonly account_options_2" {{getChecked($privileged,"Transactions","readonly")}} type="checkbox" id="transactions_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><label for="my_clients_option" class="form-label"><b><i>Expenses</i></b></label></td>
                                                            <td><input class="all_view account_options" {{getChecked($privileged,"Expenses","view")}} type="checkbox" id="expenses_option_view"></td>
                                                            <td><input class="all_readonly account_options_2"  {{getChecked($privileged,"Expenses","readonly")}} type="checkbox" id="expenses_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>My Routers</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"My Routers","view")}} type="checkbox" id="my_routers_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"My Routers","readonly")}} type="checkbox" id="my_routers_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>SMS</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"SMS","view")}} type="checkbox" id="sms_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"SMS","readonly")}} type="checkbox" id="sms_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>Account & Profile</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"Account and Profile","view")}} type="checkbox" id="account_profile_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"Account and Profile","readonly")}} type="checkbox" id="account_profile_option_readonly"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-primary" {{$readonly}} type="submit"><i
                                                        class="ft-upload"></i> Update Administrator</button>
                                            </div>
                                        </div>
                                        <hr>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end view administrators --}}
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com/sims/"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>

    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/view_admin.js"></script>
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
