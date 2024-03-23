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
    <title>Hypbits - Router Details</title>
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
@php
date_default_timezone_set('Africa/Nairobi');
    $privilleged = session("priviledges");
    $priviledges = ($privilleged);
    function showOption($priviledges,$name){
        if (isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return "";
                    }
                }
            }
        }
        return "hide";
    }
    function readOnly($priviledges,$name){
        if (isJson($priviledges)){
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->readonly) {
                        return "disabled";
                    }
                }
            }
        }
        return "";
    }
    // get the readonly value
    $readonly = readOnly($priviledges,"My Routers");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<style>
    .funga,.my_funga{
        font-weight: 800;
        font-size: 20px;
        cursor: pointer;
        color: gray;
        position: relative;
    }
    .funga:hover,.my_funga:hover{
        color: orangered;
    }
    .hide{
        display: none;
    }
</style>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->

    <nav
        class="header-navbar navbar-expand-md navbar  navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light">
        <div class="navbar-wrapper">
            <div class="navbar-container content ">
                <div class="collapse navbar-collapse show" id="navbar-mobile">
                    <ul class="nav navbar-nav mr-auto float-left">
                        <li class="nav-item d-block d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                                href="#"><i class="ft-menu"></i></a></li>
                        <li class="nav-item dropdown navbar-search">
                            <span class="text-light">Hello, {{ session('Usernames') }}</span>
                        </li>
                    </ul>
                    @if (!Session::has('Usernames'))
                        @php
                            header('Location: ' . URL::to('/Login'), true, 302);
                            exit();
                        @endphp
                    @endif
                    <ul class="nav navbar-nav float-right">
                        <li>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="arrow_box_right"><a class="dropdown-item" href="#"><i
                                            class="ft-book"></i> Read Notices</a><a class="dropdown-item"
                                        href="#"><i class="ft-check-square"></i> Mark all Read </a></div>
                            </div>
                        </li>
                        <li class="dropdown dropdown-user nav-item"><a
                                class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <span class="avatar avatar-online"> <img style="width: 100px; height: 40px;"
                                        src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                        alt="avatar"><i></i> </span></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="arrow_box_right"><a class="dropdown-item" href="#"><span
                                            class="avatar avatar-online"><img style="width: 100px; height: 30px;"
                                                src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                                alt="avatar"><br><br><span
                                                class="user-name text-bold-700 ml-1">{{ session('Usernames') }}</span></span></a>
                                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/Accounts"><i
                                            class="ft-user"></i>Account & Settings</a>
                                    {{-- <a class="dropdown-item" href="#"><i class="ft-mail"></i> My Inbox</a><a class="dropdown-item" href="#"><i class="ft-check-square"></i> Task</a><a class="dropdown-item" href="#"><i class="ft-message-square"></i> Chats</a> --}}
                                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/Login"><i
                                            class="ft-power"></i> Logout</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true"
        data-img="/theme-assets/images/backgrounds/02.jpg">
        <div class="navbar-header" style="height: 120px">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75 text-center" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard">
                        <img class="w-100 mx-auto" height="100" alt="Your Logo Appear Here"
                            src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                    </a></li>
                <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
            </ul>
        </div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span
                            class="menu-title" data-i18n="">Dashboard</span></a>
                </li>
                <li class="{{showOption($priviledges,"My Clients")}} nav-item"><a href="/Clients"><i class="ft-users"></i><span class="menu-title"
                            data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} nav-item"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                    </ul>
                </li>
                <li class="{{showOption($priviledges,"My Routers")}} active"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title"
                            data-i18n="">My Routers</span></a>
                </li>
                <li class="{{showOption($priviledges,"SMS")}} nav-item"><a href="/sms"><i class="ft-mail"></i><span class="menu-title"
                            data-i18n="">SMS</span></a>
                </li>
                <li class="{{showOption($priviledges,"Account and Profile")}} nav-item"><a href="/Accounts"><i class="ft-lock"></i><span
                            class="menu-title" data-i18n="">Account and Profile</span></a>
                </li>
            </ul>
        </div>
        <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
        <div class="navigation-background">
        </div>
    </div>
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
                                <h4 class="card-title">Update Router</h4>
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
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/Routers" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to
                                                list</a>
                                        </div>
                                        <div class="col-md-6">
                                            <button {{$readonly}} id="delete_user" class="btn btn-danger text-lg float-right"><i class="ft-trash-2"> Delete</i></button>
                                            <div class="container d-none" id="prompt_del_window">
                                                <p class="text-primary" ><strong>Are you sure you want to permanently delete this Router?</strong></p>
                                                <p><b>Note:</b></p>
                                                <p>- All {{ $user_count[0]->Total }} User(s) associated to this router will be deleted from the database</p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <a href="/Routers/Delete/{{ $router_data[0]->router_id }}" class="btn btn-danger btn-sm" >Proceed to Delete</a>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="btn btn-secondary btn-sm" id="delet_user_no">Cancel</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p><b>Note</b></p>
                                    From the configurations below:
                                    <ul>
                                        <li>A user account will be created and given full rights. This rights will be important to handle all the router operations via the API.</li>
                                        <li>API services will be activated.</li>
                                        <li>Copy the following configuration paste it on your routers terminal to configure it for remote access. The come back and click connect button below.</li>
                                    </ul>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h6 class="text-center"><u>Router Infor</u></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="form-control-label"><b>Router`s Location:</b></label>
                                                <span>
                                                    @php
                                                        echo strlen($router_data[0]->router_coordinates) > 0 ? "<a class='text-danger' href = 'https://www.google.com/maps/place/".$router_data[0]->router_coordinates."' target = '_blank'><u>Locate Router</u> </a>" :"No Co-ordinates provided for the router!" ;
                                                    @endphp
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @if (session('success_router'))
                                        <p class='text-success'>{{ session('success_router') }}</p>
                                    @endif
                                    @if (session('error_router'))
                                        <p class='text-danger'>{{ session('error_router') }}</p>
                                    @endif
                                    <button id="configuration_show_button" class="btn btn-secondary btn-sm my-2 {{$router_data[0]->activated == 0 ? "d-none" : ""}}">Show Router Configuration</button>
                                    <div id="configuration_window" class="container shadow-0 border border-rounded p-1 w-100 {{$router_data[0]->activated == 1 ? "d-none" : ""}}">
                                        <button class="btn btn-sm btn-primary mb-2" id="send_to_clipboard"><i class="ft-copy" ></i> Copy</button>
                                        <h4 class="text-center">Router Configuration</h4>
                                        <p id="command_holder">
                                            {{-- <span class="text-success">## Set the SSTP Profile</span><br> --}}
                                            /ppp profile add name="SYSTEM_SSTP" comment="Do not delete: Default SYSTEM VPN profile"<br><br>
                                            
                                            {{-- <span class="text-success">## Add the SSTP Interface</span><br> --}}
                                            /interface sstp-client add name="SYSTEM_SSTP_ONE" connect-to={{$ip_address}} user={{$router_data[0]->sstp_username}} password={{$router_data[0]->sstp_password}} profile="SYSTEM_SSTP" authentication=pap,chap,mschap1,mschap2 disabled=no comment="Do not delete: SYSTEM connection to {{$router_data[0]->router_name}}"<br><br>
                                            
                                            {{-- <span class="text-success">## Configure routes</span><br> --}}
                                            /ip route add dst-address=192.168.254.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK1"<br>
                                            /ip route add dst-address=192.168.253.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK2"<br>
                                            /ip route add dst-address=192.168.252.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK3"<br><br>
                                            
                                            {{-- <span class="text-success">## Configure firewall</span><br> --}}
                                            /ip firewall filter add chain=input action=accept in-interface=SYSTEM_SSTP_ONE log=no log-prefix="" comment="Do not delete: Allow SYSTEM remote access" disabled=no<br>
                                            /ip firewall filter move [find where in-interface=SYSTEM_SSTP_ONE] destination=0<br><br>

                                            {{-- <span class="text-success">## Enable required services</span><br> --}}
                                            /ip service set api disabled=no port={{$router_data[0]->api_port}}<br>
                                            /ip service set winbox disabled=no port={{$router_data[0]->winbox_port}}<br>
                                            /ip service set api-ssl disabled=yes<br>
                                            /ip service set ftp disabled=yes<br>
                                            /ip service set ssl disabled=yes<br>
                                            /ip service set ftp disabled=yes<br>
                                            /ip service set www disabled=yes<br>
                                            /ip service set www-ssl disabled=yes<br><br>
                                            
                                            {{-- <span class="text-success">## version 6.49.10</span><br> --}}
                                            /user group add name="SYSTEM_FULL" policy="local,telnet,ssh,ftp,reboot,read,write,policy,test,winbox,password,web,sniff,sensitive,api,romon,tikapp,!dude" comment="Do not delete: SYSTEM user group"<br>
                                            <br>
                                            
                                            {{-- <span class="text-success">## version 7.11.2</span><br> --}}
                                            /user group add name="SYSTEM_FULL" policy="local,telnet,ssh,ftp,reboot,read,write,policy,test,winbox,password,web,sniff,sensitive,api,romon,rest-api" comment="Do not delete: SYSTEM user group"<br>
                                            
                                            /user add name="{{$router_data[0]->sstp_username}}" password="{{$router_data[0]->sstp_password}}" group="SYSTEM_FULL" comment="Do not delete: SYSTEM API User" <br>
                                            
                                            /beep
                                            <br>
                                        </p>
                                            <a href="{{url()->route("connect_router",$router_data[0]->router_id)}}" class="btn btn-success btn-sm mt-1 {{$router_data[0]->activated == 0 ? "" : "d-none"}}"><i class="ft-settings"></i> Connect</a>
                                    </div>
                                    <form action="{{url()->route("update_router")}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="router_name" class="form-control-label"><b>Router name</b></label>
                                                <input type="hidden" name="router_id"
                                                    value="{{ $router_data[0]->router_id }}">
                                                <input type="text" name="router_name" id="router_name"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_name }}"
                                                    placeholder="Router name" required>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="physical_location" class="form-control-label"><b>Physical Location</b></label>
                                                <input type="text" name="physical_location" id="physical_location"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_location }}"
                                                    placeholder="Router name" required>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="router_coordinates" class="form-control-label"><b>Routers Co-ordinates (Optional) <i class="ft-info" data-toggle="tooltip" title="" data-original-title="On google map, right click on the router`s pin location and copy the co-ordinates then paste them here!"></i></b></label>
                                                <input type="text" name="router_coordinates" id="router_coordinates"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_coordinates }}"
                                                    placeholder="Google maps co-ordinates">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="winbox_ports" class="form-control-label"><b>Winbox Port</b></label>
                                                <input type="text" name="winbox_ports" id="winbox_ports"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->winbox_port }}"
                                                    placeholder="Deafult - 8291" required>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="api_ports" class="form-control-label"><b>API Port</b></label>
                                                <input type="text" name="api_ports" id="api_ports"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->api_port }}"
                                                    placeholder="Deafult - 8728" required>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button {{$readonly}} class="btn btn-success text-dark" type="submit"><i
                                                        class="ft-upload"></i> Update</button>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-secondary btn-outline" href="/Routers"
                                                    type="button"><i class="ft-x"></i> Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                @if (count($router_stats) > 0)
                    {{-- router more information start --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">More Information</h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
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
                                        <p class="card-text"><strong>Note:</strong> <br>
                                            - Detailed information about the router and some actions to be carried out
                                            by the router
                                            <br>- The numeric data changes every time so when you referesh the page the
                                            data won`t be the same
                                            .
                                        </p>
                                        <h6 class="text-primary"><strong><u>Router Actions</u></strong></h6>
                                        {{-- reboot restart and reset the router --}}
                                        <div class="row my-1">
                                            <div class="col-md-4">
                                                <a href="/Router/Reboot/{{ $router_data[0]->router_id }}"
                                                    class="btn btn-primary disabled {{$readonly}}">Reboot</a>
                                            </div>
                                        </div>
                                        @if (session('success_router'))
                                            <p class='text-success'>{{ session('success_router') }}</p>
                                        @endif
                                        @if (session('error_router'))
                                            <p class='text-danger'>{{ session('error_router') }}</p>
                                        @endif
                                        {{-- start of router information --}}
                                        <h6 class="text-primary"><strong><u>Router Detail</u></strong></h6>
                                        <div class="row">
                                            <div class="col-lg-8 row">
                                                <div class="col-md-6">
                                                    <p><strong>Router Identity: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_stats[0]['version'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Up-Time: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_stats[0]['uptime'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Memory: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ number_format($router_stats[0]['free-memory'] / (1024*1024),2) }} MBS Out Of {{ number_format($router_stats[0]['total-memory'] / (1024*1024),2) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>HDD Space: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ number_format($router_stats[0]['free-hdd-space'] / (1024*1024),2) }} MBS Out Of {{ number_format($router_stats[0]['total-hdd-space'] / (1024*1024),2) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>CPU Load: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_stats[0]['cpu-load'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Board Name: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_stats[0]['board-name'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Clients Hosted: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $user_count[0]->Total }} Client(s)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ROuter end information --}}
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">More Information</h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
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
                                        <p>Connection to your router cannot be established this could be due to : <br> -
                                            Router may be rebooting at the the moment. <br> - The router ip address
                                            provided is not the correct one.</p>
                                        <p>You can refresh your page with the button below</p>
                                        <a href="" class="btn btn-primary">Refresh</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
    <script>
        var router_detail = @json($router_detail ?? '');
        var load_router_infor = "0";
        if (router_detail.length > 0) {
            load_router_infor = "1";
        }
        var router_data = @json($router_data ?? '');
    </script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    {{-- <script src="/theme-assets/js/core/view_router.js"></script> --}}
    <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
        var delete_user = document.getElementById("delete_user");
        delete_user.addEventListener("click", function () {
            document.getElementById("prompt_del_window").classList.remove("d-none");
        });
        var delet_user_no = document.getElementById("delet_user_no");
        delet_user_no.addEventListener("click", function () {
            document.getElementById("prompt_del_window").classList.add("d-none");
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
            .then(() => {
                console.log('Text successfully copied to clipboard:', text);
            })
            .catch(err => {
                console.error('Unable to copy text to clipboard', err);
            });
        }
        var send_to_clipboard = document.getElementById("send_to_clipboard");
        send_to_clipboard.addEventListener("click", function () {
            var this_inner_text = document.getElementById("command_holder").innerText;
            copyToClipboard(this_inner_text);
            // console.log(this_inner_text);
        });

        document.getElementById("configuration_show_button").onclick = function () {
            document.getElementById("configuration_window").classList.toggle("d-none");
        }
    </script>
</body>

</html>
