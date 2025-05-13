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
    <title>Hypbits - New Clients Static Assignment</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>

    
</head>
<style>
    .showBlock{
      display: block;
    }
    .hide{
        display: none;
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


<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">
    
    <!-- fixed-top-->
    <x-menu active="client_issues"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Clients Issues");
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Client Issues</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/Client-Reports">Client Issues</a></li>
                                <li class="breadcrumb-item">Record Report</li>
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
                                <h4 class="card-title">View "{{$report_details->client_name}}`s" Report</h4>
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
                                <a href="/Client-Reports" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                    to list</a>
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
                                    <div class="container">
                                        <div class="modal fade text-left" id="change_issue_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11_2" style="padding-right: 17px;" aria-modal="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success white">
                                                    <h4 class="modal-title white" id="myModalLabel11_2">Change Status</h4>
                                                    <input type="hidden" id="delete_columns_ids_2">
                                                    <button id="hide_delete_issue_2" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container">
                                                            <form action="{{route("changeReportStatus")}}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="report_id" value="{{$report_details->report_id}}">
                                                                <div class="form-group">
                                                                    <label for="report_status" class="form-control-label"><b>Report Status</b></label>
                                                                    <select name="report_status" id="report_status" class="form-control" required>
                                                                        <option hidden>Select Report Status</option>
                                                                        <option {{session('report_status') ? (session('report_status') == 'pending' ? 'selected' : '') : ($report_details->status == "pending" ? 'selected' : '')}} value="pending">Pending</option>
                                                                        <option {{session('report_status') ? (session('report_status') == 'cleared' ? 'selected' : '') : ($report_details->status == "cleared" ? 'selected' : '')}} value="cleared">Resolved</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group" id="hide_the_technician_field">
                                                                    <label for="admin_attender" class="form-control-label"><b>Resolved By(Technician)</b></label>
                                                                    <div class="autocomplete">
                                                                        <input type="text" name="admin_attender" id="admin_attender"
                                                                            class="form-control rounded-lg p-1"
                                                                            placeholder="Type your technician name..."
                                                                            value="{{ session('admin_attender') ? session('admin_attender') : $report_details->admin_attender}}">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="form-group col-md-6">
                                                                        <label for="resolve_date" class="form-control-label"><b>Resolved Date</b></label>
                                                                        <div class="autocomplete">
                                                                            <input type="date" name="resolve_date" id="resolve_date"
                                                                                class="form-control rounded-lg p-1"
                                                                                placeholder="Resolved By" required
                                                                                value="{{ session('resolve_date') ? session('resolve_date') : ($report_details->resolve_time != null ? date("Y-m-d", strtotime($report_details->resolve_time)) : date("Y-m-d"))}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group col-md-6">
                                                                        <label for="resolve_time" class="form-control-label"><b>Resolved Time</b></label>
                                                                        <div class="autocomplete">
                                                                            <input type="time" name="resolve_time" id="resolve_time"
                                                                                class="form-control rounded-lg p-1"
                                                                                placeholder="Resolved By" required
                                                                                value="{{ session('resolve_time') ? session('resolve_time') : ($report_details->resolve_time != null ? date("H:i", strtotime($report_details->resolve_time)) : date("H:i"))}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 mb-1">
                                                                        <label for="client_address"
                                                                            class="form-control-label"><b>Resolution:</b></label>
                                                                        <textarea name="solution" id="solution" cols="30" rows="3" class="form-control"
                                                                            placeholder="e.g., The client`s tenda router was faulty">{{ session('solution') ? session('solution') : $report_details->solution }}</textarea>
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
                                    <form action="{{route("updateReports")}}" method="post">
                                        @csrf
                                        <input type="hidden" name="report_id" value="{{$report_details->report_id}}">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="my-2">
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Ticket Number:</b></td>
                                                        <td class="px-1">{{$report_details->report_code}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Reported By(Client) :</b></td>
                                                        <td class="px-1">{{$report_details->client_name}} - ({{$report_details->client_account}})</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Date Reported :</b></td>
                                                        <td class="px-1">{{date("D dS M Y H:iA", strtotime($report_details->report_date))}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Date Resolved :</b></td>
                                                        <td class="px-1">{{$report_details->resolve_time ? date("D dS M Y H:iA", strtotime($report_details->resolve_time)) : "Not Resolved Yet"}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Opened By (Admin):</b></td>
                                                        <td class="px-1">{{$report_details->admin_reporter_fullname ?? "Invalid Administrator"}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Resolved By (Technician):</b></td>
                                                        <td class="px-1">{{$report_details->status == "pending" ? "Still pending!" : ($report_details->admin_attender ?? "Resolved but no technician set!")}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Closed By (Admin):</b></td>
                                                        <td class="px-1">{{$report_details->status == "pending" ? "Still pending!" : ($report_details->closed_by ?? "Resolved but at the time of closing no Admin was set!")}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-1 text-right"><b>Status:</b></td>
                                                        <td class="px-1">
                                                            @if ($report_details->status == "pending")
                                                                <span class="badge text-light bg-danger text-dark" data-toggle="tooltip" title="" data-original-title="Pending!">Pending</span>
                                                            @else
                                                                <span class="badge text-light bg-success text-dark" data-toggle="tooltip" title="" data-original-title="Resolved!">Resolved</span>
                                                            @endif
                                                            <button class="btn btn-sm btn-outline-success" {{$readonly}} id="change_status" type="button"><i class="ft-refresh"></i> Change Status</button>
                                                            {{-- <a href="/Client-Reports/View/Change-Status/{{$report_details->report_id}}" data-toggle="tooltip" title="" data-original-title="Click me to change status!" class="btn btn-sm btn-outline-success">Change Status</a> --}}
                                                        </td>
                                                    </tr>
                                                </table>
                                                {{-- <ul>
                                                    <li><b>Reported By(Client):</b> <u>{{$report_details->client_name}} - ({{$report_details->client_account}})</u></li>
                                                    <li><b>Date Reported:</b> {{date("D dS M Y H:i:sA", strtotime($report_details->report_date))}}</li>
                                                    <li><b>Recorded By:</b> {{$report_details->admin_reporter_fullname ?? "Invalid Administrator"}}</li>
                                                    <li><b>Attended By:</b> {{$report_details->admin_attender_fullname ?? "Not yet!"}}</li>
                                                </ul> --}}
                                            </div>
                                            <div class="col-md-12">
                                                <p class="card-text">Fill all the fields to add a new client report.</p>
                                            </div>
                                            <div class="col-md-4 form-group mt-1">
                                                <label for="report_title" class="form-control-label"><b>Report Title</b></label>
                                                <div class="autocomplete">
                                                    <input type="text" name="report_title" id="report_title"
                                                        class="form-control rounded-lg p-1"
                                                        placeholder="e.g., Unstable Internet" required
                                                        value="{{ session('report_title') ? session('report_title') : $report_details->report_title }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-1">
                                                <label for="client_account" class="form-control-label"><b>Reported By({{$report_details->client_name}}):</b></label>
                                                <div class="autocomplete">
                                                    <input type="text" name="client_account" id="client_account"
                                                        class="form-control rounded-lg p-1"
                                                        placeholder="Reported By..." required
                                                        value="{{ session('client_account') ? session('client_account') : $report_details->client_account }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-1">
                                                <label for="report_date" class="form-control-label"><b>Report Date</b></label>
                                                <div class="autocomplete">
                                                    <input type="date" name="report_date" id="report_date"
                                                        class="form-control rounded-lg p-1"
                                                        placeholder="Resolved By" required
                                                        value="{{ session('report_date') ? date("Y-m-d", strtotime(session('report_date'))) : date("Y-m-d", strtotime($report_details->report_date))}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <label for="client_address"
                                                    class="form-control-label"><b>Problem:</b></label>
                                                <textarea name="problem" id="problem" cols="30" rows="3" class="form-control"
                                                    placeholder="e.g., The client`s tenda router was faulty">{{ session('problem') ? session('problem') : $report_details->problem }}</textarea>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <label for="client_address"
                                                    class="form-control-label"><b>Diagnosis:</b></label>
                                                <textarea name="diagnosis" id="diagnosis" cols="30" rows="3" class="form-control"
                                                    placeholder="e.g., The client`s tenda router was faulty">{{ session('diagnosis') ? session('diagnosis') : $report_details->diagnosis }}</textarea>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <label for="client_address"
                                                    class="form-control-label"><b>Comment:</b></label>
                                                <textarea name="comment" id="comment" cols="30" rows="3" class="form-control"
                                                    placeholder="e.g., The client`s tenda router was faulty">{{ session('comment') ? session('comment') : $report_details->report_description }}</textarea>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <label for="client_address"
                                                    class="form-control-label"><b>Resolution:</b></label>
                                                <textarea name="solution" disabled id="solution" cols="30" rows="3" class="form-control"
                                                    placeholder="e.g., The client`s tenda router was faulty">{{ session('solution') ? session('solution') : $report_details->solution }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <button class="btn btn-success text-dark" {{$readonly}} type="submit"><i class="ft-upload"></i> Update Report</button>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-outline-purple" {{$readonly}} id="DeleteTable"><i class="ft-trash"></i> Delete</button>
                                                <div class="container">
                                                    <div class="modal fade text-left" id="delete_client_report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger white">
                                                                <h4 class="modal-title white" id="myModalLabel11">Confirm Delete.</h4>
                                                                <input type="hidden" id="delete_columns_ids">
                                                                <button id="hide_delete_column" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="container">
                                                                        <p class="text-dark"><b>Are you sure you want to delete "{{$report_details->client_name}}" issue?</b></p>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" id="close_this_window_delete" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                                                    <a href="/Client-Reports/Delete-Report/{{$report_details->report_id}}" class="btn btn-sm btn-danger my-1 "><i class="ft-trash"></i> Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <a class="btn btn-secondary btn-outline" href="/Client-Reports"><i
                                                        class="ft-x"></i> Cancel</a>
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
    
    <script>
        function cObj(id) {
            return document.getElementById(id);
        }

        // delete table
        cObj("DeleteTable").onclick = function () {
            cObj("delete_client_report").classList.remove("hide");
            cObj("delete_client_report").classList.add("show");
            cObj("delete_client_report").classList.add("showBlock");
        }

        cObj("close_this_window_delete").onclick = function () {
            cObj("delete_client_report").classList.add("hide");
            cObj("delete_client_report").classList.remove("show");
            cObj("delete_client_report").classList.remove("showBlock");
        }

        cObj("hide_delete_column").onclick = function () {
            cObj("delete_client_report").classList.add("hide");
            cObj("delete_client_report").classList.remove("show");
            cObj("delete_client_report").classList.remove("showBlock");
        }

        //============================CHANGE CLIENT STATUS===================================
        cObj("change_status").onclick = function () {
            cObj("change_issue_status").classList.remove("hide");
            cObj("change_issue_status").classList.add("show");
            cObj("change_issue_status").classList.add("showBlock");
        }

        if (cObj("report_status").value == "pending") {
            cObj("hide_the_technician_field").classList.add("d-none");
            cObj("admin_attender").disabled = true;
            cObj("admin_attender").disabled = true;
            cObj("admin_attender").value = "";
        }else{
            cObj("admin_attender").disabled = false;
            cObj("hide_the_technician_field").classList.remove("d-none");
            cObj("admin_attender").disabled = false;
        }

        cObj("hide_delete_issue_2").onclick = function () {
            cObj("change_issue_status").classList.add("hide");
            cObj("change_issue_status").classList.remove("show");
            cObj("change_issue_status").classList.remove("showBlock");
        }

        cObj("close_update_status_window").onclick = function () {
            cObj("change_issue_status").classList.add("hide");
            cObj("change_issue_status").classList.remove("show");
            cObj("change_issue_status").classList.remove("showBlock");
        }
    </script>

    {{-- START OF THE ROUTER DATA RETRIEVAL --}}
    <script>
        var old_title_reports = @json($old_title_reports ?? '');
        var my_clients = @json($my_clients ?? '');
        var admin_tables = @json($admin_tables ?? '');


        // client data
        var client_names = [];
        var client_contacts = [];
        var client_account = [];
        for (let index = 0; index < my_clients.length; index++) {
            const element = my_clients[index];
            client_names.push(element['client_name']);
            client_contacts.push(element['clients_contacts']);
            client_account.push(element['client_account']);
        }
        autocomplete(document.getElementById("client_account"), client_contacts, client_account, client_names);

        var report_titles = [];
        for (let index = 0; index < old_title_reports.length; index++) {
            const element = old_title_reports[index];
            report_titles.push(element['report_title']);
        }
        autocomplete1(document.getElementById("report_title"), report_titles);

        // admin_attender
        var attendees = [];
        for (let index = 0; index < admin_tables.length; index++) {
            const element = admin_tables[index];
            attendees.push(element['admin_fullname']);
        }
        autocomplete1(document.getElementById("admin_attender"), attendees);


        function autocomplete(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value.split(",")[this.value.split(",").length - 1];
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
                            var input_value = inp.value;
                            var final_str = "";
                            if (input_value.length > 1) {
                                input_value = input_value.split(",");
                                for (let index = 0; index < input_value.length; index++) {
                                    const element = input_value[index];
                                    if (index+2 > input_value.length) {
                                        continue;
                                    }
                                    if (element.trim().length > 0) {
                                        final_str+=element+",";
                                    }
                                }
                            }
                            inp.value = final_str+this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                        counter++;
                    }
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

        function autocomplete1(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value.split(",")[this.value.split(",").length - 1];
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list-1");
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
                    if (arr[i].toUpperCase().includes(val.toUpperCase())
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr[i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            var input_value = inp.value;
                            var final_str = "";
                            if (input_value.length > 1) {
                                input_value = input_value.split(",");
                                for (let index = 0; index < input_value.length; index++) {
                                    const element = input_value[index];
                                    if (index+2 > input_value.length) {
                                        continue;
                                    }
                                    if (element.trim().length > 0) {
                                        final_str+=element+",";
                                    }
                                }
                            }
                            inp.value = final_str+this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list-1");
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

        // report status
        cObj("report_status").onchange = function () {
            if (this.value == "pending") {
                cObj("hide_the_technician_field").classList.add("d-none");
                cObj("admin_attender").disabled = true;
                cObj("admin_attender").value = "";
            }else{
                cObj("hide_the_technician_field").classList.remove("d-none");
                cObj("admin_attender").disabled = false;
            }
        }
    </script>
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
