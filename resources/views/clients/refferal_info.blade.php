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
    <title>Hypbits - Refferal Details</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
    <style>
        .dt-search {
            display: none;
        }
        .showBlock{
        display: block;
        overflow-y: scroll;
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
        .hide:{
            display: none;
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- END Custom CSS-->
</head>
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    
    <!-- fixed-top-->
    <x-client-menu active="referrals"></x-client-menu>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">My Clients</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/ClientDashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Refferals">My Refferals</a>
                                </li>
                                <li class="breadcrumb-item">View Refferal Information
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
                                <h4 id="view_clients_inform" class="card-title"><i class="ft-eye"></i> View <span class="text-secondary">{{ ucwords(strtolower($client_data->client_name)) }}</span> - <span>{{$client_data->client_account}}</span> @if ($client_data->client_status == 1) <div class='badge badge-success'>Activated</div> @else <div class='badge badge-danger'>De-Activated</div> @endif</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        {{-- <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li> --}}
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
                                    <a href="/Refferals" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to list</a>
                                    @if (session('success'))
                                        <p class="success">{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class="danger">{{ session('error') }}</p>
                                    @endif
                                    <div class="mx-auto my-2">
                                        <ul class="nav nav-tabs nav-justified" id="myTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab"><i class="ft-info mr-1"></i> Client Information</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab"><i class="ft-flag mr-1"></i> Commission Earned</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content" id="myTabsContent">
                                        <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                            <table class="table table-bordered mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Account Number:</strong></div>
                                                                <div class="col-sm-5">{{ $client_data->client_account }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                            <div class="col-sm-7"><strong>Account Status:</strong></div>
                                                            <div class="col-sm-5">                          
                                                                @if ($client_data->client_status == 1)
                                                                    <div class='badge badge-success'>Activated</div>
                                                                @else
                                                                    <div class='badge badge-danger'>De-Activated</div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Phone Number:</strong></div>
                                                                <div class="col-sm-5">{{ $client_data->clients_contacts }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Monthly Payment:</strong></div>
                                                                <div class="col-sm-5">Kes {{ $client_data->monthly_payment }}</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Registration Date:</strong></div>
                                                                <div class="col-sm-5">{{ date('D jS M Y', strtotime($client_data->clients_reg_date)) }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Wallet Amount:</strong></div>
                                                                <div class="col-sm-5">Kes {{ number_format($client_data->wallet_amount, 2) }}</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Next Billing Date:</strong></div>
                                                                <div class="col-sm-5">{{ date('D jS M Y', strtotime($client_data->next_expiration_date)) }} <br> {{date('H:i:s', strtotime($client_data->next_expiration_date))}}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Location:</strong></div>
                                                                <div class="col-sm-5">
                                                                    {{$client_data->client_address}}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade show" id="tab2" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="search" id="searchkey"
                                                        class="form-control rounded-lg p-1" placeholder="Search here ..">
                                                </div>
                                                <div class="col-md-6">
                                                    @if(session('success'))
                                                        <p class='text-success'>{{session('success')}}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <table class="table table-bordered mb-0" id="commision_table">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Commission</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($client_data->reffered_by != null && count($client_data->reffered_by->payment_history) > 0)
                                                        @foreach ($client_data->reffered_by->payment_history as $index => $commission)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>Kes {{ number_format($commission->amount, 2) }}</td>
                                                                <td>{{ date('D jS M Y', strtotime($commission->date)) }} at {{date('H:i:s', strtotime($commission->date))}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="3" class="text-center">No commissions earned yet.</td>
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        var table = $("#commision_table").DataTable({
            order: [[0, "asc"]],
            dom: 'lrtip' // removes default search box + length dropdown
        });
        $('#searchkey').on('keyup', function () {
            table.search(this.value).draw();
        });
    </script>
    
</body>

</html>
