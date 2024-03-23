<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - SMS</title>
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
    $readonly = readOnly($priviledges,"SMS");

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
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

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


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true" data-img="/theme-assets/images/backgrounds/02.jpg">
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
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span class="menu-title" data-i18n="">Dashboard</span></a>
                </li>
                <li class="{{showOption($priviledges,"My Clients")}} nav-item"><a href="/Clients"><i class="ft-users"></i><span class="menu-title" data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} nav-item"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                    </ul>
                </li>
                <li class="{{showOption($priviledges,"My Routers")}} nav-item"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title" data-i18n="">My Routers</span></a>
                </li>
                <li class="{{showOption($priviledges,"SMS")}} active"><a href="/sms"><i class="ft-mail"></i><span class="menu-title" data-i18n="">SMS</span></a>
                </li>
                <li class="{{showOption($priviledges,"Account and Profile")}} nav-item"><a href="/Accounts"><i class="ft-lock"></i><span class="menu-title" data-i18n="">Account and Profile</span></a>
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
                    <h3 class="content-header-title">SMS</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">SMS
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
                                <h4 class="card-title">SMS Table</h4>
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
                                    <div class="container border border-secondary rounded shadow-sm w-100 m-0 my-2 p-1">
                                        <h5 class="card-title">SMS Statistics</h5>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <span>Sms sent today: <span class="text-primary">{{$sms_count}}</span></span><br>
                                                <span>Sms Sent Last week: <span class="text-primary">{{$last_week}}</span></span><br>
                                                <span>Total SMS Sent: <span class="text-primary">{{$total_sms}}</span></span><br>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" id="show_sms_balance_btn" class="btn btn-primary btn-sm">Show Balance</button>
                                                <p>Sms Balance: <span></span>: <span id="show_sms_balance">0 SMS</span><small class="text-primary d-none" id="show_sms_loader"> Loading...</small></p>
                                            </div>
                                        </div>
                                    </div>
                                    @if(session('success_sms'))
                                        <p class='text-success'>{{session('success_sms')}}</p>
                                    @endif
                                    @if(session('error_sms'))
                                        <p class='text-danger'>{{session('error_sms')}}</p>
                                    @endif
                                    <div class="w-20">
                                        <a href="/sms/system_sms" class="btn btn-secondary text-bolder {{$readonly}}">Customize SMS</a>
                                        <span data-toggle="tooltip" title="SMS Reports" class="btn btn-info" id="sms_reports_btn"><i class="ft-file-text"></i></span>
                                    </div>
                                    <div class="row  my-2">
                                        <div class="col-md-12 border border-primary rounded p-1 hide" id="show_generate_reports_window">
                                            <h6 class="text-center">Generate Reports</h6>
                                            <form action="/SMS/generateReports" target="_blank" method="GET">
                                                {{-- @csrf --}}
                                                <div class="row">
                                                    <div class="col-md-3 " id="date_option">
                                                        <label for="sms_date_option" class="form-label">Select SMS sent date:</label>
                                                        <select name="sms_date_option" id="sms_date_option" class="form-control">
                                                            <option value="" hidden>Select an Option</option>
                                                            <option value="select date">Select a Date</option>
                                                            <option id="default_reg_date" selected value="all dates">All Dates</option>
                                                            <option value="between dates">Between Dates</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_from_date_win">
                                                        <label for="from_select_date" class="form-label">From:</label>
                                                        <input type="date" name="from_select_date" id="from_select_date" value="<?php echo date("Y-m-d",strtotime("-7 days"));?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_to_date_win">
                                                        <label for="to_select_date" class="form-label">To:</label>
                                                        <input type="date" name="to_select_date" id="to_select_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_date_win">
                                                        <label for="select_registration_date" class="form-label">Select Date:</label>
                                                        <input type="date" name="select_registration_date" id="select_registration_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 " id="select_router_window">
                                                        <label for="select_user_option" class="form-label">Select User Options:</label>
                                                        <select name="select_user_option" id="select_user_option" class="form-control">
                                                            <option value="" hidden>Select Router</option>
                                                            <option selected value="All">All</option>
                                                            <option value="specific_user">Specific User by Account No</option>
                                                            <option value="specific_user_phone">Specific User by Phone No</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 hide" id="client_status_opt">
                                                        <label for="myInput" class="form-label">Type the Client Name, Account Number or Phone Number <small class="text-primary">{Populate Acc No.}</small> </label><div class="autocomplete">
                                                        <input id="myInput" type="text" class="form-control"
                                                            name="client_account"
                                                            placeholder="Phone number, Account Number, Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 hide" id="client_status_phone_no">
                                                        <label for="myInput2" class="form-label">Type the Client Name, Account Number or Phone Number <small class="text-primary">{Populate Phone No.}</small> </label><div class="autocomplete">
                                                        <input id="myInput2" type="text" class="form-control"
                                                            name="client_phone"
                                                            placeholder="Phone number, Account Number, Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="contain_text_option" class="form-label">Select Text Option:</label>
                                                        <select name="contain_text_option" id="contain_text_option" class="form-control">
                                                            <option value="" hidden>Select Text Option</option>
                                                            <option selected value="All">All</option>
                                                            <option value="text_containing">Display Text Containing</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 hide" id="text_containing_window">
                                                        <label for="text_keyword" class="form-label">Type the Message keyword here</label>
                                                        <input type="text" name="text_keyword" id="text_keyword" class="form-control" placeholder="Text containing">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button class="btn btn-primary mt-2" type="submit"><i class="ft-settings"></i> Generate Reports</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="card-text">In this table all sms sent by the system are displayed.</p>
                                    <p><span class="text-bold-600">SMS Table:</span></p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey" class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                        <div class="col-md-6">
                                            <a href="/sms/compose" class="btn btn-info text-bolder float-right {{$readonly}}"><i class="ft-plus"> Write Message</i></a>
                                        </div>
                                    </div>
                                    <div class="container border border-secondary rounded p-1 hide" id="action_for_selected_window">
                                        <h6 class="text-center">Action for Selected SMS</h6>
                                        <div class="row">
                                            <div class="col-md-3 border-right border-secondary">
                                                <label for="select_all_clients" class="form-label">Select All</label>
                                                <input type="checkbox" name="select_all_clients" id="select_all_clients">
                                                <br>
                                                <span id="client_select_counts" class="text-info">0 SMS(s) Selected</span>
                                            </div>
                                            <form action="/Delete_bulk_sms" method="POST" class="col-md-3 border-right border-secondary my-1">
                                                @csrf
                                                <input type="hidden" name="hold_user_id_data" id="hold_user_id_data">
                                                <button class="btn btn-sm btn-danger" {{$readonly}} id="delete_clients_id" type="button"><i class="ft-trash"></i> Delete</button>
                                                <div class="container hide" id="delete_clients_window">
                                                    <p><b>Are you sure you want to delete <span id="delete_number_clients">0</span> SMS(s)</b>?</p>
                                                    <button class="btn btn-danger" {{$readonly}} type="submit">Yes</button>
                                                    <button class="btn btn-secondary" id="no_dont_delete_selected" type="button">No</button>
                                                </div>
                                            </form>
                                            <form class="col-md-3" action="/Resend_bulk_sms" method="POST" class="col-md-3 my-1">
                                                @csrf
                                                <input type="hidden" name="hold_user_id_data" id="hold_user_id_data_2">
                                                <button class="btn btn-sm btn-info" {{$readonly}} type="submit"><i class="fa-solid fa-paper-plane"></i> Re-send SMS</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="table-responsive"  id="transDataReciever">
                                        <div class="container text-center my-2">
                                            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                        </div>
                                        {{-- <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date Sent</th>
                                                    <th>Message Body</th>
                                                    <th>Message Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">
                                                        <span  class="d-flex flex-row align-items-center">
                                                            <span>1.</span>
                                                            <i class="ft-refresh-ccw"></i>
                                                        </span>
                                                    </th>
                                                    <td>22nd June 2022 <span class="badge badge-success"> </span><br><small>OWEN MALINGU</small></td>
                                                    <td>Confirmed You have recieved Kes 2,000 from Acc: 0743551250.... </td>
                                                    <td>Transaction</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this transaction"><i class="ft-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>22nd June 2022 <span class="badge badge-danger"> </span><br><small>OWEN MALINGU</small></td>
                                                    <td>Confirmed You have recieved Kes 2,000 from Acc: 0743551250.... </td>
                                                    <td>Notification</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this transaction"><i class="ft-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>22nd June 2022 <span class="badge badge-success"> </span><br><small>OWEN MALINGU</small></td>
                                                    <td>Confirmed You have recieved Kes 2,000 from Acc: 0743551250.... </td>
                                                    <td>Transaction</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this transaction"><i class="ft-eye"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <nav aria-label="Page navigation example" id="tablefooter">
                                        <ul class="pagination" id="datatable_paginate">
                                            <li class="page-item"  id="tofirstNav">
                                                <a class="page-link" href="#" aria-label="Fisrt">
                                                    <span aria-hidden="true">&laquo; &laquo;</span>
                                                    <span class="sr-only">First</span>
                                                </a>
                                            </li>
                                            <li class="page-item" id="toprevNac">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                            </li>
                                            <li class="page-item"><button disabled class="page-link" id="pagenumNav">Page: 1</button></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next" id="tonextNav">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Last Page"  id="tolastNav">
                                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo">1</span> to <span class="text-secondary"  id="finishNo">10</span> records of <span  id="tot_records">56</span></p>
                                    </nav>
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
    <div  class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span class="float-md-left d-block d-md-inline-block"><?php echo date("Y"); ?> &copy; Copyright Hypbits Enterprises</span>
        <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
            <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com" target="_blank"> Ladybird Softech Co.</a></li>
        </ul>
    </div>
</footer>
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

    {{-- the sms javascript --}}
    <script>
        var sms_data = @json($sms_data ?? '');
        var client_names = @json($client_names ?? '');
        var dates = @json($dates ?? '');
        // console.log(sms_data);
        var client_named = @json($clients_name ?? '');
        var client_contacts = @json($clients_phone ?? '');
        var client_account = @json($clients_acc ?? '');
    </script>
    <script>
        function autocomplete(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                var counter = 0;
                for (i = 0; i < arr.length; i++) {
                    if (counter > 10) {
                        break;
                    }
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr2[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr3[i].substr(0, val.length).toUpperCase() == val.toUpperCase()
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr3[i] + " (" + arr[i] + ") - " + arr2[
                            i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                    // console.log(counter);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        // var countries = client_contacts;

        /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
        autocomplete(document.getElementById("myInput"), client_account, client_contacts, client_named);
        autocomplete(document.getElementById("myInput2"), client_contacts, client_account, client_named);
    </script>
    <script src="/theme-assets/js/core/sms.js" type="text/javascript"></script>
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