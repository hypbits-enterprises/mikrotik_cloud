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

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
</head>


<style>
    .hide{
        display: none;
    }
    #sms_table thead th {
        background: #f4f5f7;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #5e6778;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
        white-space: nowrap;
    }
    #sms_table tbody tr {
        transition: background 0.15s;
    }
    #sms_table tbody td {
        vertical-align: middle;
        padding: 10px 12px;
        font-size: 0.88rem;
    }
    #sms_table tbody tr:hover td {
        background: #f0f4ff;
    }
    #sms_table .badge-pill {
        font-size: 0.75rem;
        padding: 4px 8px;
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
        max-height: 250; /* Set the maximum height */
        overflow-y: auto; /* Enable vertical scrolling */
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

    
    <x-menu active="sms"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"SMS");
    @endphp

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
                                <h4 class="card-title">SMS Statistics</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        @php
                                            $btnText = "<i class='ft-plus'></i> Show Statistics";
                                            $otherClasses = "";
                                            $btnLink = "#";
                                            $otherAttributes = "data-action='collapse'";
                                        @endphp
                                        <x-button-link btnType="primary" btnSize="sm" toolTip="SMS Statistics" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <li><a data-action="expand"><i class="ft-maximize"></i></a></li> --}}
                                        <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body">
                                    <div class="border border-secondary rounded shadow-sm w-100 m-0 my-2 p-1">
                                        <h5 class="card-title">SMS Statistics</h5>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <span>Sms sent today: <span class="text-primary">{{$sms_count}}</span></span><br>
                                                <span>Sms Sent Last week: <span class="text-primary">{{$last_week}}</span></span><br>
                                                <span>Total SMS Sent: <span class="text-primary">{{$total_sms}}</span></span><br>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-eye\"></i> Show Balance";
                                                    $otherClasses = "";
                                                    $btn_id = "show_sms_balance_btn";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button type="button" id="show_sms_balance_btn" class="btn btn-primary btn-sm">Show Balance</button> --}}
                                                <p>Sms Balance: <span></span>: <span id="show_sms_balance">0 SMS</span><small class="text-primary d-none" id="show_sms_loader"> Loading...</small></p>
                                            </div>
                                        </div>
                                        <x-sms.statistics :monthlyStats="$monthlyStats" :weeklyStats="$weeklyStats" :dailyStats="$dailyStats"/>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                    {{-- <div class="container border border-secondary rounded shadow-sm w-100 m-0 my-2 p-1">
                                        <h5 class="card-title">SMS Statistics</h5>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <span>Sms sent today: <span class="text-primary">{{$sms_count}}</span></span><br>
                                                <span>Sms Sent Last week: <span class="text-primary">{{$last_week}}</span></span><br>
                                                <span>Total SMS Sent: <span class="text-primary">{{$total_sms}}</span></span><br>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-eye\"></i> Show Balance";
                                                    $otherClasses = "";
                                                    $btn_id = "show_sms_balance_btn";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" /> --}}
                                                {{-- <button type="button" id="show_sms_balance_btn" class="btn btn-primary btn-sm">Show Balance</button> --}}
                                                {{-- <p>Sms Balance: <span></span>: <span id="show_sms_balance">0 SMS</span><small class="text-primary d-none" id="show_sms_loader"> Loading...</small></p>
                                            </div>
                                        </div>
                                    </div> --}}
                                    @if(session('success_sms'))
                                        <p class='text-success'>{{session('success_sms')}}</p>
                                    @endif
                                    @if(session('error_sms'))
                                        <p class='text-danger'>{{session('error_sms')}}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class="text-danger">{{ session('error') }}</p>
                                    @endif
                                    <div class="w-20">
                                        @php
                                            $btnText = "Customize SMS";
                                            $otherClasses = "text-bolder";
                                            $btnLink = "/sms/system_sms";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/sms/system_sms" class="btn btn-secondary text-bolder {{$readonly}}">Customize SMS</a> --}}
                                        @php $btnText = "Email Templates"; $otherClasses = ""; $btnLink = "/email-templates"; @endphp
                                        <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />

                                        @php
                                            $btnText = "<i class=\"ft-file-text\" ></i> SMS Reports";
                                            $otherClasses = "";
                                            $btn_id = "sms_reports_btn";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        {{-- <span data-toggle="tooltip" title="SMS Reports" class="btn btn-info" id="sms_reports_btn"><i class="ft-file-text"></i> SMS Reports</span> --}}
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
                                                        @php
                                                            $btnText = "<i class=\"ft-settings\"></i> Generate Reports";
                                                            $otherClasses = "mt-2";
                                                            $btn_id = "";
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        {{-- <button class="btn btn-primary mt-2" type="submit"><i class="ft-settings"></i> Generate Reports</button> --}}
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="card-text">All messages sent by the system are displayed here.</p>

                                    {{-- Channel Filter Tabs --}}
                                    <ul class="nav nav-tabs mb-2" id="channelTabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#" data-channel="all">All</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-channel="sms"><i class="ft-message-square"></i> SMS</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-channel="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-channel="email"><i class="ft-mail"></i> Email</a>
                                        </li>
                                    </ul>

                                    <p><span class="text-bold-600">Message Table:</span></p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey" class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Write Message";
                                                $otherClasses = "text-bolder float-right";
                                                $btnLink = "/sms/compose";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="info" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
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
                                                @php
                                                    $btnText = "<i class=\"ft-trash\"></i> Delete";
                                                    $otherClasses = "mt-2 ".$readonly;
                                                    $btn_id = "delete_clients_id";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-sm btn-danger" {{$readonly}} id="delete_clients_id" type="button"><i class="ft-trash"></i> Delete</button> --}}
                                                <div class="container hide" id="delete_clients_window">
                                                    <p><b>Are you sure you want to delete <span id="delete_number_clients">0</span> SMS(s)</b>?</p>
                                                    @php
                                                        $btnText = "Yes";
                                                        $otherClasses = "mt-2 ".$readonly;
                                                        $btn_id = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="danger" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-danger" {{$readonly}} type="submit">Yes</button> --}}
                                                    @php
                                                        $btnText = "No";
                                                        $otherClasses = "mt-2 ".$readonly;
                                                        $btn_id = "no_dont_delete_selected";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-secondary" id="no_dont_delete_selected" type="button">No</button> --}}
                                                </div>
                                            </form>
                                            <form class="col-md-3" action="/Resend_bulk_sms" method="POST" class="col-md-3 my-1">
                                                @csrf
                                                <input type="hidden" name="hold_user_id_data" id="hold_user_id_data_2">
                                                @php
                                                    $btnText = "<i class=\"fa-solid fa-paper-plane\"></i> Re-send SMS";
                                                    $otherClasses = "mt-3 ".$readonly;
                                                    $btn_id = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-sm btn-info" {{$readonly}} type="submit"><i class="fa-solid fa-paper-plane"></i> Re-send SMS</button> --}}
                                            </form>
                                        </div>
                                    </div>
                                    {{-- Hidden stubs required by sms.js top-level onclick/onchange assignments --}}
                                    <div id="transDataReciever" style="display:none;"></div>
                                    <nav id="tablefooter" style="display:none;">
                                        <button id="tofirstNav"></button>
                                        <button id="toprevNac"></button>
                                        <button id="tonextNav"></button>
                                        <button id="tolastNav"></button>
                                        <span id="pagenumNav"></span>
                                        <span id="startNo"></span>
                                        <span id="finishNo"></span>
                                        <span id="tot_records"></span>
                                        <span id="client_select_counts"></span>
                                        <span id="delete_number_clients"></span>
                                    </nav>

                                    {{-- DataTable --}}
                                    <div class="table-responsive">
                                        <table id="sms_table" class="table table-hover table-bordered" style="width:100%">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:50px">#</th>
                                                    <th style="width:170px"><i class="ft-clock mr-1"></i>Date Sent</th>
                                                    <th><i class="ft-message-square mr-1"></i>Message</th>
                                                    <th style="width:100px"><i class="ft-tag mr-1"></i>Type</th>
                                                    <th style="width:80px" class="text-center"><i class="ft-layers mr-1"></i>Units</th>
                                                    <th style="width:100px" class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        var weekly_sms_data = @json($week_stats ?? '');
        var readonly = @json($readonly ?? '');
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
                a.style.maxHeight = "250px";
                a.style.overflowY = "auto";
                a.style.overflowX = "hidden";
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
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="/theme-assets/js/core/sms.js" type="text/javascript"></script>
    <script>
    (function () {
        var activeChannel = 'all';

        var smsTable = $('#sms_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/sms/datatable',
                type: 'GET',
                data: function (d) { d.channel = activeChannel; }
            },
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mb-2"lf>t<"d-flex justify-content-between align-items-center mt-2"ip>',
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            language: {
                processing: '<div class="text-primary"><i class="ft-loader"></i> Loading…</div>',
                emptyTable: '<div class="text-center text-muted py-3"><i class="ft-inbox" style="font-size:2rem"></i><br>No messages found</div>',
                zeroRecords: '<div class="text-center text-muted py-3"><i class="ft-search" style="font-size:2rem"></i><br>No matching messages found</div>'
            },
            columns: [
                { data: 'rownum',      orderable: false, searchable: false, className: 'align-middle' },
                { data: 'date_sent',   className: 'align-middle' },
                { data: 'sms_content', className: 'align-middle' },
                { data: 'sms_type',    className: 'align-middle text-center' },
                { data: 'sms_units',   orderable: false, searchable: false, className: 'align-middle text-center' },
                { data: 'actions',     orderable: false, searchable: false, className: 'align-middle text-center' }
            ]
        });

        // Hook external search box into DataTables
        document.getElementById('searchkey').addEventListener('keyup', function () {
            smsTable.search(this.value).draw();
        });

        // Channel tab filter
        document.querySelectorAll('#channelTabs .nav-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('#channelTabs .nav-link').forEach(function (l) {
                    l.classList.remove('active');
                });
                this.classList.add('active');
                activeChannel = this.getAttribute('data-channel');
                smsTable.ajax.reload();
            });
        });

        // Re-init tooltips, bulk-select checkboxes, and hover effect after each draw
        smsTable.on('draw.dt', function () {
            $('[data-toggle="tooltip"]').tooltip();
            checkedUnchecked();
            hoverEffect();
        });
    }());
    </script>
    <script>
        // sms.js only calls plotGraph when sms_data.length > 0, which is never true
        // now that the table uses DataTables. Call it directly here instead.
        window.addEventListener('load', function () {
            if (typeof plotGraph === 'function' && weekly_sms_data) {
                plotGraph(weekly_sms_data);
            }
        });
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