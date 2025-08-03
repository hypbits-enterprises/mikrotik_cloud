<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Quick Register</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    

    <!-- BEGIN Custom CSS-->
    <style>
        .hide{
            display: none;
        }
        .tooltip-inner {
            text-align: left !important;
        }
    </style>
    <!-- END Custom CSS-->
</head>
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    <x-menu active="quick_register"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Quick Register");
    @endphp
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Client Quick Register</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Client Quick Register 
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
                                <h4 class="card-title">Client Quick Register</h4>
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
                                <p>- Register clients in either of the following ways!</p>
                                @if(session('success_reg'))
                                    <p class="text-success">{{session('success_reg')}}</p>
                                @endif
                                @if (session('success'))
                                    <p class="success">{{ session('success') }}</p>
                                @endif
                                @if (session('network_presence'))
                                    <p class="text-danger">{{ session('network_presence') }}</p>
                                @endif
                                @if (session('error'))
                                    <p class="text-danger">{{ session('error') }}</p>
                                @endif
                                @php
                                    $btnText = "<i class=\"ft-user-check\"></i> Register Static Client";
                                    $otherClasses = "mt-1 ".$readonly;
                                    $btnLink = "/Quick-Register/New-Static";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link :otherAttributes="$otherAttributes" toolTip="Register new statically assigned client!" :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="md" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/Quick-Register/New-Static" data-toggle="tooltip" title="Register new statically assigned client!" class="btn btn-primary {{$readonly}}"><i class="ft-user-check"></i> Register Static Client</a> --}}
                                @php
                                    $btnText = "<i class=\"ft-user-plus\"></i> Register PPPoE Client";
                                    $otherClasses = "mt-1 ".$readonly;
                                    $btnLink = "/Quick-Register/New-PPPoE";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link :otherAttributes="$otherAttributes" toolTip="Register new PPPoE assigned client!" :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="md" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/Quick-Register/New-PPPoE" data-toggle="tooltip" title="Register new PPPoE assigned client!" class="btn btn-secondary {{$readonly}}"><i class="ft-user-plus"></i> Register PPPoE Client</a> --}}
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
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- END VENDOR JS-->

    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    
    <!-- BEGIN CLIENT JS-->
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