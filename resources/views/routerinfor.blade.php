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
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard"><img class="brand-logo w-100 mb-1 " alt="Chameleon admin logo"
                            src="/theme-assets/images/logo.jpeg" />
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
                                    <p class="card-text">Fill all the fields to add a router.</p>
                                    @if (session('success_router'))
                                        <p class='text-success'>{{ session('success_router') }}</p>
                                    @endif
                                    @if (session('error_router'))
                                        <p class='text-danger'>{{ session('error_router') }}</p>
                                    @endif
                                    <form action="/updateRouter" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="router_name" class="form-control-label">Router name</label>
                                                <input type="hidden" name="router_id"
                                                    value="{{ $router_data[0]->router_id }}">
                                                <input type="text" name="router_name" id="router_name"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_name }}"
                                                    placeholder="Router name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ip_address" class="form-control-label">Router ip
                                                    Address</label>
                                                <input type="text" name="ip_address" id="ip_address"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_ipaddr }}"
                                                    placeholder="ex 10.10.10.1" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="api_username" class="form-control-label">Router API
                                                    username</label>
                                                <input type="text" name="api_username" id="api_username"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_api_username }}"
                                                    placeholder="Router API username" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_api_password" class="form-control-label">Router API
                                                    password</label>
                                                <input type="password" name="router_api_password"
                                                    id="router_api_password" class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_api_password }}"
                                                    placeholder="Router API password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="router_api_port" class="form-control-label">Router API
                                                    port</label>
                                                <input type="number" value="8728" name="router_api_port"
                                                    id="router_api_port" class="form-control rounded-lg p-1"
                                                    value="{{ $router_data[0]->router_api_port }}"
                                                    placeholder="Router port number" required>
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
                @if (count($router_detail) > 0)
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
                                                    class="btn btn-primary {{$readonly}}">Reboot</a>
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
                                                    <p>{{ $router_detail[1] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Model: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[0] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Up-Time: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[2] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Memory: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[3] }} out of {{ $router_detail[4] }} Free</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>HDD Space: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[6] }} out of {{ $router_detail[7] }} Free</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>CPU Load: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[5] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Board Name: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $router_detail[8] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Clients Hosted: </strong></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>{{ $user_count[0]->Total }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="p-0 border border-cyan">
                                        <h6>Router Configuration</h6>
                                        <div class="container border border-teal p-2">
                                            <div class="container " id="view_config">
                                                <b>Interface configuration <span class="text-primary d-none" id="interface_conf"><i class="fas fa-spinner fa-spin"></i></span> <span class="text-primary" id="edit_interfaces" style="cursor: pointer"><i class="fas fa-pen-fancy"></i> <small>Edit</small></span></b>
                                                <span id="interface_configs"></span>
                                            </div>
                                            <div class="container d-none" id="edit_interfaces_win">
                                                <div class="my-1 container p-1 border border-teal">
                                                    <h6>Edit Interfaces.</h6>
                                                    <span class="text-primary" id="interface_config_back" style="cursor: pointer"><i class="fas fa-arrow-left"></i> <small>Back</small></span><br>
                                                    <span>- Edit your router interfaces. <br>
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
                                                            <p id="error_handlers"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="p-0 border border-cyan">
                                            <div class="container" id="net_access_method">
                                                <b>Internet Access Method <span class="text-primary d-none" id="load_internet_access_methods"><i class="fas fa-spinner fa-spin"></i></span> <span class="text-primary" id="edit_net_access" style="cursor: pointer"><i class="fas fa-pen-fancy"></i> <small>Edit</small></span></b>
                                                <span id="internet_access_method"></span>
                                            </div>
                                            <div class="container d-none" id="edit_access_methods">
                                                <div class="my-1 container p-1">
                                                    <h6>Configure how are you getting your internet?.</h6>
                                                    <span class="text-primary" id="back_to_internet_access" style="cursor: pointer"><i class="fas fa-arrow-left"></i> <small>Back</small></span><br>
                                                    <span>- Configure ways you get your internet from to your router.</span><br>
                                                    <span>- <b class="text-danger">When you make changes we will remove all prior settings that existed before.</b></span><br>
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
                                            <hr class="p-0 border border-cyan">
                                            <div class="container" id="view_internet_distros">
                                                <b>Internet Distribution Settings <span class="text-primary d-none" id="internet_distro_method"><i class="fas fa-spinner fa-spin"></i></span> <span class="text-primary" id="net_distro_modes" style="cursor: pointer"><i class="fas fa-pen-fancy"></i> <small>Edit</small></span></b>
                                                <span id="internet_distribution_method"></span>
                                            </div>
                                            <div class="container d-none" id="reset_internet_distro">
                                                <div class="my-1 container p-1 border border-teal">
                                                    <h6>Configure how are your users accessing your internet?.</h6>
                                                    <span class="text-primary" id="view_net_configurations" style="cursor: pointer"><i class="fas fa-arrow-left"></i> <small>Back</small></span><br>
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
                                            <hr class="p-0 border border-cyan">
                                            <div class="container" id="wireless_int_conf">
                                                <b>Wireless Configuration <span class="text-primary d-none" id="wireless_configuration"><i class="fas fa-spinner fa-spin"></i></span> <span class="text-primary" id="wireless_configs" style="cursor: pointer"><i class="fas fa-pen-fancy"></i> <small>Edit</small></span></b>
                                                <span id="wireless_conf"></span>
                                            </div>
                                            <div class="container d-none" id="wireless_config_windows">
                                                <div class="my-1 container p-1 border border-teal">
                                                    <h6>Edit wireless.</h6>
                                                    <span class="text-primary" id="edit_wireless_configs" style="cursor: pointer"><i class="fas fa-arrow-left"></i> <small>Back</small></span><br>
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com/sims/"
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
    <script src="/theme-assets/js/core/view_router.js"></script>
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
    </script>
</body>

</html>
