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
    <title>Hypbits - Hotspot Plans</title>
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
                    <h3 class="content-header-title">Hotspot Plans</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="#">Plans</a></li>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="ft-package"></i> Hotspot Plans</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p class="text-muted">The packages customers can buy on your hotspot, e.g. KES 20 for 1 hour. Each plan is set up on every router Hypbits manages for you. Inactive plans are hidden from customers but tickets already sold keep working.</p>
                                    <div class="mb-1">
                                        @php $btnText = "<i class=\"ft-plus\"></i> New plan"; @endphp
                                        <x-button-link :btnText="$btnText" btnType="primary" btnSize="sm" :btnLink="route('hotspot.plan_new')" :readOnly="$readonly ? 'disabled' : ''" otherClasses="mr-1" />
                                        @if (count($routers) > 0 && count($plans) > 0)
                                            <form method="POST" action="{{ route('hotspot.plans_sync') }}" class="d-inline">
                                                @csrf
                                                @php $btnText = "<i class=\"ft-refresh-cw\"></i> Sync to routers"; @endphp
                                                <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" type="submit" btnId="hotspot_plans_sync" :readOnly="$readonly ? 'disabled' : ''" />
                                            </form>
                                        @endif
                                    </div>
                                    @if (count($routers) == 0)
                                        <div class="alert alert-warning">No router is managed by Hypbits yet. Set one up under Hotspot &rarr; Routers and your plans are added to it automatically.</div>
                                    @endif
                                    @if (count($plans) == 0)
                                        <p class="text-muted mb-0">No plans yet. Add your first plan, e.g. "1 hour" for KES 20.</p>
                                    @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Plan</th><th>Price</th><th>Duration</th><th>Speed (up / down)</th><th>Data</th><th>Devices</th><th>Status</th>
                                                    @foreach ($routers as $router)
                                                        <th>{{ $router->router_name }}</th>
                                                    @endforeach
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($plans as $plan)
                                                    <tr>
                                                        <td><b>{{ $plan->plan_name }}</b><br><small class="text-muted">Order {{ $plan->sort_order }}</small></td>
                                                        <td>Kes {{ number_format($plan->price, $plan->price == floor($plan->price) ? 0 : 2) }}</td>
                                                        <td>{{ \App\Classes\HotspotPlans::durationText($plan->duration_minutes) }}<br><small class="text-muted">{{ $plan->validity_type == 'paused' ? 'while connected' : 'from first login' }}</small></td>
                                                        <td>{{ \App\Classes\HotspotPlans::speedText($plan->upload_speed) }} / {{ \App\Classes\HotspotPlans::speedText($plan->download_speed) }}</td>
                                                        <td>{{ \App\Classes\HotspotPlans::dataText($plan->data_limit_mb) }}</td>
                                                        <td>{{ $plan->shared_users }}</td>
                                                        <td>{!! $plan->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' !!}</td>
                                                        @foreach ($routers as $router)
                                                            @php
                                                                $task = $last_sync_task[$router->router_id] ?? null;
                                                                $synced = $router->hotspot_last_sync && $router->hotspot_last_sync >= $plan->date_changed;
                                                                if ($synced) { $sync_badge = '<span class="badge badge-success">In sync</span>'; }
                                                                elseif ($task && in_array($task->status, ['queued', 'running'])) { $sync_badge = '<span class="badge badge-info">Updating</span>'; }
                                                                elseif ($task && $task->status == 'failed') { $sync_badge = '<span class="badge badge-danger">Failed</span>'; }
                                                                else { $sync_badge = '<span class="badge badge-warning">Pending</span>'; }
                                                            @endphp
                                                            <td>{!! $sync_badge !!}</td>
                                                        @endforeach
                                                        <td class="text-nowrap">
                                                            @php $btnText = "<i class=\"ft-edit\"></i> Edit"; @endphp
                                                            <x-button-link :btnText="$btnText" btnType="primary" btnSize="sm" :btnLink="route('hotspot.plan_edit', $plan->plan_id)" otherClasses="mr-1" />
                                                            <form method="POST" action="{{ route('hotspot.plan_toggle', $plan->plan_id) }}" class="d-inline">
                                                                @csrf
                                                                @php $btnText = $plan->is_active ? "<i class=\"ft-eye-off\"></i> Deactivate" : "<i class=\"ft-eye\"></i> Activate"; @endphp
                                                                <x-button :btnText="$btnText" :btnType="$plan->is_active ? 'warning' : 'success'" btnSize="sm" type="submit" otherClasses="mr-1" :readOnly="$readonly ? 'disabled' : ''" />
                                                            </form>
                                                            @php
                                                                $btnText = "<i class=\"ft-trash-2\"></i> Delete";
                                                                $deleteAttributes = "data-toggle=modal data-target=#hotspot_plan_delete_modal data-action=\"" . route('hotspot.plan_delete', $plan->plan_id) . "\" data-name=\"" . e($plan->plan_name) . "\"";
                                                            @endphp
                                                            <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :otherAttributes="$deleteAttributes" :readOnly="$readonly ? 'disabled' : ''" />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="hotspot_plan_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="#" id="hotspot_plan_delete_form">
                                @csrf
                                <div class="modal-header bg-danger white">
                                    <h4 class="modal-title white">Delete plan "<span id="hotspot_plan_delete_name"></span>"?</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">Customers can no longer buy it. Tickets already sold for this plan keep working until they run out; its router profile is removed once no ticket uses it. To hide it for a while instead, use Deactivate.</p>
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-trash-2\"></i> Delete plan"; @endphp
                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" type="submit" btnId="hotspot_plan_delete" />
                                </div>
                            </form>
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
        $("#hotspot_plan_delete_modal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            $("#hotspot_plan_delete_form").attr("action", button.data("action"));
            $("#hotspot_plan_delete_name").text(button.data("name"));
        });
    </script>
</body>

</html>
