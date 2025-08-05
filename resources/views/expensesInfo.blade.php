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
    <title>Hypbits - Expenses</title>
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
    
    .hide{
        display: none;
    }
</style>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <x-menu active="expenses"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Expenses");
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Expenses</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#"><span
                                    class="menu-title" data-i18n="">Accounts</span></a>
                                </li>
                                <li class="breadcrumb-item active">Expenses
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
                            <div class="modal fade text-left hide" style="background-color: rgba(0, 0, 0, 0.5);" id="delete_expense_window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" style="padding-right: 17px;" aria-modal="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger white">
                                        <h4 class="modal-title white" id="myModalLabel2">Confirm Delete Of {{ucwords(strtolower($expense_data->name))}}.</h4>
                                        <button id="hide_delete_expense" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container">
                                                <p>Are you sure you want to delete <b>"{{$expense_data->name}}"</b> record permanently!</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row w-100">
                                                <div class="col-md-6">
                                                    @php
                                                        $btnText = "<i class=\"fas fa-trash\"></i> Delete";
                                                        $otherClasses = "btn-block";
                                                        $btnLink = "/Expense/DeleteRecords/".$expense_data->id;
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                    {{-- <a href="/Expense/DeleteRecords/{{$expense_data->id}}" class="btn btn-danger">Yes</a> --}}
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
                                <h4 class="card-title">Expenses Table</h4>
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
                                <p>- View, update or delete the expense entry!</p>
                                <div class="row">
                                    <div class="col-md-8">
                                        {{-- @php
                                            $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to List";
                                            $otherClasses = "text-primary";
                                            $btnLink = "/Expenses";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button-link btnType="white" btnSize="sm" toolTip="Expense Statistics" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                        <a href="/Expenses" class="btn btn-infor text-primary"><i class="fas fa-arrow-left"></i> Back to List</a>
                                    </div>
                                    <div class="col-md-4">
                                        @php
                                            $btnText = "<i class=\"fas fa-trash\"></i> Delete";
                                            $otherClasses = "";
                                            $btn_id = "delete_expense";
                                            $btnSize="sm";
                                            $type = "button";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button toolTip="" btnType="danger" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        {{-- <button class="btn btn-danger" {{$readonly}} id="delete_expense"><i class="fas-fa-trash"></i> Delete</button> --}}
                                    </div>
                                </div>
                            </div>
                            <hr class="p-0 m-0">
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <p class="card-text">In this table below Expenses information can be
                                        displayed.</p>
                                        @if (session('expense_success'))
                                            <p class="text-success">{{ session('expense_success') }}</p>
                                        @endif
                                        @if (session('expense_error'))
                                            <p class="text-danger">{{ session('expense_error') }}</p>
                                        @endif
                                    <p>
                                    <form class="row" method="POST" action="/Expense/Update">
                                        @csrf
                                        <div class="col-md-4">
                                            <label for="expense_name" class="form-label">Expense Name</label>
                                            <input type="hidden" name="expense_id" value="{{$expense_data->id}}">
                                            <input type="text" name="expense_name" id="expense_name" value="{{$expense_data->name}}" class="form-control" placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_category" class="form-label">Expense Category <b data-toggle="tooltip" title="This is the category that this expense will lie, example can be daily-expense or labour." class="text-dark"><i class="ft-info"></i></b></label>
                                            <select name="expense_category" id="expense_category" class="form-control" required>
                                                <option value="" hidden>Select option</option>
                                                @if (count($exp_category) > 0)
                                                    @for ($i = 0; $i < count($exp_category); $i++)
                                                        <option {{$expense_data->category == $exp_category[$i]->name ? "selected" : ""}} value="{{$exp_category[$i]->name}}">{{$exp_category[$i]->name}}</option>
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="expense_date" class="form-label">Date <b data-toggle="tooltip" title="This is the date you incurred the cost, by default it takes today." class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="date" name="expense_date" value="{{date("Y-m-d",strtotime($expense_data->date_recorded))}}" max="{{date("Y-m-d")}}" id="expense_date" class="form-control" placeholder="Expense Name">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_quantity" class="form-label">Quantity <b data-toggle="tooltip" title="This is the number of commodities bought. e.g.,10 - (Routers)" class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="number" name="expense_quantity" value="{{$expense_data->unit_amount}}" id="expense_quantity" step="0.5" value="0" class="form-control" placeholder="Example: 10 (Mikrotik Routers)">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit_price" class="form-label">Unit Price <b data-toggle="tooltip" title="This is the price of one commodity bought. e.g.,One router costs Kes 1000" class="text-dark"><i class="ft-info"></i></b></label>
                                            <input type="number" name="expense_unit_price" value="{{$expense_data->unit_price}}" step="0.005" id="expense_unit_price" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_total_price" class="form-label">Total Price <b data-toggle="tooltip" title="This is the calculated total price of all the commodities bought. e.g.,10 - (Routers) cost Kes 10,000: This field is readonly!" class="text-dark"><i class="ft-info"></i></b> <span class="text-danger">{Read-only!}</span></label>
                                            <input type="number" readonly name="expense_total_price" value="{{$expense_data->total_price}}" id="expense_total_price" class="form-control" placeholder="Total Price" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="expense_unit" class="form-label">Unit Of Measurement <b data-toggle="tooltip" title="This is the unit of measurement, If you inccured a cost of a commodity that has weight and its charged per certain weight you would say (10 Kgs of Sugar) Kgs being the unit of measurement." class="text-dark"><i class="ft-info"></i></b> </label>
                                            <input type="text" name="expense_unit" value="{{$expense_data->unit_of_measure}}" id="expense_unit" class="form-control" placeholder="Kgs, Litres">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="expense_description" class="form-label">Description</label>
                                            <textarea name="expense_description" id="expense_description" cols="30" value="" rows="5" class="form-control" placeholder="Describe Expense here..">{{$expense_data->description}}</textarea>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            @php
                                                $btnText = "Update Expense";
                                                $otherClasses = "";
                                                $btn_id = "";
                                                $btnSize="sm";
                                                $type = "submit";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button class="btn btn-block btn-primary" {{$readonly}} type="submit">Update Expense</button> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    {{-- <x-footerRouteAdmin > --}}
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->

    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    {{--  --}}
    <!-- END PAGE LEVEL JS-->


    <script src="/theme-assets/js/core/expenseview.js"></script>

    {{-- script to create tables in the transaction table --}}
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
