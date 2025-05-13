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
    <title>Hypbits - Client Issues</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
    <style>
        .dt-search {
            display: none;
        }
    </style>
</head>

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
        max-height: 350; /* Set the maximum height */
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

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <!-- fixed-top-->
    <x-menu active="client_issues"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Clients Issues");
    @endphp
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Client Issues</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Client Issues
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
                                <h4 class="card-title">Client Issues</h4>
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
                                <span data-toggle="tooltip" title="Transaction Reports" class="btn btn-info d-none" id="transaction_reports_btn"><i class="ft-download"></i> Download Reports</span>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 border border-primary rounded p-1 hide" id="show_generate_reports_window">
                                            <h6 class="text-center">Generate Reports</h6>
                                            <form action="/Transaction/generateReports" target="_blank" method="GET">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-3 " id="date_option">
                                                        <label for="transaction_date_option" class="form-label">Select Transaction date Options</label>
                                                        <select name="transaction_date_option" id="transaction_date_option" class="form-control">
                                                            <option value="" hidden>Select an Option</option>
                                                            <option value="select date">Select a Date</option>
                                                            <option id="default_reg_date" selected value="all dates">All Dates</option>
                                                            <option value="between dates">Between Dates</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_from_date_win">
                                                        <label for="from_select_date" class="form-label">From</label>
                                                        <input type="date" name="from_select_date" id="from_select_date" value="<?php echo date("Y-m-d",strtotime("-7 days"));?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_to_date_win">
                                                        <label for="to_select_date" class="form-label">To</label>
                                                        <input type="date" name="to_select_date" id="to_select_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_date_win">
                                                        <label for="select_registration_date" class="form-label">Select Date</label>
                                                        <input type="date" name="select_registration_date" id="select_registration_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 " id="select_router_window">
                                                        <label for="select_user_option" class="form-label">Select User</label>
                                                        <select name="select_user_option" id="select_user_option" class="form-control">
                                                            <option value="" hidden>Select Router</option>
                                                            <option selected value="All">All</option>
                                                            <option value="specific_user">Specific User Transactions</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 hide" id="client_status_opt">
                                                        <label for="myInput" class="form-label">Type the Client Name, Account Number or Phone Number </label><div class="autocomplete">
                                                        <input id="myInput" type="text" class="form-control"
                                                            name="client_account"
                                                            placeholder="Phone number, Account Number, Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button class="btn btn-primary mt-2" type="submit"><i class="ft-settings"></i> Generate Reports</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="card-text">In this table below Client Issues will be displayed.</p>
                                        @if (session('success'))
                                            <p class="success">{{ session('success') }}</p>
                                        @endif

                                        @if (session('error'))
                                            <p class="danger">{{ session('error') }}</p>
                                        @endif
                                    <p><span class="text-bold-600">Client Report Table:</span></p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey"
                                                class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                        <div class="col-md-3">

                                        </div>
                                        <div class="col-md-3">
                                            <a href="/Client-Reports/New" class="btn btn-purple btn-sm {{$readonly}}"><i class="ft-plus"></i> New Issue</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <div class="container text-center my-2" id="logo_loaders">
                                            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                        </div>
                                        <table class="table d-none" id="myTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Ticket Number</th>
                                                    <th>Problem Title</th>
                                                    <th>Problem Description</th>
                                                    <th>Client Name</th>
                                                    <th>Report Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($client_reports as $key => $report)
                                                    <tr>
                                                        <th scope="row">{{$key + 1}}
                                                            @if ($report->status == "pending")
                                                                <span class="badge text-light bg-danger text-dark" data-toggle="tooltip" title="" data-original-title="Pending!">P</span>
                                                            @else
                                                                <span class="badge text-light bg-success text-dark" data-toggle="tooltip" title="" data-original-title="Resolved!">R</span>
                                                            @endif
                                                        </th>
                                                        <td>{{$report->report_code ?? "NULL"}}</td>
                                                        <td>{{$report->report_title}}</td>
                                                        <td data-toggle="tooltip" title="" data-original-title="{{$report->report_description}}">{{strlen($report->report_description) > 100 ? substr($report->report_description, 0, 100)."...." : $report->report_description}}</td>
                                                        <td><a href="/Clients/View/{{$report->client_id}}" target="_blank" class="text-dark">{{ucwords(strtolower($report->client_name))}} - ({{$report->client_account}})</a></td>
                                                        <td>{{date("D dS M Y H:i:sA", strtotime($report->report_date))}}</td>
                                                        <td><a href="/Client-Reports/View/{{$report->report_id}}" class="btn btn-sm btn-purple text-bolder"
                                                                data-toggle="tooltip" title="View this issue."><i
                                                                    class="ft-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
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
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/datatables.js"></script>
    <script src="/theme-assets/js/core/datatables.min.js"></script>
    <!-- END CHAMELEON  JS-->
    <script>
        $(document).ready( function () {
            cObj("logo_loaders").classList.add("d-none");
            cObj("myTable").classList.remove("d-none");
            var table = $('#myTable').DataTable({
                "pagingType": "full_numbers", // Alternative styles: "simple", "numbers", etc.
                "language": {
                    "search": "<strong>Search:</strong>", // Custom label for the search box
                    "lengthMenu": "Show _MENU_ entries per page"
                },
                "pageLength" : 50,
                "order": [[1, "desc"]]
            });

            $('#searchkey').on('keyup', function() {
                table.search(this.value).draw();
            });
        } );

        function cObj(object_id) {
            return document.getElementById(object_id);
        }
    </script>

    {{-- GET THE TRANSACTION DATA --}}
    <script>
        var client_reports = @json($client_reports ?? []);
    </script>

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
