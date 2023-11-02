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
    <title>Hypbits - Edit {{$table_details->table_name}}</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/vendors/css/charts/chartist.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/app-lite.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/pages/dashboard-ecommerce.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <!-- END Custom CSS-->
</head>
<style>
  .hide{
    display: none;
  }
  .showBlock{
    display: block;
  }
</style>
@php
    date_default_timezone_set('Africa/Nairobi');
    $privilleged = session("priviledges");
    $priviledges = ($privilleged);
    function showOption($priviledges,$name){
        if (isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return "";
                    }
                }
            }
        }
        return "hide";
    }
    function readOnly($priviledges,$name){
        if (isJson($priviledges)){
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->readonly) {
                        return "disabled";
                    }
                }
            }
        }
        return "";
    }

    // get the readonly value
    $readonly = readOnly($priviledges,"Account and Profile");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
    <nav
    class="header-navbar navbar-expand-md navbar  navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light">
    <div class="navbar-wrapper">
        <div class="navbar-container content ">
            <div class="collapse navbar-collapse show" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-block d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                            href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item dropdown navbar-search">
                        <span class="text-light">Hello, {{ session('Usernames') }}</span>
                    </li>
                </ul>
                @if (!Session::has('Usernames'))
                    @php
                        header('Location: ' . URL::to('/Login'), true, 302);
                        exit();
                    @endphp
                @endif
                <ul class="nav navbar-nav float-right">
                    <li>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="arrow_box_right"><a class="dropdown-item" href="#"><i
                                        class="ft-book"></i> Read Notices</a><a class="dropdown-item"
                                    href="#"><i class="ft-check-square"></i> Mark all Read </a></div>
                        </div>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a
                            class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="avatar avatar-online"> <img style="width: 100px; height: 40px;"
                                    src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                    alt="avatar"><i></i> </span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="arrow_box_right"><a class="dropdown-item" href="#"><span
                                        class="avatar avatar-online"><img style="width: 100px; height: 30px;"
                                            src="{{ session('dp_locale') ? session('dp_locale') : '/theme-assets/images/pngegg.png' }}"
                                            alt="avatar"><br><br><span
                                            class="user-name text-bold-700 ml-1">{{ session('Usernames') }}</span></span></a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="/Accounts"><i
                                        class="ft-user"></i>Account & Settings</a>
                                {{-- <a class="dropdown-item" href="#"><i class="ft-mail"></i> My Inbox</a><a class="dropdown-item" href="#"><i class="ft-check-square"></i> Task</a><a class="dropdown-item" href="#"><i class="ft-message-square"></i> Chats</a> --}}
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="/Login"><i
                                        class="ft-power"></i> Logout</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true"
        data-img="/theme-assets/images/backgrounds/02.jpg">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
                <li class="nav-item mr-auto p-0 w-75" style="width: fit-content"><a class="navbar-brand "
                        href="/Dashboard"><img class="brand-logo w-100 mb-1 " alt="Chameleon admin logo"
                            src="/theme-assets/images/logo.jpeg" />
                    </a></li>
                <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
            </ul>
        </div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item"><a href="/Dashboard"><i class="ft-home"></i><span
                            class="menu-title" data-i18n="">Dashboard</span></a>
                </li>
                <li class="{{showOption($priviledges,"My Clients")}} nav-item"><a href="/Clients"><i class="ft-users"></i><span class="menu-title"
                            data-i18n="">My Clients</span></a>
                </li>
                <li class="{{(showOption($priviledges,"Transactions") == "hide" && showOption($priviledges,"Expenses") == "hide") ? "hide" : ""}} nav-item has-sub"><a href="#"><i class="ft-activity"></i><span class="menu-title" data-i18n="">Accounts</span></a>
                    <ul class="menu-content" style="">
                        <li class="{{showOption($priviledges,"Transactions")}} nav-item"><a href="/Transactions"><span><i class="ft-award"></i> Transactions</span></a>
                        </li>
                      <li class="{{showOption($priviledges,"Expenses")}} nav-item"><a href="/Expenses"><i class="ft-bar-chart-2"></i> Expenses</a></li>
                    </ul>
                </li>
                <li class="{{showOption($priviledges,"My Routers")}}  nav-item"><a href="/Routers"><i class="ft-layers"></i><span class="menu-title"
                            data-i18n="">My Routers</span></a>
                </li>
                <li class="{{showOption($priviledges,"SMS")}} nav-item"><a href="/sms"><i class="ft-mail"></i><span class="menu-title"
                            data-i18n="">SMS</span></a>
                </li>
                <li class="{{showOption($priviledges,"Account and Profile")}} active"><a href="/Accounts"><i class="ft-lock"></i><span
                            class="menu-title" data-i18n="">Account and Profile</span></a>
                </li>
            </ul>
        </div>
        <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
        <div class="navigation-background">
        </div>
    </div>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Account & Settings</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Accounts">Account & Settings</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Shared Tables</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables end -->
                <div class="row">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header">
                              <h4 class="card-title">Edit Shared Tables - {{$table_details->table_name}}</h4>
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
                                <a href="/SharedTables/View/{{$table_id}}/Name/{{$table_name}}" class="btn btn-sm btn-primary my-1"><i class="ft-arrow-left"></i> Back to List</a>
                                  @if (session('shared_table_error'))
                                      <p class="text-danger">{{ session('shared_table_error') }}</p>
                                  @endif
                                  <p><b>Note:</b>- Kindly read the instruction by hovering your mouse over <span data-html="true" data-toggle="tooltip" title="Yeah! You got it."><i class="ft-alert-circle"></i> </span> icon.</p>
                                  <p class="text-danger border border-danger p-1 rounded"><b>Kind reminder: If you remove any column below, any data under it will be deleted along with it.</b></p>
                                  <div class="container border border-secondary col-md-6 p-1 rounded mx-0 my-1">
                                    <h6 class="text-center"><b>Edit Table</b></h6>
                                    <label for="table_names" class="form-control-label">Table Name <span data-html="true" data-toggle="tooltip" title="This is the name of the table, It will be a way of identifying the table in the table list."><i class="ft-alert-circle"></i></span></label>
                                    <input type="text" {{$readonly}} class="form-control text-primary" value="{{$table_details->table_name}}" placeholder="Table Name" id="table_names">
                                    <hr class="my-1">
                                    <h6 class="text-center">Define Table Columns <span data-html="true" data-toggle="tooltip" title="Define the column name for this table you are creating from this field,After which you click the add column button to add the column in the list and display how the table will appear."><i class="ft-alert-circle"></i></span></h6>
                                    <label for="column_name" class="form-label"><b>Column Name</b> </label>
                                    <input type="text" {{$readonly}} name="column_name" id="column_name" class="form-control text-primary" placeholder="Column Name">
                                    <label for="column_default_value" class="form-label"><b>Default Value</b></label>
                                    <input type="text" {{$readonly}} name="column_default_value" id="column_default_value" class="form-control text-primary" placeholder="Default Value : (Optional)">
                                    <label for="field_type" class="form-control-label"><b>Field Type:</b> <span data-html="true" data-toggle="tooltip" title="Select the input field type you want from this section. The textfield is abit smaller than the text area its used to input data like names or phone number while the text area is used to fill data like comments or reviews."><i class="ft-alert-circle"></i></span></label>
                                    <select name="" {{$readonly}} id="field_type" class="form-control text-primary">
                                        <option value="" hidden >Select an Option</option>
                                        <option selected value="textfield">Text Field</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="datepicker">Date Picker</option>
                                    </select>
                                    <div class="col-md-6">
                                        <button type="button" {{$readonly}} class="btn btn-primary btn-sm my-1" id="add_columns_table"><i class="ft-plus"></i> Add Column</button>
                                    </div>
                                    <hr class="my-1">
                                    <form class="row" action="/UpdateTableCreated" method="post">
                                        <div class="col-md-12">
                                            <label for="table_comments" class="form-control-label"><b>Comment</b></label>
                                            <textarea {{$readonly}} class="form-control text-primary" name="table_comments" id="table_comments" cols="30" rows="5" placeholder="Comment here...">{{$comments}}</textarea>
                                        </div>
                                        @csrf
                                        <input type="hidden" name="table_name" value="{{$table_name}}">
                                        <input type="hidden" name="table_id" value="{{$table_id}}">
                                        <input type="hidden" name="table_carry_data" id="table_carry_data" value='{"table_name":"{{$table_details->table_name}}","columns":{{json_encode($table_details->columns)}}}'>
                                        <div class="col-md-6">
                                            {{-- <button type="button" class="btn btn-primary btn-sm my-1" id="add_columns_table"><i class="ft-plus"></i> Add Column</button> --}}
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" {{$readonly}} data-html="true" data-toggle="tooltip" title="Click this button to save the table after defining all the columns you need!" class="btn btn-success text-dark btn-sm my-1"><i class="ft-save"></i> Save Changes</button>
                                        </div>
                                    </form>
                                  </div>
                                  {{-- display tables here --}}
                                  <hr>
                                  <div class="container">
                                    <div class="modal fade text-left hide" id="edit_column_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info white">
                                                <h4 class="modal-title white" id="myModalLabel11">Edit Column | <span id="editted_columns"></span></h4>
                                                <button id="hide_edit_schools" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container">
                                                        <label for="column_name_change" class="form-label"><b>Change Column Name</b></label>
                                                        <input type="hidden" name="" id="store_column_ids">
                                                        <input type="text" name="column_name_change" id="column_name_change" class="form-control" placeholder="Column Name">
                                                        <label for="change_field_type" class="form-label"><b>Change Field Type</b></label>
                                                        <select name="" id="change_field_type" class="form-control">
                                                            <option class="opts" value="" hidden >Select an Option</option>
                                                            <option class="opts" value="textfield">Text Field</option>
                                                            <option class="opts" value="textarea">Text Area</option>
                                                            <option class="opts" value="datepicker">Date Picker</option>
                                                        </select>
                                                        <label for="column_default_value_change" class="form-label"><b>Default Value</b></label>
                                                        <input type="text" name="column_default_value_change" id="column_default_value_change" class="form-control" placeholder="Default Value : (Optional)">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" id="close_this_window" class="btn grey btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" {{$readonly}} id="save_column_data" class="btn btn-info">Save</button>
                                                </div>
                                            </div>
										</div>
									</div>
                                  </div>

                                  {{-- delete columns --}}
                                  <div class="container">
                                    <div class="modal fade text-left hide" id="delete_column_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger white">
                                                <h4 class="modal-title white" id="myModalLabel11">Confirm Delete Column</h4>
                                                <input type="hidden" id="delete_columns_ids">
                                                <button id="hide_delete_column" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container">
                                                        <p class="text-dark"><b>Are you sure you want to delete "<span id="column_names_id"></span>" Column?</b></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" id="close_this_window_delete" class="btn grey btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" {{$readonly}} id="yes_delete_column_data" class="btn btn-danger">Delete</button>
                                                </div>
                                            </div>
										</div>
									</div>
                                  </div>
                                  <div class="container">
                                    <h6>Table Name: <b id="table_name_holder">Sample Table</b></h6>
                                    <div class="table-responsive" id="transDataReciever">
                                        <h6 class="text-secondary text-center"><span style="font-size: 36px;"><i class="ft-alert-triangle"></i></span><br> Start By defining your columns your table sample will appear here!</h6>
                                        {{-- <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Table Name <span style="cursor: pointer;" class="text-danger"><i class="ft-trash"></i></span></th>
                                                    <th>Date Created</th>
                                                    <th>Last Modified</th>
                                                    <th>Creator</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Mark Otto <span class="badge badge-success"> </span></td>
                                                    <td>Mon 12th Jan 2023</td>
                                                    <td>Mon 12th Jan 2023</td>
                                                    <td>Mr G</td>
                                                    <td> <a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this User"><i class="ft-eye"></i></a> </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>Mark Otto <span class="badge badge-success"> </span></td>
                                                    <td>Mon 12th Jan 2023</td>
                                                    <td>Mon 12th Jan 2023</td>
                                                    <td>Mr G</td>
                                                    <td> <a href="#" class="btn btn-sm btn-primary text-bolder" data-toggle="tooltip" title="View this User"><i class="ft-eye"></i></a> </td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <nav aria-label="Page navigation example" class="hide" id="tablefooter">
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
                                        <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo">1</span> to <span class="text-secondary"  id="finishNo">2</span> records of <span  id="tot_records">2</span></p>
                                    </nav>
                                  </div>
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com/sims/"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>


    <script src="/theme-assets/js/core/editSharedFiles.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/bootstrap.bundle.min.js"></script>

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
