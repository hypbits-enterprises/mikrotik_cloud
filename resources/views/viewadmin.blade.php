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
    <title>Hypbits - View Admin Details</title>
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
    .hide{
        display: none;
    }
</style>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <x-menu active="account_and_profile"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"SMS");
        $view = showOption($priviledges,"Account and Profile");
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Add Administrator</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Clients">Account And Profile</a>
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
                        <div class="container">
                            {{-- DELETE THE CLIENT --}}
                            <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_admin_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger white">
                                        <h4 class="modal-title white" id="myModalLabel2">Confirm Delete Of {{ucwords(strtolower($admin_data[0]->admin_fullname))}}.</h4>
                                        <button id="hide_delete_expense" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <p>Are you sure you want to permanently delete this user?</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row w-100">
                                                <div class="col-md-6">
                                                    @php
                                                        $btnText = "<i class=\"fas fa-trash\"></i> Delete";
                                                        $otherClasses = "btn-block";
                                                        $btnLink = "".url()->route("delete_admin",[$admin_data[0]->admin_id]);
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
                                <h4 class="card-title">Account and Profile</h4>
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
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (session('network_presence'))
                                        <p class="text-danger">{{ session('network_presence') }}</p>
                                    @endif
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-arrow-left\"></i> Back to list";
                                                $otherClasses = "";
                                                $btnLink = "/Accounts/add";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="infor" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="/Accounts/add" class="btn btn-infor"><i class="ft-arrow-left"></i>
                                                Back to list</a> --}}
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-trash-2\"></i> Delete";
                                                $otherClasses = "text-lg float-right";
                                                $btn_id = "delete_user";
                                                $btnSize="sm";
                                                $type = "button";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button toolTip="" btnType="danger" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button id="delete_user" class="btn btn-danger text-lg float-right" {{$readonly}}><i class="ft-trash-2"> Delete</i></button> --}}
                                        </div>
                                    </div>
                                    <h6><strong>Update Administrator</strong></h6>
                                    <p class="card-text">Fill all fields to add the Administrator.</p>
                                    <form action="/updateAdministrator" method="post" onsubmit="return validateForm()">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="admin_name" class="form-control-label">Fullname</label>
                                                <input type="text" name="admin_name" id="admin_name"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $admin_data[0]->admin_fullname }}"
                                                    placeholder="Admin Fullname .." required>
                                                <input type="hidden" name="admin_id"
                                                    value="{{ $admin_data[0]->admin_id }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="client_address" class="form-control-label">Contacts</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    value="{{ $admin_data[0]->contacts }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator contacts" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="client_email" class="form-control-label">E-Mail</label>
                                                <input type="text" name="client_email" id="client_email"
                                                    value="{{ $admin_data[0]->email }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator E-Mail" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="admin_username" class="form-control-label">Username <span
                                                        class="text-danger" id="error_acc_no"></span></label>
                                                <input type="text" name="admin_username" id="admin_username"
                                                    value="{{ $admin_data[0]->admin_username }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator Username" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Password</label>
                                                <input type="password" name="admin_password" id="admin_password"
                                                    value="{{ $admin_data[0]->admin_password }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Status</label>
                                                <select class="form-control" name="status" id="status" required>
                                                    <option value="" hidden>Select an option</option>
                                                    <option {{ $admin_data[0]->activated == "0" ? "selected" : ""}} value="0">In-Active</option>
                                                    <option {{$admin_data[0]->activated == "1" ? "selected" : ""}} value="1">Active</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="privileges" id="privileged" value="{{$admin_data[0]->priviledges}}">
                                        <div class="container my-2">
                                            @php
                                                // check json structure
                                                function isJson_report($string) {
                                                    return ((is_string($string) &&
                                                            (is_object(json_decode($string)) ||
                                                            is_array(json_decode($string))))) ? true : false;
                                                }
                                                $privileged = [];
                                                if (isJson_report($admin_data[0]->priviledges)) {
                                                    $privileged = json_decode($admin_data[0]->priviledges);
                                                }
                                                function getChecked($privileged,$name,$option){
                                                    $found = false;
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->option == $name) {
                                                            $found = true;
                                                            if ($option == "view") {
                                                                if ($privileged[$ind]->view) {
                                                                    return "checked";
                                                                }
                                                            }
                                                            if ($option == "readonly") {
                                                                if ($privileged[$ind]->readonly) {
                                                                    return "checked";
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (!$found && $option == "view") {
                                                        return "checked";
                                                    }
                                                    return "";
                                                }
                                                function checkAllView($privileged){
                                                    $counter = 0;
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->view) {
                                                            $counter++;
                                                        }
                                                    }
                                                    if ($counter == count($privileged)) {
                                                        return "checked";
                                                    }else{
                                                        return "";
                                                    }
                                                }
                                                function checkAllReadonly($privileged){
                                                    $counter = 0;
                                                    for ($ind=0; $ind < count($privileged); $ind++) { 
                                                        if ($privileged[$ind]->readonly) {
                                                            $counter++;
                                                        }
                                                    }
                                                    if ($counter == count($privileged)) {
                                                        return "checked";
                                                    }else{
                                                        return "";
                                                    }
                                                }
                                            @endphp
                                            <h6 class="text-center"><u>Assign Administrator Privileges</u></h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Menu</th>
                                                            <th>View <input type="checkbox" {{checkAllView($privileged)}} id="all_view"></th>
                                                            <th>Read-only <input {{checkAllReadonly($privileged)}} type="checkbox" id="all_readonly"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th rowspan="4" scope="row">1</th>
                                                            <td><label for="my_clients_option" class="form-label"><b>Clients</b></label></td>
                                                            <td><input class="" {{getChecked($privileged,"Clients","view")}}  type="checkbox" id="clients_option_view"></td>
                                                            <td><input class="" {{getChecked($privileged,"Clients","readonly")}} type="checkbox" id="clients_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="my_clients_option" class="form-label"><b>My Clients</b></label></td>
                                                            <td><input class="all_view client_options" {{getChecked($privileged,"My Clients","view")}}  type="checkbox" id="my_clients_option_view"></td>
                                                            <td><input class="all_readonly client_options_2" {{getChecked($privileged,"My Clients","readonly")}} type="checkbox" id="my_clients_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="my_clients_option" class="form-label"><b>Quick Register</b></label></td>
                                                            <td><input class="all_view client_options" {{getChecked($privileged,"Quick Register","view")}}  type="checkbox" id="quick_register_view"></td>
                                                            <td><input class="all_readonly client_options_2" {{getChecked($privileged,"Quick Register","readonly")}} type="checkbox" id="quick_register_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="my_clients_option" class="form-label"><b>Clients Issues</b></label></td>
                                                            <td><input class="all_view client_options" {{getChecked($privileged,"Clients Issues","view")}}  type="checkbox" id="clients_issues_view"></td>
                                                            <td><input class="all_readonly client_options_2" {{getChecked($privileged,"Clients Issues","readonly")}} type="checkbox" id="clients_issues_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th rowspan="3" scope="row">2</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>Accounts</b></label></td>
                                                            <td><input class="" type="checkbox" id="accounts_option_view"></td>
                                                            <td><input class="" type="checkbox" id="accounts_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><label for="my_clients_option" class="form-label"><b><i>Transactions</i></b></label></td>
                                                            <td><input class="all_view account_options" {{getChecked($privileged,"Transactions","view")}} type="checkbox" id="transactions_option_view"></td>
                                                            <td><input class="all_readonly account_options_2" {{getChecked($privileged,"Transactions","readonly")}} type="checkbox" id="transactions_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><label for="my_clients_option" class="form-label"><b><i>Expenses</i></b></label></td>
                                                            <td><input class="all_view account_options" {{getChecked($privileged,"Expenses","view")}} type="checkbox" id="expenses_option_view"></td>
                                                            <td><input class="all_readonly account_options_2"  {{getChecked($privileged,"Expenses","readonly")}} type="checkbox" id="expenses_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>My Routers</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"My Routers","view")}} type="checkbox" id="my_routers_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"My Routers","readonly")}} type="checkbox" id="my_routers_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>SMS</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"SMS","view")}} type="checkbox" id="sms_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"SMS","readonly")}} type="checkbox" id="sms_option_readonly"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td ><label for="my_clients_option" class="form-label"><b>Account & Profile</b></label></td>
                                                            <td><input class="all_view" {{getChecked($privileged,"Account and Profile","view")}} type="checkbox" id="account_profile_option_view"></td>
                                                            <td><input class="all_readonly" {{getChecked($privileged,"Account and Profile","readonly")}} type="checkbox" id="account_profile_option_readonly"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    @php
                                                        $btnText = "<i class=\"ft-upload\"></i> Update Administrator";
                                                        $otherClasses = "";
                                                        $btn_id = "update_admin";
                                                        $btnSize="sm";
                                                        $type = "submit";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-primary" {{$readonly}} type="submit"><i
                                                        class="ft-upload"></i> Update Administrator</button> --}}
                                            </div>
                                        </div>
                                        <hr>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end view administrators --}}
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

    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/view_admin.js"></script>
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
