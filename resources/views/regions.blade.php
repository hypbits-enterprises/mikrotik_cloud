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
    <title>Hypbits - Regions</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="account_and_profile"></x-menu>
    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges,"Account and Profile");
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
                                <li class="breadcrumb-item"><a href="#">Regions</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Regions - What you need to know.</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
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
                                    @php
                                        $btnText = "<i class=\"ft-arrow-left\"></i> Back to Accounts";
                                        $otherClasses = "";
                                        $btnLink = "/Accounts";
                                        $otherAttributes = "";
                                    @endphp
                                    <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    <p>- <code class="highlighter-rouge"><b>Regions</b></code> group your clients by their place of origin (town, estate, zone, etc). <br>
                                    - Once created, a region can be assigned to any client from their profile page, and is used when composing messages so you can target audiences by area.<br>
                                    - Renaming a region updates every client already assigned to it. Deleting a region unassigns it from those clients without deleting the clients themselves.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Regions - Manage List.</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @if (session('region_error'))
                                        <p class="text-danger">{{ session('region_error') }}</p>
                                    @endif
                                    @if (session('region_success'))
                                        <p class="text-success">{{ session('region_success') }}</p>
                                    @endif

                                    <form action="/Regions/Add" method="post" class="row w-100 mx-0 mb-2">
                                        @csrf
                                        <div class="col-md-6 form-group">
                                            <input type="text" name="region_name" class="form-control" placeholder="New region name (e.g. Kasarani)" required {{ $readonly }}>
                                        </div>
                                        <div class="col-md-3">
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Add Region";
                                                $otherClasses = "w-100";
                                            @endphp
                                            <x-button :btnText="$btnText" btnType="primary" type="submit" btnSize="md" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Region Name</th>
                                                    <th>Clients Assigned</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($regions as $region)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $region->name }}</td>
                                                        <td><span class="badge badge-info">{{ $region_counts[$region->name] ?? 0 }}</span></td>
                                                        <td>
                                                            @php
                                                                $btnText = "<i class=\"ft-edit\"></i> Edit";
                                                                $otherClasses = "";
                                                                $btn_id = "edit_region_btn_".$region->index;
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                            @php
                                                                $btnText = "<i class=\"ft-trash\"></i> Delete";
                                                                $otherClasses = "";
                                                                $btn_id = "delete_region_btn_".$region->index;
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                            <div class="modal fade text-left hide" id="delete_region_modal_{{ $region->index }}" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-danger white">
                                                                            <h4 class="modal-title white">Delete "{{ $region->name }}"</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_delete_region_modal_{{ $region->index }}">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Are you sure you want to delete the <b>"{{ $region->name }}"</b> region? Clients assigned to it will be unassigned, not deleted.</p>
                                                                            <div class="row w-100 mx-0 mt-1">
                                                                                <div class="col-md-6">
                                                                                    @php
                                                                                        $btnText = "<i class=\"ft-trash\"></i> Yes, Delete";
                                                                                        $btnLink = "/Regions/Delete/".$region->index;
                                                                                        $otherClasses = "w-100";
                                                                                        $otherAttributes = "";
                                                                                    @endphp
                                                                                    <x-button-link btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    @php
                                                                                        $btnText = "<i class=\"fas fa-x\"></i> Cancel";
                                                                                        $otherClasses = "w-100";
                                                                                    @endphp
                                                                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_delete_region_modal_2_{{ $region->index }}" :readOnly="$readonly" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade text-left hide" id="edit_region_modal_{{ $region->index }}" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-info white">
                                                                            <h4 class="modal-title white">Rename "{{ $region->name }}"</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_edit_region_modal_{{ $region->index }}">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="/Regions/Update" method="post">
                                                                                @csrf
                                                                                <input type="hidden" name="region_index" value="{{ $region->index }}">
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label">Region Name</label>
                                                                                    <input type="text" name="region_name" class="form-control" value="{{ $region->name }}" required {{ $readonly }}>
                                                                                </div>
                                                                                @php
                                                                                    $btnText = "<i class=\"fas fa-save\"></i> Save";
                                                                                    $otherClasses = "w-100";
                                                                                @endphp
                                                                                <x-button :btnText="$btnText" btnType="info" type="submit" btnSize="sm" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No regions have been created yet. Add one above to get started.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span
                class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits
                Enterprises</span>
            <ul class="list-inline float-md-right d-block d-md-inline-blockd-none d-lg-block mb-0">
                <li class="list-inline-item">Created By<a class="my-1" href="https://ladybirdsmis.com"
                        target="_blank"> Ladybird Softech Co.</a></li>
            </ul>
        </div>
    </footer>

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/bootstrap.bundle.min.js"></script>

    <script>
        @foreach ($regions as $region)
            document.getElementById('edit_region_btn_{{ $region->index }}').onclick = function () {
                $('#edit_region_modal_{{ $region->index }}').modal('show');
            };
            document.getElementById('close_edit_region_modal_{{ $region->index }}').onclick = function () {
                $('#edit_region_modal_{{ $region->index }}').modal('hide');
            };
            document.getElementById('delete_region_btn_{{ $region->index }}').onclick = function () {
                $('#delete_region_modal_{{ $region->index }}').modal('show');
            };
            document.getElementById('close_delete_region_modal_{{ $region->index }}').onclick = function () {
                $('#delete_region_modal_{{ $region->index }}').modal('hide');
            };
            document.getElementById('close_delete_region_modal_2_{{ $region->index }}').onclick = function () {
                $('#delete_region_modal_{{ $region->index }}').modal('hide');
            };
        @endforeach
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
