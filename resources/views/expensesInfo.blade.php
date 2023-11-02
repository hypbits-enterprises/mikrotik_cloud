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
    <title>Hypbits - Expenses</title>
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
    $readonly = readOnly($priviledges,"Expenses");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<style>
    .hide{
        display: none;
    }
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        display: inline-block;
        width: 100%
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }

</style>

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


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true"
        data-img="/theme-assets/images/backgrounds/02.jpg">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard"><img class="brand-logo w-100 mb-1 " alt="Chameleon admin logo"
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
                <li class="nav-item {{showOption($priviledges,"My Clients")}}"><a href="/Clients"><i class="ft-users"></i><span class="menu-title"
                            data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} active has-sub open"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} active"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                    </ul>
                </li>
                <li class="{{showOption($priviledges,"My Routers")}} nav-item"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title"
                            data-i18n="">My Routers</span></a>
                </li>
                <li class="{{showOption($priviledges,"SMS")}} nav-item"><a href="/sms"><i class="ft-mail"></i><span class="menu-title"
                            data-i18n="">SMS</span></a>
                </li>
                <li class="{{showOption($priviledges,"Account and Profile")}} nav-item"><a href="/Accounts"><i class="ft-lock"></i><span
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
                    <h3 class="content-header-title">Expenses</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#"><span
                                    class="menu-title" data-i18n="">Accounts</span></a>
                                </li>
                                <li class="breadcrumb-item active">Expenses
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
                                <h4 class="card-title">Expenses Table</h4>
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
                            <div class="card-header">
                                <p>- View, update or delete the expense entry!</p>
                                <div class="row">
                                    <div class="col-md-8">
                                        <a href="/Expenses" class="btn btn-white text-primary"><i class="fas fa-arrow-left"></i> Back to List</a>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" {{$readonly}} id="delete_expense"><i class="fas-fa-trash"></i> Delete</button>
                                        <div class="container hide border border-primary rounded p-1 my-2" id="delete_expense_window">
                                            <p><b class="text-primary">Are you sure you want to delete "{{$expense_data->name}}" record permanently!</b></p>
                                            <div class="row">
                                                <div class="col-md-6"><a href="/Expense/DeleteRecords/{{$expense_data->id}}" class="btn btn-danger">Yes</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="p-0 m-0">
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <p class="card-text">In this table below Expenses information can be
                                        displayed.</p>
                                        @if (session('expense_success'))
                                            <p class="text-success">{{ session('expense_success') }}</p>
                                        @endif
                                        @if (session('expense_error'))
                                            <p class="text-danger">{{ session('expense_error') }}</p>
                                        @endif
                                    <p>
                                    <form class="row" method="POST" action="/Expense/Update">
                                        @csrf
                                        <div class="col-md-4">
                                            <label for="expense_name" class="form-label">Expense Name</label>
                                            <input type="hidden" name="expense_id" value="{{$expense_data->id}}">
                                            <input type="text" name="expense_name" id="expense_name" value="{{$expense_data->name}}" class="form-control" placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_category" class="form-label">Expense Category <b data-toggle="tooltip" title="This is the category that this expense will lie, example can be daily-expense or labour." class="text-dark"><i class="ft-info"></i></b></label>
                                            <select name="expense_category" id="expense_category" class="form-control" required>
                                                <option value="" hidden>Select option</option>
                                                @if (count($exp_category) > 0)
                                                    @for ($i = 0; $i < count($exp_category); $i++)
                                                        <option {{$expense_data->category == $exp_category[$i]->name ? "selected" : ""}} value="{{$exp_category[$i]->name}}">{{$exp_category[$i]->name}}</option>
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_date" class="form-label">Date <b data-toggle="tooltip" title="This is the date you incurred the cost, by default it takes today." class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="date" name="expense_date" value="{{date("Y-m-d",strtotime($expense_data->date_recorded))}}" max="{{date("Y-m-d")}}" id="expense_date" class="form-control" placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_quantity" class="form-label">Quantity <b data-toggle="tooltip" title="This is the number of commodities bought. e.g.,10 - (Routers)" class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="number" name="expense_quantity" value="{{$expense_data->unit_amount}}" id="expense_quantity" step="0.5" value="0" class="form-control" placeholder="Example: 10 (Mikrotik Routers)">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit_price" class="form-label">Unit Price <b data-toggle="tooltip" title="This is the price of one commodity bought. e.g.,One router costs Kes 1000" class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="number" name="expense_unit_price" value="{{$expense_data->unit_price}}" step="0.005" id="expense_unit_price" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_total_price" class="form-label">Total Price <b data-toggle="tooltip" title="This is the calculated total price of all the commodities bought. e.g.,10 - (Routers) cost Kes 10,000: This field is readonly!" class="text-dark"><i class="ft-info"></i></b> <span class="text-danger">{Read-only!}</span></label>
                                            <input type="number" readonly name="expense_total_price" value="{{$expense_data->total_price}}" id="expense_total_price" class="form-control" placeholder="Total Price" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit" class="form-label">Unit Of Measurement <b data-toggle="tooltip" title="This is the unit of measurement, If you inccured a cost of a commodity that has weight and its charged per certain weight you would say (10 Kgs of Sugar) Kgs being the unit of measurement." class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="text" name="expense_unit" value="{{$expense_data->unit_of_measure}}" id="expense_unit" class="form-control" placeholder="Kgs, Litres">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="expense_description" class="form-label">Description</label>
                                            <textarea name="expense_description" id="expense_description" cols="30" value="" rows="5" class="form-control" placeholder="Describe Expense here..">{{$expense_data->description}}</textarea>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button class="btn btn-block btn-primary" {{$readonly}} type="submit">Update Expense</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    {{-- <x-footerRouteAdmin > --}}
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    {{--  --}}
    <!-- END PAGE LEVEL JS-->


    <script src="/theme-assets/js/core/expenseview.js"></script>

    {{-- script to create tables in the transaction table --}}
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
