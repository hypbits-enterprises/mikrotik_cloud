<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
@php
    
date_default_timezone_set('Africa/Nairobi');
@endphp
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Refferals</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    
    {{-- css styling --}}
    <x-css></x-css>

</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
    <x-client-menu active="commission"></x-client-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">My Refferals</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/ClientDashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">My Refferals
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
                                <h4 class="card-title">Commission Table</h4>
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
                                    <div class="row">
                                        <div class="col-md-4">
                                            @if(session('success'))
                                                <p class='text-success border border-success rounded p-1'>{{session('success')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-6 col-12">
                                            <a href="/Commission?period=today" class="card bg-gradient-x-blue-cyan" data-toggle="tooltip" title="Click me to see!">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-top">
                                                                <i class="ft-sun text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right align-self-bottom mt-3">
                                                                <span class="d-block mb-1 font-medium-1">Last 24 Hrs</span>
                                                                <h1 class="text-white mb-0">Kes {{$stats_today_total}}</h1>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-12">
                                            <a href="/Commission?period=this_week" class="card bg-gradient-x-blue-green" data-toggle="tooltip" title="Click me to see!">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-top">
                                                                <i class="ft-grid text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right align-self-bottom mt-3">
                                                                <span class="d-block mb-1 font-medium-1">Last One Week</span>
                                                                <h1 class="text-white mb-0">Kes {{$stats_this_week_total}}</h1>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-12">
                                            <a href="/Commission?period=this_month" class="card bg-gradient-x-purple-blue" data-toggle="tooltip" title="Click me to see!">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-top">
                                                                <i class="ft-calendar text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right align-self-bottom mt-3">
                                                                <span class="d-block mb-1 font-medium-1">Last One Month</span>
                                                                <h1 class="text-white mb-0">Kes {{$stats_this_month_total}}</h1>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-12">
                                            <a href="/Commission?period=this_year" class="card bg-gradient-x-orange-yellow" data-toggle="tooltip" title="Click me to see!">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="media d-flex">
                                                            <div class="align-self-top">
                                                                <i class="ft-bar-chart-2 text-white font-large-2 float-left"></i>
                                                            </div>
                                                            <div class="media-body text-white text-right align-self-bottom mt-3">
                                                                <span class="d-block mb-1 font-medium-1">Last One Year</span>
                                                                <h1 class="text-white mb-0">Kes {{$stats_this_year_total}}</h1>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="card-text">This table holds the commissions you have earned.</p>
                                    <p><span class="text-bold-600">Commission Table:</span></p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey"
                                                class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <h5 class="text-center">{{$table_title}}</h5>
                                        <table class="table" @if (count($commisions) > 0) 
                                                                id="refferal_table"
                                                            @endif
                                            >
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Refferal Name</th>
                                                    <th>Commission</th>
                                                    <th>Monthly Payment</th>
                                                    <th>Payment Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($commisions) > 0)
                                                    @foreach ($commisions as $index => $commission)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>{{ ucwords(strtolower($commission['client_name'])) }} <span class="badge badge-success"> </span></td>
                                                            <td>Kes {{ number_format($commission['amount'], 2) }}</td>
                                                            <td>Kes {{ number_format($commission['monthly_payment'], 2) }}</td>
                                                            <td>{{ date('dS M Y', strtotime($commission['date'])) }} at {{ date('h:i:sa', strtotime($commission['date'])) }}</td>
                                                            <td>
                                                                @php
                                                                    $btnText = "<i class='ft-eye'></i> View Refferee";
                                                                    $otherClasses = "";
                                                                    $btnLink = "/Refferals/View/{$commission['client_id']}";
                                                                    $otherAttributes = "";
                                                                @endphp
                                                                <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No refferals found.</td>
                                                    </tr>
                                                @endif
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
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
        
        if(document.getElementById('transDataReciever') != null){
            var table = $("#refferal_table").DataTable({
                order: [[0, "desc"]],
                dom: 'lrtip' // removes default search box + length dropdown
            });
            $('#searchkey').on('keyup', function () {
                table.search(this.value).draw();
            });
        }
        // enable tooltips every where
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    <!-- END PAGE LEVEL JS-->
</body>

</html>