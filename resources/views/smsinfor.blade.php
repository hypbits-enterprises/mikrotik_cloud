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
    <title>Hypbits - SMS details </title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
</head>
<style>
    .hide{
        display: none;
    }
</style>


<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
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
                    <h3 class="content-header-title">View SMS details</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/sms">My SMS</a>
                                </li>
                                <li class="breadcrumb-item">View SMS Details
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
                                <h4 class="card-title">View SMS details</h4>
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
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                        $otherClasses = "ml-1";
                                        $btnLink = "/sms";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/sms" class="btn btn-infor"><i class="fas fa-arrow-left"></i>
                                        Back to list</a>
                                </div>
                                <div class="container p-1">
                                    @php
                                        $btnText = "<i class=\"fas fa-refresh\"></i> Resend Sms";
                                        $otherClasses = "ml-1";
                                        $btnLink = "/sms/resend/".$sms_data[0]->sms_id;
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    {{-- <a href="/sms/resend/{{ $sms_data[0]->sms_id }}"
                                        class="btn btn-primary btn-sm {{$readonly}}">Resend Sms</a> --}}
                                </div>
                                <div class="row card-body">
                                    <div class="col-md-7">
                                        <label for="" class="form-control-label"><strong>Sms Content</strong></label>
                                        <div class="card p-1" style="background-color: rgb(231, 231, 231)">
                                            <p>{{ $sms_data[0]->sms_content }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-5 ">
                                        <div>
                                            <h6>Message Data</h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p>Sms Id:</p>
                                            </div>
                                            <div class="col-md-8">
                                                <p>{{ $sms_data[0]->sms_id }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p>Date Sent:</p>
                                            </div>
                                            <div class="col-md-8">
                                                <p>{{ $date }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p>Sms Recipient:</p>
                                            </div>
                                            <div class="col-md-8">
                                                <p>{{ $sms_data[0]->recipient_phone }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p>Recipient Name:</p>
                                            </div>
                                            <div class="col-md-8">
                                                <p>{{ $client_name }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p>SMS Type:</p>
                                            </div>
                                            <div class="col-md-8">
                                                <p>{{ $sms_type }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                            $otherClasses = "";
                                            $btnLink = "/sms";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/sms" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back
                                            to list</a> --}}
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-trash\"></i> Delete";
                                            $otherClasses = "";
                                            $btnLink = "/sms/delete/".$sms_data[0]->sms_id;
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/sms/delete/{{ $sms_data[0]->sms_id }}" class="btn btn-danger {{$readonly}}"><i
                                                class="fas fa-trash"></i> Delete</a> --}}
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
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
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

</body>

</html>
