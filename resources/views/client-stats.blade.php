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
    <title>Hypbits - Clients Statistics</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
    <style>
        .form-control-label{
            font-weight: 600;
            cursor: pointer;
        }
        .hide{
            display: none;
        }
    </style>
</head>


<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <!-- fixed-top-->
    <x-menu active="myclients"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"My Clients");
    @endphp
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Client Statistics</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Clients">My CLients</a>
                                </li>
                                <li class="breadcrumb-item active">Client Statistics
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
                                <h4 class="card-title">Client On-Boarding Statistics</h4>
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
                                    <a href="/Clients" class="text-primary"><i class="ft-arrow-left"></i> Back to Clients</a>
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p class="card-text">View clients registration statistics below.</p>
                                    <div class="container row">
                                        <div class="col-md-4">
                                            <label for="period_selection_reg" class="form-control-label">Select Period</label>
                                            <select name="period_selection_reg" id="period_selection_reg" class="form-control">
                                                <option value="" hidden>Select an option</option>
                                                <option selected value="Weekly" >Weekly</option>
                                                <option value="Monthly" >Monthly</option>
                                                <option value="Yearly" >Yearly</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="clients_chart_type" class="form-control-label">Select Chart Type</label>
                                            <select name="clients_chart_type" id="clients_chart_type" class="form-control">
                                                <option value="" hidden>Select an option</option>
                                                <option value="bar" >Bar Chart</option>
                                                <option selected value="line" >Line Chart</option>
                                                <option value="pie" >Pie Chart</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="client_status_reg" class="form-control-label">Select Client Status</label>
                                            <select name="client_status_reg" id="client_status_reg" class="form-control">
                                                <option value="" hidden>Select Status</option>
                                                    <option selected value="2">All</option>
                                                    <option value="0">In-Active</option>
                                                    <option value="1">Active</option>
                                                    <option value="3">Reffered</option>
                                                    <option value="4">Static Assigned</option>
                                                    <option value="5">PPPoE Assigned</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="container row my-2">
                                        <div class="col-md-4">
                                            <label for="show_x_axis" class="form-control-label">Show X axis</label>
                                            <input type="checkbox" name="show_x_axis" id="show_x_axis" checked>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="show_y_axis" class="form-control-label">Show Y axis</label>
                                            <input type="checkbox" name="show_y_axis" id="show_y_axis" checked>
                                        </div>
                                    </div>
                                    <div class="container" id="data_navigators">
                                        <small class="text-secondary">Navigate</small>
                                        <div class="d-flex align-items-center align-items-stretch" style="cursor: pointer;">
                                            <button class="btn btn-sm btn-secondary" id="previous_data" type="button"><i class="ft-arrow-left"></i> Earlier</button>
                                            {{-- <span class="p-1 border border-primary" id="previous_data"><i class="ft-arrow-left"></i> Prev</span> --}}
                                            <span id="selective_data">
                                                <select name="select_week_link" id="select_week_link" class="page-item border border-primary bg-white p-1">
                                                    <option value="" hidden>Select Option</option>
                                                </select>
                                            </span>
                                            <button class="btn btn-sm btn-secondary" id="next_data" type="button">Later <i class="ft-arrow-right"></i></button>
                                            {{-- <span class="p-1 border border-primary" id="next_data">Next <i class="ft-arrow-right"></i></span> --}}
                                        </div>
                                    </div>
                                    <div class="container p-1 my-2 overflow-auto p-2" style="min-width: 200px;">
                                        <canvas class="w-75 mx-auto" id="onboarding_canvas" aria-label="Data will appear here" role="img" >
                                            <p class="text-secondary text-bold-700" >Data will be displayed here!</p>
                                        </canvas>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12 form-group row">
                                                <div class="col-md-6"></div>
                                                <div class="col-md-6">
                                                    <input type="text" name="search" id="searchkey" class="form-control rounded-lg " placeholder="Your keyword ..">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container p-1">
                                            <div class="table-responsive" id="transDataReciever">
                                                <p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='fas fa-exclamation-triangle'></i></span> <br>Load the data before proceeding!</p>
                                                {{-- <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Full Names</th>
                                                            <th>Account Number</th>
                                                            <th>Location</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>Mark Otto <span class="badge badge-success"> </span></td>
                                                            <td>0743551250</td>
                                                            <td>Kigajo corner 3</td>
                                                            <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this User"><i class="ft-eye"></i></a> <a href="#" class="btn btn-sm btn-secondary text-bolder" data-toggle="tooltip" title="Edit this User"><i class="ft-edit"></i></a>  <a href="#" class="btn btn-sm btn-warning text-bolder"  data-toggle="tooltip" title="Disable this User"><i class="ft-alert-octagon"></i></a></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>Jacob Thornton <span class="badge badge-danger"> </span></td>
                                                            <td>0743551223</td>
                                                            <td>Ruiru Bypass</td>
                                                            <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this User"><i class="ft-eye"></i></a> <a href="#" class="btn btn-sm btn-secondary text-bolder" data-toggle="tooltip" title="Edit this User"><i class="ft-edit"></i></a>  <a href="#" class="btn btn-sm btn-warning text-bolder"  data-toggle="tooltip" title="Disable this User"><i class="ft-alert-octagon"></i></a></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>Larry the Bird <span class="badge badge-success"> </span></td>
                                                            <td>0713620727</td>
                                                            <td>Kijabe</td>
                                                            <td><a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this User"><i class="ft-eye"></i></a> <a href="#" class="btn btn-sm btn-secondary text-bolder" data-toggle="tooltip" title="Edit this User"><i class="ft-edit"></i></a> <a href="#" class="btn btn-sm btn-warning text-bolder"  data-toggle="tooltip" title="Disable this User"><i class="ft-alert-octagon"></i></a> </td>
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
                                        <hr class="bg-secondary">
                                        <div class="container p-1">
                                            <h5 class="text-center text-primary">Clients Due Date Demographics</h5>
                                            <p>
                                                <b class="text-secondary">Note</b><br>
                                                - Select a period to show clients who are to be due from today.
                                            </p>
                                            <div class="container row border border-secondary p-1">
                                                <div class="col-md-3">
                                                    <label for="select_dates" class="form-control-label">Select a period
                                                        <span class="invisible" id="interface_load"><i class="fas ft-rotate-cw fa-spin"></i></span>
                                                    </label>
                                                    <select name="select_dates" id="select_dates" class="form-control">
                                                        <option value="" hidden>Select an option</option>
                                                        <option selected value="1 week">One week</option>
                                                        <option value="2 week">Two week</option>
                                                        <option value="3 week">Three week</option>
                                                        <option value="1 Month">One Month</option>
                                                        <option value="2 Months">Two Months</option>
                                                        <option value="Select date">Select A date</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 invisible" id="specific_dates">
                                                    <label for="select_due_dates_demo" class="form-control-label">Select a date</label>
                                                    <input type="date" class="form-control" id="select_due_dates_demo" value="<?php echo date('Y-m-d');?>">
                                                </div>
                                                <div class="col-md-3" >
                                                    <label for="from_todays" class="form-control-label">From today</label>
                                                    <input type="checkbox" name="from_todays" id="from_todays" checked>
                                                </div>
                                                <div class="col-md-3" >
                                                    <span class="btn btn-primary" id="display_clients">Display Clients</span>
                                                </div>
                                            </div>
                                            <span class="hide" id="display_data"></span>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 form-group row">
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="search" id="searchkey_2" class="form-control rounded-lg " placeholder="Your keyword ..">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container" id="demographics_data">

                                            </div>
                                            <nav aria-label="Page navigation example" id="tablefooter_2">
                                                <ul class="pagination" id="datatable_paginate_2">
                                                    <li class="page-item"  id="tofirstNav_2">
                                                        <a class="page-link" href="#" aria-label="Fisrt">
                                                            <span aria-hidden="true">&laquo; &laquo;</span>
                                                            <span class="sr-only">First</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item" id="toprevNac_2">
                                                        <a class="page-link" href="#" aria-label="Previous">
                                                            <span aria-hidden="true">&laquo;</span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item"><button disabled class="page-link" id="pagenumNav_2">Page: 1</button></li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Next" id="tonextNav_2">
                                                            <span aria-hidden="true">&raquo;</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a class="page-link" href="#" aria-label="Last Page"  id="tolastNav_2">
                                                            <span aria-hidden="true">&raquo;&raquo;</span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo_2">0</span> to <span class="text-secondary"  id="finishNo_2">0</span> records of <span  id="tot_records_2">0</span></p>
                                            </nav>
                                        </div>
                                    </div>
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
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->

    <script>
        var clients_weekly = @json($clients_weekly);
        var client_metrics_weekly = @json($client_metrics_weekly);
        var clients_statistics_monthly = @json($clients_statistics_monthly);
        var clients_monthly = @json($clients_monthly);
        var clients_statistics_yearly = @json($clients_statistics_yearly);
        var clients_data_yearly = @json($clients_data_yearly);
    </script>

    <script src="theme-assets/js/core/client-stats.js"></script>
    <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
    </script>
    <!-- END CLIENT JS-->
</body>

</html>
