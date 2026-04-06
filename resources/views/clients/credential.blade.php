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
    <title>Hypbits - Dashboard</title>
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
    <style>
        .hide{
            display: none;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
    <x-client-menu active="dashboard"></x-client-menu>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Change Credential</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/ClientDashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">Change Credential
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
                                <h4 class="card-title">Change Credential</h4>
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
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to Dashboard";
                                        $otherClasses = "my-1";
                                        $btnLink = "/ClientDashboard";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="infor" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/ClientDashboard" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                                    <p class="card-text">- Below you will be able to change your password. <br>
                                    - Enter your old password and your new password to change it.</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="text-primary"><strong><u>Change Password</u></strong></h6>
                                            <p id="transaction" class="hide"></p>
                                            <p id="clientids" class="hide">{{session('client_id')}}</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="/changePassword">
                                        @if(session('success'))
                                            <p class="text-success">{{session('success')}}</p>
                                        @endif
                                        @if(session('error'))
                                            <p class="text-danger">{{session('error')}}</p>
                                        @endif
                                        <div class="row">
                                            @csrf
                                            <div class="col-md-4">
                                                <label for="old_password" class="form-control-label">Old Password</label>
                                                <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="new_password" class="form-control-label">New Password</label>
                                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="repeat_password" class="form-control-label">Repeat Password</label>
                                                <input type="password" name="repeat_password" id="repeat_password" class="form-control" placeholder="Repeat Password" required>
                                            </div>
                                            <div class="col-md-4 my-2">
                                                {{-- <button type="submit" class="btn btn-primary">Change Password</button> --}}
                                                @php
                                                    $btnText = "Change Password";
                                                    $otherClasses = "";
                                                    $btn_id = "login-btn";
                                                    $btnSize="md";
                                                    $type = "submit";
                                                    $readonly = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            </div>
                                        </div>
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
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
    {{-- <script src="/theme-assets/js/core/confirm.js" type="text/javascript"></script> --}}
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <!-- END PAGE LEVEL JS-->
</body>

</html>