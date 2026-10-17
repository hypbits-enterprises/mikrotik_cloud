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
    <title>Hypbits - Hotspot Plan</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_plans"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Hotspot Plan</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotspot.plans') }}">Plans</a></li>
                                <li class="breadcrumb-item"><a href="#">{{ $plan ? "Edit" : "New" }}</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                @if (session("success_hotspot"))
                    <div class="alert alert-success alert-dismissible fade show">{{ session("success_hotspot") }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
                @endif
                @if (session("error_hotspot"))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session("error_hotspot") }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
                @endif
                <div class="row mb-1">
                    <div class="col-12">
                        @php $btnText = "<i class=\"ft-arrow-left\"></i> Back to Plans"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.plans')" />
                    </div>
                </div>
                @php
                    [$duration_value, $duration_unit] = $plan ? \App\Classes\HotspotPlans::durationParts($plan->duration_minutes) : [1, "hours"];
                    [$up_value, $up_unit] = \App\Classes\HotspotPlans::speedParts($plan->upload_speed ?? null);
                    [$down_value, $down_unit] = \App\Classes\HotspotPlans::speedParts($plan->download_speed ?? null);
                    $data_value = ""; $data_unit = "MB";
                    if ($plan && $plan->data_limit_mb) {
                        if ($plan->data_limit_mb % 1024 == 0) { $data_value = $plan->data_limit_mb / 1024; $data_unit = "GB"; } else { $data_value = $plan->data_limit_mb; }
                    }
                    $value = function ($field, $default) { return old($field, $default); };
                @endphp
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-package"></i> {{ $plan ? "Edit plan \"" . $plan->plan_name . "\"" : "New plan" }}</h4></div>
                            <div class="card-content"><div class="card-body">
                                <form method="POST" action="{{ $plan ? route('hotspot.plan_update', $plan->plan_id) : route('hotspot.plan_store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="plan_name"><b>Plan name</b></label>
                                            <input class="form-control" type="text" name="plan_name" id="plan_name" maxlength="100" required placeholder="e.g. 1 hour" value="{{ $value('plan_name', $plan->plan_name ?? '') }}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="price"><b>Price (Kes)</b></label>
                                            <input class="form-control" type="number" name="price" id="price" min="1" step="1" required placeholder="e.g. 20" value="{{ $value('price', $plan ? rtrim(rtrim($plan->price, '0'), '.') : '') }}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="duration_value"><b>Duration</b></label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="duration_value" id="duration_value" min="1" step="1" required value="{{ $value('duration_value', $duration_value) }}">
                                                <select class="form-control" name="duration_unit">
                                                    @foreach (array_keys(\App\Classes\HotspotPlans::DURATION_UNITS) as $unit)
                                                        <option value="{{ $unit }}" {{ $value('duration_unit', $duration_unit) == $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small class="text-muted">A month counts as 30 days.</small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="shared_users"><b>Devices per ticket</b></label>
                                            <input class="form-control" type="number" name="shared_users" id="shared_users" min="1" max="10" step="1" required value="{{ $value('shared_users', $plan->shared_users ?? 1) }}">
                                            <small class="text-muted">How many phones/laptops can use one ticket at the same time.</small>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label class="d-block"><b>How the time is counted</b></label>
                                            @php $validity = $value('validity_type', $plan->validity_type ?? 'elapsed'); @endphp
                                            <label class="d-block mb-0"><input type="radio" name="validity_type" value="elapsed" {{ $validity == 'elapsed' ? 'checked' : '' }}> <b>From first login</b> (elapsed)</label>
                                            <small class="d-block text-muted mb-1">The clock runs continuously from the moment the customer first logs in, connected or not. E.g. a 6-hour ticket bought at 10:00 ends at 16:00.</small>
                                            <label class="d-block mb-0"><input type="radio" name="validity_type" value="paused" {{ $validity == 'paused' ? 'checked' : '' }}> <b>While connected</b> (paused)</label>
                                            <small class="d-block text-muted">Time only counts while the customer is connected; it pauses when they disconnect. E.g. a 1-hour ticket can be used as 20 minutes today and 40 minutes tomorrow.</small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="upload_speed"><b>Upload speed</b> <span class="text-muted">(optional)</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="upload_speed" id="upload_speed" min="0" step="any" placeholder="No limit" value="{{ $value('upload_speed', $up_value) }}">
                                                <select class="form-control" name="upload_unit">
                                                    @foreach (["Mbps", "Kbps"] as $unit)
                                                        <option value="{{ $unit }}" {{ $value('upload_unit', $up_unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="download_speed"><b>Download speed</b> <span class="text-muted">(optional)</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="download_speed" id="download_speed" min="0" step="any" placeholder="No limit" value="{{ $value('download_speed', $down_value) }}">
                                                <select class="form-control" name="download_unit">
                                                    @foreach (["Mbps", "Kbps"] as $unit)
                                                        <option value="{{ $unit }}" {{ $value('download_unit', $down_unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="data_limit"><b>Data limit</b> <span class="text-muted">(optional)</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="number" name="data_limit" id="data_limit" min="0" step="any" placeholder="Unlimited" value="{{ $value('data_limit', $data_value) }}">
                                                <select class="form-control" name="data_unit">
                                                    @foreach (["MB", "GB"] as $unit)
                                                        <option value="{{ $unit }}" {{ $value('data_unit', $data_unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small class="text-muted">Upload and download together. The ticket ends when it is used up.</small>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="sort_order"><b>Order on the portal</b></label>
                                            <input class="form-control" type="number" name="sort_order" id="sort_order" step="1" value="{{ $value('sort_order', $plan->sort_order ?? 0) }}">
                                            <small class="text-muted">Lower numbers are shown first.</small>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label class="mb-0"><input type="checkbox" name="is_active" value="1" {{ $value('is_active', $plan ? $plan->is_active : 1) ? 'checked' : '' }}> <b>Active</b> — customers can buy this plan</label>
                                        </div>
                                    </div>
                                    @php $btnText = "<i class=\"ft-save\"></i> " . ($plan ? "Save plan" : "Create plan"); @endphp
                                    <x-button :btnText="$btnText" btnType="primary" btnSize="sm" type="submit" btnId="hotspot_plan_save" :readOnly="$readonly ? 'disabled' : ''" />
                                </form>
                            </div></div>
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
</body>

</html>
