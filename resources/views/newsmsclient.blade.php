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
    <title>Hypbits - New Billing SMS client</title>
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
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard"><img class="brand-logo w-100 mb-1 " alt="Company Logo"
                            src="/theme-assets/images/logo.jpeg" />
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
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i>Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} nav-item"><a href="/Expenses"><i class="ft-bar-chart-2"></i>Expenses</a></li>
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
                                    <p class="card-text">Fill all the fields to add the client.</p>
                                    <form action="/register_new" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="checkbox" name="send_sms" id="send_sms" checked>
                                                <label for="send_sms" class="form-control-label text-primary"
                                                    style="font-weight: 800;cursor: pointer;">Send Welcome SMS</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label for="client_name" class="form-control-label">Clients / Company
                                                    Fullname</label>
                                                <input type="text" name="client_name" id="client_name"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Clients Fullname .." required
                                                    value="{{ session('client_name') ? session('client_name') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_address" class="form-control-label">Clients
                                                    Address</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="eg. Kiambu or Mombasa" required
                                                    value="{{ session('client_address') ? session('client_address') : '' }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="client_phone" class="form-control-label">Clients Phone
                                                    number</label>
                                                <input type="number" name="client_phone" id="client_phone"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client valid phone number" required
                                                    value="{{ session('client_phone') ? session('client_phone') : '' }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label for="package_list" class="form-control-label">Clients Package List: 
                                                    <span class="invisible" id="interface_load"><i
                                                        class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                <p id="packages_lists"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="client_email" class="form-control-label">Clients
                                                    Email</label>
                                                <input type="text" name="client_email" id="client_email"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Ex. client@email.com"
                                                    value="{{ session('client_email') ? session('client_email') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_acc_number" class="form-control-label">Clients SMS
                                                    Acc No. {<span
                                                        class="primary">{{ $client_accounts[0] ?? '' }}</span>}
                                                    <span class="text-danger"
                                                        id="error_acc_no">{{ session('account_number_present') ? 'Account number in use!' : '' }}</span></label>
                                                <input type="text" name="client_acc_number" id="client_acc_number"
                                                    class="form-control rounded-lg p-1 {{ session('account_number_present') ? 'border border-danger' : '' }}"
                                                    placeholder="ex. HSMS101" required
                                                    value="{{ session('client_acc_number') ? session('client_acc_number') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="client_sms_rates" class="form-control-label">Clients
                                                    SMS Buy Rates</label>
                                                <input type="number" step="0.05" name="client_sms_rates" id="client_sms_rates"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Ex. Kes 0.7" required
                                                    value="{{ session('client_sms_rates') ? session('client_sms_rates') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="licence_acc_no" class="form-control-label">Clients
                                                    Licence Acc {{ $client_lc_acc[0] ?? '' }} <span class="text-danger"
                                                    id="error_lc_acc_no">{{ session('account_number_present') ? 'Account number in use!' : '' }}</span></label>
                                                <input type="text" step="0.1" name="licence_acc_no" id="licence_acc_no"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Ex. HLC101" required
                                                    value="{{ session('licence_acc_no') ? session('licence_acc_no') : '' }}">
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-12">
                                                <label for="comments"
                                                    class="form-control-label">Comments:</label>
                                                <textarea name="comments" id="comments" cols="30" rows="3" class="form-control"
                                                    placeholder="Comment here">{{ session('comments') ? session('comments') : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-6">
                                                <label for="client_username" class="form-control-label">Client Username
                                                    <span class="text-danger"
                                                        id="err_username">{{ session('client_username_present') ? 'Username provided is present' : '' }}</span>
                                                </label>
                                                <input type="text" name="client_username" id="client_username"
                                                    class="form-control {{ session('client_username_present') ? 'border border-danger' : '' }}"
                                                    value="{{ session('client_username') ? session('client_username') : '' }}"
                                                    required placeholder="Client`s Username">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_password" class="form-control-label">Client`s
                                                    Password</label>
                                                <input type="password" name="client_password" id="client_password"
                                                    class="form-control"
                                                    value="{{ session('client_password') ? session('client_password') : '' }}"
                                                    required placeholder="Client`s Password">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-success text-dark" {{$readonly}} type="submit"><i
                                                        class="ft-plus"></i> Add User</button>
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com/sims/"
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
        var client_lc_acc = @json($client_lc_acc ?? '');
    </script>
    {{-- <script>
        // check if the field is pasted
        function pasted(e, id) {
            var clipboardData, pastedData;
            // console.log(id);
            // Stop data actually being pasted into div
            e.stopPropagation();
            e.preventDefault();

            // Get pasted data via clipboard API
            clipboardData = e.clipboardData || window.clipboardData;
            pastedData = clipboardData.getData('Text');

            // Do whatever with pasteddata
            // go for character by character and take only characters that are of cetain type
            // alert(pastedData);
            var data_accept = "";
            var strlen = pastedData.length;
            for (let index = 0; index < strlen; index++) {
                var crcode = pastedData.charCodeAt(index);
                if (crcode > 31 && (crcode < 48 || crcode > 57)) {
                    if (crcode == 45 || crcode == 44 || crcode == 46) {
                        data_accept += pastedData.charAt(index);
                    }
                } else {
                    data_accept += pastedData.charAt(index);
                }
            }
            document.getElementById("location_coordinates").value = data_accept;
        }
    </script> --}}

    <script src="/theme-assets/js/core/newbillsmsclient.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
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
