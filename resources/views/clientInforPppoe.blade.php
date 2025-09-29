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
    <title>Hypbits - Client Details - PPPoE Assignment</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
    
    <style>
        .hide{
          display: none;
        }
        .showBlock{
          display: block;
        }
        .dt-search {
            display: none;
        }
        .ct-chart {
            display: flex; /* chart + legend side by side */
        }
        .ct-legend {
            position: relative;
            margin-right: 15px;
            width: 100px;   /* adjust as needed */
        }

        .ct-legend li {
            display: block; /* stack items vertically */
            margin-bottom: 8px;
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
    {{-- script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <!-- fixed-top-->
    <x-menu active="myclients"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"My Clients");
        $readonly_finance = readOnly($priviledges,"Transactions");
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
                                <li class="breadcrumb-item"><a href="/Clients">My Clients</a>
                                </li>
                                <li class="breadcrumb-item">View Clients
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
                                <h4 id="view_clients_inform" class="card-title"><i class="ft-settings"></i> <span class="text-secondary">{{ ucwords(strtolower($clients_data[0]->client_name)) }}</span> Settings</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Client Settings";
                                                $otherClasses = "";
                                                $btnLink = "#";
                                                $otherAttributes = "data-action=\"collapse\"";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body">
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <x-client.dash :clientRefferal="$client_refferal" :lastClientDetails="$last_client_details" :routerData="$router_data" :expireDate="$expire_date" :registrationDate="$registration_date" :readonly="$readonly" :clientData="$clients_data"/>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 id="view_clients_inform" class="card-title"><i class="ft-eye"></i> View <span class="text-secondary">{{ ucwords(strtolower($clients_data[0]->client_name)) }} - {{ (($clients_data[0]->client_account)) }}</span> @if ($clients_data[0]->client_status == 1) <div class='badge badge-success'>Activated</div> @else <div class='badge badge-danger'>De-Activated</div> @endif</h4>
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
                                    
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                        $otherClasses = "";
                                        $btnLink = "/Clients";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/Clients" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                        to list</a>
                                    @if (session('success'))
                                        <p class="success">{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class="danger">{{ session('error') }}</p>
                                    @endif
                                    <div class="container">
                                        <div class="modal fade text-left" id="change_issue_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11_2" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success white">
                                                    <h4 class="modal-title white" id="myModalLabel11_2">Validate User!</h4>
                                                    <input type="hidden" id="delete_columns_ids_2">
                                                    <button id="hide_delete_issue_2" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            <form action="{{route("validate_user")}}" method="post">
                                                                @csrf
                                                                <div class="form-group">
                                                                    @php
                                                                        $btnText = "<i class=\"ft-trash\"></i>";
                                                                        $otherClasses = "";
                                                                        $btn_id = "delete_user_from_the_system";
                                                                    @endphp
                                                                    <x-button :btnText="$btnText" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                    {{-- <button type="button" id="delete_user_from_the_system" class="btn btn-sm btn-outline-danger"><i class="ft-trash"></i></button> --}}
                                                                    <div class="container my-1 border border-dark rounded p-1 d-none" id="delete_the_user">
                                                                        <h4 class="text-center">Delete User!</h4>
                                                                        <p><b>Delete this user from the system?</b></p>
                                                                        @php
                                                                            $btnText = "<i class=\"ft-trash\"></i> Delete User";
                                                                            $otherClasses = "btn-block";
                                                                            $btnLink = "/delete_user/".$clients_data[0]->client_id;
                                                                            $otherAttributes = "";
                                                                        @endphp
                                                                        <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                                        {{-- <a href="/delete_user/{{$clients_data[0]->client_id}}" class="btn btn-sm btn-outline-danger btn-block"><i class="ft-trash"></i> Delete User</a> --}}
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="expiry_date" class="form-control-label"><b>Expiry Date</b></label>
                                                                    <input type="hidden" name="client_ids" id="client_ids" value="{{$clients_data[0]->client_id}}">
                                                                    <div class="autocomplete">
                                                                        <input type="date" name="expiry_date" id="expiry_date"
                                                                            class="form-control rounded-lg p-1"
                                                                            placeholder="Resolved By" required
                                                                            value="{{date("Y-m-d", strtotime("1 month"))}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="expiry_time" class="form-control-label"><b>Expiry Time</b></label>
                                                                    <div class="autocomplete">
                                                                        <input type="time" name="expiry_time" id="expiry_time"
                                                                            class="form-control rounded-lg p-1"
                                                                            placeholder="Resolved By" required
                                                                            value="{{date("H:i", strtotime("20250101000000"))}}">
                                                                    </div>
                                                                </div>
                                                                <div class="container row">
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"ft-save\"></i> Save";
                                                                            $otherClasses = "w-100";
                                                                            $btn_id = "";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button class="btn btn-success btn-sm {{$readonly}}" type="submit"><i class="ft-save"></i> Save</button> --}}
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                                            $otherClasses = "w-100";
                                                                            $btn_id = "close_update_status_window";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button type="button" id="close_update_status_window" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> --}}
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mx-auto my-2">
                                        <ul class="nav nav-tabs nav-justified" id="myTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab"><i class="ft-info mr-1"></i> Client Information</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab"><i class="ft-flag mr-1"></i> Client Issues 
                                                    @if ($pending_issues[0]->Total > 0)
                                                        <div class="badge badge-danger ml-1">{{$pending_issues[0]->Total > 9 ? '9+' : $pending_issues[0]->Total}}</div>
                                                    @endif
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link " id="tab3-tab" data-toggle="tab" href="#tab3" role="tab"><i class="ft-file mr-1"></i> Invoices</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link " id="tab4-tab" data-toggle="tab" href="#tab4" role="tab"><i class="ft-activity mr-1"></i> Usage Statistics</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="mx-auto my-2 {{$clients_data[0]->validated == 1 ? "d-none" : ""}}">
                                        <div class="d-flex justify-content-center">
                                            @php
                                                $btnText = "<i class=\"ft-refresh\"></i> Validate User";
                                                $otherClasses = "w-50";
                                                $btn_id = "change_status";
                                            @endphp
                                            <x-button :btnText="$btnText" btnType="success" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button class="btn btn-sm btn-block btn-success" {{$readonly}} id="change_status" type="button"><i class="ft-refresh"></i> Validate User</button> --}}
                                        </div>
                                    </div>
                                    <div class="tab-content" id="myTabsContent">
                                        <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                            <x-client.summary-usage-stats :dailyStats="$daily_stats" :bandwidthStats="$bandwidth_stats" :monthlyStats="$monthly_stats" :clientStatus="$client_status" />
                                            <hr class="w-75">
                                            <form class="form-group" action="/updateClients" method="POST">
                                                @csrf
                                                <input type="hidden" name="clients_id"
                                                    value="{{ $clients_data[0]->client_id }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="checkbox" name="allow_router_changes"
                                                            id="allow_router_changes" checked>
                                                        <label for="allow_router_changes"
                                                            class="form-control-label text-primary"
                                                            style="font-weight: 800;cursor: pointer;">Apply changes to
                                                            router</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 form-group">
                                                        <label for="client_name" class="form-control-label">Clients Fullname <span class="text-danger">*</span> {
                                                            <span
                                                                class="primary">{{ $clients_data[0]->client_name }}</span>
                                                            }</label>
                                                        <input type="text" name="client_name" id="client_name"
                                                            class="form-control rounded-lg p-1"
                                                            placeholder="Clients Fullname .." required
                                                            value="{{ old('client_name') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="client_address" class="form-control-label">Clients Address <span class="text-danger">*</span> 
                                                            { <span
                                                                class="primary">{{ $clients_data[0]->client_address }}</span>
                                                            }</label>
                                                        <input type="text" name="client_address" id="client_address"
                                                            class="form-control rounded-lg p-1" placeholder="Client location"
                                                            required value="{{ old('client_address') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="location_coordinates" class="form-control-label">Location
                                                            co-ordinates { <span
                                                                class="primary">{{ $clients_data[0]->location_coordinates ?? '' }}</span>
                                                            }</label>
                                                        <input type="text" name="location_coordinates"
                                                            onkeypress="return isNumber(event)" id="location_coordinates"
                                                            class="form-control rounded-lg p-1"
                                                            placeholder="Exclude All special characters"
                                                            value="{{ $clients_data[0]->location_coordinates ?? '' }}"
                                                            onpaste="return pasted(event,'location_coordinates');">
                                                    </div>
                                                </div>
                                                <div class="row d-none">
                                                    <div class="col-md-4 form-group">
                                                        <label for="client_phone" class="form-control-label">Clients Phone
                                                            number { <span
                                                                class="primary">{{ $clients_data[0]->clients_contacts }}</span>
                                                            }</label>
                                                        <input type="number" name="client_phone" id="client_phone"
                                                            class="form-control rounded-lg p-1"
                                                            placeholder="Client valid phone number" required
                                                            value="{{ old('client_phone') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="client_account_number" class="form-control-label">Clients
                                                            Account Number <span class="text-danger">*</span> { <span
                                                                class="primary">{{ $clients_data[0]->client_account }}</span>
                                                            }</label>
                                                        <input type="text" name="client_account_number"
                                                            id="client_account_number" class="form-control rounded-lg p-1"
                                                            placeholder="Client account number" readonly
                                                            value="{{ $clients_data[0]->client_account }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="client_monthly_pay" class="form-control-label">Clients
                                                            Monthly Payment <span class="text-danger">*</span> { <span
                                                                class="primary">{{ $clients_data[0]->monthly_payment }}</span>
                                                            }</label>
                                                        <input type="number" name="client_monthly_pay" id="client_monthly_pay"
                                                            class="form-control rounded-lg p-1"
                                                            placeholder="Client Monthly Payment" required
                                                            value="{{ old('client_monthly_pay') }}">
                                                    </div>
                                                </div>
                                                <p></p>
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        @if (session('network_error'))
                                                            <p class="danger">{{ session('network_error') }}</p>
                                                        @endif
                                                        <label  id="errorMsg" for="client_secret_username" class="form-control-label">Clients Username
                                                            <span class="text-danger">*</span> { <span class="primary" id="secret_username"></span> }</label>
                                                        <input type="text" name="client_secret_username" id="client_secret_username"
                                                            class="form-control rounded-lg p-1" placeholder="ex 10.10.30.0"
                                                            required value="{{ old('client_secret_username') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span class="d-none" id="secret_holder"></span>
                                                        <label  id="errorMsg1" for="client_secret_password" class="form-control-label">Clients Secret Password <span class="text-danger">*</span> {<span class="primary" id="addresses"></span>}</label>
                                                            @php
                                                                $btnText = "<i class=\"fas fa-eye\"></i>";
                                                                $otherClasses = "w-25";
                                                                $btn_id = "display_secret";
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="infor" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button type="button" id="display_secret" class="btn btn-sm btn-infor"><span class="text-secondary"><i class="fas fa-eye"></i></span></button> --}}
                                                        <input type="password" name="client_secret_password" id="client_secret_password"
                                                            class="form-control rounded-lg p-1 w-100" placeholder="ex 10.10.30.1/24"
                                                            required value="{{ old('client_secret_password') }}">
                                                    </div>
                                                </div>
                                                <div class="row my-1">
                                                    <div class="col-md-6 form-group">
                                                        <label for="router_name" class="form-control-label">Router Name: <span class="text-danger">*</span> {
                                                            <span class="primary bolder" id="router_named">Hilary Dev</span> }
                                                            <span class="invisible" id="interface_load"><i
                                                                    class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                        <p id="router_data"><span class="secondary">The router list will
                                                                appear here.. If this message is still present you have no
                                                                routers present in your database.</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="client_address" class="form-control-label">Router
                                                            Profile: <span class="text-danger">*</span> { <span class="primary bolder"
                                                                id="router_profiles"></span> } </label>
                                                        <p class="text-secondary" id="profile_holder">The router secret profiles
                                                            will appear here If the router is selected.If this message is still
                                                            present a router is not selected.</p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        @if ($clients_data[0]->validated == 1)
                                                            @php
                                                                $btnText = "<i class=\"ft-upload\"></i> Update User";
                                                                $otherClasses = "";
                                                                $btn_id = "";
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                            {{-- <button {{$readonly}} class="btn btn-success text-dark" type="submit"><i
                                                                    class="ft-upload"></i> Update User</button> --}}
                                                        @else
                                                            <p>Update button appears here but user is not validated yet!</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        @php
                                                            $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                            $otherClasses = "";
                                                            $btnLink = "/Clients";
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                        {{-- <a class="btn btn-secondary btn-outline" href="/Clients"><i
                                                                class="ft-x"></i> Cancel</a> --}}
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <x-Client.client-tab-2 :clientData="$clients_data" :clientIssues="$client_issues" :readOnlyFinance="$readonly_finance" :readonly="$readonly"/>
                                        @php
                                            $client_n_invoice = [];
                                            $client_n_invoice['clients_data'] =$clients_data;
                                            $client_n_invoice['invoice_id'] =$invoice_id;
                                        @endphp
                                        <x-Client.client-tab-3 :invoices="$invoices" :clientData="$client_n_invoice" :readOnlyFinance="$readonly_finance" :readonly="$readonly"/>

                                        {{-- TAB 4 TO SHOW CLIENT USAGE STATISTICS --}}
                                        <x-Client.client-usage-stats :clientsData="$clients_data" :readonly="$readonly"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                {{-- Transactions done by the client --}}

            </div>
            <div class="content-body {{count($reffer_details)>0 ? "":"d-none" }}">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 id="view_clients_inform" class="card-title"><span class="text-secondary">{{ ucwords(strtolower($clients_data[0]->client_name)) }}</span>`s refferee</h4>
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
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                        $otherClasses = "";
                                        $btnLink = "/Clients";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/Clients" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to list</a>
                                    @if (session('success'))
                                        <p class="success">{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class="danger">{{ session('error') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-9">
                                            <p><strong>Note: </strong><br> 
                                                - View user payment history.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row my-2 w-50">
                                        <input type="hidden" name="clients_id"
                                    value="{{ $clients_data[0]->client_id }}">
                                        <input type="hidden" name="refferal_account_no" id="refferer_acc_no2">
                                        <div class="col-md-6">
                                            <p><b>Refferer Fullname :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_name">{{$reffer_details[0] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer Acc No : </b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_acc_no">{{$reffer_details[1] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer wallet :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="reffer_wallet">{{$reffer_details[2] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer Location :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_location">{{$reffer_details[3] ?? 'Unknown'}}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-8 form-group row border-right border-dark">
                                        <div class="col-md-6">
                                            <input type="text" name="search" id="searchkey" class="form-control rounded-lg " placeholder="Your keyword ..">
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Kes 10,100</td>
                                                    <td>Mon 10th June 2022 10:48:00 AM</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>Kes 10,100</td>
                                                    <td>Mon 10th June 2022 10:48:00 AM</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav aria-label="Page navigation example" id="tablefooter">
                                        <ul class="pagination" id="datatable_paginate">
                                            <li class="page-item"  id="tofirstNav">
                                                <a class="page-link" href="#" aria-label="Fisrt">
                                                    <span aria-hidden="true">&laquo; &laquo;</span>
                                                    <span class="sr-only">First</span>
                                                </a>
                                            </li>
                                            <li class="page-item" id="toprevNac">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                            </li>
                                            <li class="page-item"><button disabled class="page-link" id="pagenumNav">Page: 1</button></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next" id="tonextNav">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Last Page"  id="tolastNav">
                                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo">1</span> to <span class="text-secondary"  id="finishNo">10</span> records of <span  id="tot_records" class="d-none">56</span></p>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                {{-- Transactions done by the client --}}

            </div>
            <div class="card p-1 {{count($reffered_list) > 0 ? "" : "d-none"}}">
                <h4 class="text-center text-dark">Refferer List</h4>
                @for ($i = 0; $i < count($reffered_list); $i++)
                    {{-- get the client information --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 id="view_clients_inform" class="card-title">{{$i+1}})  Reffered : {{$reffered_list[$i]->reffered->client_name}}</h4>
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
                                        @if (session('success'))
                                            <p class="success">{{ session('success') }}</p>
                                        @endif
                                        @if (session('error'))
                                            <p class="danger">{{ session('error') }}</p>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p><strong>Note: </strong><br> 
                                                    - View user payment history.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row my-2 w-50">
                                            <input type="hidden" name="clients_id"
                                        value="{{ $clients_data[0]->client_id }}">
                                            <input type="hidden" name="refferal_account_no" id="">
                                            <div class="col-md-6">
                                                <p><b>Refferer Fullname :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_name}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer Acc No : </b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_account}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer wallet :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">Kes {{$reffered_list[$i]->reffered->wallet_amount}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer Location :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_address}}</p>
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($index = 0; $index < count($reffered_list[$i]->payment_history); $index++)
                                                        <tr>
                                                            <th scope="row">{{$index+1}}</th>
                                                            <td>Kes {{number_format($reffered_list[$i]->payment_history[$index]->amount)}}</td>
                                                            <td>{{date("D dS M  H:i:s A",$reffered_list[$i]->payment_history[$index]->date)}}</td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endfor
                <hr>
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
    <script src="/theme-assets/js/core/datatables.js"></script>
    <script src="/theme-assets/js/core/datatables.min.js"></script>


    {{-- START OF THE ROUTER DATA RETRIEVAL --}}
    <script>
        var clients_data = @json($clients_data ?? '');
        // console.log(clients_data);

        // display the router data
        var router_data = @json($router_data ?? '');
        var data_to_display =
            "<select name='router_name' class='form-control' id='router_name' required ><option value='' hidden>Select an option</option>";
        for (let index = 0; index < router_data.length; index++) {
            const element = router_data[index];
            data_to_display += "<option class='router_id_infor' value='" + element['router_id'] + "'>" + element[
                'router_name'] + "</option>";
        }
        data_to_display += "</select>";
        var router_data = document.getElementById("router_data");
        router_data.innerHTML = data_to_display;
    </script>
    <script>
        // only the special characters allowed
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            console.log(charCode);
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                if (charCode == 45 || charCode == 44 || charCode == 46) {
                    return true;
                }
                return false;
            }
            return true;
        }
        // check if the field is pasted
        function pasted(e,id) {
            var clipboardData, pastedData;
            // console.log(id);
            // Stop data actually being pasted into div
            e.stopPropagation();
            e.preventDefault();

            // Get pasted data via clipboard API
            clipboardData = e.clipboardData || window.clipboardData;
            pastedData = clipboardData.getData('Text');

            // Do whatever with pasteddata
            // go for character by character and take only characters that are of cetain type
            // alert(pastedData);
            var data_accept = "";
            var strlen = pastedData.length;
            for (let index = 0; index < strlen; index++) {
                var crcode = pastedData.charCodeAt(index);
                if (crcode > 31 && (crcode < 48 || crcode > 57)) {
                    if (crcode == 45 || crcode == 44 || crcode == 46) {
                        data_accept+=pastedData.charAt(index);
                    }
                }else{
                    data_accept+=pastedData.charAt(index);
                }
            }
            document.getElementById("location_coordinates").value = data_accept;
        }
    </script>
    <script src="/theme-assets/js/core/viewclientpppoe.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/client_usage_report.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        var client_names = @json($clients_names ?? '');
        var client_contacts = @json($clients_contacts ?? '');
        var client_account = @json($clients_account ?? '');
        var refferal_payment = @json($refferal_payment ?? '');
        var reffered_list = @json($reffered_list ?? '');
        console.log(reffered_list);
    </script>
    <script src="/theme-assets/js/core/refferer.js"></script>
    <script>
        function autocomplete(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                a.style.maxHeight = "250px";
                a.style.overflowY = "auto";
                a.style.overflowX = "hidden";
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                var counter = 0;
                for (i = 0; i < arr.length; i++) {
                    if (counter > 10) {
                        break;
                    }
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].toUpperCase().includes(val.toUpperCase()) ||
                        arr2[i].toUpperCase().includes(val.toUpperCase()) ||
                        arr3[i].toUpperCase().includes(val.toUpperCase())
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr3[i] + " (" + arr[i] + ") - " + arr2[
                            i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr2[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                            getUser();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                    console.log(counter);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        var countries = client_contacts;

        /*initiate the autocomplete function on the "search_refferer_keyword" element, and pass along the countries array as possible autocomplete values:*/
        autocomplete(document.getElementById("search_refferer_keyword"), client_contacts, client_account, client_names);
    </script>
    <script>
        $(document).ready( function () {
            cObj("logo_loaders").classList.add("d-none");
            cObj("myTable").classList.remove("d-none");
            var table = $('#myTable').DataTable({
                "pagingType": "full_numbers", // Alternative styles: "simple", "numbers", etc.
                "language": {
                    "search": "<strong>Search:</strong>", // Custom label for the search box
                    "lengthMenu": "Show _MENU_ entries per page"
                },
                "pageLength" : 50
            });

            $('#searchkey_2').on('keyup', function() {
                table.search(this.value).draw();
            });
            
            cObj("logo_loaders_2").classList.add("d-none");
            cObj("invoice_table").classList.remove("d-none");
            var table = $('#invoice_table').DataTable({
                "pagingType": "full_numbers", // Alternative styles: "simple", "numbers", etc.
                "language": {
                    "search": "<strong>Search:</strong>", // Custom label for the search box
                    "lengthMenu": "Show _MENU_ entries per page"
                },
                "pageLength" : 50
            });

            $('#searchkey_3').on('keyup', function() {
                table.search(this.value).draw();
            });

        } );

        function cObj(object_id) {
            return document.getElementById(object_id);
        }

        cObj("change_status").onclick = function () {
            cObj("change_issue_status").classList.remove("hide");
            cObj("change_issue_status").classList.add("show");
            cObj("change_issue_status").classList.add("showBlock");
        }

        cObj("hide_delete_issue_2").onclick = function () {
            cObj("change_issue_status").classList.add("hide");
            cObj("change_issue_status").classList.remove("show");
            cObj("change_issue_status").classList.remove("showBlock");
        }

        cObj("delete_user_from_the_system").onclick = function () {
            cObj("delete_the_user").classList.toggle("d-none");
        }

        cObj("close_update_status_window").onclick = function () {
            cObj("change_issue_status").classList.add("hide");
            cObj("change_issue_status").classList.remove("show");
            cObj("change_issue_status").classList.remove("showBlock");
        }
    </script>
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
    <script>
        var freeze_type = document.getElementById("freeze_type");
        freeze_type.onchange = function () {
            var freeze_window = document.getElementById("freeze_window");
            if(this.value == "Indefinite"){
                freeze_window.classList.add("d-none");
            }else{
                freeze_window.classList.remove("d-none");
            }
        }

        function checkOnline() {
            // check after two minutes if the client is online
            var datapass = "/Client/Check-Online/"+cObj("client_account_number").value;
            sendDataGet("GET", datapass, cObj("status_holder"), cObj("client_status_loader"), function () {
                var response = (cObj("status_holder").innerText);
                if (hasJsonStructure(response)) {
                    var result = JSON.parse(response);
                    console.log(result);

                    if (result.status == "online") {
                        cObj("offline_status").classList.add("d-none");
                        cObj("online_status").classList.remove("d-none");
                        cObj("offline_last_seen").classList.add("d-none");
                        cObj("online_last_seen").classList.remove("d-none");
                        cObj("offline_last_seen").innerHTML = "Last seen: "+result.last_seen
                    }else{
                        cObj("offline_status").classList.remove("d-none");
                        cObj("online_status").classList.add("d-none");
                        cObj("offline_last_seen").classList.remove("d-none");
                        cObj("online_last_seen").classList.add("d-none");
                        cObj("offline_last_seen").innerHTML = "Last seen: "+result.last_seen
                    }
                }
            });
        }

        // CHECK TO SEE IF THE CLIENT IS STILL ONLINE
        setInterval(checkOnline, 120000);
    </script>
</body>

</html>
