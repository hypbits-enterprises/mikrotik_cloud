<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - My Clients</title>
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
    </style>
    <!-- END Custom CSS-->
</head>
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    <x-menu active="myclients"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"My Clients");
        $export_data = readOnly($priviledges,"Account and Profile");
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
                                <li class="breadcrumb-item active">My Clients
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Frozen Clients</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    {{-- <button data-action="collapse" class="btn btn-primary"><i class="ft-plus"></i> Add Administrator</button> --}}
                                    <ul class="list-inline mb-0">
                                        <li>
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Check Frozen Clients";
                                                $otherClasses = "";
                                                $btnLink = "#";
                                                $otherAttributes = "data-action=\"collapse\"";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a class="btn btn-outline-primary" data-action="collapse"><i class="ft-plus"></i> Check Frozen Clients</a> --}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-header">
                                    <p>- List of clients who are frozen!</p>
                                </div>
                                <div class="card-body">
                                    <table id="myTable" class="table table-striped table-bordered sourced-data dataTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Days Left</th>
                                                <th>Due Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($frozen_clients); $i++)
                                                <tr>
                                                    <td>{{$i+1}}</td>
                                                    <td>{{ucwords(strtolower($frozen_clients[$i]->client_name))}}</td>
                                                    <td>{{$frozen_clients[$i]->freeze_days_left}}</td>
                                                    <td>{{($frozen_clients[$i]->client_freeze_untill) == "00000000000000" ? "Indefinate" : date("D dS M Y",strtotime($frozen_clients[$i]->client_freeze_untill))}}</td>
                                                    <td>
                                                        @php
                                                            $btnText = "<i class=\"fas fa-eye\"></i> View";
                                                            $otherClasses = "";
                                                            $btnLink = "/Clients/View/".$frozen_clients[$i]->client_id;
                                                            $otherAttributes = "";
                                                            $btnId="no_id_".$i;
                                                        @endphp
                                                        <x-button-link :btnId="$btnId" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" btnType="info" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                        {{-- <a href="/Clients/View/{{$frozen_clients[$i]->client_id}}" class="btn btn-outline-infor btn-sm p-0 my-0"><i class="fas fa-eye"></i> View</a> --}}
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Client Stats</h4>
                                <a class="heading-elements-toggle">
                                    <i class="la la-ellipsis-v font-medium-3"></i>
                                </a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> View Client Stats";
                                                $otherClasses = "";
                                                $btnLink = "#";
                                                $otherAttributes = "data-action=\"collapse\"";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        </li>
                                        {{-- <li>
                                            <a data-action="reload">
                                                <i class="ft-rotate-cw"></i>
                                            </a>
                                        </li> --}}
                                        {{-- <li>
                                            <a data-action="close">
                                                <i class="ft-x"></i>
                                            </a>
                                        </li> --}}
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body">
                                    <div class="alert round bg-primary alert-icon-left alert-dismissible ml-2 mt-1 mx-auto" role="alert">
                                        <span class="alert-icon">
                                            <i class="ft-bell bell-shake"></i>
                                        </span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        <b class="text-center">New Feature Alert</b><br>
                                        Now you are able to see your clients usage stats, this includes bandwidth and data usage. <br>
                                        This requires your router to run MikrotikOS version <u>7.18</u> and above. <br>
                                        This upgrade may affect your router`s perfomance, check <u><b id="show_router_model_list" style="cursor: pointer;">this router list</b></u> provided to know your optimal router capability.
                                    </div>
                                    <div id="vertical-bar2" class="row">
                                        <div class="col-md-4 border border-secondary rounded shadow">
                                            <h6 class="text-sm mt-1">Clients Stats</h6>
                                            <hr class="my-1">
                                            <p><b># of Total Clients</b>: {{$client_count}} Client(s)</p>
                                            <p><b># of Frozen Clients</b>: {{$frozen_count}} Client(s)</p>
                                            <p><b># of Active Clients</b>: {{$active_clients}} Client(s)</p>
                                            <p><b># of In-Active Clients</b>: {{$inactive_clients}} Client(s)</p>
                                            <p><b># Added Last one week</b>: {{$total_added_last_week}} Client(s)</p>
                                        </div>
                                        <div class="col-md-8">
                                            <canvas class="w-75 mx-auto" id="onboarding_canvas" aria-label="Data will appear here" role="img" >
                                                <p class="text-secondary text-bold-700" >Data will be displayed here!</p>
                                            </canvas>
                                        </div>
                                    </div>
                                    <hr>
                                    <x-Client.all-clients-usage-stats :clientStatus="$client_status" :monthlyStats="$monthly_stats" :dailyStats="$daily_stats" :bandwidthStats="$bandwidth_stats_data"/>
                                    
                                    <hr>
                                    {{-- retrive all clients stats --}}
                                    <x-Client.client-usage-stats :clientsData="$client_data[0]" isTab="false" :readonly="$readonly"/>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Client Table</h4>
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
                                <p>- Manage Clients Further!</p>
                                @if(session('success_reg'))
                                    <p class="text-success">{{session('success_reg')}}</p>
                                @endif
                                @if (session('success'))
                                    <p class="success">{!! session('success') !!}</p>
                                @endif
                                @if (session('error_clients'))
                                    <p class="text-danger">{{ session('error_clients') }}</p>
                                @endif
                                @if (session()->has("error"))
                                    <p style="color: orangered">{!!session("error")!!}</p>
                                @endif
                                @if (session()->has("file_list"))
                                    <div class="text-success">
                                        <h6 class="text-success">Download List</h6>
                                        <ul>
                                            @foreach (session("file_list") as $file)
                                                <li>Download : <code><a class="text-danger" href="{{$file[0]}}" download>{{$file[1]}}</a></code></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if (session()->has("router_data_migrate"))
                                    <form class="container mb-1" action="/reverse_migration" method="post">
                                        @csrf
                                        <input type="hidden" name="reverse_data" value="{{json_encode(session("reverse_list"))}}">
                                        @php
                                            $btnText = "<i class=\"fas fa-undo\"></i> Undo";
                                            $otherClasses = "mt-1";
                                            $btn_id = "reverse_migration";
                                        @endphp
                                        <x-button :btnText="$btnText" btnType="success" type="submit" toolTip="Reverse Migration" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        @php
                                            $btnText = "<i class=\"fas fa-check\"></i> Done";
                                            $otherClasses = "mt-1";
                                            $btnLink = "/Clients";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link :otherAttributes="$otherAttributes" toolTip="Click when done with the migration!" :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    </form>
                                    <div class="container border border-primary rounded p-1">
                                        <h6 class="text-dark text-center">Copy the following commands to the respective routers</h6>
                                        <b>Note</b>
                                        <ul>
                                            <li>Do not reload the page until you are done, you`ll lose the commands</li>
                                            <li>Copy the command and paste it to the respective router!</li>
                                        </ul>
                                        <table class="table table-striped table-bordered zero-configuration dataTable no-footer">
                                            <tr><th>No.</th><th>Router</th><th>Command</th><th>Action</th></tr>
                                            @php
                                                $index = 1;
                                            @endphp
                                            @foreach (session("router_data_migrate") as $router)
                                                <tr><td>{{$index}}</td><td>{{$router['router_name']}}</td><td><code id="command_code_{{$router['client_router_id']}}">/tool fetch url={{$router['link']}} mode=http keep-result=yes dst-path={{"add_".session("database_name")."_".$router['client_router_id']}}.rsc;<br>delay 1;<br>/import file-name={{"add_".session("database_name")."_".$router['client_router_id']}}.rsc;<br>/beep</code></td><td><button class="btn btn-sm btn-primary copy_command_btn" id="copy_command_btn_{{$router['client_router_id']}}" data-toggle="tooltip" title="Copy Command"><i class="fa fa-copy"></i> Copy</button></td></tr>
                                                @php
                                                    $index++;
                                                @endphp
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                                {{-- <span class='badge badge-warning text-dark'>Reffered</span> --}}
                                @php
                                    $btnText = "<i class=\"ft-bar-chart-2\"></i> Client`s Statistics";
                                    $otherClasses = "mt-1";
                                    $btnLink = "/Client-Statistics";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link :otherAttributes="$otherAttributes" toolTip="Client`s Statistics" :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="md" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/Client-Statistics" class="btn btn-secondary mt-1" style="padding: 3px;" data-toggle="tooltip" title="Client`s Statistics"><span class="d-inline-block border border-white" style="border-radius: 2px; padding: 10px;"><i class="ft-bar-chart-2"></i> Client`s Statistics</span></a> --}}
                                @php
                                    $btnText = "<i class=\"ft-file-text\"></i> Client`s Reports";
                                    $otherClasses = "mt-1";
                                    $btn_id = "client_reports_btn";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" type="button" toolTip="Client`s Reports" btnSize="md" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                {{-- <button class="btn btn-info mt-1" style="padding: 3px;" data-toggle="tooltip" title="Client`s Reports" id="client_reports_btn"><span class="d-inline-block border border-white" style="border-radius: 2px; padding: 10px;"><i class="ft-file-text"></i> Client`s Reports</span></button> --}}
                                @php
                                    $btnText = "<i class=\"fa fa-file-export\"></i> Export Client Data";
                                    $otherClasses = "mt-1 ".($export_data == "disabled" ? "d-none" : "");
                                    $btn_id = "export_client_data_btn";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" type="button" :disabled="$export_data" toolTip="Export Client Data" btnSize="md" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                {{-- <button class="btn btn-info mt-1 {{$export_data == "disabled" ? "d-none" : ""}}" style="padding: 3px;" {{$export_data}} data-toggle="tooltip" title="Export Client Data" id="export_client_data_btn"><span class="d-inline-block border border-white" style="border-radius: 2px; padding: 10px;"><i class="fa fa-file-export"></i> Export Client Data</span></button> --}}
                                @php
                                    $btnText = "<i class=\"fa fa-file-import\"></i> Import Client Data";
                                    $otherClasses = "mt-1 ".($export_data == "disabled" ? "d-none" : "");
                                    $btn_id = "import_client_data_btn";
                                @endphp
                                <x-button :btnText="$btnText" btnType="info" type="button" :disabled="$export_data" toolTip="Import Client Data" btnSize="md" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                {{-- <button class="btn btn-info mt-1 {{$export_data == "disabled" ? "d-none" : ""}}" style="padding: 3px;" {{$export_data}} data-toggle="tooltip" title="Export Client Data" id="import_client_data_btn"><span class="d-inline-block border border-white" style="border-radius: 2px; padding: 10px;"><i class="fa fa-file-export"></i> Export Client Data</span></button> --}}
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="container">
                                        {{-- EXPORT CLIENTS DATA --}}
                                        <div class="modal fade text-left hide" id="router_board_lists" tabindex="-1" role="dialog" aria-labelledby="myModalLabel7" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                    <h4 class="modal-title white" id="myModalLabel7"><i class="fa fa-file-export"></i> Router Board List</h4>
                                                    <button id="close_router_board_list_window" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            @php
                                                                $path = public_path('mikrotik_router/mikrotik_routers_25_fullspec_mbps.json');
                                                                // Read and decode the JSON file
                                                                $json = file_get_contents($path);
                                                                $all_routers_model = json_decode($json, false);
                                                            @endphp
                                                            <table class="table table-striped table-bordered zero-configuration" id="router_board_table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Model</th>
                                                                        <th>RAM</th>
                                                                        <th>C.P.U</th>
                                                                        <th>Approx. Clients</th>
                                                                        <th>Max throughput</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($all_routers_model as $router_data)
                                                                        <tr>
                                                                            <td>{{$router_data->rank}}</td>
                                                                            <td>{{$router_data->model}}</td>
                                                                            <td>{{$router_data->ram_mb}} MB</td>
                                                                            <td>{{$router_data->cpu}}</td>
                                                                            <td>{{$router_data->approx_devices_at_10mbps}}</td>
                                                                            <td>{{$router_data->throughput_mbps}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- EXPORT CLIENTS DATA --}}
                                        <div class="modal fade text-left hide" id="import_client_data_window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel6" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                    <h4 class="modal-title white" id="myModalLabel6"><i class="fa fa-file-export"></i> Import Client data</h4>
                                                    <input type="hidden" id="import_client_data_list">
                                                    <button id="close_import_client_data_window_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            <div class="container border border-primary rounded p-1 mb-2">
                                                                <b>Note:</b><br>
                                                                <ul>
                                                                    <li>Download the sample file and fill.</li>
                                                                    <li>Download from here : <code><a class="text-danger" href="/export/HBS IMPORT SAMPLE - Sample.csv" download class="code">Click to download!</a></code></li>
                                                                </ul>
                                                            </div>
                                                            <form action="/import_client_data" enctype="multipart/form-data" method="post" class="form-control-group">
                                                                @csrf
                                                                <h6 class="text-center" >Import Client`s data</h6>
                                                                
                                                                <label for="selected_files" class="form-control-label" id=""><b>Select file</b></label>
                                                                <input type="file" name="selected_files" id="selected_files" class="form-control" accept=".csv">
                                                                
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-file-import\"></i> Import";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="info" type="submit" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button type="submit" class="btn btn-outline-info btn-sm w-100 my-1" {{$export_data == "disabled" ? "d-none" : ""}}><i class="fas fa-download"></i> Download</button> --}}
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "close_import_client_data_window_2";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="secondary" type="button" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button class="btn btn-outline-secondary btn-sm w-100 my-1" type="button" id="close_export_client_data_2">Cancel</button> --}}
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
                                        {{-- EXPORT CLIENTS DATA --}}
                                        <div class="modal fade text-left hide" id="export_client_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                    <h4 class="modal-title white" id="myModalLabel4"><i class="fa fa-file-export"></i> Export Client data</h4>
                                                    <input type="hidden" id="delete_columns_ids">
                                                    <button id="close_export_client_data_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            <div class="container border border-primary rounded p-1 mb-2">
                                                                <b>Note:</b><br>
                                                                <ul>
                                                                    <li>Use <code>.rsc</code> type since it checks if the client config exists.</li>
                                                                    <li>To import using <code>.rsc</code>:</li>
                                                                    <ul>
                                                                        <li>Open Winbox and connect to your MikroTik router.</li>
                                                                        <li>Go to Files.</li>
                                                                        <li>Drag and drop your .rsc file into the Files window.</li>
                                                                        <li>then run this command <code>/import file-name="file-name.rsc"</code></li>
                                                                    </ul>
                                                                    <li>If you plan to use the <code>.txt</code> type</li>
                                                                    <li>Copy and paste the command on your terminal</li>
                                                                </ul>
                                                            </div>
                                                            <form action="/export_client_data" target="_blank" method="post" class="form-control-group">
                                                                @csrf
                                                                <h6 class="text-center" >Export Client`s data</h6>
                                                                
                                                                <label for="router_selected" class="form-control-label" id=""><b>Select Router</b></label>
                                                                <select required name="router_selected" id="router_selected" class="form-control">
                                                                    <option value="" hidden>Select a router</option>
                                                                    @php
                                                                        for ($index=0; $index < count($router_infor); $index++) { 
                                                                            echo "<option value=".$router_infor[$index]->router_id.">".$router_infor[$index]->router_name."</option>";
                                                                        }
                                                                    @endphp
                                                                    <option value="all">All routers</option>
                                                                </select>

                                                                <label for="download_as" class="form-control-label"><b>Download Export File As</b></label>
                                                                <select required name="download_as" id="download_as" class="form-control">
                                                                    <option value="" hidden>Select Option</option>
                                                                    <option selected value="rsc">An RSC File</option>
                                                                    <option value="txt">A Text File</option>
                                                                    <option value="csv">A CSV File</option>
                                                                </select>
                                                                
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-download\"></i> Download";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="info" type="submit" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button type="submit" class="btn btn-outline-info btn-sm w-100 my-1" {{$export_data == "disabled" ? "d-none" : ""}}><i class="fas fa-download"></i> Download</button> --}}
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "close_export_client_data_2";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="secondary" type="button" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button class="btn btn-outline-secondary btn-sm w-100 my-1" type="button" id="close_export_client_data_2">Cancel</button> --}}
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
                                        {{-- EXPORT CLIENTS DATA --}}
                                        <div class="modal fade text-left hide" id="migrate_clients_window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel5" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                    <h4 class="modal-title white" id="myModalLabel5"><i class="fa fa-file-export"></i> Migrate Client`s data</h4>
                                                    <button id="close_migrate_clients_window" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            <h6 class="text-center" ><u>Migrate Client`s data</u></h6>
                                                            <div class="container p-0 mb-1">
                                                                <ul>
                                                                    <li>Migrating will move the selected clients to the desired router.</li>
                                                                    <li>The clients will be deleted from the routers they currently connected to.</li>
                                                                    <li>Ensure there is stable connection in the router before you begin this process.</li>
                                                                </ul>
                                                            </div>
                                                            <hr>
                                                            <p id="clients_migrate_counter" class="border border-secondary rounded p-1 m-1">
                                                                <b>Note:</b> <span id="migrate_clients_counts" class="text-info">0 Client(s) Selected</span>
                                                            </p>
                                                            <form action="/migrate_client_data" target="_blank" method="post" class="form-control-group">
                                                                @csrf
                                                                <input type="hidden" name="migrate_client_list" id="migrate_client_list">
                                                                <label for="migrate_to_router" class="form-control-label"><b>Migrate To:</b></label>
                                                                <select required name="migrate_to_router" id="migrate_to_router" class="form-control">
                                                                    <option value="" hidden>Select a router</option>
                                                                    @php
                                                                        for ($index=0; $index < count($router_infor); $index++) { 
                                                                            echo "<option value=".$router_infor[$index]->router_id.">".$router_infor[$index]->router_name."</option>";
                                                                        }
                                                                    @endphp
                                                                </select>
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-file-export\"></i> Migrate";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "migrate_client_data_btn";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="info" type="submit" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button type="submit" class="btn btn-outline-info btn-sm w-100 my-1" {{$export_data == "disabled" ? "d-none" : ""}}><i class="fas fa-download"></i> Download</button> --}}
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                                            $otherClasses = "w-100 my-1 ".($export_data == "disabled" ? "d-none" : "");
                                                                            $btn_id = "close_migrate_clients_window_2";
                                                                        @endphp
                                                                        <x-button :btnText="$btnText" btnType="secondary" type="button" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                                        {{-- <button class="btn btn-outline-secondary btn-sm w-100 my-1" type="button" id="close_export_client_data_2">Cancel</button> --}}
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
                                    <div class="row">
                                        <div class="col-md-12 border border-primary rounded p-1 hide" id="show_generate_reports_window">
                                            <h6 class="text-center">Generate Reports</h6>
                                            <form action="/Clients/generateReports" target="_blank" method="GET">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="client_report_option" class="form-label">Client Report Option</label>
                                                        <select name="client_report_option" id="client_report_option" class="form-control" required>
                                                            <option value="" hidden>Select an Option</option>
                                                            <option value="client registration">Client Registration</option>
                                                            <option value="client information">Client Information</option>
                                                            <option value="client router information">Client Router Information</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 hide" id="date_option">
                                                        <label for="client_registration_date_option" class="form-label">Select registration date</label>
                                                        <select name="client_registration_date_option" id="client_registration_date_option" class="form-control">
                                                            <option value="" hidden>Select an Option</option>
                                                            <option value="select date">Select a Date</option>
                                                            <option id="default_reg_date" selected value="all dates">All Dates</option>
                                                            <option value="between dates">Between Dates</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_from_date_win">
                                                        <label for="from_select_date" class="form-label">From</label>
                                                        <input type="date" name="from_select_date" id="from_select_date" value="<?php echo date("Y-m-d",strtotime("-7 days"));?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_to_date_win">
                                                        <label for="to_select_date" class="form-label">To</label>
                                                        <input type="date" name="to_select_date" id="to_select_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_date_win">
                                                        <label for="select_registration_date" class="form-label">Select Date</label>
                                                        <input type="date" name="select_registration_date" id="select_registration_date" value="<?php echo date("Y-m-d");?>" max="<?php echo date("Y-m-d");?>" class="form-control">
                                                    </div>
                                                    <div class="col-md-3 hide" id="select_router_window">
                                                        <label for="select_router_option" class="form-label">Select Router</label>
                                                        <select name="select_router_option" id="select_router_option" class="form-control">
                                                            <option value="" hidden>Select Router</option>
                                                            <option selected value="All">All</option>
                                                            @if (count($router_infor) > 0)
                                                                @for ($i = 0; $i < count($router_infor); $i++)
                                                                    <option value="{{$router_infor[$i]->router_id}}">{{$router_infor[$i]->router_name}}</option>
                                                                @endfor
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 hide" id="client_status_opt">
                                                        <label for="client_statuses" class="form-label">Client Status</label>
                                                        <select name="client_statuses" id="client_statuses" class="form-control">
                                                            <option value="" hidden>Select Status</option>
                                                            <option selected value="2">All</option>
                                                            <option value="0">In-Active</option>
                                                            <option value="1">Active</option>
                                                            <option value="3">Reffered</option>
                                                            <option value="4">Static Assigned</option>
                                                            <option value="5">PPPoE Assigned</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        @php
                                                            $btnText = "<i class=\"ft-settings\"></i> Generate Reports";
                                                            $otherClasses = "mt-2 ".($export_data == "disabled" ? "d-none" : "");
                                                            $btn_id = "";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="primary" type="submit" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        {{-- <button class="btn btn-outline-primary mt-2" type="submit"><i class="ft-settings"></i> Generate Reports</button> --}}
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p class="card-text">In this table below Client information can be displayed.</p>
                                    <p>Search options <span id="clients_search_loader" class="text-primary invisible"><i class="fas fa-refresh fa-spin"></i> Loading...</span></p>
                                    <p class="d-none" id="client_search_results"></p>
                                    <div class="row">
                                        <div class="col-md-9 form-group row border-right border-dark">
                                            <div class="col-md-5">
                                                <div class="autocomplete">
                                                    <input type="text" name="search" id="searchkey" class="form-control rounded-lg " placeholder="Your keyword ..">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                @if (isset($router_infor))
                                                    <select name="select_router" id="select_router"
                                                        class="form-control" required>
                                                        <option value="" hidden>Select a Router</option>
                                                        <option value="" >All</option>
                                                    @for ($i = 0; $i < count($router_infor); $i++)
                                                        <option value="{{$router_infor[$i]->router_id}}" >{{$router_infor[$i]->router_name}}</option>
                                                        {{-- {{"<option value=".$router_infor[$i]->router_id." >".$router_infor[$i]->router_id."</option>"}} --}}
                                                    @endfor
                                                    </select>
                                                @else
                                                    <p id="select_router" class="text-secondary">No routers found! Please add a router to proceed</p>
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <select name="client_status" id="client_status" class="form-control">
                                                    <option value="" hidden>Select Status</option>
                                                    <option value="">All</option>
                                                    <option value="inactive">In-Active</option>
                                                    <option value="active">Active</option>
                                                    <option value="static">Static Assigned</option>
                                                    <option value="pppoe">PPPoE Assigned</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="btn-group mr-1 mb-1 float-right">
                                                @php
                                                    $btnText = "<i class=\"ft-plus\"></i> New";
                                                    $otherClasses = "mt-2 btn-min-width dropdown-toggle";
                                                    $btn_id = "";
                                                    $otherAttributes = "data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\"";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button type="button" {{$readonly}} class="btn btn-outline-info btn-min-width dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="ft-plus"> New</i></button> --}}
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="/Clients/NewStatic">Static Assignment</a>
                                                    <a class="dropdown-item" href="/Clients/NewPPPoE">PPPoE Assignment</a>
                                                </div>
                                            </div>
                                            {{-- <a href="/Clients/NewStatic" class="btn btn-info text-bolder float-right"></a> --}}
                                        </div>
                                        <input type="hidden" value="{{$readonly}}" id="readonly_flag">
                                    </div>
                                    <div class="container border border-secondary rounded p-1 hide" id="action_for_selected_window">
                                        <h6 class="text-center">Action for Selected Clients</h6>
                                        <div class="row">
                                            <div class="col-md-3 border-right border-secondary">
                                                <label for="select_all_clients" class="form-label">Select All</label>
                                                <input type="checkbox" name="select_all_clients" id="select_all_clients">
                                                <br>
                                                <span id="client_select_counts" class="text-info">0 Client(s) Selected</span>
                                            </div>
                                            <form action="/delete_clients" method="POST" class="col-md-3 border-right border-secondary my-1">
                                                @csrf
                                                <input type="hidden" name="hold_user_id_data" id="hold_user_id_data">
                                                @php
                                                    $btnText = "<i class=\"ft-trash\"></i> Delete";
                                                    $otherClasses = "btn-block";
                                                    $btn_id = "delete_clients_id";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-sm btn-outline-danger" {{$readonly}} id="delete_clients_id" type="button"><i class="ft-trash"></i> Delete</button> --}}
                                                <div class="container hide" id="delete_clients_window">
                                                    <p><b>Are you sure you want to delete <span id="delete_number_clients"></span> Client(s)</b>?</p>
                                                    <label for="delete_from_router" class="form-label">Delete Client Data on Router</label>
                                                    <input type="checkbox" checked name="delete_from_router" id="delete_from_router">
                                                    @php
                                                        $btnText = "<i class=\"ft-check\"></i> Yes";
                                                        $otherClasses = "";
                                                        $btn_id = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="danger" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-outline-danger" {{$readonly}} type="submit">Yes</button> --}}
                                                    @php
                                                        $btnText = "<i class=\"ft-x\"></i> No";
                                                        $otherClasses = "";
                                                        $btn_id = "no_dont_delete_selected";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-outline-secondary" id="no_dont_delete_selected" type="button">No</button> --}}
                                                </div>
                                            </form>
                                            <form class="col-md-3" action="/send_sms_clients" method="POST" class="col-md-3 my-1">
                                                @csrf
                                                <input type="hidden" name="hold_user_id_data" id="hold_user_id_data_2">
                                                @php
                                                    $btnText = "<i class=\"fa-solid fa-paper-plane\"></i> Send SMS";
                                                    $otherClasses = "btn-block mt-1";
                                                    $btn_id = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-sm btn-outline-info" {{$readonly}} type="submit"><i class="fa-solid fa-paper-plane"></i> Send SMS</button> --}}
                                            </form>
                                            <div class="col-md-3 border-right border-secondary">
                                                @php
                                                    $btnText = "<i class=\"fa fa-file-export\"></i> Migrate Clients";
                                                    $otherClasses = "btn-block mt-1 ".($export_data == "disabled" ? "d-none" : "");
                                                    $btn_id = "export_selected_clients_data_btn";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" :readOnly="$readonly" btnType="info" type="button" :disabled="$export_data" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-sm btn-outline-info {{$export_data == "disabled" ? "d-none" : ""}}" {{$readonly}} {{$export_data}} type="button" id="export_selected_clients_data_btn"><i class="fa fa-file-export"></i> Export Data</button> --}}
                                            </div>
                                            <div class="col-md-12 card-content">
                                                <h6 class="text-center"><u>Clients Selected</u></h6>
                                                <input type="hidden" id="clients_list_selected">
                                                <small class="text-secondary bg-white" id="clients_selected"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <div class="container text-center my-2" id="loading_clients_data">
                                            <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                        </div>
                                        <table class="table table-striped table-bordered zero-configuration dataTable d-none" id="clients_table">
                                            <thead>
                                                <tr>
                                                    <th><span>#</span></th>
                                                    <th><span>Full Names</span></th>
                                                    <th><span>Account Number</span></th>
                                                    <th><span>Location</span></th>
                                                    <th><span>Due Date</span></th>
                                                    <th><span>Network &amp; Gateway</span></th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
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
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/client_usage_report.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <script>
        var data = @json($client_data);
        var added_last_week = @json($added_last_week);
        var readonly = @json($readonly);
        // here now you create the clients table
        // console.log(data);
    </script>
    <!-- BEGIN CLIENT JS-->
    <script src="theme-assets/js/core/client.js"></script>
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