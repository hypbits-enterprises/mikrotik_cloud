<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
@php
    
date_default_timezone_set('Africa/Nairobi');
@endphp
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Payment</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- BEGIN VENDOR CSS-->
    
    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
    <style>
        .dt-search {
            display: none;
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
        .hide:{
            display: none;
        }

    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
    <x-client-menu active="transactions"></x-client-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">My Transactions</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/ClientDashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">My Transactions
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">

                {{-- Initiate Payement --}}
                <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="initiate_payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel110" style="padding-right: 17px;" aria-modal="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info white">
                            <h4 class="modal-title white" id="myModalLabel110">Initiate Payment to "{{ucwords(strtolower($client_data->client_name))}}".</h4>
                            {{-- <input type="hidden" id="delete_columns_ids"> --}}
                            <button id="close_initiate_payment_modal_1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <form action="/update_client_comment" method="post" class="form-control-group">
                                        @csrf
                                        <h6 class="text-center">Update Comment</h6>
                                        <p><b>Note</b> This will only work if we have done M-Pesa Integration</p>
                                        <div class="form-group">
                                            <label for="client_amount" class="form-control-label">Amount to Pay</label>
                                            <input type="number" name="client_amount" id="client_amount" placeholder="Client Amount" class="form-control" value="{{$client_data->monthly_payment}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="client_phone_number" class="form-control-label">Phone number to pay</label>
                                            <input type="text" class="form-control" id="client_phone_number" placeholder="Phone number to pay" value="{{$client_data->clients_contacts}}" name="client_phone_number" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="clients_account_number" class="form-control-label">Account Number</label>
                                            <input type="text" class="form-control" id="clients_account_number" placeholder="Phone number to pay" value="{{$client_data->client_account}}" name="clients_account_number" required>
                                        </div>
                                        <input type="hidden" name="clients_id" value="{{ $client_data->client_id }}">
                                        <p id="error_mpesa_holder"></p>
                                        <div class="row w-100">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<span id='initiate_process_holder'><i class=\"fas fa-money-bill\"></i> Initiate</span>";
                                                    $otherClasses = "w-100 my-1";
                                                    $readonly = "";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="initiate_client_payment_mpesa" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                    $otherClasses = "w-100 my-1";
                                                @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_initiate_payment_modal_2" :readOnly="$readonly" />
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
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Transaction Table</h4>
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
                                    <p>CLick the button below to for STK push and pay for your subscription.</p>
                                    <p>@if(session('error_stk'))
                                        <span class="text-danger text-bolder">{{session('error_stk')}}</span>
                                    @endif</p>
                                    <p>@if(session('success_stk'))
                                        <span class="text-success text-bolder">{{session('success_stk')}}</span>
                                    @endif</p>
                                    {{-- <a href="/Payment/stkpush_init" class="btn btn-primary">STK Push</a> --}}
                                    
                                    @php
                                        $btnText = "<i class=\"ft-cash\"></i> STK Push";
                                        $otherClasses = "";
                                        $btn_id = "initiate_payment";
                                    @endphp
                                    <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                    
                                    {{-- @php
                                        $btnText = "STK Push";
                                        $otherClasses = "";
                                        $btnLink = "/Payment/stkpush_init";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" /> --}}
                                    
                                    <p class="card-text">In this table below all payment you have done with us will appear here.</p>
                                    <p><span class="text-bold-600">Transaction Table:</span></p>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="search" id="searchkey"
                                                class="form-control rounded-lg p-1" placeholder="Search here ..">
                                        </div>
                                        <div class="col-md-6">
                                            @if(session('success'))
                                                <p class='text-success'>{{session('success')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <table class="table" @if (count($transData) > 0) 
                                                                id="transaction_table"
                                                            @endif>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Transaction ID</th>
                                                    <th>Account Number</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($transData) > 0)
                                                    @foreach ($transData as $index => $trans)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>{{ $trans->transaction_mpesa_id }}</td>
                                                            <td>{{ $trans->transaction_account }}</td>
                                                            <td>Kes {{ number_format($trans->transacion_amount, 2) }}</td>
                                                            <td>{{ date('dS M Y', strtotime($trans->transaction_date)) }} at {{ date('h:i:sa', strtotime($trans->transaction_date)) }}</td>
                                                            <td>
                                                                @php
                                                                    $btnText = "<i class='ft-eye'></i> View";
                                                                    $otherClasses = "";
                                                                    $btnLink = "/Payment/View/{$trans->transaction_id}";
                                                                    $otherAttributes = "";
                                                                @endphp
                                                                <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center">No transactions found.</td>
                                                    </tr>
                                                @endif
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
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script>
        if(document.getElementById('transDataReciever') != null){
            var table = $("#transaction_table").DataTable({
                order: [[0, "desc"]],
                dom: 'lrtip' // removes default search box + length dropdown
            });
            $('#searchkey').on('keyup', function () {
                table.search(this.value).draw();
            });
        }
    
        function cObj(modal_id) {
            return document.getElementById(modal_id);
        }    
    
        function showModal(modal_id) {
            cObj(modal_id).classList.remove("hide");
            cObj(modal_id).classList.add("show");
            cObj(modal_id).classList.add("showBlock");
        }

        function hideModal(modal_id) {
            cObj(modal_id).classList.add("hide");
            cObj(modal_id).classList.remove("show");
            cObj(modal_id).classList.remove("showBlock");
        }
        
        cObj("initiate_payment").onclick = function() {
            showModal("initiate_payment_modal");
        }

        cObj("close_initiate_payment_modal_1").onclick = function() {
            hideModal("initiate_payment_modal");
        }

        cObj("close_initiate_payment_modal_2").onclick = function() {
            hideModal("initiate_payment_modal");
        }

        cObj("initiate_client_payment_mpesa").onclick = function () {
            var err = checkBlank("client_amount");
            err += checkBlank("client_phone_number");
            err += checkBlank("clients_account_number");
            if (err == 0) {
                cObj("initiate_client_payment_mpesa").disabled = true;
                cObj("initiate_client_payment_mpesa").classList.add("disabled");
                cObj("error_mpesa_holder").innerHTML = "";
                cObj("initiate_process_holder").innerHTML = "<span id='initiate_loader' class='invisible'></span> <i class='fas fa-refresh fa-spin'></i> Please wait...";
                sendDataPost1("POST", "/Payment/stkpush", "amount="+cObj("client_amount").value+"&phone_number="+cObj("client_phone_number").value+"&account_number="+cObj("clients_account_number").value, cObj("error_mpesa_holder"), cObj("initiate_loader"), function (){
                    cObj("initiate_client_payment_mpesa").disabled = false;
                    cObj("initiate_client_payment_mpesa").classList.remove("disabled");
                    cObj("initiate_process_holder").innerHTML = "<i class=\"fas fa-money-bill\"></i> Initiate";
                });
            }else{
                cObj("error_mpesa_holder").innerHTML = "<p class='text-danger'>Please fill all fields covered with a red border!</p>";
            }
        }

        function checkBlank(object_id) {
            if (cObj(object_id).value.trim().length > 0) {
                cObj(object_id).classList.remove("border");
                cObj(object_id).classList.remove("border-danger");
                cObj(object_id).classList.add("border-secondary");
                return 0;
            }else{
                cObj(object_id).classList.remove("border");
                cObj(object_id).classList.remove("border-secondary");
                cObj(object_id).classList.add("border-danger");
                return 1;
            }
        }

        // Send date with post request
        function sendDataPost1(method, file, datapassing, object1, object2, callback = null) {
            //make the loading window show
            object2.classList.remove("invisible");
            let xml = new XMLHttpRequest();
            xml.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    object1.innerHTML = this.responseText;
                    object2.classList.add("invisible");

                    // ✅ Run the callback after updating DOM
                    if (typeof callback === "function") {
                        callback();
                    }
                } else if (this.status == 500) {
                    object2.classList.add("invisible");
                    object1.innerHTML = "<p class='red_notice'>Cannot establish connection to server.<br>Try reloading your page</p>";
                } else if (this.status == 204) {
                    object2.classList.add("invisible");
                    object1.innerHTML = "<p class='red_notice'>Password updated successfully!</p>";
                }
                // console.log(this.status);
            };
            xml.open(method, "" + file, true);
            xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xml.send(datapassing);
        }
    </script>
    <!-- END PAGE LEVEL JS-->
</body>

</html>