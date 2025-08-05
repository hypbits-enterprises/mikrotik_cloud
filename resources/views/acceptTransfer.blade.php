<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Transfer funds</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
    <style>
        html{
            scroll-behavior: smooth;
        }
        .hide{
            display: none;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="transactions"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Transactions");
    @endphp
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Confirm Bill Transfer</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Transactions">My Transaction</a>
                                </li>
                                <li class="breadcrumb-item">Confirm Bill Transfer
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
                                <h4 class="card-title">Confirm Bill Transfer</h4>
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
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back";
                                        $otherClasses = "";
                                        $btnLink = "/Transactions/View/".$transaction_id;
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/Transactions/View/{{$transaction_id}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back</a>
                                    <p><strong>Note</strong><br>- Confirm the data below is correct before confirming the transfer!</p>
                                </div>
                                <div class="card-body row">
                                    <div class="col-lg-6 row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="transaction_id" value="{{$transaction_details[0]->transaction_id}}">
                                            <h6 class="text-primary"><strong><u>Transaction Detail</u></strong></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Code: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->transaction_mpesa_id}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Amount: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Kes {{$transaction_details[0]->transacion_amount}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Date: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_date}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Account: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->transaction_account}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>MSISDN: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->phone_transacting}}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="client_id" value="{{$client_data[0]->client_id}}">
                                            <h6 class="text-primary"><strong><u>Clients Detail</u></strong></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client Name: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$client_data[0]->client_name}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Wallet Balance: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Kes {{$client_data[0]->wallet_amount}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client`s Monthly payment: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Kes {{$client_data[0]->monthly_payment}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client Account No: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$client_data[0]->client_account}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client`s Next Expiration Date: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$expiration_date}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "Confirm Transfer";
                                            $otherClasses = "";
                                            $btnLink = "/confirmTransfer/".$client_data[0]->client_id."/".$transaction_details[0]->transaction_id;
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="primary" btnSize="sm" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/confirmTransfer/{{$client_data[0]->client_id}}/{{$transaction_details[0]->transaction_id}}" class="btn btn-primary {{$readonly}}">Confirm Transfer</a> --}}
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "Cancel";
                                            $otherClasses = "";
                                            $btnLink = "/Transactions/View/".$transaction_id;
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="danger" btnSize="sm" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                        {{-- <a href="/Transactions/View/{{$transaction_id}}" class="btn btn-danger">Cancel</a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer style="margin-bottom: 0% !important" class="footer footer-static footer-light navbar-border navbar-shadow">
    <div  class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span class="float-md-left d-block d-md-inline-block"><?php echo date("Y"); ?> &copy; Copyright Hypbits Enterprises</span>
        <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
            <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com" target="_blank"> Ladybird Softech Co.</a></li>
        </ul>
    </div>
</footer>
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>

    {{-- transfer the php value to js --}}
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