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
    <title>Hypbits - My profile</title>
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
                                <li class="breadcrumb-item inactive"><a href="#">Account & Settings</a>
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
                                      @php
                                          $btnText = "<i class=\"ft-plus\"></i> Manage Admin";
                                          $otherClasses = $readonly." ".$view;
                                          $btnLink = "/Accounts/add";
                                          $otherAttributes = "";
                                      @endphp
                                      <x-button-link btnType="primary" btnSize="sm" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    {{-- <a href="/Accounts/add" class="btn btn-primary {{$readonly}} {{$view}}" ><i class="ft-plus"></i> Manage Admin</a> --}}
                                      @php
                                          $btnText = "<i class=\"ft-wind\"></i> Shared Tables";
                                          $otherClasses = $readonly." ".$view;
                                          $btnLink = "/SharedTables";
                                          $otherAttributes = "";
                                      @endphp
                                      <x-button-link btnType="info" btnSize="sm" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    {{-- <a href="/SharedTables" class="btn btn-info {{$readonly}} {{$view}}"><i class="ft-wind"></i> Shared Tables</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
            </div>
            <section class="section profile">
              <div class="row">
                <div class="col-xl-4">
        
                  <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                      <img style="width: 200px;height: 200px;" src="{{session('dp_locale') ? session('dp_locale') :'/theme-assets/images/pngegg.png'}}" alt="Profile" class="rounded-circle">
                      <h4 class="my-1">{{ session('Usernames') }}</h4>
                      <div class="p-1 my-1">
                        @if (session('error'))
                            <p class="text-danger text-bolder">{{ session('error') }}</p>
                        @endif
                        @if (session('success'))
                            <p class="text-success text-bolder">{{ session('success') }}</p>
                        @endif
                      </div>
                    </div>
                  </div>
        
                </div>
        
                <div class="col-xl-8">
        
                  <div class="card">
                    <div class="card-body pt-3">
                      <!-- Bordered Tabs -->
                      <ul class="nav nav-tabs nav-tabs-bordered">
        
                        <li class="nav-item">
                          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                        </li>
        
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                        </li>
        
                        <li class="nav-item {{$view}}">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">General Settings</button>
                        </li>
                        
                        <li class="nav-item {{$view}}">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#company-profile-edit">Company Profile</button>
                        </li>
        
                      </ul>
                      <div class="tab-content pt-2">
        
                        <div class="tab-pane fade show active profile-overview text-lg" id="profile-overview">
                          <h5 class="card-title">Profile Details</h5>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label ">Full Name</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->admin_fullname ?$admin_data[0]->admin_fullname:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Company</div>
                            <div class="col-lg-9 col-md-8">{{$organization->organization_name ? $organization->organization_name :"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Role</div>
                            <div class="col-lg-9 col-md-8">Administrator</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Last time Login</div>
                            <div class="col-lg-9 col-md-8">{{$date_time ?$date_time:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Phone</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->contacts ?$admin_data[0]->contacts:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Email</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->email ?$admin_data[0]->email:"Null"}}</div>
                          </div>
        
                        </div>
        
                        <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
        
                          <div class="container">
                            <div class="row mb-3">
                              <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                              <div class="col-md-8 col-lg-9">
                                <img style="width: 150px" src="{{session('dp_locale') ? session('dp_locale') :'/theme-assets/images/pngegg.png'}}" alt="Profile">
                                <div class="pt-2">
                                  @php
                                      $btnText = "<i class=\"ft-upload\"></i>";
                                      $otherClasses = "";
                                      $btn_id = "update_dp_btn";
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="Upload new profile image" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                  {{-- <button type="button" id="update_dp_btn" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="ft-upload"></i></button> --}}
                                  @php
                                      $btnText = "<i class=\"ft-trash\"></i>";
                                      $otherClasses = " ";
                                      $btnLink = "/delete_pp/".$admin_data[0]->admin_id;
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button-link btnType="danger" btnSize="sm" :otherAttributes="$otherAttributes" toolTip="Remove my profile image" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                  {{-- <a href="/delete_pp/{{$admin_data[0]->admin_id}}" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="ft-trash"></i></a> --}}
                                </div>
                              </div>
                            </div>

                            <div class="container d-none" id="change_dp_window">
                                <form action="/update_dp" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <hr>
                                    <h5 class="my-2">Change Profile Picture</h5>
                                    <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                                    <p id="mine_dp_errors"></p>
                                    <label for="mine_dp" class="form-control-label">Select an Image</label>
                                    <input type="file" name="mine_dp" id="mine_dp" class="form-control" required>
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- <button type="submit" id="upload_image" class="btn btn-primary my-1 text-lg">Save Image</button> --}}
                                            @php
                                                $btnText = "Save Image";
                                                $otherClasses = "my-1 text-lg";
                                                $btn_id = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "Cancel";
                                                $otherClasses = "my-1 text-lg";
                                                $btn_id = "close_window_btn";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="button" id="close_window_btn" class="btn btn-secondary my-1 text-lg">Cancel</button> --}}
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                            </div>
                          </div>
                          <!-- Profile Edit Form -->
                          <form action="/update_admin"  enctype="multipart/form-data" method="POST">
                            @csrf
                            <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                            <div class="row mb-3">
                              <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="fullName" type="text" class="form-control" id="fullName" value="{{$admin_data[0]->admin_fullname ?$admin_data[0]->admin_fullname:""}}" placeholder="Fullname" required>
                              </div>
                            </div>
        
        
                            <div class="row mb-3">
                              <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="phone" type="text" class="form-control" id="Phone" value="{{$admin_data[0]->contacts ?$admin_data[0]->contacts:""}}" placeholder="Phone Number" required>
                              </div>
                            </div>
        
                            <div class="row mb-3">
                              <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="email" type="email" class="form-control" id="Email" value="{{$admin_data[0]->email ?$admin_data[0]->email:""}}" placeholder="Email" >
                              </div>
                            </div>
        
                            <div class="text-center">
                              @php
                                  $btnText = "Save Changes";
                                  $otherClasses = "";
                                  $btn_id = "";
                                  $otherAttributes = "";
                              @endphp
                              <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                              {{-- <button type="submit" class="btn btn-primary">Save Changes</button> --}}
                            </div>
                          </form><!-- End Profile Edit Form -->
        
                        </div>
                        <div class="tab-pane fade profile-edit pt-3" id="company-profile-edit">
        
                          <div class="container">
                            <div class="row mb-3">
                              <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Company Profile Image</label>
                              <div class="col-md-8 col-lg-9">
                                <img class="border border-success rounded" style="width: 150px" src="{{$organization->organization_logo != null ? $organization->organization_logo :'/theme-assets/images/logo-placeholder-image.png'}}" alt="Profile">
                                <div class="pt-2">
                                  @php
                                      $btnText = "<i class=\"ft-upload\"></i>";
                                      $otherClasses = "my-1 text-lg";
                                      $btn_id = "update_company_profile_btn";
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button :otherAttributes="$otherAttributes" toolTip="Upload new profile image" :btnText="$btnText" toolTip="" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                  {{-- <button type="button" id="update_company_profile_btn" {{$readonly}} class="btn btn-primary btn-sm" title="Upload new profile image"><i class="ft-upload"></i></button> --}}
                                  @php
                                      $btnText = "<i class=\"ft-trash\"></i>";
                                      $otherClasses = " ";
                                      $btnLink = "/delete_pp_organization";
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button-link btnType="danger" btnSize="sm" toolTip="Remove my profile image" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                  {{-- <a href="/delete_pp_organization" class="btn btn-danger btn-sm {{$readonly}}" title="Remove my profile image"><i class="ft-trash"></i></a> --}}
                                </div>
                              </div>
                            </div>

                            <div class="container d-none" id="change_company_dp_window">
                                <form action="/update_company_dp" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <hr>
                                    <h5 class="my-2">Change Profile Picture</h5>
                                    <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                                    <p id="mine_dp_errors"></p>
                                    <label for="mine_dp" class="form-control-label">Select an Image</label>
                                    <input type="file" name="mine_dp" id="mine_dp" class="form-control" required>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "Save Image";
                                                $otherClasses = "my-1 text-lg";
                                                $btn_id = "upload_image";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="submit" id="upload_image" {{$readonly}} class="btn btn-primary my-1 text-lg">Save Image</button> --}}
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "Cancel";
                                                $otherClasses = "my-1 text-lg";
                                                $btn_id = "close_company_profile_dp_window_btn";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="button" id="close_company_profile_dp_window_btn" class="btn btn-secondary my-1 text-lg">Cancel</button> --}}
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                            </div>
                          </div>
                          <!-- Profile Edit Form -->
                          <form action="/update_organization_profile"  enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="row mb-3">
                              <label for="organization_name" class="col-md-4 col-lg-3 col-form-label">Company Name</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="organization_name" type="text" class="form-control" id="organization_name" 
                                value="{{$organization->organization_name ? $organization->organization_name : ""}}" placeholder="Company Name" >
                              </div>
                            </div>
        
                            {{-- <div class="row mb-3">
                              <label for="Job" class="col-md-4 col-lg-3 col-form-label">Role</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="job" type="text" class="form-control" id="Job" value="Web Designer">
                              </div>
                            </div> --}}

                            <div class="row mb-3">
                              <label for="organization_address" class="col-md-4 col-lg-3 col-form-label">Organization Address</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="organization_address" required type="text" class="form-control" id="organization_address" value="{{$organization->organization_address ?$organization->organization_address:""}}" placeholder="Organization Address" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="organization_main_contact" class="col-md-4 col-lg-3 col-form-label">Organization Main Contact</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="organization_main_contact" type="text" class="form-control" id="organization_main_contact" value="{{$organization->organization_main_contact ?$organization->organization_main_contact:""}}" placeholder="Company`s Main Contact" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="organization_email" class="col-md-4 col-lg-3 col-form-label">Organization Email</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="organization_email" type="text" class="form-control" id="organization_email" value="{{$organization->organization_email ?$organization->organization_email:""}}" placeholder="Main E-mail" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="BusinessShortCode" class="col-md-4 col-lg-3 col-form-label">Business Short Code (Paybill)</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="BusinessShortCode" type="text" class="form-control" id="BusinessShortCode" value="{{$organization->BusinessShortCode ?$organization->BusinessShortCode:""}}" placeholder="Business Short Code" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="sms_sender" class="col-md-4 col-lg-3 col-form-label">SMS SENDER</label>
                              <div class="col-md-8 col-lg-9">
                                <select name="sms_sender" id="sms_sender" class="form-control">
                                  <option value="" hidden>Select Sender</option>
                                  <option {{$organization->sms_sender == "celcom" ? "selected" : ""}} value="celcom">Celcom Kenya</option>
                                  <option {{$organization->sms_sender == "afrokatt" ? "selected" : ""}} value="afrokatt">Afrokatt Kenya</option>
                                  <option {{$organization->sms_sender == "hostpinnacle" ? "selected" : ""}} value="hostpinnacle">Hostpinnacle Kenya</option>
                                </select>
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="sms_api_key" class="col-md-4 col-lg-3 col-form-label">SMS API Key/ API USERNAME</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="sms_api_key" type="text" class="form-control" id="sms_api_key" value="{{$organization->sms_api_key ?$organization->sms_api_key:""}}" placeholder="(leave blank if not present)" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="sms_partner_id" class="col-md-4 col-lg-3 col-form-label">SMS PATNER ID/ API PASSWORD</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="sms_partner_id" type="text" class="form-control" id="sms_partner_id" value="{{$organization->sms_partner_id ?$organization->sms_partner_id:""}}" placeholder="(leave blank if not present)" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="sms_short_code" class="col-md-4 col-lg-3 col-form-label">SMS SHORT CODE / SENDER ID</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="sms_short_code" type="text" class="form-control" id="sms_short_code" value="{{$organization->sms_shortcode ?$organization->sms_shortcode:""}}" placeholder="(leave blank if not present)" >
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="sms_short_code" class="col-md-4 col-lg-3 col-form-label">PAYMENT DESCRIPTION FOR RECEIPT/INVOICE</label>
                              <div class="col-md-8 col-lg-9">
                                <textarea name="payment_description" id="payment_description" cols="30" rows="10" class="form-control" placeholder="e.g : Pay using Paybill 202020 Account number 1000.">{{$organization->payment_description ?$organization->payment_description:""}}</textarea>
                              </div>
                            </div>
                            <div class="text-center">
                              @php
                                  $btnText = "Save Changes";
                                  $otherClasses = "my-1 text-lg";
                                  $btn_id = "";
                                  $otherAttributes = "";
                              @endphp
                              <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="secondary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                              {{-- <button type="submit" {{$readonly}} class="btn btn-primary">Save Changes</button> --}}
                            </div>
                          </form><!-- End Profile Edit Form -->
        
                        </div>
        
                        <div class="tab-pane fade" id="profile-settings">
                          <!-- Settings Form -->
                          <form method="POST" action="/update_delete_option">
                            @csrf
                            <p class="text-danger">Note: <br> <small>Changes done here will affect the whole system</small></p>
                            <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                            <h5>Select period of deleting your data</h5>
                            <div class="row mb-3 pt-2">
                              <div class="col-md-6">
                                <label for="delete_message_records">Frequency of deleting SMS records</label>
                              </div>
                              <div class="col-md-6">
                                <select class="form-control" name="delete_message_records" id="delete_message_records">
                                  <option value="" hidden>Select an option</option>
                                  <option {{$delete_sms == "daily" ? "selected":""}} value="daily">Daily</option>
                                  <option {{$delete_sms == "weekly" ? "selected":""}} value="weekly">Weekly</option>
                                  <option {{$delete_sms == "monthly" ? "selected":""}} value="monthly">Monthly</option>
                                  <option {{$delete_sms == "yearly" ? "selected":""}} value="yearly">Yearly</option>
                                  <option {{$delete_sms == "2years" ? "selected":""}} value="2years">2 Years</option>
                                  <option {{$delete_sms == "5years" ? "selected":""}} value="5years">5 Years</option>
                                  <option {{$delete_sms == "never" ? "selected":""}} value="never">Never</option>
                                </select>
                              </div>
                            </div>
                            <div class="row mb-3">
                              <div class="col-md-6">
                                <label for="delete_transactions">Frequency of deleting Transaction records</label>
                              </div>
                              <div class="col-md-6">
                                <select class="form-control" name="delete_transactions" id="delete_transactions">
                                  <option value="" hidden>Select an option</option>
                                  <option {{$delete_trans == "daily" ? "selected":""}} value="daily">Daily</option>
                                  <option {{$delete_trans == "weekly" ? "selected":""}} value="weekly">Weekly</option>
                                  <option {{$delete_trans == "monthly" ? "selected":""}} value="monthly">Monthly</option>
                                  <option {{$delete_trans == "yearly" ? "selected":""}} value="yearly">Yearly</option>
                                  <option {{$delete_trans == "2years" ? "selected":""}} value="2years">2 Years</option>
                                  <option {{$delete_trans == "5years" ? "selected":""}} value="5years">5 Years</option>
                                  <option {{$delete_trans == "never" ? "selected":""}} value="never">Never</option>
                                </select>
                              </div>
                            </div>
                            <div class="text-center">
                              @php
                                  $btnText = "Save Changes";
                                  $otherClasses = "";
                                  $btn_id = "";
                                  $otherAttributes = "";
                              @endphp
                              <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="secondary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                              {{-- <button type="submit" {{$readonly}} class="btn btn-primary">Save Changes</button> --}}
                            </div>
                          </form><!-- End settings Form -->
                          <div class="container d-none">
                            <p><strong>Export user data</strong></p>
                            @php
                                $btnText = "<i class=\"ft-command\"></i> Export";
                                $otherClasses = "text-bolder disabled";
                                $btnLink = "/Clients/Export";
                                $otherAttributes = "";
                            @endphp
                            <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            {{-- <a href="/Clients/Export" class="btn btn-secondary text-bolder {{$readonly}} disabled"><i class="ft-command"> </i>Export</a> --}}
                            <hr>
                            <p><strong>Manage Billing SMSes</strong></p>
                            @php
                                $btnText = "<i class=\"ft-settings\"> </i> Manage";
                                $otherClasses = "text-bolder disabled ".$readonly;
                                $btnLink = "/BillingSms/Manage";
                                $otherAttributes = "";
                            @endphp
                            <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            {{-- <a href="/BillingSms/Manage" class="btn btn-secondary text-bolder {{$readonly}} disabled"><i class="ft-settings"> </i>Manage</a> --}}
                          </div>
                        </div>
        
                        <div class="tab-pane fade pt-3" id="profile-change-password">
                          <!-- Change Password Form -->
                            <form action="/changePasswordAdmin" method="post">
                                <h6><strong>Edit Password</strong></h6>
                                <div class="row">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="username" class="form-control-label">Username</label>
                                        <input type="text" class="form-control" name="username"
                                            placeholder="Username" required>
                                        <input type="hidden" class="form-control" name="admin_id"
                                            value="{{ $admin_data[0]->admin_id }}" placeholder="Username">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="old_password" class="form-control-label">Old
                                            Password</label>
                                        <input type="password" class="form-control" name="old_password"
                                            placeholder="Old password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-control-label">New Password</label>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="New password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-control-label">Confirm
                                            Password</label>
                                        <input type="password" class="form-control" name="confirm_password"
                                            placeholder="Confirm password" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        @php
                                            $btnText = "Save Changes";
                                            $otherClasses = "";
                                            $btn_id = "";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        {{-- <button class="btn btn-primary" type="submit">Save Changes</button> --}}
                                    </div>
                                </div>
                            </form>
                                    <!-- End Change Password Form -->
        
                        </div>
        
                      </div><!-- End Bordered Tabs -->
        
                    </div>
                  </div>
        
                </div>
              </div>
            </section>
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


    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/bootstrap.bundle.min.js"></script>
    <script>
      document.getElementById('mine_dp').onchange = function(){
          var filesize = document.getElementById('mine_dp').files[0].size;
          console.log(filesize);
          var size_in_mb = filesize/1000000;
          size_in_mb = size_in_mb.toFixed();
          if (size_in_mb > 7) {
            document.getElementById("mine_dp_errors").innerHTML = "<p class='text-danger'>Your image size should not be greater than 7MBS</p>";
            document.getElementById("mine_dp").classList.add("border");
            document.getElementById("mine_dp").classList.add("border-danger");
            document.getElementById("upload_image").disabled = true;
          }else{
            document.getElementById("mine_dp_errors").innerHTML = "";
            document.getElementById("mine_dp").classList.remove("border");
            document.getElementById("mine_dp").classList.remove("border-danger");
            document.getElementById("upload_image").disabled = false;
          }
      }

      document.getElementById("update_dp_btn").onclick = function () {
        document.getElementById("change_dp_window").classList.remove("d-none");
      }
      document.getElementById("close_window_btn").onclick = function () {
        document.getElementById("change_dp_window").classList.add("d-none");
      }
      document.getElementById("update_company_profile_btn").onclick = function () {
        document.getElementById("change_company_dp_window").classList.remove("d-none");
      }
      document.getElementById("close_company_profile_dp_window_btn").onclick = function () {
        document.getElementById("change_company_dp_window").classList.add("d-none");
      }
    </script>
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
