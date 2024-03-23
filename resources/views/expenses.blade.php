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
    <link
        href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700"
        rel="stylesheet">
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
    $readonly = readOnly($priviledges,"Expenses");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<style>
    .hide {
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
                                class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                data-toggle="dropdown">
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
        data-img="theme-assets/images/backgrounds/02.jpg">
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
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span class="menu-title"
                            data-i18n="">Dashboard</span></a>
                </li>
                <li class="{{showOption($priviledges,"My Clients")}} nav-item"><a href="/Clients"><i class="ft-users"></i><span class="menu-title"
                            data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} active has-sub open"><a href="#"><i class="ft-activity"></i><span
                            class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i>
                                    Transactions</span></a>
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
                <li class="{{showOption($priviledges,"Account and Profile")}} nav-item"><a href="/Accounts"><i class="ft-lock"></i><span class="menu-title"
                            data-i18n="">Account and Profile</span></a>
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
                                <li class="breadcrumb-item"><a href="#"><span class="menu-title"
                                            data-i18n="">Accounts</span></a>
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
                                <p>- Expense Tools!</p>
                                <a href="/Expense/Statistics" data-toggle="tooltip" title="Expense Statistics"
                                    class="btn btn-secondary"><i class="ft-bar-chart-2"></i></a>
                                <button data-toggle="tooltip" title="Expense Reports" class="btn btn-info"
                                    id="expense_report_btn"><i class="ft-file-text"></i></button>
                                <button data-toggle="tooltip" title="Add Expenses Category" class="btn btn-info" {{$readonly}}
                                    id="add_expense_category_btn"><i class="ft-plus"></i></button>
                                <button data-toggle="tooltip" title="View Expenses Category" class="btn btn-info"
                                    id="view_expense_category_btn"><i class="ft-eye"></i></button>
                                <button data-toggle="tooltip" title="Generate Income Statements" class="btn btn-info"
                                    id="view_income_statements"><i class="ft-book"></i></button>
                            </div>
                            <div class="card-header border border-primary rounded mx-1 hide"
                                id="generate_income_statements">
                                <h6>Generate Income Statement</h6>
                                <form class="row" target="_blank" method="GET"
                                    action="/Expenses/Generate/FinStats">
                                    <div class="col-md-4">
                                        <label for="select_income_statement_period" class="form-label">Select
                                            Period</label>
                                        <select name="select_income_statement_period"
                                            id="select_income_statement_period" required class="form-control">
                                            <option value="" hidden>Select an Option</option>
                                            <option value="All">All</option>
                                            <option value="Daily">Daily</option>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Yearly">Yearly</option>
                                            <option value="Between Dates">Between Dates</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 hide" id="date_income_statement">
                                        <label for="select_report_date" class="form-label">Select a date</label>
                                        <input type="date" name="select_report_date" id="select_report_date"
                                            class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4 hide" id="from_income_statement">
                                        <label for="select_report_from_date" class="form-label">From</label>
                                        <input type="date" name="select_report_from_date"
                                            id="select_report_from_date" class="form-control"
                                            value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                                    </div>
                                    <div class="col-md-4 hide" id="to_income_statement">
                                        <label for="select_report_to_date" class="form-label">To</label>
                                        <input type="date" name="select_report_to_date" id="select_report_to_date"
                                            class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4 hide" id="select_mon_income_statement">
                                        <label for="select_mon_option" class="form-label">Select Month</label>
                                        <select name="select_mon_option" id="select_mon_option" class="form-control">
                                            <option value="" hidden>Select an Option</option>
                                            <option selected value="Jan">January</option>
                                            <option value="Feb">February</option>
                                            <option value="Mar">March</option>
                                            <option value="Apr">April</option>
                                            <option value="May">May</option>
                                            <option value="Jun">June</option>
                                            <option value="Jul">July</option>
                                            <option value="Aug">August</option>
                                            <option value="Sep">September</option>
                                            <option value="Oct">October</option>
                                            <option value="Nov">November</option>
                                            <option value="Dec">December</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 hide" id="select_yr_income_statement">
                                        <label for="select_year_option" class="form-label">Select Year</label>
                                        <select name="select_year_option" id="select_year_option"
                                            class="form-control">
                                            <option value="" hidden>Select Year</option>
                                            @for ($i = date('Y'); $i >= 2021; $i--)
                                                @if ($i == date('Y'))
                                                    <option selected value="{{ $i }}">{{ $i }}
                                                    </option>
                                                @else
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary mt-2" type="submit">Generate Reports</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-header border border-primary rounded mx-1 hide"
                                id="generate_expense_report">
                                <h6>Generate Expense Reports</h6>
                                <form class="row" target="_blank" method="GET"
                                    action="/Expenses/Generate/Reports">
                                    <div class="col-md-4">
                                        <label for="expense_date_option" class="form-label">Select Date
                                            Options</label>
                                        <select name="expense_date_option" id="expense_date_option"
                                            class="form-control">
                                            <option value="select date">Select a date</option>
                                            <option value="select between date">Select between Dates</option>
                                            <option selected value="all">All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 hide" id="single_date_window">
                                        <label for="single_date" class="form-label">Select Date</label>
                                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                            name="single_date">
                                    </div>
                                    <div class="col-md-4 hide" id="from_date_window">
                                        <label for="from_date" class="form-label">From:</label>
                                        <input type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                            value="{{ date('Y-m-d', strtotime('-7 days')) }}" name="from_date">
                                    </div>
                                    <div class="col-md-4 hide" id="to_date_window">
                                        <label for="to_date" class="form-label">To:</label>
                                        <input type="date" class="form-control" max="{{ date('Y-m-d') }}"
                                            value="{{ date('Y-m-d') }}" name="to_date">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="expense_categories" class="form-label">Select Expense
                                            Category</label>
                                        <select name="expense_categories" id="expense_categories"
                                            class="form-control">
                                            <option selected value="All">All</option>
                                            @if (count($exp_category) > 0)
                                                @for ($i = 0; $i < count($exp_category); $i++)
                                                    <option value="{{ $exp_category[$i]->name }}">
                                                        {{ $exp_category[$i]->name }}</option>
                                                @endfor
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary mt-2" type="submit">Generate reports</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-header border border-primary m-1 rounded hide" id="add_expense_category">
                                <h6 class="text-center">Add Expense Category</h6>
                                <form class="row" method="POST" action="/Expense/Category/Add">
                                    @csrf
                                    <div class="col-md-4">
                                        <label for="expense_category" class="form-label">Expense Category</label>
                                        <input type="text" class="form-control" name="expense_category"
                                            id="expense_category" placeholder="Expense Category">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary mt-2">Save Expense
                                            Category</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-header border border-primary m-1 rounded hide"
                                id="show_expense_category">
                                <h6 class="text-center">Expense Categories</h6>
                                @if (count($exp_category) > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Expense Category Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($exp_category); $i++)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $exp_category[$i]->name }}</td>
                                                    <td>@if ($readonly == "")
                                                        <a href='/Expense/Delete/{{$exp_category[$i]->index}}'class='card-link text-danger'><i class='ft-trash'></i> Delete</a>
                                                    @else
                                                        <span><i class='ft-trash'></i><s> Delete</s></span>
                                                    @endif</td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                @else
                                    <p class="border border-secondary rounded text-secondary p-1">
                                        No Expense Category recorded yet!
                                    </p>
                                @endif
                            </div>
                            <hr class="p-0 m-0">
                            <div class="card-header">
                                <p>Add an expenses that you incurred in your business!</p>
                                <button {{$readonly}} class="btn btn-info" data-toggle="tooltip" id="addExpenseBtnWindow"
                                    title="Add Expense"><i class="ft-plus"></i> Add Expense</button>
                                <div class="container border border-secondary rounded p-1 my-1 hide"
                                    id="addExpenseWindow">
                                    <h6 class="text-center"><u>Add Expense</u></h6>
                                    <form class="row" method="POST" action="/Expense/Add">
                                        @csrf
                                        <div class="col-md-4">
                                            <label for="expense_name" class="form-label">Expense Name</label>
                                            <input type="text" name="expense_name" id="expense_name"
                                                class="form-control" placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_category" class="form-label">Expense Category <b
                                                    data-toggle="tooltip"
                                                    title="This is the category that this expense will lie, example can be daily-expense or labour."
                                                    class="text-dark"><i class="ft-info"></i></b></label>
                                            <select name="expense_category" id="expense_category"
                                                class="form-control" required>
                                                <option value="" hidden>Select option</option>
                                                @if (count($exp_category) > 0)
                                                    @for ($i = 0; $i < count($exp_category); $i++)
                                                        <option value="{{ $exp_category[$i]->name }}">
                                                            {{ $exp_category[$i]->name }}</option>
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_date" class="form-label">Date <b
                                                    data-toggle="tooltip"
                                                    title="This is the date you incurred the cost, by default it takes today."
                                                    class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}"
                                                max="{{ date('Y-m-d') }}" id="expense_date" class="form-control"
                                                placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_quantity" class="form-label">Quantity <b
                                                    data-toggle="tooltip"
                                                    title="This is the number of commodities bought. e.g.,10 - (Routers)"
                                                    class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="number" name="expense_quantity" id="expense_quantity"
                                                step="0.5" value="0" class="form-control"
                                                placeholder="Example: 10 (Mikrotik Routers)">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit_price" class="form-label">Unit Price <b
                                                    data-toggle="tooltip"
                                                    title="This is the price of one commodity bought. e.g.,One router costs Kes 1000"
                                                    class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="number" name="expense_unit_price" step="0.005"
                                                id="expense_unit_price" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_total_price" class="form-label">Total Price <b
                                                    data-toggle="tooltip"
                                                    title="This is the calculated total price of all the commodities bought. e.g.,10 - (Routers) cost Kes 10,000: This field is readonly!"
                                                    class="text-dark"><i class="ft-info"></i></b> <span
                                                    class="text-danger">{Read-only!}</span></label>
                                            <input type="number" readonly name="expense_total_price"
                                                id="expense_total_price" class="form-control"
                                                placeholder="Total Price" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit" class="form-label">Unit Of Measurement <b
                                                    data-toggle="tooltip"
                                                    title="This is the unit of measurement, If you inccured a cost of a commodity that has weight and its charged per certain weight you would say (10 Kgs of Sugar) Kgs being the unit of measurement."
                                                    class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="text" name="expense_unit" id="expense_unit"
                                                class="form-control" placeholder="Kgs, Litres">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="expense_description" class="form-label">Description</label>
                                            <textarea name="expense_description" id="expense_description" cols="30" rows="5" class="form-control" placeholder="Describe Expense here.."></textarea>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button class="btn btn-block btn-primary" {{$readonly}} type="submit">Save
                                                Expense</button>
                                        </div>
                                    </form>
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
                                        <span class="text-bold-600">Expense Table:</span>
                                    </p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey"
                                                class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                        @php
                                            function checkPresnt($array, $string)
                                            {
                                                if (count($array) > 0) {
                                                    for ($i = 0; $i < count($array); $i++) {
                                                        if ($string == $array[$i]) {
                                                            return 1;
                                                        }
                                                    }
                                                }
                                                return 0;
                                            }
                                            $expense_cats = [];
                                            for ($index = 0; $index < count($expenses); $index++) {
                                                if (checkPresnt($expense_cats, $expenses[$index]->category) == 0) {
                                                    array_push($expense_cats, $expenses[$index]->category);
                                                }
                                            }
                                        @endphp
                                        <div class="col-md-3">
                                            <select name="expense_category_filter" id="expense_category_filter"
                                                class="form-control" required>
                                                <option value="" hidden>Filter By Category</option>
                                                <option value="">All</option>
                                                @if (count($expense_cats) > 0)
                                                    @for ($i = 0; $i < count($expense_cats); $i++)
                                                        <option value="{{ $expense_cats[$i] }}">
                                                            {{ $expense_cats[$i] }}</option>
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <div class="container text-center my-2">
                                            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                        </div>
                                        {{-- <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Transaction ID</th>
                                                    <th>Account Number</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>PKLJKJKHUJ <span class="badge badge-success"> </span></td>
                                                    <td>0743551250</td>
                                                    <td>Kes 1,000</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder"
                                                            data-toggle="tooltip" title="View this transaction"><i
                                                                class="ft-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>OOJKUKJUIJ <span class="badge badge-danger"> </span></td>
                                                    <td>0743551223</td>
                                                    <td>Kes 3,000</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder"
                                                            data-toggle="tooltip" title="View this transaction"><i
                                                                class="ft-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">3</th>
                                                    <td>PIKJJHIUHKJ <span class="badge badge-success"> </span></td>
                                                    <td>0713620727</td>
                                                    <td>Kes 3,000</td>
                                                    <td><a href="#" class="btn btn-sm btn-primary text-bolder"
                                                            data-toggle="tooltip" title="View this transaction"><i
                                                                class="ft-eye"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <nav aria-label="Page navigation example" id="tablefooter">
                                        <ul class="pagination" id="datatable_paginate">
                                            <li class="page-item" id="tofirstNav">
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
                                            <li class="page-item"><button disabled class="page-link"
                                                    id="pagenumNav">Page: 1</button></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next"
                                                    id="tonextNav">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Last Page"
                                                    id="tolastNav">
                                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <p class="card-text text-xxs">Showing from <span class="text-primary"
                                                id="startNo">1</span> to <span class="text-secondary"
                                                id="finishNo">10</span> records of <span id="tot_records">56</span>
                                        </p>
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
    <footer style="margin-bottom: 0% !important"
        class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span
                class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits
                Enterprises</span>
            <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    {{-- <x-footerRouteAdmin > --}}
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    {{--  --}}
    <!-- END PAGE LEVEL JS-->

    {{-- GET THE TRANSACTION DATA --}}
    <script>
        var expenses = @json($expenses ?? '');
        var exp_category = @json($exp_category ?? '');
        // console.log(today);
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
        // autocomplete(document.getElementById("myInput"), client_account, client_contacts, client_names);
    </script>
    <script src="theme-assets/js/core/expense.js"></script>

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
