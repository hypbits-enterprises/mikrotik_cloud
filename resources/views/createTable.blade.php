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
    <title>Hypbits - Create Shared Tables</title>
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
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Create Shared Tables</h4>
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
                                    {{-- @php
                                        $btnText = "<i class=\"fas fa-arrow-left\"></i> Back to list";
                                        $otherClasses = "my-1";
                                        $btnLink = "/SharedTables";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" /> --}}
                                    <a href="/SharedTables" class="btn btn-sm btn-primary my-1"><i class="ft-arrow-left"></i> Back to List</a>
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
                                    <p>- <code class="highlighter-rouge"><b>A Shared table</b></code>- is a table that can be worked on by a team simultaneously without being excempted access, unlike normal files that cannot be access by two individuals at once. This shared tables can be accessed by more than one person. <br>
                                    - Start by creating a tables and giving it a name then define its columns and the column`s default values.<br>
                                    - You will be able to manipulate the data of the table except the records that are being change by someone else.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                <div class="row">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header">
                              <h4 class="card-title">Create Shared Tables</h4>
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
                                  @if (session('shared_table_error'))
                                      <p class="text-danger">{{ session('shared_table_error') }}</p>
                                  @endif
                                  <p><b>Note:</b>- Kindly read the instruction by hovering your mouse over <span data-html="true" data-toggle="tooltip" title="Yeah! You got it."><i class="ft-alert-circle"></i> </span> icon.</p>
                                  <div class="container border border-secondary col-md-6 p-1 rounded mx-0 my-1">
                                    <h6 class="text-center"><b>Create Table</b></h6>
                                    <label for="table_names" class="form-control-label">Table Name <span data-html="true" data-toggle="tooltip" title="This is the name of the table, It will be a way of identifying the table in the table list."><i class="ft-alert-circle"></i></span></label>
                                    <input type="text" {{$readonly}} class="form-control" placeholder="Table Name" id="table_names">
                                    <hr class="my-1">
                                    <h6 class="text-center">Define Table Columns <span data-html="true" data-toggle="tooltip" title="Define the column name for this table you are creating from this field,After which you click the add column button to add the column in the list and display how the table will appear."><i class="ft-alert-circle"></i></span></h6>
                                    <label for="column_name" class="form-label"><b>Column Name</b> </label>
                                    <input type="text" {{$readonly}} name="column_name" id="column_name" class="form-control" placeholder="Column Name">
                                    <label for="column_default_value" class="form-label"><b>Default Value</b></label>
                                    <input type="text" {{$readonly}} name="column_default_value" id="column_default_value" class="form-control" placeholder="Default Value : (Optional)">
                                    <label for="field_type" class="form-control-label"><b>Field Type:</b> <span data-html="true" data-toggle="tooltip" title="Select the input field type you want from this section. The textfield is abit smaller than the text area its used to input data like names or phone number while the text area is used to fill data like comments or reviews."><i class="ft-alert-circle"></i></span></label>
                                    <select name="" {{$readonly}} id="field_type" class="form-control">
                                        <option value="" hidden >Select an Option</option>
                                        <option selected value="textfield">Text Field</option>
                                        <option value="textarea">Text Area</option>
                                        <option value="datepicker">Date Picker</option>
                                    </select>
                                    <div class="col-md-6">
                                        <button type="button" {{$readonly}} class="btn btn-primary btn-sm my-1" id="add_columns_table"><i class="ft-plus"></i> Add Column</button>
                                    </div>
                                    <hr class="my-1">
                                    <form class="row" action="/SaveTable"  method="post">
                                        <div class="col-md-12">
                                            <label for="table_comments" class="form-control-label"><b>Comment</b></label>
                                            <textarea class="form-control"  {{$readonly}} name="table_comments" id="table_comments" cols="30" rows="5" placeholder="Comment here..."></textarea>
                                        </div>
                                        @csrf
                                        <input type="hidden" name="table_carry_data" id="table_carry_data" value='{"table_name":"","columns":[]}'>
                                        <div class="col-md-6">
                                            {{-- <button type="button" class="btn btn-primary btn-sm my-1" id="add_columns_table"><i class="ft-plus"></i> Add Column</button> --}}
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-save\"></i> Save Table";
                                                $otherClasses = "my-1 ".$readonly;
                                                $btn_id = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="Click this button to save the table after defining all the columns you need!" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="submit" {{$readonly}} data-html="true" data-toggle="tooltip" title="Click this button to save the table after defining all the columns you need!" class="btn btn-success text-dark btn-sm my-1"><i class="ft-save"></i> Save Table</button> --}}
                                        </div>
                                    </form>
                                  </div>
                                  {{-- display tables here --}}
                                  <hr>
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
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>


    <script src="/theme-assets/js/core/createSharedTable.js" type="text/javascript"></script>
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
