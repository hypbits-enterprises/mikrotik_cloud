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
    <title>Hypbits - New Routers</title>
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
    
    <x-menu active="myrouters"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"My Routers");
    @endphp

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
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Routers">My Routers</a>
                                </li>
                                <li class="breadcrumb-item active">Add Router
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
                                @php
                                    $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                    $otherClasses = "my-2";
                                    $btnLink = "/Routers";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link btnType="infor" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/Routers" class="btn btn-infor my-2"><i class="fas fa-arrow-left"></i> Back to
                                    list</a> --}}
                                    <hr>
                                <h4 class="card-title">Add Router</h4>
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
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p class="card-text">Fill all the fields to add a router.</p>
                                    @if (session('success_router'))
                                        <p class='text-success'>{{ session('success_router') }}</p>
                                    @endif
                                    @if (session('error_router'))
                                        <p class='text-danger'>{{ session('error_router') }}</p>
                                    @endif
                                    <form action="{{url()->route("newCloudRouter")}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="router_name" class="form-control-label"><b>Router Name</b></label>
                                                <input type="text" name="router_name" id="router_name"
                                                    class="form-control rounded-lg p-1" placeholder="Router name"
                                                    required
                                                    @if (session("router_name"))
                                                        value="{{session("router_name")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="routers_physical_address" class="form-control-label"><b>Router Physical Location</b></label>
                                                <input type="text" name="routers_physical_address" id="routers_physical_address"
                                                    class="form-control rounded-lg p-1" placeholder="ex Mshomoroni, Mombasa, Ke."
                                                    required
                                                    @if (session("routers_physical_address"))
                                                        value="{{session("routers_physical_address")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="routers_coordinates" class="form-control-label"><b>Routers Co-ordinates (Optional) <i class="ft-info" data-toggle="tooltip" title="" data-original-title="On google map, right click on the router`s pin location and copy the co-ordinates then paste them here!"></i></b></label>
                                                <input type="text" name="routers_coordinates" id="routers_coordinates"
                                                    class="form-control rounded-lg p-1" placeholder="Google maps co-ordinates"
                                                    @if (session("routers_coordinates"))
                                                        value="{{session("routers_coordinates")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group d-none">
                                                <label for="winbox_port" class="form-control-label"><b>Winbox Port</b></label>
                                                <input type="hidden" name="winbox_port" id="winbox_port"
                                                    class="form-control rounded-lg p-1" placeholder="Default - 8291" 
                                                    required
                                                    @if (session("winbox_port"))
                                                        value="{{session("winbox_port")}}"
                                                    @else
                                                        value="8291"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group d-none">
                                                <label for="api_ports" class="form-control-label"><b>API Port</b></label>
                                                <input type="text" name="api_ports" id="api_ports"
                                                    class="form-control rounded-lg p-1" placeholder="Default - 1982"
                                                    required
                                                    @if (session("api_ports"))
                                                        value="{{session("api_ports")}}"
                                                    @else
                                                        value="1982"
                                                    @endif
                                                    >
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-plus\"></i> Add Router";
                                                    $otherClasses = "";
                                                    $btn_id = "";
                                                    $btnSize="sm";
                                                    $type = "submit";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="danger" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-danger" {{$readonly}} type="submit"><i
                                                        class="ft-plus"></i> Add Router</button> --}}
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                    $otherClasses = "";
                                                    $btnLink = "/Routers";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button-link btnType="secondary" btnSize="sm" toolTip="Transaction`s Statistics" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                {{-- <a class="btn btn-secondary btn-outline" href="/Routers"
                                                    type="button"><i class="ft-x"></i> Cancel</a> --}}
                                            </div>
                                        </div>
                                    </form>
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
</body>

</html>
