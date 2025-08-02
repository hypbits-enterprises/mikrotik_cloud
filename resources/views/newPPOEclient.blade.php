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
    <title>Hypbits - New Clients PPPoE Assignment</title>
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
    
    <!-- fixed-top-->
    <x-menu active="myclients"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Quick Register");
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
                                <li class="breadcrumb-item">PPPoE Assignment
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
                                <h4 class="card-title">Client PPPoE Assignment</h4>
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
                                @php
                                    $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                    $otherClasses = "ml-1";
                                    $btnLink = "/Clients";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link btnType="infor" btnSize="sm" toolTip="Transaction`s Statistics" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                {{-- <a href="/Clients" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                    to list</a> --}}
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
                                    <p class="card-text">Fill all the fields to add the client.</p>
                                    <form action="{{route("clients.addppoe")}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <input type="checkbox" name="allow_router_changes"
                                                    id="allow_router_changes" checked>
                                                <label for="allow_router_changes"
                                                    class="form-control-label text-primary"
                                                    style="font-weight: 800;cursor: pointer;">Apply changes to
                                                    router</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="checkbox" name="send_sms" id="send_sms" checked>
                                                <label for="send_sms" class="form-control-label text-primary"
                                                    style="font-weight: 800;cursor: pointer;">Send Welcome SMS</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 form-group">
                                                <label for="client_name" class="form-control-label">Clients
                                                    Fullname</label>
                                                <input type="text" name="client_name" id="client_name"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Clients Fullname .." required
                                                    value="{{ session('client_name') ? session('client_name') : '' }}">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="client_address" class="form-control-label">Clients
                                                    Address</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="eg. Kiambu or Mombasa" required
                                                    value="{{ session('client_address') ? session('client_address') : '' }}">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="location_coordinates" class="form-control-label">Location
                                                    Co-ordinates</label>
                                                <input type="text" name="location_coordinates" id="location_coordinates"
                                                    class="form-control rounded-lg p-1"
                                                    onkeypress="return isNumber(event)"
                                                    placeholder="Location Co-ordinates"
                                                    value="{{ session('location_coordinates') ? session('location_coordinates') : '' }}"
                                                    onpaste="return pasted(event,'location_coordinates');">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 form-group">
                                                <label for="client_phone" class="form-control-label">Clients Phone
                                                    number</label>
                                                <input type="number" name="client_phone" id="client_phone"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client valid phone number" required
                                                    value="{{ session('client_phone') ? session('client_phone') : '' }}">
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="client_acc_number" class="form-control-label">Clients
                                                    Account Number {<span
                                                        class="primary">{{ $client_accounts[0] ?? '' }}</span>}
                                                    <span class="text-danger"
                                                        id="error_acc_no">{{ session('account_number_present') ? 'Account number in use!' : '' }}</span></label>
                                                <input type="text" name="client_acc_number" id="client_acc_number"
                                                    class="form-control rounded-lg p-1 {{ session('account_number_present') ? 'border border-danger' : '' }}"
                                                    placeholder="Client account no ex HYP001" required
                                                    value="{{ session('client_acc_number') ? session('client_acc_number') : '' }}">
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="client_monthly_pay" class="form-control-label">Clients
                                                    Monthly Payment</label>
                                                <input type="number" name="client_monthly_pay" id="client_monthly_pay"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client Monthly Payment" required
                                                    value="{{ session('client_monthly_pay') ? session('client_monthly_pay') : '' }}">
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="minimum_payment" class="form-control-label">Client`s Minimum Payment</label>
                                                <select name="minimum_payment" id="minimum_payment" class="form-control" required>
                                                    <option hidden>Select Minimum Payment </option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '25' ? 'selected' : '') : ''}} value="25">25%</option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '50' ? 'selected' : '') : ''}} value="50">50%</option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '75' ? 'selected' : '') : ''}} value="75">75%</option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '80' ? 'selected' : '') : ''}} value="80">80%</option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '90' ? 'selected' : '') : ''}} value="90">90%</option>
                                                    <option {{session('minimum_payment') ? (session('minimum_payment') == '100' ? 'selected' : '') : 'selected'}} value="100">Full Payment</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 form-group">
                                                <label for="client_secret_username" id="errorMsg" class="form-control-label">Clients Secret
                                                    Username</label>
                                                <input type="text" name="client_secret_username" id="client_secret_username"
                                                    class="form-control rounded-lg p-1" placeholder="ex esmond"
                                                    required readonly
                                                    value="{{ session('client_secret_username') ? session('client_secret_username') : '' }}">
                                            </div>
                                            <div class="col-lg-4">
                                                @php
                                                    $password = rand(100000,999999);
                                                @endphp
                                                <label for="client_secret_password"  id="errorMsg1" class="form-control-label">Clients Secret
                                                    Password <span class="text-primary">{ {{$password}} }</span></label>
                                                <input type="password" name="client_secret_password" id="client_secret_password"
                                                    class="form-control rounded-lg p-1" placeholder="Secret Password"
                                                    required
                                                    value="{{ $password }}">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="repeat_secret_password"  id="errorMsg1" class="form-control-label">Repeat Secret
                                                    Password</label>
                                                <input type="password" name="repeat_secret_password" id="repeat_secret_password"
                                                    class="form-control rounded-lg p-1" placeholder="Secret Password"
                                                    required
                                                    value="{{ $password }}">
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-lg-4 form-group">
                                                <label for="expiration_date" class="form-control-label">Expiration
                                                    Date</label>
                                                <input type="date" name="expiration_date" id="expiration_date"
                                                    class="form-control" placeholder="Customer Expiration Date"
                                                    value="{{ session('expiration_date') ? session('expiration_date') : '' }}">
                                            </div>
                                            <div class="col-lg-4 form-group">
                                                <label for="router_name" class="form-control-label">Router Name
                                                    {{ session('router_name') ? '{' . session('router_name') . '}' : '' }}<span
                                                        class="invisible" id="interface_load"><i
                                                            class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                <p id="router_data"><span class="text-secondary">The router list will
                                                        appear here.. If this message is still present you have no
                                                        routers present in your database.</span></p>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="pppoe_profile" class="form-control-label">PPPoE Profile
                                                    {{ session('interface_name') ? '{' . session('interface_name') . '}' : '' }}
                                                    :</label>
                                                <p class="text-secondary" id="interface_holder">The PPPoE Profiles
                                                    will appear here If the router is selected.If this message is still
                                                    present the router is not selected.</p>
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-lg-12">
                                                <label for="comments"
                                                    class="form-control-label">Comments:</label>
                                                <textarea name="comments" id="comments" cols="30" rows="3" class="form-control"
                                                    placeholder="Comment here">{{ session('comments') ? session('comments') : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row my-1 d-none">
                                            <div class="col-lg-6">
                                                <label for="client_username" class="form-control-label">Client Username
                                                    <span class="text-danger"
                                                        id="err_username">{{ session('client_username_present') ? 'Username provided is present' : '' }}</span>
                                                </label>
                                                <input type="text" name="client_username" id="client_username"
                                                    class="form-control {{ session('client_username_present') ? 'border border-danger' : '' }}"
                                                    value="{{ session('client_username') ? session('client_username') : '' }}"
                                                    required placeholder="Client`s Username">
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="client_password" class="form-control-label">Client`s
                                                    Password</label>
                                                <input type="password" name="client_password" id="client_password"
                                                    class="form-control"
                                                    value="{{ session('client_password') ? session('client_password') : '' }}"
                                                    required placeholder="Client`s Password">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                @php
                                                    $btnText = "<i class=\"ft-plus\"></i> Add User";
                                                    $otherClasses = "text-dark";
                                                    $btn_id = "";
                                                    $btnSize="sm";
                                                    $type = "submit";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="success" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-success text-dark {{$readonly}}" type="submit"><i
                                                        class="ft-plus"></i> Add User </button> --}}
                                            </div>
                                            <div class="col-lg-6">
                                                @php
                                                    $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                    $otherClasses = "";
                                                    $btnLink = "/Clients";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button-link btnType="secondary" btnSize="sm" toolTip="Transaction`s Statistics" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                {{-- <a class="btn btn-secondary btn-outline" href="/Clients"><i
                                                        class="ft-x"></i> Cancel</a> --}}
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


    {{-- START OF THE ROUTER DATA RETRIEVAL --}}
    <script>
        var router_data = @json($router_data ?? '');
        var client_accounts = @json($client_accounts ?? '');
        var client_username = @json($client_username ?? '');
        var data_to_display =
            "<select name='router_name' class='form-control' id='router_name' required ><option value='' hidden>Select an option</option>";
        for (let index = 0; index < router_data.length; index++) {
            const element = router_data[index];
            data_to_display += "<option value='" + element['router_id'] + "'>" + element['router_name'] + " (" + element[
                'router_id'] + ")" + "</option>";
        }
        data_to_display += "</select>";
        var router_data = document.getElementById("router_data");
        router_data.innerHTML = data_to_display;

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
    </script>
    <script>
        // check if the field is pasted
        function pasted(e, id) {
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
                        data_accept += pastedData.charAt(index);
                    }
                } else {
                    data_accept += pastedData.charAt(index);
                }
            }
            document.getElementById("location_coordinates").value = data_accept;
        }
    </script>

    <script src="/theme-assets/js/core/newclientpppoe.js" type="text/javascript"></script>
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
