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
    </style>
    <!-- END Custom CSS-->
</head>

<style>
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
                                <h4 id="view_clients_inform" class="card-title">View <span class="text-secondary">{{ ucwords(strtolower($clients_data[0]->client_name)) }} - {{ (($clients_data[0]->client_account)) }}</span></h4>
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
                                    </ul>
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
                                                                    <button type="button" id="delete_user_from_the_system" class="btn btn-sm btn-outline-danger"><i class="ft-trash"></i></button>
                                                                    <div class="container my-1 border border-dark rounded p-1 d-none" id="delete_the_user">
                                                                        <h4 class="text-center">Delete User!</h4>
                                                                        <p><b>Delete this user from the system?</b></p>
                                                                        <a href="/delete_user/{{$clients_data[0]->client_id}}" class="btn btn-sm btn-outline-danger btn-block"><i class="ft-trash"></i> Delete User</a>
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
                                                                <div class="container">
                                                                    <button class="btn btn-success btn-sm {{$readonly}}" type="submit"><i class="ft-save"></i> Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" id="close_update_status_window" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
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
                                        </ul>
                                    </div>
                                    <div class="mx-auto my-2 {{$clients_data[0]->validated == 1 ? "d-none" : ""}}">
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-sm btn-block btn-success" {{$readonly}} id="change_status" type="button"><i class="ft-refresh"></i> Validate User</button>
                                        </div>
                                    </div>
                                    <div class="tab-content" id="myTabsContent">
                                        <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <p><strong>Note: </strong><br> - User status when active the user will recieve
                                                        internet connection. <br>
                                                        - Automate transaction when active the system will monitor the clients payment
                                                        process and activate or deactivate the client when necessary <br>
                                                        - When a user is frozen don`t activate any option either the <b>Automate Transaction</b> or the <b>User Status</b>
                                                    </p>
                                                </div>
                                                <div class="col-md-3 border-left border-secondary">
                                                    <button id="prompt_delete" class="btn btn-secondary float-right btn-sm {{$clients_data[0]->validated == 0 ? "d-none" : ""}} {{$readonly}}"><i class="fas fa-trash"></i> Delete</button>
                                                </div>
                                            </div>
                                            <div class="container">
                                                {{-- DELETE THE CLIENT --}}
                                                <div class="modal fade text-left hide" id="delete_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Confirm Delete Of {{ucwords(strtolower($clients_data[0]->client_name))}}.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="hide_delete_column" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <p class="text-dark"><b>Are you sure you want to delete "{{ucwords(strtolower($clients_data[0]->client_name))}}"?</b></p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        <button type="button" id="close_this_window_delete" class="btn grey btn-secondary btn-sm w-100" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <a href="/delete_user/{{$clients_data[0]->client_id}}" class="btn btn-danger btn-sm w-100 "><i class="ft-trash"></i> Delete</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- UPDATE CLIENT PHONE NUMBER --}}
                                                <div class="modal fade text-left hide" id="update_phone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Phone Number.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_phone_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/change_client_phone" method="post" class="form-control-group">
                                                                        @csrf
                                                                        <h6 class="text-center" >Change Phone Number</h6>
                                                                        <input type="hidden" name="clients_id"
                                                                            value="{{ $clients_data[0]->client_id }}">
                                                                        <label for="client_new_phone" class="form-control-label" id="">New Phone Number</label>
                                                                        <input type="number" required name="client_new_phone" id="client_new_phone" class="form-control" value="{{ $clients_data[0]->clients_contacts }}" placeholder="New Phone Number">
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button {{$readonly}} type="submit" class="btn btn-info btn-sm w-100 my-1"><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary btn-sm w-100 my-1" type="button" id="close_update_phone_2">Cancel</button>
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

                                                {{-- UPDATE EXPIRATION DATE --}}
                                                <div class="modal fade text-left hide" id="update_expiration_date_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Expiration Date.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_expiration_date_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/changeExpDate" method="post" class="form-control-group">
                                                                        @csrf
                                                                        <h6 class="text-center" >Change Expiration Date</h6>
                                                                        <input type="hidden" name="clients_id"
                                                                            value="{{ $clients_data[0]->client_id }}">
                    
                                                                        <label for="expiration_date_edits" class="form-control-label" id="">New Expiration Date</label>
                                                                        <input type="date" value="<?=date("Y-m-d", strtotime($clients_data[0]->next_expiration_date))?>" required name="expiration_date_edits" id="expiration_date_edits" class="form-control" placeholder="New Expiration Date">
                    
                                                                        <label for="expiration_time_edits" class="form-control-label" id="">New Expiration Time</label>
                                                                        <input type="time" value="<?=date("H:i", strtotime($clients_data[0]->next_expiration_date))?>" required name="expiration_time_edits" id="expiration_time_edits" class="form-control" placeholder="New Expiration Time">
                    
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button type="submit" class="btn btn-info btn-sm w-100 my-1" {{$readonly}}><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary btn-sm w-100 my-1" type="button" id="close_update_expiration_date_modal_2">Cancel</button>
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

                                                {{-- UPDATE MONTHLY PAYMENT --}}
                                                <div class="modal fade text-left hide" id="update_monthly_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Monthly Payment.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_monthly_payment_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/change_client_monthly_payment" method="post" class="form-control-group">
                                                                        @csrf
                                                                        <h6 class="text-center" >Change Monthly Payment</h6>
                                                                        <input type="hidden" name="clients_id"
                                                                            value="{{ $clients_data[0]->client_id }}">
                                                                        <label for="client_monthly_payment" class="form-control-label" id="">New Monthly Payment</label>
                                                                        <input type="number" required name="client_monthly_payment" id="client_monthly_payment" class="form-control" value="{{ $clients_data[0]->monthly_payment }}" placeholder="New Phone Number">
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button {{$readonly}} type="submit" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_monthly_payment_2">Cancel</button>
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

                                                {{-- UPDATE MONTHLY MINIMUM PAYMENT --}}
                                                <div class="modal fade text-left hide" id="update_monthly_min_pay_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Monthly Minimum Payment.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_monthly_min_pay_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form method="POST" action="{{route("client.update.minimum_payment.static")}}" class="form-control-group border border-primary rounded p-1">
                                                                        @csrf
                                                                        <h6 class="text-center">Change Minimum Payment</h6>
                                                                        <input type="hidden" value="{{$clients_data[0]->client_id}}" name="client_id">
                                                                        <label for="change_minimum_payment" class="form-control-label">Change Minimum Payment</label>
                                                                        <select name="change_minimum_payment" id="change_minimum_payment" class="form-control" required>
                                                                            <option hidden value="">Select Payment Option</option>
                                                                            <option {{$clients_data[0]->min_amount == "10" ? "selected" : ""}} value="10">10%</option>
                                                                            <option {{$clients_data[0]->min_amount == "15" ? "selected" : ""}} value="15">15%</option>
                                                                            <option {{$clients_data[0]->min_amount == "25" ? "selected" : ""}} value="25">25% (¼ Payment)</option>
                                                                            <option {{$clients_data[0]->min_amount == "50" ? "selected" : ""}} value="50">50% (½ Payment)</option>
                                                                            <option {{$clients_data[0]->min_amount == "75" ? "selected" : ""}} value="75">75% (¾ Payment)</option>
                                                                            <option {{$clients_data[0]->min_amount == "80" ? "selected" : ""}} value="80">80%</option>
                                                                            <option {{$clients_data[0]->min_amount == "90" ? "selected" : ""}} value="90">90%</option>
                                                                            <option {{$clients_data[0]->min_amount == "100" ? "selected" : ""}} value="100">Full Payment</option>
                                                                        </select>
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button type="submit" class="btn btn-info btn-sm mt-1 w-100">Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary btn-sm mt-1 w-100" type="button" id="close_update_monthly_min_pay_modal_2">Close</button>
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

                                                {{-- UPDATE WALLET AMOUNT --}}
                                                <div class="modal fade text-left hide" id="update_wallet_amount_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" wallet amount.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_wallet_amount_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/changeWallet" method="post" class="form-control-group">
                                                                        @csrf
                                                                        <h6 class="text-center" >Change wallet balance</h6>
                                                                        <input type="hidden" name="clients_id"
                                                                            value="{{ $clients_data[0]->client_id }}">
                                                                        <label for="wallet_amounts" class="form-control-label" id="">New Wallet Amount</label>
                                                                        <input type="number" required name="wallet_amounts" id="wallet_amounts" class="form-control" value="{{$clients_data[0]->wallet_amount}}" placeholder="New wallet amounts">
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button type="submit" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary btn-sm my-1 w-100" type="button" id="close_update_wallet_amount_modal_2">Cancel</button>
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

                                                {{-- EDIT FREEZE --}}
                                                <div class="modal fade text-left hide" id="update_freeze_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Freeze status.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_freeze_status_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/set_freeze" method="post" class="form-control-group border border-primary rounded p-1">
                                                                        @csrf
                                                                        <h6 class="text-center" >Freeze Until</h6>
                                                                        @if ($clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)))
                                                                            <a href="/Client/deactivate_freeze/{{$clients_data[0]->client_id}}" class="btn btn-secondary btn-sm">Deactivate Freeze</a>
                                                                            <hr>
                                                                        @else
                                                                            {{-- <a href="/Client/activate_freeze/{{$clients_data[0]->client_id}}" class="btn btn-danger">Activate</a> 
                                                                            <hr>--}}
                                                                        @endif
                                                                        <br>
                                                                        <div class="container">
                                                                            <label for="freeze_date">Freeze Date</label>
                                                                            <select name="freeze_date" required id="freeze_date" class="form-control">
                                                                                <option value="" hidden>Select Option</option>
                                                                                <option value="set_freeze">Set Freezing Date</option>
                                                                                <option selected value="freeze_now">Freeze Now</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container d-none" id="setFreezeDate">
                                                                            <label for="freezing_date">Select Freeze Date</label>
                                                                            <input required type="date" name="freezing_date" id="freezing_date" value="{{date("Y-m-d",strtotime("1 day"))}}" min="{{date("Y-m-d")}}" class="form-control">
                                                                        </div>
                                                                        <div class="container">
                                                                            <label for="freeze_type">Freeze Type</label>
                                                                            <select name="freeze_type" required id="freeze_type" class="form-control">
                                                                                <option value="" hidden>Select Option</option>
                                                                                <option selected value="definate">Definate Freezing</option>
                                                                                <option value="Indefinite">In-definate Freezing</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container" id="freeze_window">
                                                                            <input type="hidden" name="clients_id"
                                                                                value="{{ $clients_data[0]->client_id }}">
                                                                            <input type="hidden" name="indefinate_freezing" value="00000000000000">
                                                                            <label for="freez_dates_edit" class="form-control-label" id="">Freeze until</label>
                                                                            <input type="date" required name="freez_dates_edit" id="freez_dates_edit" class="form-control" min="<?php echo date("Y-m-d",strtotime("1 day"));?>" value='{{date("Y-m-d",strtotime("1 day"))}}' placeholder="New Expiration Date">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <button type="submit" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_freeze_status_modal_2">Cancel</button>
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

                                                {{-- UPDATE REFEREE --}}
                                                <div class="modal fade text-left hide" id="update_refferee_by_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Set "{{ucwords(strtolower($clients_data[0]->client_name))}}" Refferee.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_refferee_by_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <div class="form-control-group">
                                                                        <p><b>What you need to know:</b></p>
                                                                        <p>- Start by searching the refferer<br>
                                                                            - If the refferer is valid set the refferers cut<br>
                                                                            - Then save. <br>
                                                                            - If there was a refferer before it will replace their details with the new refferer
                                                                        </p>
                                                                        <label for="wallet_amounts" class="form-control-label" id="">Search Refferer
                                                                            <span class="invisible" id="search_referer_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                                        <div class="row">
                                                                            <div class="col-md-9">
                                                                                <div class="autocomplete">
                                                                                    <input type="text" required name="search_refferer_keyword" id="search_refferer_keyword" class="form-control" placeholder="Type keyword: name, acc no, phone number">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <button class="btn btn-infor" id="find_user_refferal" type="button"><i class="fas fa-search"></i></button>
                                                                            </div>
                                                                        </div>
                                                                        <p id="refferer_data" class="d-none"></p>
                                                                        <span id="show_data_inside"></span>
                                                                        <hr class="border border-primary">
                                                                        <div class="container my-2">
                                                                            <h6 class="text-center"><u>Refferer Details</u></h6>
                                                                        </div>
                                                                        <form action="/set_refferal" method="post">
                                                                            @csrf
                                                                            <div class="row my-2">
                                                                                <input type="hidden" name="clients_id"
                                                                            value="{{ $clients_data[0]->client_id }}">
                                                                                <input type="hidden" name="refferal_account_no" id="refferer_acc_no2">
                                                                                <div class="col-md-6">
                                                                                    <p><b>Refferer Fullname</b></p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p class="user_data" id="refferer_name">{{$reffer_details[0] ?? 'Unknown'}}</p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p><b>Refferer Acc No</b></p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p class="user_data" id="refferer_acc_no">{{$reffer_details[1] ?? 'Unknown'}}</p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p><b>Refferer wallet</b></p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p class="user_data" id="reffer_wallet">{{$reffer_details[2] ?? 'Unknown'}}</p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p><b>Refferer Location</b></p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p class="user_data" id="refferer_location">{{$reffer_details[3] ?? 'Unknown'}}</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <label for="refferer_amount" class="form-control-label">Refferer Cut</label>
                                                                                <input type="number" class="form-control" name="refferer_amount" id="refferer_amount" placeholder="Refferers Cut - how much is he given" required>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <button disabled type="submit" class="btn btn-info my-1 btn-sm w-100" id="save_data_inside"><i class="fas fa-save"></i> Set</button>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_refferee_by_modal_2">Cancel</button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- UPDATE COMMENT --}}
                                                <div class="modal fade text-left hide" id="update_comments_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info white">
                                                            <h4 class="modal-title white" id="myModalLabel11">Update "{{ucwords(strtolower($clients_data[0]->client_name))}}" Comment.</h4>
                                                            <input type="hidden" id="delete_columns_ids">
                                                            <button id="close_update_comments_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container">
                                                                    <form action="/update_client_comment" method="post" class="form-control-group">
                                                                        @csrf
                                                                        <h6 class="text-center">Update Comment</h6>
                                                                        <input type="hidden" name="clients_id" value="{{ $clients_data[0]->client_id }}">
                                                                        <label for="comments" class="form-control-label" id="">Update Comment</label>
                                                                        <textarea name="comments" id="comments" cols="30" rows="3" class="form-control" placeholder="Comment here">{{ $clients_data[0]->comment}}</textarea>
                                                                        <div class="row w-100">
                                                                            <div class="col-md-6">
                                                                                <button {{$readonly}} type="submit" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Save</button>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_update_comments_modal_2">Cancel</button>
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
                                            <div class="container my-2">
                                                <h4 class="text-center"><u>Edit "{{ucwords(strtolower($clients_data[0]->client_name))}}" Data</u></h4>
                                                <table class="table table-bordered mb-0">
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6"><strong>Account Number:</strong></div>
                                                                <div class="col-sm-6">{{ $clients_data[0]->client_account }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{-- client payment automatiom --}}
                                                            @if ($clients_data[0]->validated == "1")
                                                                @if ($clients_data[0]->payments_status == 1)
                                                                    <div class="row">
                                                                        <div class="col-sm-6"><strong>Automate Transaction:</strong><div class='badge badge-success'>Activated</div>
                                                                        </div>
                                                                        <div class="col-sm-6"><a
                                                                                href="/deactivatePayment/{{ $clients_data[0]->client_id }}"
                                                                                class="btn btn-sm btn-danger {{$clients_data[0]->client_freeze_status == "1" ? "disabled":""}} {{$readonly}}">De-Activate</a><p class="text-success d-none"><b>Activated</b></p></div>
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-sm-6"><strong>Automate Transaction:</strong><div class='badge badge-danger'>De-activated</div>
                                                                        </div>
                                                                        <div class="col-sm-6"><a
                                                                                href="/activatePayment/{{ $clients_data[0]->client_id }}"
                                                                                class="btn btn-sm btn-success {{$clients_data[0]->client_freeze_status == "1" ? "disabled":""}} {{$readonly}}">Activate</a><p class="text-danger d-none"><b>De-activated</b></p></div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="row">
                                                                    <div class="col-sm-6"><strong>Automate Transaction:{!!$clients_data[0]->payments_status == "1" ? "<div class='badge badge-success'>Activated</div>" : "<div class='badge badge-danger'>De-activated</div>"!!}</strong></div>
                                                                    <div class="col-sm-6">
                                                                        <p>User is not validated yet!</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6"><strong>Phone Number:</strong> <br>{{ $clients_data[0]->clients_contacts }}</div>
                                                                <div class="col-sm-6"><button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_phone_number"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Monthly Payment:</strong> <br>Kes {{ number_format($clients_data[0]->monthly_payment) }}</div>
                                                                <div class="col-sm-5"><button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_monthly_payments"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @if ($clients_data[0]->validated == "1")
                                                                @if ($clients_data[0]->client_status == 1)
                                                                    <div class="row">
                                                                        <div class="col-sm-6"><strong>User status: <div class='badge badge-success'>Activated</div></strong></div>
                                                                        <div class="col-sm-6"><a
                                                                                href="/deactivate/{{ $clients_data[0]->client_id }}"
                                                                                class="btn btn-sm btn-danger {{$clients_data[0]->client_freeze_status == "1" ? "disabled":""}} {{$readonly}}">De-Activate</a><p class="text-success d-none"><b>Activated</b></p></div>
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-sm-6"><strong>User status: <div class='badge badge-danger'>De-activated</div></strong></div>
                                                                        <div class="col-sm-6"><a
                                                                                href="/activate/{{ $clients_data[0]->client_id }}"
                                                                                class="btn btn-sm btn-success {{$clients_data[0]->client_freeze_status == "1" ? "disabled":""}} {{$readonly}}">Activate</a><p class="text-danger d-none"><b>De-activated</b></p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="row">
                                                                    <div class="col-sm-6"><strong>User status: {!!$clients_data[0]->client_status == "1" ? "<div class='badge badge-success'>Activated</div>" : "<div class='badge badge-danger'>De-activated</div>"!!}</strong></div>
                                                                    <div class="col-sm-6">
                                                                        <p>User is not validated yet!</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong class="text-secondary">Minimum Payment:</strong> <br>{{$clients_data[0]->min_amount != 100 ? "Kes ".number_format(($clients_data[0]->min_amount / 100) * $clients_data[0]->monthly_payment)." (".$clients_data[0]->min_amount."%) of Kes ".number_format($clients_data[0]->monthly_payment) : "Full Payment (Kes ".number_format($clients_data[0]->monthly_payment).")"}}</div>
                                                                <div class="col-sm-5">
                                                                     <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} id="edit_minimum_amount"><i class="fas fa-pen"></i> Edit</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6"><strong>Registration Date:</strong></div>
                                                                <div class="col-sm-6">{{ $registration_date }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Wallet Amount:</strong> <br>Kes {{ $clients_data[0]->wallet_amount }}</div>
                                                                <div class="col-sm-5"><button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" style="width: fit-content;" id="edit_wallet"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6"><strong>Expiration Date:</strong> <br>{{$expire_date ? $expire_date : "Null"}}</div>
                                                                <div class="col-sm-6"> <button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} style="width: fit-content;" id="edit_expiration_date"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong class="text-secondary">Freeze Client:</strong> <span class="badge {{$clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "badge-success" : "badge-danger";}}">{{$clients_data[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "Active" : "In-Active";}}</span> <br><p>{{date("YmdHis") < date("YmdHis",strtotime($clients_data[0]->freeze_date)) ? "Client will be frozen on : ".date("D dS M Y",strtotime($clients_data[0]->freeze_date))." until " : "Frozen Until:"}} {{isset($freeze_date) && strlen($freeze_date) > 0 ? $freeze_date : "Not Set"}}</p></div>
                                                                <div class="col-sm-5"><button class="text-secondary btn btn-infor btn-sm mx-1 {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" {{$readonly}} id="edit_freeze_client"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-6"><strong>Location:</strong></div>
                                                                <div class="col-sm-6">
                                                                    @php
                                                                        echo $clients_data[0]->location_coordinates ? "<a class='text-danger' href = 'https://www.google.com/maps/place/".$clients_data[0]->location_coordinates."' target = '_blank'><u>Locate Client</u> </a>" :"No Co-ordinates provided for the client!" ;
                                                                    @endphp
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-sm-7"><strong>Reffered By:</strong> <br><p>{{$client_refferal ?? 'Refferal Not set'}} </p></div>
                                                                <div class="col-sm-5"><button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" id="edit_refferal"><i class="fas fa-pen"></i> Edit</button></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="row">
                                                                <div class="col-md-10">
                                                                    <strong>Comment:</strong> <br><p>{{isset($clients_data[0]->comment) ? ucwords(strtolower($clients_data[0]->comment)) : "No comments set!"}} </p>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button {{$readonly}} class="btn btn-infor btn-sm mx-1 text-xxs text-secondary {{$clients_data[0]->validated == 0 ? "d-none" : ""}}" id="edit_comments"><i class="fas fa-pen"></i> Edit</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <p><strong>Note: </strong><br> - Some fields can`t be left blank the default
                                                configuration is surounded with the {curly braces} you may select that if you
                                                dont want to change anything</small><br>
                                                - The upload and download speed might not work because of the fast track in
                                                firewall filter. <br>
                                                - Fill all the fields to update the client. <br>
                                                - When the "Allow router change" is not checked the changes will only be made in
                                                the database
                                            </p>
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
                                                        <label for="client_name" class="form-control-label">Clients Fullname {
                                                            <span
                                                                class="primary">{{ $clients_data[0]->client_name }}</span>
                                                            }</label>
                                                        <input type="text" name="client_name" id="client_name"
                                                            class="form-control rounded-lg p-1"
                                                            placeholder="Clients Fullname .." required
                                                            value="{{ old('client_name') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="client_address" class="form-control-label">Clients Address
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
                                                            Account Number { <span
                                                                class="primary">{{ $clients_data[0]->client_account }}</span>
                                                            }</label>
                                                        <input type="text" name="client_account_number"
                                                            id="client_account_number" class="form-control rounded-lg p-1"
                                                            placeholder="Client account number" readonly
                                                            value="{{ $clients_data[0]->client_account }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="client_monthly_pay" class="form-control-label">Clients
                                                            Monthly Payment { <span
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
                                                            { <span class="primary" id="secret_username"></span> }</label>
                                                        <input type="text" name="client_secret_username" id="client_secret_username"
                                                            class="form-control rounded-lg p-1" placeholder="ex 10.10.30.0"
                                                            required value="{{ old('client_secret_username') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span class="d-none" id="secret_holder"></span>
                                                        <label  id="errorMsg1" for="client_secret_password" class="form-control-label">Clients Secret Password {
                                                            <span class="primary" id="addresses"></span> } <button type="button" id="display_secret" class="btn btn-sm btn-infor"><span class="text-secondary"><i class="fas fa-eye"></i></span></button></label>
                                                        <input type="password" name="client_secret_password" id="client_secret_password"
                                                            class="form-control rounded-lg p-1" placeholder="ex 10.10.30.1/24"
                                                            required value="{{ old('client_secret_password') }}">
                                                    </div>
                                                </div>
                                                <div class="row my-1">
                                                    <div class="col-md-6 form-group">
                                                        <label for="router_name" class="form-control-label">Router Name: {
                                                            <span class="primary bolder" id="router_named">Hilary Dev</span> }
                                                            <span class="invisible" id="interface_load"><i
                                                                    class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                        <p id="router_data"><span class="secondary">The router list will
                                                                appear here.. If this message is still present you have no
                                                                routers present in your database.</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="client_address" class="form-control-label">Router
                                                            Profile: { <span class="primary bolder"
                                                                id="router_profiles"></span> } </label>
                                                        <p class="text-secondary" id="interface_holder">The router secret profiles
                                                            will appear here If the router is selected.If this message is still
                                                            present a router is not selected.</p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        @if ($clients_data[0]->validated == 1)
                                                            <button {{$readonly}} class="btn btn-success text-dark" type="submit"><i
                                                                    class="ft-upload"></i> Update User</button>
                                                        @else
                                                            <p>Update button appears here but user is not validated yet!</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <a class="btn btn-secondary btn-outline" href="/Clients"><i
                                                                class="ft-x"></i> Cancel</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="tab2" role="tabpanel">
                                            <p class="card-text">In this table below <b>{{ ucwords(strtolower($clients_data[0]->client_name)) }}</b> Issues will be displayed.</p>
                                                @if (session('success'))
                                                    <p class="success">{{ session('success') }}</p>
                                                @endif
                                            <p><span class="text-bold-600">Client Report Table:</span></p>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="search" id="searchkey_2"
                                                        class="form-control rounded-lg p-1" placeholder="Search here ..">
                                                </div>
                                                <div class="col-md-3">
        
                                                </div>
                                                <div class="col-md-3">
                                                    {{-- <a href="/Client-Reports/New" class="btn btn-purple btn-sm {{$readonly}}"><i class="ft-plus"></i> New Issue</a> --}}
                                                </div>
                                            </div>
                                            <div class="table-responsive" id="transDataReciever_2">
                                                <div class="container text-center my-2" id="logo_loaders">
                                                    <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                        src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                                </div>
                                                <table class="table" id="myTable">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Ticket Number</th>
                                                            <th>Report Title</th>
                                                            <th>Report Description</th>
                                                            <th>Reported By</th>
                                                            <th>Report Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($client_issues as $key => $report)
                                                            <tr>
                                                                <th scope="row">{{$key + 1}}
                                                                    @if ($report->status == "pending")
                                                                        <span class="badge text-light bg-danger text-dark" data-toggle="tooltip" title="" data-original-title="Pending!">P</span>
                                                                    @else
                                                                        <span class="badge text-light bg-success text-dark" data-toggle="tooltip" title="" data-original-title="Resolved!">R</span>
                                                                    @endif
                                                                </th>
                                                                <td>{{$report->report_code ?? "NULL"}}</td>
                                                                <td>{{$report->report_title}}</td>
                                                                <td data-toggle="tooltip" title="" data-original-title="{{$report->report_description}}">{{strlen($report->report_description) > 100 ? substr($report->report_description, 0, 100)."...." : $report->report_description}}</td>
                                                                <td>{{ucwords(strtolower($report->admin_reporter_fullname))}}</td>
                                                                <td>{{date("D dS M Y H:i:sA", strtotime($report->report_date))}}</td>
                                                                <td><a href="/Client-Reports/View/{{$report->report_id}}" class="btn btn-sm btn-purple text-bolder"
                                                                        data-toggle="tooltip" title="View this issue."><i
                                                                            class="ft-eye"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab3" role="tabpanel">
                                            <p class="card-text">In this table below you will see previously generated invoices for <b>{{ ucwords(strtolower($clients_data[0]->client_name)) }}</b>.</p>
                                            
                                            {{-- GENERATE INVOICE --}}
                                            <div class="modal fade text-left hide" id="generate_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info white">
                                                        <h4 class="modal-title white" id="myModalLabel11">Generate Invoice</h4>
                                                        <button id="close_generate_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form class="container" action="/New-Invoice" method="POST">
                                                                <div class="form-group">
                                                                    <input type="hidden" required id="client_id_invoice" name="client_id" value="{{ $clients_data[0]->client_id }}">
                                                                    <label for="invoice_id" class="form-control-label">Invoice No</label>
                                                                    <input type="text" class="form-control" readonly required name="invoice_number" value="{{$invoice_id}}" id="invoice_id" placeholder="Invoice number eg HYP-AB1">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="amount_to_pay" class="form-control-label">Amount to Pay</label>
                                                                    <input type="number" required class="form-control" name="amount_to_pay" id="amount_to_pay" value="{{$clients_data[0]->monthly_payment}}" placeholder="e.g: Kes 3,000">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="amount_to_pay" class="form-control-label">Payment Duration</label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input class="form-control" value="1" required type="number" name="period_duration" id="period_duration" placeholder="e.g: 1">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select required name="period_unit" id="period_unit" class="form-control">
                                                                                <option value="" hidden>Select period</option>
                                                                                <option selected value="month">Months</option>
                                                                                <option value="week">Weeks</option>
                                                                                <option value="year">Years</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="payment_from" class="form-control-label">Payment From</label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="date" required name="payment_from_date" id="payment_from_date" class="form-control" value="{{date("Ymd", strtotime($clients_data[0]->next_expiration_date)) < date("Ymd") ? date("Y-m-d") : date("Y-m-d", strtotime($clients_data[0]->next_expiration_date))}}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="time" required name="payment_from_time" id="payment_from_time" class="form-control" value="{{date("h:i", strtotime($clients_data[0]->next_expiration_date))}}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="invoice_deadline" class="form-control-label">Invoice Deadline</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control position-relative" name="invoice_deadline" id="invoice_deadline" placeholder="30 days" value="0" required>
                                                                        <div class="input-group-postpend">
                                                                            <span class="input-group-text" id="validationTooltipUsernamePrepend">Days</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="vat_included" class="form-control-label">Include VAT</label>
                                                                    <select name="vat_included" required id="vat_included" class="form-control">
                                                                        <option value="" hidden>Select an option</option>
                                                                        <option value="include_vat">Include 16% VAT in the total</option>
                                                                        <option value="exclude_vat">Exclude 16% VAT in the total</option>
                                                                        <option selected value="no_vat">No VAT</option>
                                                                    </select>
                                                                </div>
                                                                <div class="container" id="reponse_holder_invoices"></div>
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        <button {{$readonly_finance}} type="submit" id="generate_invoice" class="btn btn-info my-1 btn-sm w-100"><i class="fas fa-save"></i> Generate Invoice <span class="invisible" id="invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_generate_client_invoice_2">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- UPDATE INVOICE --}}
                                            <div class="modal fade text-left hide" id="view_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel12" style="padding-right: 17px;" aria-modal="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                        <h4 class="modal-title white" id="myModalLabel12">Update Invoice</h4>
                                                        <button id="close_view_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form class="container" method="POST" action="/Update-Invoice">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <input type="hidden" id="edit_client_id_invoice" name="client_id" value="{{ $clients_data[0]->client_id }}">
                                                                    <label for="edit_invoice_id" class="form-control-label">Invoice No</label>
                                                                    <input type="text" class="form-control" name="edit_invoice_id" readonly id="edit_invoice_id" placeholder="Invoice number eg HYP-AB1">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="edit_amount_to_pay" class="form-control-label">Amount to Pay</label>
                                                                    <input type="number" class="form-control" name="edit_amount_to_pay" id="edit_amount_to_pay" placeholder="e.g: Kes 3,000">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="amount_to_pay" class="form-control-label">Payment Duration</label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input class="form-control" value="1" type="number" name="edit_period_duration" id="edit_period_duration" placeholder="e.g: 1">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select name="edit_period_unit" id="edit_period_unit" class="form-control">
                                                                                <option value="" hidden>Select period</option>
                                                                                <option selected value="month">Months</option>
                                                                                <option value="week">Weeks</option>
                                                                                <option value="year">Years</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="payment_from" class="form-control-label">Payment From</label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="date" name="edit_payment_from_date" id="edit_payment_from_date" class="form-control">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="time" name="edit_payment_from_time" id="edit_payment_from_time" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="edit_invoice_deadline" class="form-control-label">Invoice Deadline</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control position-relative" id="edit_invoice_deadline" name="edit_invoice_deadline" placeholder="30 days" value="0" required>
                                                                        <div class="input-group-postpend">
                                                                            <span class="input-group-text" id="edit_validationTooltipUsernamePrepend">Days</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="edit_vat_included" class="form-control-label">Include VAT</label>
                                                                    <select name="edit_vat_included" id="edit_vat_included" class="form-control">
                                                                        <option value="" hidden>Select an option</option>
                                                                        <option value="include_vat">Include 16% VAT in the total</option>
                                                                        <option value="exclude_vat">Exclude 16% VAT in the total</option>
                                                                        <option selected value="no_vat">No VAT</option>
                                                                    </select>
                                                                </div>
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        <button {{$readonly_finance}} type="submit" id="edit_generate_invoice" class="btn btn-primary my-1 btn-sm w-100"><i class="fas fa-save"></i> Update Invoice <span class="invisible" id="edit_invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_view_client_invoice_2">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SEND INVOICE --}}
                                            <div class="modal fade text-left hide" id="send_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="padding-right: 17px;" aria-modal="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success white">
                                                        <h4 class="modal-title white" id="myModalLabel13">Send Invoice</h4>
                                                        <button id="close_send_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form class="container" method="POST" action="/Send-Invoice">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <p><b>Invoice Number : </b><span id="invoice_number_holder">NULL</span></p>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><b>Name</b></p>
                                                                    </div>
                                                                    <div class="col-md-6 ">
                                                                        <p><b>Tag</b></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>1. Fullname</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[client_name]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>2. First Name</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[client_f_name]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>3. Address</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[client_addr]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>4. Expiration Date</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[exp_date]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>5. Monthly Fees</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[monthly_fees]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>6. Account No.</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[acc_no]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>7. Wallet</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[client_wallet]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>8. Transaction Amount</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[trans_amnt]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>9. Transaction Amount</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[trans_amnt]</code></p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p>10. Invoice Link</p>
                                                                    </div>
                                                                    <div class="col-md-6  border border-light">
                                                                        <p><code>[inv_link]</code></p>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mt-2">
                                                                    <input type="hidden" class="form-control" name="send_invoice_id" id="send_invoice_id">
                                                                    <label for="invoice_message" class="form-control-label"><b>Invoice Message</b></label>
                                                                    <textarea name="invoice_message" id="invoice_message" cols="30" rows="3" class="form-control" placeholder="Compose..">Hello [client_name], use this link to download your invoice, Incase you have any questions call us via {{session()->has("organization") ? session("organization")->organization_main_contact : "0720268519"}}. Link: [inv_link].</textarea>
                                                                </div>
                                                                <div class="row w-100">
                                                                    <div class="col-md-6">
                                                                        <button {{$readonly_finance}} type="submit" id="send_client_invoice" class="btn btn-success my-1 btn-sm w-100"><i class="ft-mail"></i> Send Invoice Link <span class="invisible" id="edit_invoice_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></button>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_send_client_invoice_2">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SEND INVOICE --}}
                                            <div class="modal fade text-left hide" id="delete_client_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel13" style="padding-right: 17px;" aria-modal="true">
                                                <div class="modal-dialog modal-dialog-centered"  role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger white">
                                                        <h4 class="modal-title white" id="myModalLabel13">Confirm Invoice Delete</h4>
                                                        <button id="close_delete_client_invoice_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <div class="container" id="delete_invoice_notice"></div>
                                                            <div class="row w-100">
                                                                <div class="col-md-6">
                                                                    <a {{$readonly_finance}} type="submit" id="delete_client_invoice_btn" href="/Delete-Invoice/" class="btn btn-danger my-1 btn-sm w-100 {{$readonly_finance}}"><i class="ft-trash"></i> Delete</a>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <button class="btn btn-secondary my-1 btn-sm w-100" type="button" id="close_delete_client_invoice_2">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <p><span class="text-bold-600">Client Invoice Table:</span></p>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="search" id="searchkey_3"
                                                        class="form-control rounded-lg p-1" placeholder="Search here ..">
                                                </div>
                                                <div class="col-md-3">
                                                    <p id="errors"></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <button {{$readonly_finance}} class="btn btn-info btn-sm" id="new_invoice"><i class="ft-file-plus"></i> Generate Invoice</button>
                                                    {{-- <a href="/Client-Reports/New" class="btn btn-purple btn-sm {{$readonly}}"><i class="ft-plus"></i> New Issue</a> --}}
                                                </div>
                                            </div>
                                            <div class="table-responsive" id="invoice_table_holder">
                                                @php
                                                    function dateDifference($date1, $date2, $only_days = false) {
                                                        $d1 = new DateTime($date1);
                                                        $d2 = new DateTime($date2);
                                                        $interval = $d1->diff($d2);
                                                        if($only_days){
                                                            return $d1->diff($d2)->days;
                                                        }

                                                        // Check if the difference is in exact years
                                                        if ($interval->m === 0 && $interval->d === 0) {
                                                            return $interval->y . ' year';
                                                        }

                                                        // Check if the difference is in exact months
                                                        if ($interval->d === 0 && $interval->y === 0) {
                                                            return $interval->m . ' month';
                                                        }

                                                        // Else return difference in days
                                                        return ($d1->diff($d2)->days / 7) . ' week';
                                                    }
                                                @endphp
                                                <div class="container text-center my-2" id="logo_loaders_2">
                                                    <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                        src="{{session("organization_logo") != null ? session("organization_logo") :'/theme-assets/images/logoplaceholder.svg'}}" />
                                                </div>
                                                <table class="table" id="invoice_table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Invoice No</th>
                                                            <th>Amount to Pay</th>
                                                            <th>Date Generated</th>
                                                            <th>Payment for</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($invoices as $key => $invoice)
                                                                @php
                                                                    $invoice->invoice_for_duration = "0 days";
                                                                    $invoice->payment_from_date = date("Y-m-d", strtotime($invoice->date_generated));
                                                                    $invoice->payment_from_time = date("H:i", strtotime($invoice->date_generated));
                                                                    $date_selected = "NULL";
                                                                    if (isJson($invoice->invoice_for)) {
                                                                        $dates = json_decode($invoice->invoice_for);
                                                                        $date_selected = date("d-M-Y", strtotime($dates[0]))." - ".date("d-M-Y", strtotime($dates[1]));
                                                                        $invoice->invoice_for = $date_selected;
                                                                        $invoice->payment_from_date = date("Y-m-d", strtotime($dates[0]));
                                                                        $invoice->payment_from_time = date("H:i", strtotime($dates[0]));
                                                                        $invoice->invoice_for_duration = dateDifference($dates[0],$dates[1]);
                                                                    }else{
                                                                        $invoice->invoice_for = "NULL";
                                                                    }
                                                                    $deadline_expiry = dateDifference($invoice->date_generated, $invoice->invoice_deadline, true);
                                                                    $invoice->deadline_duration = $deadline_expiry;
                                                                    $invoice->date_generated_1 = date("D dS M Y", strtotime($invoice->date_generated));
                                                                    $invoice->date_generated_2 = date("Y-m-d", strtotime($invoice->date_generated));
                                                                    $invoice->amount_to_pay_f = "Kes ".number_format($invoice->amount_to_pay);
                                                                @endphp
                                                            <tr>
                                                                <th scope="row">{{$key + 1}} <input type="hidden" value="{{json_encode($invoice)}}" id="invoice_data_holder_{{$invoice->invoice_id}}"> </th>
                                                                <td>{{$invoice->invoice_number ?? "NULL"}}</td>
                                                                <td>{{$invoice->amount_to_pay_f}}</td>
                                                                <td>{{$invoice->date_generated_1}}</td>
                                                                <td>{{$date_selected}}</td>
                                                                <td>
                                                                    <button {{$readonly_finance}} data-toggle="tooltip" title="View Invoice" class="btn btn-sm btn-primary view_invoice" id="view_invoice_{{$invoice->invoice_id}}"><i class="ft-eye"></i></button>
                                                                    <a data-toggle="tooltip" title="Print Invoice" target="_blank" href="/Invoice/Print/{{$invoice->invoice_id}}" class="btn btn-sm btn-info"><i class="ft-printer"></i></a>
                                                                    <button {{$readonly_finance}} data-toggle="tooltip" title="Delete Invoice" class="btn btn-sm btn-danger delete_invoice" id="delete_invoice_{{$invoice->invoice_id}}"><i class="ft-trash"></i></button>
                                                                    <button {{$readonly_finance}} data-toggle="tooltip" title="Send Invoice" class="btn btn-sm btn-success send_invoice" id="send_invoice_{{$invoice->invoice_id}}"><i class="ft-mail"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                    </ul>
                                    <a href="/Clients" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                        to list</a>
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
                                        </ul>
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
    </script>
</body>

</html>
