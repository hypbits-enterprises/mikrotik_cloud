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
    <title>Hypbits - Client Profile</title>
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

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
    <x-client-menu active="profile"></x-client-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = "";
    @endphp
    <div class="app-content content pt-2">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Chart -->
                <div class="d-none row match-height">
                    <div class="col-12">
                        <div class="">
                            <div id="gradient-line-chart1" class="height-250 GradientlineShadow1"></div>
                        </div>
                    </div>
                </div>
                <!-- Chart -->
                <!-- eCommerce statistic -->
                <div class="card text-center p-1">
                    <h4 class="text-dark">My Profile</h4>
                </div>
                <!--/ eCommerce statistic -->

                <!-- Statistics -->
                <!-- Statistics -->
               
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                
                                <h4 class="card-title">@php
                                    $time = date("H");
                                    $greeting = "";
                                    if ($time < 11) {
                                        $greeting = "Good Morning";
                                    }elseif ($time >=11 && $time<=12) {
                                        $greeting = "Hello";
                                    }elseif ($time >12 && $time<=15) {
                                        $greeting = "Good Afternoon";
                                    }else {
                                        $greeting = "Good Evening";
                                    }
                                    echo $greeting;
                                @endphp "{{session('fullname')? session('fullname'):"Null";}}"</h4>
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
                                    {{-- reboot restart and reset the router --}}
                                    @if(session('success_router'))
                                        <p class='text-success'>{{session('success_router')}}</p>
                                    @endif
                                    @if(session('error_router'))
                                        <p class='text-danger'>{{session('error_router')}}</p>
                                    @endif
                                    {{-- start of router information --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><strong><u>Personal Detail</u></strong></h6>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <table class="table">
                                            <tr>
                                                <td><b>Name</b></td>
                                                <td>{{$client_data[0]->client_name}}</td>
                                                <td><b>Address</b></td>
                                                <td>{{$client_data[0]->client_address}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Registration Date:</b></td>
                                                <td>{{$reg_date}}</td>
                                                <td><b>Monthly Pay:</b></td>
                                                <td>{{$client_data[0]->monthly_payment}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Contacts:</b></td>
                                                <td>{{$client_data[0]->clients_contacts}}</td>
                                                <td><b>Wallet Amount:</b></td>
                                                <td>{{$client_data[0]->wallet_amount}}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Username:</b></td>
                                                <td>{{$client_data[0]->client_username}}</td>
                                                <td><b>Change Password:</b></td>
                                                <td>
                                                    @php
                                                        $btnText = "Change password";
                                                        $otherClasses = "";
                                                        $btnLink = "/Credentials";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><strong><u>Account Detail</u></strong></h6>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <table class="table">
                                            <tr>
                                                <td><b>Account Number</b></td>
                                                <td>{{$client_data[0]->client_account}}</td>
                                                <td><b>Account Status</b></td>
                                                <td>
                                                    <p>
                                                        @if ($client_data[0]->client_status == 1)
                                                            <small class = 'badge bg-success'>Active</small>
                                                        @else
                                                            <small class = 'badge bg-danger'>In-Active</small>
                                                        @endif
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Subscription Plan:</b></td>
                                                <td>{{ ($client_data[0]->assignment == "static" ? $client_data[0]->max_upload_download : $client_data[0]->client_profile)." @ Kes ".$client_data[0]->monthly_payment}}</td>
                                                <td><b>Expiration Date:</b></td>
                                                <td>{{$expiration_date}}</td>
                                            </tr>
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
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    
    <!-- END PAGE LEVEL JS-->
</body>

</html>