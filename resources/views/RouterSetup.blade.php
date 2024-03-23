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
    <title>Hypbits - New Routers -Set Up</title>
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
                                class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                data-toggle="dropdown">
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
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span class="menu-title"
                            data-i18n="">Dashboard</span></a>
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
                <li class="{{showOption($priviledges,"Account and Profile")}} nav-item"><a href="/Accounts"><i class="ft-lock"></i><span class="menu-title"
                            data-i18n="">Account and Profile</span></a>
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
                    <h3 class="content-header-title">New Mikrotik Router Set-up</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Routers">My Routers</a>
                                </li>
                                <li class="breadcrumb-item active">Router Set-Up
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
                                <h4 class="card-title">Router Set-Up</h4>
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
                                    <p class="text-secondary"><b>Note:</b>
                                        <br>
                                        Fill all the fields to add a router.</p>
                                    @if (session('success_router'))
                                        <p class='text-success'>{{ session('success_router') }}</p>
                                    @endif
                                    @if (session('error_router'))
                                        <p class='text-danger'>{{ session('error_router') }}</p>
                                    @endif
                                    <h5 class="text-center"><b>Router Configuration</b></h5>
                                    <hr class="p-0 my-1 border border-primary w-75 mx-auto">
                                    
                                    <nav class="w-50 mx-auto">
                                        <div class="nav nav-pills nav-fill d-flex" id="nav-tab" role="tablist">
                                            <span style="cursor: pointer;" class="nav-link active"
                                                id="step1-tab">Step 1</span>
                                            <span style="cursor: pointer;" class="nav-link " id="step2-tab">Step
                                                2</span>
                                            <span style="cursor: pointer;" class="nav-link " id="step3-tab">Step
                                                3</span>
                                            <span style="cursor: pointer;" class="nav-link " id="step4-tab">Step
                                                4</span>
                                            <span style="cursor: pointer;" class="nav-link " id="step5-tab">Step
                                                5</span>
                                            <span style="cursor: pointer;" class="nav-link " id="step6-tab">Step
                                                6</span>
                                            {{-- <span style="cursor: pointer;" class="nav-link " id="step7-tab">Step
                                                7</span> --}}
                                        </div>
                                        <p id=""></p>
                                    </nav>
                                    <div class="container border border-secondary w-50 mx-auto" id="">
                                        <p class="text-secondary"><b>Message:</b></p>
                                        <p id="error_handlers"></p>
                                    </div>
                                    <div class="container my-1 steppers" id="step1">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 1: </b>Reset & Connect to your router.</h6>
                                            <span class="text-danger"><b>- Reset your router first before running this set-up.</b></span><br>
                                            <span class="text-danger">- Do not restore any <b>default configurations.</b></span><br>
                                            <span>- Use winbox to set API credentials</span><br>
                                            <span>- Set up bridges and their IP Addresses.</span><br>
                                            <span>- Lastly down below provide your router`s API username and API password to proceed.</span>
                                        </div>
                                        <div class="container p-1 row">
                                            <div class="col-md-4">
                                                <label for="router_name" class="form-control-label">Router Name:</label>
                                                <input type="text" value="Router 1" class="form-control" id="router_name" placeholder="Name to identify router">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_ip_address" class="form-control-label">Router IP address:</label>
                                                <input type="text" class="form-control" value="192.168.88.1" id="router_ip_address" placeholder="Router Ip address">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_username" class="form-control-label">Router Username:</label>
                                                <input type="text" class="form-control" value="hillary" id="router_username" placeholder="Router Username">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_password" class="form-control-label">Router Password:</label>
                                                <input type="password" value="1234" class="form-control" id="router_password" placeholder="Router Password">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_api_port" class="form-control-label">Router API port:</label>
                                                <input type="text" class="form-control" value="8728" id="router_api_port" placeholder="ex: 8728">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container my-1 steppers d-none" id="step2">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 2: </b> Configure Interfaces.</h6>
                                            <span>- Configure your router interfaces. <br>
                                                - Create a bridge and add the interfaces to that bridge <br>
                                                - We advise that you create two bridges one for the <b>PPPoE Assignment</b> and the other for <b>Static Assignment</b>
                                            </span><br>
                                            <span><b class="text-danger" >Note:</b> <br>- When un-assigning and assigning interfaces to a bridge you might loose connection especially on the interface connected to this device.</span>
                                        </div>
                                        <div class="container row">
                                            <div class="col-md-4 p-1 mr-1 border border-teal">
                                                <h6 class="text-center">Interfaces List In Router <span class="text-primary d-none" id="loading"><i class="fas fa-spinner fa-spin"></i></span></h6>
                                                <span id="interface_lists"></span>
                                                {{-- <ul>
                                                    <li>Bridge 1</li>
                                                    <ul>
                                                        <li>ether1</li>
                                                    </ul>
                                                    <li>Bridge 2</li>
                                                    <ul>
                                                        <li>ether3 <span data-toggle='tooltip' title='Remove port from Bridge 2' class='funga ml-1'>x</span></li>
                                                        <li>ether4</li>
                                                        <li>ether5</li>
                                                    </ul>
                                                </ul> --}}
                                            </div>
                                            <div class="col-md-7 p-1 border border-teal">
                                                <div class="container" id="add_bridge_window">
                                                    <h6 class="text-center"><b>Create a bridge</b></h6>
                                                    <label for="bridge_name" class="form-control-label"><b>Bridge Name:</b></label>
                                                    <input type="text" class="form-control" value="bridge-static" id="bridge_name" placeholder="Bridge Name">
                                                    <label for="" class="form-control-label"><b>Select Interfaces to include in the bridge</b></label>
                                                    <span id="interface_list"></span>
                                                    <div class="row my-2">
                                                        <div class="col-md-6">
                                                            <button id="edit_bridges" type="button" class="btn btn-transparent"><small>Edit existing Bridge</small></button><br><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary float-right" id="add_bridge" ><span class="d-none " id="bridge_create_loader"><i class="fas fa-spinner fa-spin"></i></span>  Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="container d-none" id="edit_bridge_window">
                                                    <h6 class="text-center"><b>Assign available interfaces to a bridge</b></h6>
                                                    <label for="bridge_list" class="form-control-label"><b>Select bridge</b></label>
                                                    <span id="bridges"></span>
                                                    <label for="" class="form-control-label"><b>Select interfaces available to be added to the bridge</b></label>
                                                    <span id="bridge-adding"></span>
                                                    <div class="container row my-2">
                                                        <div class="col-md-6">
                                                            <button id="add_bridges" type="button" class="btn btn-transparent"><small>Click to add bridge</small></button><br><br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button type="button" id="change_bridge" class="btn btn-primary float-right"> <span class="d-none " id="bridge_edit_loader"><i class="fas fa-spinner fa-spin"></i></span> Change Bridge</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="container border border-secondary mx-auto" id="">
                                                    <p class="text-secondary"><b>Message:</b></p>
                                                    <p id="error_handling"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container my-1 steppers d-none" id="step3">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 3: </b> How are you getting your internet?.</h6>
                                            <span>- Configure ways you get your internet from to your router.</span><br>
                                            <span>- <b class="text-danger">Don`t configure more than one way of accessing your internet it may cause conflict causing no access to the internet at all.</b></span><br>
                                            <span>- The left window show the set mode of accessing your internet, its left blank if no mode is set.</span>
                                        </div>
                                        <div class="container p-1 border border-teal mb-2">
                                            <div class="container w-50 mx-0">
                                                <label for="internet_access_methods" class="form-control-label">How do you get internet access to the router</label>
                                                <select name="internet_access_methods" id="internet_access_methods" class="form-control border-teal">
                                                    <option value="" hidden>Select Option</option>
                                                    <option selected value="dynamic">Dynamic Assignment</option>
                                                    <option value="static">Static Assignment</option>
                                                    <option value="ppp">PPPoE Assignment</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mr-1 border border-teal p-1">
                                                <h6><b>Mode of Internet access</b> <span class="text-primary d-none" id="load_internet_access"><i class="fas fa-spinner fa-spin"></i></span></h6>
                                                <div class="container" id="internet_access_modes">

                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="container mx-1 border border-teal py-1 s_win d-none" id="static_window">
                                                    <h6>Static Assignment</h6>
                                                    <label for="ip_address_assigned" class="form-control-label">Provide the IP address assigned</label>
                                                    <input type="text" name="ip_address_assigned" id="ip_address_assigned" class="form-control" placeholder="ex: 192.168.1.13/24">
                                                    <label for="default_gw" class="form-control-label">Provide the default gateway IP address</label>
                                                    <input type="text" name="default_gw" id="default_gw" class="form-control" placeholder="ex: 192.168.1.1">
                                                    <label for="default_dns_server" class="form-control-label">Provide the DNS server address</label>
                                                    <input type="text" name="default_dns_server" id="default_dns_server" class="form-control" placeholder="ex: 8.8.8.8">
                                                    <button class="btn btn-sm btn-primary my-1" id="set_static_assignment" type="button">Set & Test <span class="text-primary d-none" id="load_static_access"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                    <p id="error_static_window"></p>
                                                </div>
                                                <div class="container mx-1 border border-teal py-1 s_win text-dark d-none" id="pppoe_window">
                                                    <h6>PPPoE Assignment</h6>
                                                    <label for="pppoer_username" class="form-control-label">PPPoE Username</label>
                                                    <input type="text" name="pppoer_username" id="pppoer_username" class="form-control" placeholder="PPPoE Username">
                                                    <label for="pppoe_password" class="form-control-label">PPPoE Password</label>
                                                    <input type="password" name="pppoe_password" id="pppoe_password" class="form-control" placeholder="PPPoE Password">
                                                    <button class="btn btn-sm btn-primary my-1" id="set_pppoe_connection" type="button">Set & Test <span class="text-primary d-none" id="load_pppoe_access"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                    <p id="error_pppoe_window"></p>
                                                </div>
                                                <div class="container mx-1 border border-teal py-1 s_win text-dark" id="win_dynamic">
                                                    <h6><b>Dynamic Assignment</b></h6>
                                                    <p>Click the button below once to set dynamic assignment</p>
                                                    <button class="btn btn-primary" id="set_dynamic">Set Dynamic Assignment <span class="text-primary d-none" id="spinner_dynamic"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                    <span id="dynamic_set_err"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container my-1 steppers d-none" id="step4">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 4: </b> How are your users accessing your internet?.</h6>
                                            <span>- Choose and configure ways your users get your internet access from your router.</span><br>
                                            <span><b class="text-danger">Note:</b><br>- Its advisable you only setup your different ways for users accessing your internet in different interfaces by creating two bridges with different ports for their purpose.</span><br>
                                            <span>- Most appropriate way will be setting up three bridges this will avoid confusion and in some cases unauthorised access.</span>
                                        </div>
                                        <div class="container p-1">
                                            <div class="container col-md-4 mx-0">
                                                <label for="internet_distro_methods" class="form-control-label">How are your users going to access the internet from your router</label>
                                                <select name="internet_distro_methods" id="internet_distro_methods" class="form-control">
                                                    <option value="" hidden>Select Option</option>
                                                    <option value="static">Set-up Static Assignment</option>
                                                    <option selected value="ppp">Set-up PPPoE Assignment</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="container mx-0 d-none give_net" id="static_setup">
                                            <div class="row">
                                                <div class="col-md-4 border border-teal p-1 mx-1">
                                                    <h6>Static assignment does not need any setup</h6>
                                                    <ul>
                                                        <li>1. Proceed and start adding your clients when done with this set up </li>
                                                        <li>2. Always select <b>Static Assignment</b> when assigning static clients </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container mx-0 d-none give_net" id="dynamic_setup">
                                            <div class="row">
                                                <div class="col-md-4 border border-teal p-1 mx-1">
                                                    <h6>Steps involved in setting up Dynamic Assignment</h6>
                                                    <ul>
                                                        <li>1. Provide network address and interfaces you want to set dynamic assignment </li>
                                                        <li>2. Create a pool</li>
                                                        <li>3. Create a server and link it to the pool of address and the interface</li>
                                                    </ul>
                                                </div>
                                                <div class="border border-teal col-md-7 p-1">
                                                    <div class="container" id="dynamic_set_1">
                                                        <p><b>Step 1: Creating network for the interface to use</b></p>
                                                        <label for="interface_selection">Select Interface</label>
                                                        <select name="interface_selection" id="interface_selection" class="form-control">
                                                            <option value="" hidden>Select Interface</option>
                                                            <option value="dynamic">Set-up Dynamic Assignment</option>
                                                            <option value="static">Set-up Static Assignment</option>
                                                            <option value="ppp">Set-up PPPoE Assignment</option>
                                                        </select>
                                                        <label for="newtork_address" class="form-control-label">Network Address</label>
                                                        <input type="text" class="form-control" id="newtork_address" placeholder="ex 192.164.88.0/24">
                                                    </div>
                                                    <div class="container" id="dynamic_set_2">
                                                        <p><b>Step 2: Creating pool address for the server</b></p>
                                                        <label for="pool_address">Pool name</label>
                                                        <input type="text" class="form-control" id="pool_address" placeholder="ex pool1">
                                                        <label for="pool_range" class="form-control-label">Pool Range</label>
                                                        <input type="text" class="form-control" id="pool_range" placeholder="192.164.88.3 - 192.164.88.200">
                                                    </div>
                                                    <div class="container" id="dynamic_set_2">
                                                        <p><b>Step 3: Creating DHCP-Server</b></p>
                                                        <label for="dhcp_server_name">DHCP-Server name</label>
                                                        <input type="text" class="form-control" id="dhcp_server_name" placeholder="Server Name">
                                                        <label for="lease_time" class="form-control-label">Address Lease time</label>
                                                        <select name="lease_time" id="lease_time" class="form-control">
                                                            <option value="" hidden>Select Lease Time</option>
                                                            <option value="10m">10 Minute</option>
                                                            <option value="15m">15 Minute</option>
                                                            <option value="30m">30 Minute</option>
                                                            <option value="60m">60 Minute</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="container mx-0 give_net" id="pppoe_set_up">
                                            <div class="row">
                                                <div class="col-md-4 border border-teal mx-1 p-1">
                                                    <h6>Steps Involved In Setting up PPPoE</h6>
                                                    <ul>
                                                        <li>1. Create a pool of address</li>
                                                        <li>2. Create a profile each assigned its own pool</li>
                                                        <li>3. Create a server assign a default profile and the interface.</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-7 border border-teal p-1">
                                                    <div class="container" id="pppoe_set_1">
                                                        <p><b>Step 1: Creating address Pool</b></p>
                                                        <b>Note:</b>
                                                        <li>A pool is a range of ip addresses that is assigned to users of your network</li>
                                                        <li>Provide the name and the ranges</li>
                                                        <br>
                                                        {{-- <span><b class="text-danger">Note:</b><br> The pool address should be in the same network as the interface.</span> --}}
                                                        <label for="pool_names" class="form-control-label"><b>Pool Name</b></label>
                                                        <input type="text" name="pool_names" placeholder="Pool Name" id="pool_names" class="form-control">
                                                        <label for="pppoe_pool" class="form-control-label"><b>Address Pool</b></label>
                                                        <p class="text-primary">Use hyphen <b>(-)</b> to separate the pool eg: <b>(10.10.10.2 - 10.10.10.252)</b> </p>
                                                        <input type="text" name="pppoe_pool" id="pppoe_pool" class="form-control" placeholder="ex: 192.168.1.3-192.168.1.254">
                                                        <div class="row my-2">
                                                            <div class="col-md-6">
                                                                <button class="btn btn-light float-left" id="skip_create_pool">Already have pool ?</button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button class="btn btn-primary float-right" id="add_pool">Add Pool<span class="text-primary d-none" id="load_add_pool_access"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                            </div>
                                                        </div>
                                                        <p id="err_handler_pools"></p>
                                                    </div>
                                                    <div class="container d-none" id="pppoe_set_2">
                                                        <p><b>Step 2: Create a Profile</b> <span class="text-primary d-none" id="load_create_profile"><i class="fas fa-spinner fa-spin"></i></span></p>
                                                        <b>Note</b>
                                                        <li>A profile is used to manage a group of users of the same characteristics.</li>
                                                        <li>Provide the fields required below to define your profile</li><br>
                                                        <label for="profile_name" class="form-control-label"><b>Profile Name:</b></label>
                                                        <input type="text" class="form-control" id="profile_name" placeholder="Profile Name">
                                                        <label class="form-control-label" for="pool_name"><b>Select Pool</b></label>
                                                        <span id="pool_lists"></span>
                                                        <label for="gateway_address" class="form-control-label"><b>Gateway Address</b></label>
                                                        <input type="text" name="gateway_address" id="gateway_address" class="form-control" placeholder="Gateway Address">
                                                        <label for="" class="form-control-label mt-2"><b>Speed Limits</b></label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="upload_limit1" class="form-control-label"><b>Upload Limit</b></label>
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <input type="number" min="0" class="form-control" id="upload_units1" placeholder="Upload Limit">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <select name="upload_limit1" id="upload_limit1" class="form-control">
                                                                            <option value="" hidden>Select unit</option>
                                                                            <option value="M">Mbps</option>
                                                                            <option value="K">Kbps</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="upload_limit2" class="form-control-label"><b>Download Limit</b></label>
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <input type="number" min="0" class="form-control" id="upload_units2" placeholder="Upload Limit">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <select name="upload_limit2" id="upload_limit2" class="form-control">
                                                                            <option value="" hidden>Select unit</option>
                                                                            <option value="M">Mbps</option>
                                                                            <option value="K">Kbps</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <label for="only_one" class="form-control-label"><b>One Account per user</b></label>
                                                        <select name="only_one" id="only_one" class="form-control">
                                                            <option value="" hidden>Select Option</option>
                                                            <option selected value="yes">Yes</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                        <div class="row my-2">
                                                            <div class="col-md-4">
                                                                <button class="btn btn-secondary" id="back_to_step1"><i class="fas fa-arrow-left"></i> Back</button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <button class="btn btn-light" id="skip_step2">I have profile</button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <button class="btn btn-primary" type="button" id="save_profiles">Save Profile<span class="text-primary d-none" id="load_save_pppoe_profiles"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                            </div>
                                                        </div>
                                                        <p id="error_handling_ppoe"></p>
                                                    </div>
                                                    <div class="container d-none" id="pppoe_set_3">
                                                        <p><b>Step 3: Create a Server</b> <span class="text-primary d-none" id="create_server"><i class="fas fa-spinner fa-spin"></i></span></p>
                                                        <b>Note:</b>
                                                        <li>- This server is responsible for authenticating users and assigning them ip addresses</li>
                                                        <label for="server_names" class="form-control-label"><b>Server Name:</b></label>
                                                        <input type="text" id="server_names" class="form-control" placeholder="Server Name">
                                                        <span id="interface_pppoe"></span>
                                                        <div class="row my-1">
                                                            <div class="col-md-6">
                                                                <button class="btn btn-transparent" id="back_step2"><i class="fas fa-arrow-left"></i> Back</button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button class="btn btn-primary" id="save_ppoe_server"><i class="fas fa-save"></i> Save <span class="text-primary d-none" id="load_save_pppoe_server"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                            </div>
                                                        </div>
                                                        <p id="error_handling_pppoe_server"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container my-1 steppers d-none" id="step5">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 5: </b> Set up wireless.</h6>
                                            <span>- Configure your wireless connection at this stage.</span>
                                        </div>
                                        <div class="container row">
                                            <div class="col-md-4 mx-1 border border-teal p-1">
                                                <p><b>Steps to configure Wi-Fi conection</b></p>
                                                <ul>
                                                    <li>Set-up Security Profile</li>
                                                    <li>Add the wireless interface to a bridge</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-7 border border-teal p-1">
                                                <div class="container" id="security_window">
                                                    <h6 class="text-center">Security Profile</h6>
                                                    <label for="security_profile_name" class="form-control-label">Security Profile Name:</label>
                                                    <input type="text" class="form-control" id="security_profile_name" placeholder="Security Profile Name">
                                                    <label for="profile_password">Profile Password</label>
                                                    <input type="text" class="form-control" id="profile_password" placeholder="Profile Password">
                                                    <div class="row my-2">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-transparent" id="skip_adding_prof">Already have profile?</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary" id="save_wireless_profile"><i class="fas fa-save"></i> Save Profile <span class="text-primary d-none" id="load_save_profiles"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                        </div>
                                                    </div>
                                                    <p id="error_security_prof"></p>
                                                </div>
                                                {{-- select wlan interface --}}
                                                <div class="container d-none" id="wifi_profile">
                                                    <h6><b>Wi-Fi Set-up</b> <span class="text-primary d-none" id="wifi_profiles"><i class="fas fa-spinner fa-spin"></i></span></h6>
                                                    <label for="wifi_name" class="form-control-label my-2"><b>Wi-Fi name</b></label>
                                                    <input type="text" class="form-control" id="wifi_name" placeholder="Wi-Fi name">
                                                    <label for="security_profile" class="form-control-label"><b>Security Profile</b></label>
                                                    <span id="security_holder"></span>
                                                    <div class="row my-2">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-transparent" id="back_to_profile"><i class="fas fa-arrow-left"></i> Set Profile</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary" id="save_wifi"><i class="fas fa-save"></i> Save <span class="text-primary d-none" id="load_save_ssid"><i class="fas fa-spinner fa-spin"></i></span></button>
                                                        </div>
                                                    </div>
                                                    <span id="profile_errors"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container my-1 steppers d-none" id="step6">
                                        <div class="my-1 container p-1 border border-teal">
                                            <h6><b>Step 6: </b> Complete Set-up.</h6>
                                            <span>- Confirm and check your configurations at this stage.</span>
                                        </div>
                                        <div class="my-1 container border border-teal">
                                            <b>Step 1 <span class="text-primary d-none" id="step_one_load"><i class="fas fa-spinner fa-spin"></i></span></b>
                                            <p>Connect Router?</p>
                                            <hr>
                                            <span id="step_one_conf"></span>
                                        </div>
                                        <div class="my-1 container border border-teal">
                                            <b>Step 2 <span class="text-primary d-none" id="step_two_load"><i class="fas fa-spinner fa-spin"></i></span></b>
                                            <p>Interface Configuration</p>
                                            <hr>
                                            <span id="step_two_conf"></span>
                                        </div>
                                        <div class="my-1 container border border-teal">
                                            <b>Step 3 <span class="text-primary d-none" id="step_three_load"><i class="fas fa-spinner fa-spin"></i></span></b>
                                            <p>How are you getting your internet ?</p>
                                            <hr>
                                            <span id="step_three_conf"></span>
                                        </div>
                                        <div class="my-1 container border border-teal">
                                            <b>Step 4 <span class="text-primary d-none" id="step_four_load"><i class="fas fa-spinner fa-spin"></i></span></b>
                                            <p>How are you users getting internet ?</p>
                                            <hr>
                                            <span id="step_four_conf"></span>
                                        </div>
                                        <div class="my-1 container border border-teal">
                                            <b>Step 5 <span class="text-primary d-none" id="step_five_load"><i class="fas fa-spinner fa-spin"></i></span></b>
                                            <p>Wireless Configuration</p>
                                            <hr>
                                            <span id="step_five_conf"></span>
                                        </div>
                                    </div>
                                    <div class="container" id="navigator">
                                        <div class="row">
                                            <div class="col-md-5 mr-1">
                                                <button class="btn btn-secondary float-right" disabled id="prev_button">Previous</button>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-primary float-left" id="next_button"> <span class="d-none " id="loader"><i class="fas fa-spinner fa-spin"></i></span> Next & Save</button>
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
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/router_setup.js"></script>
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
