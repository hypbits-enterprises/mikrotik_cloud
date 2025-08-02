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
    <title>Hypbits - Add Records {{$table_name}}</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
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
                <!-- Basic Tables end -->
                <div class="row">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header">
                              <h4 class="card-title">Shared Tables - Add Records {{$table_name}}.</h4>
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
                                  @if (session('shared_table_success'))
                                      <p class="text-success">{{ session('shared_table_success') }}</p>
                                  @endif

                                  <div class="container p-1">
                                    @php
                                        $btnText = "<i class=\"ft-arrow-left\"></i> Back to list";
                                        $otherClasses = "ml-1";
                                        $btnLink = "/SharedTables/View/".$table_id."/Name/".$link_table_name;
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    {{-- <a href="/SharedTables/View/{{$table_id}}/Name/{{$link_table_name}}" class="btn btn-primary btn-sm"><i class="ft-arrow-left"></i> Back to list</a> --}}
                                  </div>
                                  <div class="container p-1 rounded mx-0">
                                    <p><b>Note:</b> - Fill all fields as neccesary! <br>
                                        - The field below will appear as you defined them while creating the table. <br>
                                        - You can edit the table details so that you can change the field type for easy data capturing.
                                    </p>
                                  </div>
                                  @php
                                    function replacePunctuationWithUnderscore($string) {
                                        $pattern = '/[^\w\s]/';
                                        $replacement = '_';
                                        return preg_replace("/\s/i","_",preg_replace($pattern, $replacement, $string));
                                    }
                                  @endphp
                                    <h6>Add Records on <b><u>{{$table_name}}</u></b>.</h6>
                                  <div class="container border border-primary rounded p-1">
                                    <form class="row my-1 mx-auto" action="/SharedTables/AddRecords" method="post">
                                        @csrf
                                        <input type="hidden" name="table_name" value="{{$link_table_name}}">
                                        <input type="hidden" name="table_id" value="{{$table_id}}">
                                        @for ($i = 0; $i < count($table_columns); $i++)
                                            <div class="col-md-4 my-1">
                                                @php
                                                    $this_id = replacePunctuationWithUnderscore($table_columns[$i]->column_name.$table_columns[$i]->id);
                                                @endphp
                                                <label for="{{$this_id}}" class="form-control-label"><b>{{$table_columns[$i]->column_name}}</b></label>
                                                @if ($table_columns[$i]->field_type == "textfield")
                                                    <input type="text" name="{{$this_id}}" id="{{$this_id}}" value="{{$table_columns[$i]->default_value}}" placeholder="{{$table_columns[$i]->column_name}}" class="form-control">
                                                @endif

                                                @if ($table_columns[$i]->field_type == "textarea")
                                                    <textarea name="{{$this_id}}" id="{{$this_id}}" cols="30" rows="3" class="form-control" placeholder="{{$table_columns[$i]->column_name}}">{{$table_columns[$i]->default_value}}</textarea>
                                                @endif

                                                @if ($table_columns[$i]->field_type == "datepicker")
                                                    <input type="date" name="{{$this_id}}" id="{{$this_id}}" value="{{date("Y-m-d")}}" placeholder="{{$table_columns[$i]->column_name}}" class="form-control">
                                                @endif
                                            </div>
                                        @endfor
                                        <div class="col-md-12">
                                            @php
                                                $btnText = "<i class=\"ft-save\"></i> Save Record!";
                                                $otherClasses = "w-100";
                                                $btn_id = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button class="btn btn-primary w-100"  {{$readonly}} type="submit"><i class="ft-save"></i> Save Record!</button> --}}
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

    <script>
    </script>

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>


    {{-- <script src="/theme-assets/js/core/viewSharedTable.js" type="text/javascript"></script> --}}
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
