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
    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<style>
    .showBlock{
      display: block;
      overflow-y: scroll;
    }
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
                {{-- DELETE THE BRIDGE --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_bridge_data_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger white">
                            <h4 class="modal-title white" id="myModalLabel5">Confirm Deletion Of <span id="bridge_name_heading">Bridge</span>.</h4>
                            <button id="close_delete_bridge_data_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <p>Are you sure you want to permanently delete this Bridge?</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="row w-100">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-trash\"></i> Proceed to Delete";
                                            $otherClasses = "btn-block";
                                            $btnLink = "#";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="danger" btnId='delete_bridge_url_holder' btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/Routers/Delete/{{ $router_data[0]->router_id }}" class="btn btn-danger btn-sm" >Proceed to Delete</a> --}}
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-x\"></i> Close";
                                            $validated = "btn-block";
                                        @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" :otherClasses="$validated" btnId="close_delete_bridge_data_modal_2" :readOnly="$readonly" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DELETE THE BRIDGE --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_profile_data_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger white">
                            <h4 class="modal-title white" id="myModalLabel5">Confirm Deletion Of <span id="profile_name_heading">Bridge</span>.</h4>
                            <button id="close_delete_profile_data_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <p>Are you sure you want to permanently delete this Bridge?</p>
                                    <label for="accept_delete_pool" style="cursor: pointer;"><input type="checkbox" name="accept_delete_pool" id="accept_delete_pool"> <b>Also delete IP pool</b></label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="row w-100">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-trash\"></i> Proceed to Delete";
                                            $otherClasses = "btn-block";
                                            $btnLink = "#";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="danger" btnId='delete_profile_url_holder' btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/Routers/Delete/{{ $router_data[0]->router_id }}" class="btn btn-danger btn-sm" >Proceed to Delete</a> --}}
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "<i class=\"fas fa-x\"></i> Close";
                                            $validated = "btn-block";
                                        @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" :otherClasses="$validated" btnId="close_delete_profile_data_modal_2" :readOnly="$readonly" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- UPDATE CLIENT PHONE NUMBER --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="sync_bridge_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info white">
                            <h4 class="modal-title white">Add Bridges</h4>
                            <button id="close_sync_bridge_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <form action="/sync_bridge_modal" method="post" class="form-control-group">
                                        @csrf
                                        <input type="hidden" name="router_id" value="{{ $router_data[0]->router_id }}">
                                        <h5 class="text-center" >Add Bridges</h5>
                                        <p class="card-text"><strong>Note:</strong> 
                                            <br>- Every bridge that is not available in your account is listed here!.
                                            <br>- Select the bridges and they will be added to your account.
                                        </p>
                                        <h6 id="selected_bridges" class="d-none text-dark badge bg-success">0 bridge(s) selected</h6>
                                        <table class="table table-striped table-bordered zero-configuration dataTable w-100" id="router_table_data_bridge">
                                            <thead>
                                                <tr>
                                                    <th><span>#</span></th>
                                                    <th><span>Bridge Names</span></th>
                                                    <th><span>Bridge Status</span></th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="4" style="text-center">Loading bridge details...</td></tr>
                                            </tbody>
                                        </table>
                                        <div class="row w-100">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_sync_bridge_modal_2" :readOnly="$readonly" />
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
                {{-- SYNC PPPOE PROFILES --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="sync_profiles_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info white">
                                <h4 class="modal-title white">Add PPPoE Profiles</h4>
                                <button id="close_sync_profile_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <form action="/sync_profile_modal" method="post" class="form-control-group">
                                        @csrf
                                        <input type="hidden" name="router_id" value="{{ $router_data[0]->router_id }}">
                                        <h5 class="text-center" >Add PPPoE Profiles</h5>
                                        <p class="card-text"><strong>Note:</strong> 
                                            <br>- Every profile that is not available in your account is listed here!.
                                            <br>- Select the profile and they will be added to your account.
                                        </p>
                                        <h6 id="selected_profiles" class="d-none text-dark badge bg-success">0 bridge(s) selected</h6>
                                        <table class="table table-striped table-bordered zero-configuration dataTable w-100" id="router_table_data_profiles">
                                            <thead>
                                                <tr>
                                                    <th><span>#</span></th>
                                                    <th><span>Profile Names</span></th>
                                                    <th><span>Profile Status</span></th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="4" style="text-center">Loading bridge details...</td></tr>
                                            </tbody>
                                        </table>
                                        <div class="row w-100">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_sync_profile_modal_2" :readOnly="$readonly" />
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

                {{-- UPDATE CLIENT BRIDGE --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="edit_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info white">
                                <h4 class="modal-title white" id="heading_profile_1">Edit Profile Details</h4>
                                <button id="close_edit_profile_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <form action="/update_profile_data" method="post" class="form-control-group" onsubmit="return validateForm()">
                                        @csrf
                                        <input type="hidden" name="router_id" value="{{ $router_data[0]->router_id }}">
                                        <h5 class="text-center" id="heading_profile_2" >Edit Profile Details <small id="loading_profile_details" class="text-small text-primary invisible"><i class="fas fa-refresh fa-spin"></i> Loading... </small></h5>
                                        <p class="card-text"><strong>Note:</strong> 
                                            <br>- Every profile that is not available in your account is listed here!.
                                            <br>- Select the profile and they will be added to your account.
                                        </p>
                                        <p class="d-none" id="profile_details"></p>
                                        <hr>
                                        <div class="form-group">
                                            <label for="edit_profile_name"  id="heading_profile_3" class="form-control-label"><b>Edit Profile Name</b></label>
                                            <input type="text" name="edit_profile_name" id="edit_profile_name" class="form-control" required placeholder="e.g 10Mbps">
                                            <input type="hidden" name="edit_profile_name_2" id="edit_profile_name_2" class="form-control">
                                        </div>
                                        <h6 class="text-left"><u>Select IP Address Pool</u></h6>
                                        <label for="new_pool"><input type="checkbox" name="new_pool" id="new_pool"> <b>Create new IP Pool</b></label>
                                        <div class="row" id="existing_pool">
                                            <div class="col-md-6">
                                                <label for="local_address" class="form-control-label">Local Address</label>
                                                <select name="local_address" id="local_address" class="form-control">
                                                    <option value="">Select Pool(No pool selected)</option>
                                                    <option value="ip_address">Enter IP Address</option>
                                                </select>
                                                <input class="form-control d-none" type="text" name="local_ip_address" id="local_ip_address" placeholder="Input IP Address">
                                                <small style="cursor: pointer;" id="back_to_ippools_list" style="width: fit-content;" class="text-primary mt-1 d-none"><i class="fa fa-arrow-left"></i> back to pool list</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="remote_address" class="form-control-label">Remote Address</label>
                                                <select name="remote_address" id="remote_address" class="form-control">
                                                    <option value="" hidden>Select Pool(No pool selected)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row d-none" id="new_pool_address">
                                            <div class="col-md-4">
                                                <label for="new_pool_name" class="form-control-label">New Pool Name</label>
                                                <input class="form-control" type="text" name="new_pool_name" id="new_pool_name" placeholder="e.g Pool A">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pool_range_start" class="form-control-label">Pool Range Start</label>
                                                <input class="form-control" type="text" name="pool_range_start" id="pool_range_start" placeholder="e.g 192.168.88.1">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pool_range_end" class="form-control-label">Pool Range End</label>
                                                <input class="form-control" type="text" name="pool_range_end" id="pool_range_end" placeholder="e.g 192.168.88.254">
                                            </div>
                                        </div>
                                        <h6 class="text-left mt-1"><u>Speed</u></h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="" class="form-control-label">Upload Speed</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input class="form-control" min="0" type="number" name="upload_speed_value" id="upload_speed_value" placeholder="e.g 10">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select name="upload_speed_unit" id="upload_speed_unit" class="form-control">
                                                            <option value="" hidden>Select Speed</option>
                                                            <option selected value="M">Mbps</option>
                                                            <option value="K">Kbps</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="form-control-label">Download Speed</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input class="form-control" min="0" type="number" name="download_speed_value" id="download_speed_value" placeholder="e.g 10">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select name="download_speed_unit" id="download_speed_unit" class="form-control">
                                                            <option value="" hidden>Select Speed</option>
                                                            <option selected value="M">Mbps</option>
                                                            <option value="K">Kbps</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row w-100">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="save_router_profile" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_edit_profile_modal_2" :readOnly="$readonly" />
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

                {{-- UPDATE CLIENT BRIDGE --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="edit_bridge_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info white">
                                <h4 class="modal-title white" id="heading_1">Edit Bridges Details</h4>
                                <button id="close_edit_bridge_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <form action="/update_bridge_data" method="post" class="form-control-group">
                                        @csrf
                                        <input type="hidden" name="router_id" value="{{ $router_data[0]->router_id }}">
                                        <h5 class="text-center" id="heading_2" >Edit Bridges Details</h5>
                                        <p class="card-text"><strong>Note:</strong> 
                                            <br>- Every bridge that is not available in your account is listed here!.
                                            <br>- Select the bridges and they will be added to your account.
                                        </p>
                                        <hr>
                                        <div class="form-group">
                                            <label for="edit_bridge_name"  id="heading_3" class="form-control-label"><b>Edit Bridge Name</b></label>
                                            <input type="text" name="edit_bridge_name" id="edit_bridge_name" class="form-control" required>
                                            <input type="hidden" name="edit_bridge_name_2" id="edit_bridge_name_2" class="form-control">
                                        </div>
                                        <h6 class="text-center text-primary"><u>Interfaces</u></h6>
                                        <table class="table table-striped table-bordered zero-configuration dataTable w-100" id="router_table_data_interfaces">
                                            <thead>
                                                <tr>
                                                    <th><span>#</span></th>
                                                    <th><span>Interface Name</span></th>
                                                    <th><span>Bridge Assigned</span></th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="4" style="text-center">Loading bridge details...</td></tr>
                                            </tbody>
                                        </table>
                                        <div class="row w-100">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="submit_bridge_details" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_edit_bridge_modal_2" :readOnly="$readonly" />
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
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="container">
                            {{-- DELETE THE CLIENT --}}
                            <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_router_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger white">
                                        <h4 class="modal-title white" id="myModalLabel2">Confirm Delete Of {{ucwords(strtolower($router_data[0]->router_name))}}.</h4>
                                        <button id="hide_delete_expense" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <p>Are you sure you want to permanently delete this Router?</p>
                                                <p><b>Note:</b></p>
                                                <p>- All {{ $user_count[0]->Total }} User(s) associated to this router will be deleted from the database</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row w-100">
                                                <div class="col-md-6">
                                                    @php
                                                        $btnText = "<i class=\"fas fa-trash\"></i> Proceed to Delete";
                                                        $otherClasses = "btn-block";
                                                        $btnLink = "/Routers/Delete/".$router_data[0]->router_id;
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                    {{-- <a href="/Routers/Delete/{{ $router_data[0]->router_id }}" class="btn btn-danger btn-sm" >Proceed to Delete</a> --}}
                                                </div>
                                                <div class="col-md-6">
                                                    @php
                                                        $btnText = "<i class=\"fas fa-x\"></i> Close";
                                                        $validated = "btn-block";
                                                    @endphp
                                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" :otherClasses="$validated" btnId="close_this_window_delete" :readOnly="$readonly" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                            @php
                                                $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                                $otherClasses = "ml-0";
                                                $btnLink = "/Routers";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="infor" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="/Routers" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to list</a> --}}
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-trash-2\"></i> Delete";
                                                $otherClasses = "text-lg float-right";
                                                $btn_id = "delete_user";
                                            @endphp
                                            <x-button :btnText="$btnText" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button {{$readonly}} id="delete_user" class="btn btn-danger text-lg float-right"><i class="ft-trash-2"> Delete</i></button> --}}
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
                                    @php
                                        $btnText = "Show Router Configuration";
                                        $otherClasses = "my-2 ".($readonly);
                                        $btn_id = "configuration_show_button";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                    {{-- <button id="configuration_show_button" class="btn btn-secondary btn-sm my-2 {{$router_data[0]->activated == 0 ? "d-none" : ""}}">Show Router Configuration</button> --}}
                                    <div id="configuration_window" class="container shadow-0 border border-rounded p-1 w-100 {{$router_data[0]->activated == 1 ? "d-none" : ""}}">
                                        @php
                                            $btnText = "<i class=\"ft-copy\" ></i> Copy";
                                            $otherClasses = "mb-2 ".($readonly);
                                            $btn_id = "send_to_clipboard";
                                        @endphp
                                        <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        {{-- <button class="btn btn-sm btn-primary mb-2" id="send_to_clipboard"><i class="ft-copy" ></i> Copy</button> --}}
                                        <h4 class="text-center">Router Configuration</h4>
                                        <p id="command_holder">
                                            {{-- <span class="text-success">## Set the SSTP Profile</span><br> --}}
                                            /ppp profile add name="SYSTEM_SSTP" comment="Do not delete: Default SYSTEM VPN profile"<br><br>
                                            
                                            {{-- <span class="text-success">## Add the SSTP Interface</span><br> --}}
                                            /interface sstp-client add name="SYSTEM_SSTP_TWO" connect-to={{$ip_address}} user={{$router_data[0]->sstp_username}} password={{$router_data[0]->sstp_password}} profile="SYSTEM_SSTP" authentication=pap,chap,mschap1,mschap2 disabled=no comment="Do not delete: SYSTEM connection to {{$router_data[0]->router_name}}"<br><br>
                                            
                                            {{-- <span class="text-success">## Configure routes</span><br> --}}
                                            /ip route add dst-address=192.168.254.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK1"<br>
                                            /ip route add dst-address=192.168.253.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK2"<br>
                                            /ip route add dst-address=192.168.252.0/24 gateway=192.168.254.1 comment="Do not delete: SYSTEM VPN SERVER NETWORK3"<br><br>
                                            
                                            {{-- <span class="text-success">## Configure firewall</span><br> --}}
                                            /ip firewall filter add chain=input action=accept in-interface=SYSTEM_SSTP_TWO log=no log-prefix="" comment="Do not delete: Allow SYSTEM remote access" disabled=no<br>
                                            /ip firewall filter move [find where in-interface=SYSTEM_SSTP_TWO] destination=0<br><br>

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
                                            /user group add name="SYSTEM_FULL" policy="local,telnet,ssh,ftp,reboot,read,write,test,winbox,read,sensitive,api" comment="Do not delete: SYSTEM user group"<br>
                                            
                                            /user add name="{{$router_data[0]->sstp_username}}" password="{{$router_data[0]->sstp_password}}" group="SYSTEM_FULL" comment="Do not delete: SYSTEM API User" <br>
                                            
                                            /beep
                                            <br>
                                        </p>
                                            @php
                                                $btnText = "<i class=\"ft-settings\"></i> Connect";
                                                $otherClasses = "mt-1 ".($router_data[0]->activated == 0 ? "" : "d-none");
                                                $btnLink = "".url()->route("connect_router",$router_data[0]->router_id);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="success" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{url()->route("connect_router",$router_data[0]->router_id)}}" class="btn btn-success btn-sm mt-1 {{$router_data[0]->activated == 0 ? "" : "d-none"}}"><i class="ft-settings"></i> Connect</a> --}}
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
                                                @php
                                                    $btnText = "<i class=\"ft-upload\" ></i> Update";
                                                    $otherClasses = "".$readonly;
                                                    $btn_id = "send_to_clipboard";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button {{$readonly}} class="btn btn-success text-dark" type="submit"><i class="ft-upload"></i> Update</button> --}}
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                    $otherClasses = "mt-1 ".($router_data[0]->activated == 0 ? "" : "d-none");
                                                    $btnLink = "/Routers";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
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
                @if (count($router_stats) > 0)
                    {{-- router more information start --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Router Information</h4>
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
                                        <div class="mx-auto my-2">
                                            <ul class="nav nav-tabs nav-justified" id="myTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab"><i class="ft-info mr-1"></i> Router Information</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab"><i class="ft-flag mr-1"></i> Bridge Management </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link " id="tab3-tab" data-toggle="tab" href="#tab3" role="tab"><i class="ft-file mr-1"></i> PPP Profile Management</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content" id="myTabsContent">
                                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                                <h5 class="text-center">Router Information</h5>
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
                                                        @php
                                                            $btnText = "Reboot";
                                                            $otherClasses = "disabled";
                                                            $btnLink = "/Router/Reboot/".$router_data[0]->router_id;
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                        {{-- <a href="/Router/Reboot/{{ $router_data[0]->router_id }}"
                                                            class="btn btn-primary disabled {{$readonly}}">Reboot</a> --}}
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
                                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                                <h5 class="text-center">Bridge Management</h5>
                                                <h6 class="text-primary"><strong><u>Router List</u></strong></h6>
                                                <div class="row my-1">
                                                    <div class="col-md-4">
                                                        @php
                                                            $btnText = "<i class=\"ft-refresh-cw\" ></i> Sync";
                                                            $otherClasses = "text-dark";
                                                            $btn_id = "sync_bridges_btn";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="success" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        
                                                        @php
                                                            $btnText = "<i class=\"ft-plus\" ></i> Add Bridge";
                                                            $otherClasses = "";
                                                            $btn_id = "add_bridges_btn";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    </div>
                                                </div>
                                                <p class="card-text"><strong>Note:</strong> <br>
                                                    - Manage your router bridges from here
                                                    <br>- Every action you carry out here will be directly applied to the router.
                                                    <br>- Sync to include the existing bridges on your router to this system.
                                                </p>
                                                <table class="table table-striped table-bordered zero-configuration dataTable w-100" id="router_table_data">
                                                    <thead>
                                                        <tr>
                                                            <th><span>#</span></th>
                                                            <th><span>Router Names</span></th>
                                                            <th><span>Bridge Status</span></th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr><td colspan="4">Loading bridge details...</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                                <h5 class="text-center">PPP Profile Management</h5>
                                                <h6 class="text-primary"><strong><u>Profile List</u></strong></h6>
                                                <div class="row my-1">
                                                    <div class="col-md-4">
                                                        @php
                                                            $btnText = "<i class=\"ft-refresh-cw\" ></i> Sync";
                                                            $otherClasses = "text-dark d-none";
                                                            $btn_id = "sync_profiles_btn";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="success" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        
                                                        @php
                                                            $btnText = "<i class=\"ft-plus\" ></i> Add PPPoE Profile";
                                                            $otherClasses = "";
                                                            $btn_id = "add_profiles_btn";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    </div>
                                                </div>
                                                <p class="card-text"><strong>Note:</strong> <br>
                                                    - Manage your router profiles from here
                                                    <br>- Every action you carry out here will be directly applied to the router.
                                                    <br>- Sync to include the existing profiles on your router to this system.
                                                </p>
                                                <table class="table table-striped table-bordered zero-configuration dataTable w-100" id="router_profile_table">
                                                    <thead>
                                                        <tr>
                                                            <th><span>#</span></th>
                                                            <th><span>Profile Name</span></th>
                                                            <th><span>Profile Status</span></th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr><td colspan="4">Loading profile details...</td></tr>
                                                    </tbody>
                                                </table>
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
                                        {{-- <a href="" class="btn btn-primary">Refresh</a> --}}
                                        @php
                                            $btnText = "Refresh";
                                            $otherClasses = "";
                                            $btnLink = "";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
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
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        var router_detail = @json($router_detail ?? '');
        var router_data = @json($router_data ?? '');
    </script>
    <script src="/theme-assets/js/core/router_infor.js"></script>
</body>

</html>
