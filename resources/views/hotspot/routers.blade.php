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
    <title>Hypbits - Hotspot Routers</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_routers"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Hotspot Routers</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="#">Routers</a></li>
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
                                <h4 class="card-title"><i class="ft-wifi"></i> Hotspot Routers</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p class="text-muted">Your cloud routers. Status is checked live over the VPN. Open a router to set up or adopt its hotspot.</p>
                                    @if (count($routers) == 0)
                                        <p class="text-muted mb-0">You have no cloud routers yet. Add one under ISP Billing &rarr; My Routers.</p>
                                    @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr><th>Router</th><th>Status</th><th>RouterOS</th><th>Hotspot on router</th><th>Managed by Hypbits</th><th>Last sync</th><th>Last task</th><th></th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($routers as $router)
                                                    @php $last = $last_activity[$router->router_id] ?? null; @endphp
                                                    <tr class="hotspot-router-row" data-router="{{ $router->router_id }}" data-status-url="{{ route('hotspot.router_status', $router->router_id) }}">
                                                        <td><b>{{ $router->router_name }}</b><br><small class="text-muted">{{ $router->router_location }}</small></td>
                                                        <td class="router-online"><span class="badge badge-secondary">Checking...</span></td>
                                                        <td class="router-version">—</td>
                                                        <td class="router-hotspots">—</td>
                                                        <td>{!! $router->hotspot_enabled ? '<span class="badge badge-success">Yes</span> <small>' . e($router->hotspot_server_name) . ' on ' . e($router->hotspot_interface) . '</small>' : '<span class="badge badge-secondary">No</span>' !!}</td>
                                                        <td>{{ $router->hotspot_last_sync ? date('D jS M Y g:i:sA', strtotime($router->hotspot_last_sync)) : '—' }}</td>
                                                        <td>{{ $last ? $last->action . ' — ' . $last->status : '—' }}</td>
                                                        <td>
                                                            @php $btnText = "<i class=\"ft-settings\"></i> Open"; @endphp
                                                            <x-button-link :btnText="$btnText" btnType="primary" btnSize="sm" :btnLink="route('hotspot.router', $router->router_id)" />
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
        // live status per router, one request each, so an offline router never holds up the page
        document.querySelectorAll(".hotspot-router-row").forEach(function (row) {
            fetch(row.getAttribute("data-status-url"), {headers: {"Accept": "application/json"}})
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    var online = row.querySelector(".router-online");
                    if (data.online) {
                        online.innerHTML = '<span class="badge badge-success">Online</span>';
                        row.querySelector(".router-version").textContent = data.version || "—";
                        var servers = data.hotspot_servers || [];
                        row.querySelector(".router-hotspots").textContent = servers.length ? servers.join(", ") : "None";
                    } else {
                        online.innerHTML = '<span class="badge badge-danger">Offline</span>';
                        online.title = data.error || "";
                    }
                })
                .catch(function () {
                    row.querySelector(".router-online").innerHTML = '<span class="badge badge-warning">Unknown</span>';
                });
        });
    </script>
</body>

</html>
