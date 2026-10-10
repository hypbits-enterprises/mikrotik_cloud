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
    <title>Hypbits - Regions & Subregions</title>
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
                                <li class="breadcrumb-item"><a href="#">Regions & Subregions</a>
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
                                    <p>- <code class="highlighter-rouge"><b>Regions</b></code> are your broad areas (e.g. "Nairobi North"), and each region is split into <code class="highlighter-rouge"><b>Subregions</b></code> (town, estate, zone, etc).<br>
                                    - Clients are assigned a subregion from their profile page or when they are added, which automatically places them in that subregion's region. Both are used when composing messages so you can target audiences by area.<br>
                                    - A region cannot be deleted while it has subregions, and a subregion cannot be deleted while it has clients. Renaming a subregion updates every client already assigned to it.</p>
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

                                    <div class="row w-100 mx-0 mb-2">
                                        <div class="col-12 text-md-right">
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Add Region";
                                                $otherClasses = "";
                                            @endphp
                                            <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="md" :otherClasses="$otherClasses" btnId="open_add_region_btn" :readOnly="$readonly" />
                                        </div>
                                    </div>

                                    <div class="modal fade text-left hide" id="add_region_modal" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary white">
                                                    <h4 class="modal-title white">Add a New Region</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_add_region_modal">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Regions are your broad areas, e.g. "Nairobi North" or "Zone 3". Once a region exists, add subregions (towns, estates) under it below; clients are assigned to those subregions.</p>
                                                    <form action="/Regions/Add" method="post">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label class="form-control-label">Region Name</label>
                                                            <input type="text" name="region_name" class="form-control" placeholder="e.g. Nairobi North" required {{ $readonly }}>
                                                        </div>
                                                        @php
                                                            $btnText = "<i class=\"ft-plus\"></i> Add Region";
                                                            $otherClasses = "w-100";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="primary" type="submit" btnSize="md" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table" id="regions_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Region Name</th>
                                                    <th>Subregions</th>
                                                    <th>Clients Assigned</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($regions as $region)
                                                    <tr class="region-row" data-region-name="{{ strtolower($region->name) }}">
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $region->name }}</td>
                                                        <td><span class="badge badge-secondary">{{ $region_subregion_counts[$region->name] ?? 0 }}</span></td>
                                                        <td><span class="badge badge-info">{{ $region_client_counts[$region->name] ?? 0 }}</span></td>
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
                                                                            <p>Are you sure you want to delete the <b>"{{ $region->name }}"</b> region? A region can only be deleted once it has no subregions.</p>
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
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Subregions - Manage List.</h4>
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
                                    @if (session('subregion_error'))
                                        <p class="text-danger">{{ session('subregion_error') }}</p>
                                    @endif
                                    @if (session('subregion_success'))
                                        <p class="text-success">{{ session('subregion_success') }}</p>
                                    @endif

                                    <div class="row w-100 mx-0 mb-2">
                                        <div class="col-12 text-md-right">
                                            @php
                                                $btnText = "<i class=\"ft-plus\"></i> Add Subregion";
                                                $otherClasses = "";
                                                $subregion_readonly = count($regions) == 0 ? "disabled" : $readonly;
                                            @endphp
                                            <x-button :btnText="$btnText" btnType="primary" type="button" btnSize="md" :otherClasses="$otherClasses" btnId="open_add_subregion_btn" :readOnly="$subregion_readonly" />
                                            @if (count($regions) == 0)
                                                <br><small class="text-muted">Add a region first.</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="modal fade text-left hide" id="add_subregion_modal" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary white">
                                                    <h4 class="modal-title white">Add a New Subregion</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_add_subregion_modal">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Subregions are where your clients are actually based — a town, an estate, a zone. Each subregion belongs to one region, and its name must be unique across all regions.</p>
                                                    <form action="/Subregions/Add" method="post">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label class="form-control-label">Region</label>
                                                            <select name="region_name" class="form-control" required {{ $readonly }}>
                                                                <option value="" hidden>Select region</option>
                                                                @foreach ($regions as $region)
                                                                    <option value="{{ $region->name }}">{{ $region->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-control-label">Subregion Name</label>
                                                            <input type="text" name="subregion_name" class="form-control" placeholder="e.g. Kasarani" required {{ $readonly }}>
                                                        </div>
                                                        @php
                                                            $btnText = "<i class=\"ft-plus\"></i> Add Subregion";
                                                            $otherClasses = "w-100";
                                                        @endphp
                                                        <x-button :btnText="$btnText" btnType="primary" type="submit" btnSize="md" :otherClasses="$otherClasses" btnId="" :readOnly="$readonly" />
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table" id="subregions_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Subregion Name</th>
                                                    <th>Region</th>
                                                    <th>Clients Assigned</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($subregions as $subregion)
                                                    <tr class="subregion-row" data-subregion-name="{{ strtolower($subregion->name.' '.$subregion->region) }}">
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $subregion->name }}</td>
                                                        <td><span class="badge badge-secondary">{{ $subregion->region }}</span></td>
                                                        <td><span class="badge badge-info">{{ $subregion_counts[$subregion->name] ?? 0 }}</span></td>
                                                        <td>
                                                            @php
                                                                $btnText = "<i class=\"ft-edit\"></i> Edit";
                                                                $otherClasses = "";
                                                                $btn_id = "edit_subregion_btn_".$subregion->index;
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="info" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                            @php
                                                                $btnText = "<i class=\"ft-trash\"></i> Delete";
                                                                $otherClasses = "";
                                                                $btn_id = "delete_subregion_btn_".$subregion->index;
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="danger" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                            <div class="modal fade text-left hide" id="delete_subregion_modal_{{ $subregion->index }}" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-danger white">
                                                                            <h4 class="modal-title white">Delete "{{ $subregion->name }}"</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_delete_subregion_modal_{{ $subregion->index }}">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>Are you sure you want to delete the <b>"{{ $subregion->name }}"</b> subregion? A subregion can only be deleted once it has no clients.</p>
                                                                            <div class="row w-100 mx-0 mt-1">
                                                                                <div class="col-md-6">
                                                                                    @php
                                                                                        $btnText = "<i class=\"ft-trash\"></i> Yes, Delete";
                                                                                        $btnLink = "/Subregions/Delete/".$subregion->index;
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
                                                                                    <x-button :btnText="$btnText" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" btnId="close_delete_subregion_modal_2_{{ $subregion->index }}" :readOnly="$readonly" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade text-left hide" id="edit_subregion_modal_{{ $subregion->index }}" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-info white">
                                                                            <h4 class="modal-title white">Edit "{{ $subregion->name }}"</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_edit_subregion_modal_{{ $subregion->index }}">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="/Subregions/Update" method="post">
                                                                                @csrf
                                                                                <input type="hidden" name="subregion_index" value="{{ $subregion->index }}">
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label">Region</label>
                                                                                    <select name="region_name" class="form-control" required {{ $readonly }}>
                                                                                        @foreach ($regions as $region)
                                                                                            <option value="{{ $region->name }}" {{ $region->name == $subregion->region ? 'selected' : '' }}>{{ $region->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label">Subregion Name</label>
                                                                                    <input type="text" name="subregion_name" class="form-control" value="{{ $subregion->name }}" required {{ $readonly }}>
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
                                                @endforeach
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
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        document.getElementById('open_add_region_btn').onclick = function () {
            $('#add_region_modal').modal('show');
        };
        document.getElementById('close_add_region_modal').onclick = function () {
            $('#add_region_modal').modal('hide');
        };
        @if ($errors->any())
            $('#add_region_modal').modal('show');
        @endif

        // Paginated with DataTables. Rows off the current page are detached from the DOM,
        // so the per-row edit/delete buttons use delegated handlers instead of getElementById.
        $('#regions_table').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{ orderable: false, searchable: false, targets: -1 }],
            language: { emptyTable: 'No regions have been created yet. Click "Add Region" above to get started.' },
        });
        $('#subregions_table').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{ orderable: false, searchable: false, targets: -1 }],
            language: { emptyTable: 'No subregions have been created yet. Click "Add Subregion" above to get started.' },
        });

        document.getElementById('open_add_subregion_btn').onclick = function () {
            $('#add_subregion_modal').modal('show');
        };
        document.getElementById('close_add_subregion_modal').onclick = function () {
            $('#add_subregion_modal').modal('hide');
        };

        // [button id prefix, action, modal id prefix]
        [
            ['edit_region_btn_', 'show', 'edit_region_modal_'],
            ['close_edit_region_modal_', 'hide', 'edit_region_modal_'],
            ['delete_region_btn_', 'show', 'delete_region_modal_'],
            ['close_delete_region_modal_2_', 'hide', 'delete_region_modal_'],
            ['close_delete_region_modal_', 'hide', 'delete_region_modal_'],
            ['edit_subregion_btn_', 'show', 'edit_subregion_modal_'],
            ['close_edit_subregion_modal_', 'hide', 'edit_subregion_modal_'],
            ['delete_subregion_btn_', 'show', 'delete_subregion_modal_'],
            ['close_delete_subregion_modal_2_', 'hide', 'delete_subregion_modal_'],
            ['close_delete_subregion_modal_', 'hide', 'delete_subregion_modal_'],
        ].forEach(function (handler) {
            $(document).on('click', '[id^="' + handler[0] + '"]', function (e) {
                var index = this.id.substring(handler[0].length);
                // "close_delete_..._modal_" also prefixes "close_delete_..._modal_2_"
                if (!/^\d+$/.test(index)) {
                    return;
                }
                $('#' + handler[2] + index).modal(handler[1]);
            });
        });
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
