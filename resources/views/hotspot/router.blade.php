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
    <title>Hypbits - Hotspot Router</title>
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
                    <h3 class="content-header-title">Hotspot Router</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotspot.routers') }}">Routers</a></li>
                                <li class="breadcrumb-item"><a href="#">{{ $router->router_name }}</a></li>
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
                        @php $btnText = "<i class=\"ft-arrow-left\"></i> Back to Hotspot Routers"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.routers')" />
                    </div>
                </div>
                @php
                    $disabled = ($readonly || !$write_allowed) ? "disabled" : "";
                @endphp
                @if (!$write_allowed)
                    <div class="alert alert-warning">This router is not on the hotspot write allowlist for this environment, so setup, adopt and remove are disabled here. (Development safety: only test routers can be changed.)</div>
                @endif
                <div class="row">
                    <div class="col-lg-5 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-server"></i> {{ $router->router_name }}</h4></div>
                            <div class="card-content"><div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th style="width:45%">Status</th><td>{!! $detect ? '<span class="badge badge-success">Online</span>' : '<span class="badge badge-danger">Offline</span> <small>' . e($offline_reason) . '</small>' !!}</td></tr>
                                    @if ($detect)
                                        <tr><th>RouterOS</th><td>{{ $detect['health']['version'] }}</td></tr>
                                        <tr><th>Uptime</th><td>{{ $detect['health']['uptime'] }}</td></tr>
                                        <tr><th>Router DNS answers clients</th><td>{{ $detect['dns_remote_requests'] ? 'Yes' : 'No' }}</td></tr>
                                    @endif
                                    <tr><th>Managed by Hypbits</th><td>{!! $router->hotspot_enabled ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>' !!}</td></tr>
                                    @if ($router->hotspot_enabled)
                                        <tr><th>Hotspot server</th><td>{{ $router->hotspot_server_name }} on {{ $router->hotspot_interface }}</td></tr>
                                    @endif
                                    <tr><th>Portal key</th><td>{!! $router->hotspot_key ? '<code>' . e($router->hotspot_key) . '</code>' : '—' !!}</td></tr>
                                    <tr><th>Last sync</th><td>{{ $router->hotspot_last_sync ? date('D jS M Y g:i:sA', strtotime($router->hotspot_last_sync)) : '—' }}</td></tr>
                                </table>
                                @if ($router->hotspot_enabled)
                                    @php $btnText = "<i class=\"ft-pause\"></i> Stop managing"; @endphp
                                    <x-button :btnText="$btnText" btnType="warning" btnSize="sm" btnId="hotspot_stop_open" :readOnly="$readonly ? 'disabled' : ''" otherAttributes="data-toggle=modal data-target=#hotspot_stop_modal" />
                                @endif
                                @php $btnText = "<i class=\"ft-trash-2\"></i> Remove from router"; @endphp
                                <x-button :btnText="$btnText" btnType="danger" btnSize="sm" btnId="hotspot_remove_open" :readOnly="$disabled" otherAttributes="data-toggle=modal data-target=#hotspot_remove_modal" />
                            </div></div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-12">
                        @if ($detect)
                            @if (count($detect['hotspots']) > 0)
                                <div class="card">
                                    <div class="card-header"><h4 class="card-title"><i class="ft-wifi"></i> Hotspot servers on the router</h4></div>
                                    <div class="card-content"><div class="card-body">
                                        <table class="table table-sm">
                                            <thead><tr><th>Name</th><th>Interface</th><th>Profile</th><th></th></tr></thead>
                                            <tbody>
                                                @foreach ($detect['hotspots'] as $hotspot)
                                                    <tr>
                                                        <td>{{ $hotspot['name'] ?? '' }}</td>
                                                        <td>{{ $hotspot['interface'] ?? '' }}</td>
                                                        <td>{{ $hotspot['profile'] ?? '' }}</td>
                                                        <td>
                                                            @if ($router->hotspot_enabled && $router->hotspot_server_name == ($hotspot['name'] ?? ''))
                                                                <span class="badge badge-success mr-1">Managed</span>
                                                                @if (($hotspot['name'] ?? '') === 'hypbits-hs')
                                                                    @php $btnText = "<i class=\"ft-trash-2\"></i> Remove"; @endphp
                                                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :readOnly="$disabled" otherAttributes="data-toggle=modal data-target=#hotspot_remove_modal" />
                                                                @else
                                                                    @php $btnText = "<i class=\"ft-pause\"></i> Stop managing"; @endphp
                                                                    <x-button :btnText="$btnText" btnType="warning" btnSize="sm" :readOnly="$readonly ? 'disabled' : ''" otherAttributes="data-toggle=modal data-target=#hotspot_stop_modal" />
                                                                @endif
                                                            @else
                                                                @php
                                                                    $btnText = "<i class=\"ft-check\"></i> Adopt";
                                                                    $adoptAttributes = "data-toggle=modal data-target=#hotspot_adopt_modal data-server=\"" . e($hotspot['name'] ?? '') . "\" data-interface=\"" . e($hotspot['interface'] ?? '') . "\"";
                                                                @endphp
                                                                <x-button :btnText="$btnText" btnType="success" btnSize="sm" :readOnly="$disabled" :otherAttributes="$adoptAttributes" otherClasses="hotspot-adopt-open mr-1" />
                                                                @if (($hotspot['name'] ?? '') !== 'hypbits-hs')
                                                                    @php
                                                                        $btnText = "<i class=\"ft-trash-2\"></i> Delete";
                                                                        $deleteAttributes = "data-toggle=modal data-target=#hotspot_delete_server_modal data-server=\"" . e($hotspot['name'] ?? '') . "\" data-interface=\"" . e($hotspot['interface'] ?? '') . "\"";
                                                                    @endphp
                                                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :readOnly="$disabled" :otherAttributes="$deleteAttributes" />
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div></div>
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><i class="ft-plus-circle"></i> Set up a new hotspot</h4></div>
                                <div class="card-content"><div class="card-body">
                                    @if (count($usable_interfaces) == 0 && count($radios) == 0)
                                        <p class="text-warning mb-0">No free interface on this router. The hotspot needs its own port or bridge with no addresses on it (not the WAN, the VPN, a PPPoE server, a bridge with clients or a bridge port). Free up or add a port and reload this page.</p>
                                    @else
                                        <p class="text-muted">Creates a hotspot that Hypbits manages: an address, address pool, DHCP server and hotspot server on the interface you choose (all named <code>hypbits-hs…</code>), and lets customers reach the payment portal ({{ $portal_host ?: 'not configured' }}). Nothing else on the router is changed unless you tick it below.</p>
                                        <form method="POST" action="{{ route('hotspot.router_setup', $router->router_id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="interface"><b>Interface</b></label>
                                                    <select class="form-control" name="interface" id="interface" {{ count($radios) > 0 ? '' : 'required' }}>
                                                        @if (count($radios) > 0)
                                                            <option value="">No wired port (Wi-Fi only)</option>
                                                        @endif
                                                        @foreach ($usable_interfaces as $interface)
                                                            <option value="{{ $interface }}">{{ $interface }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">The port customers plug into. With Wi-Fi as well, both join one hotspot.</small>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="subnet"><b>Hotspot network</b></label>
                                                    <input class="form-control" type="text" name="subnet" id="subnet" value="{{ $suggested_subnet }}" required>
                                                    <small class="text-muted">A free /24. The router becomes .1, customers get .10–.254.</small>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="dns_name"><b>Login page name</b> <span class="text-muted">{{ $login_https ? '(required)' : '(optional)' }}</span></label>
                                                    <input class="form-control" type="text" name="dns_name" id="dns_name" placeholder="e.g. wifi.yourisp.co.ke" {{ $login_https ? 'required' : '' }}>
                                                    <small class="d-block text-muted mt-1">
                                                        The address of the router's own login page, e.g. <code>wifi.yourisp.co.ke</code>. Use a dedicated subdomain of your domain, never your main website's domain (customers on the hotspot would reach the router instead of your website).
                                                        @if ($login_https)
                                                            The login page uses <b>HTTPS</b>: the router requests a free Let's Encrypt certificate for this name, so the name needs a public DNS record pointing at the router's public IP and port 80 must be reachable from the internet. If the certificate can't be issued, the login page stays on HTTP and the activity log says why.
                                                        @else
                                                            Test mode: the login page uses plain <b>HTTP</b>, and the name works on the hotspot without any public DNS record. Leave it empty to use the router's IP address.
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            @if (count($radios) > 0)
                                                <div class="border rounded p-1 mb-1">
                                                    <label class="mb-0"><input type="checkbox" name="wifi" value="1" id="hotspot_wifi" checked> <b>Broadcast a hotspot Wi-Fi network</b></label>
                                                    <small class="d-block text-muted mb-1">An open network (no password: customers log in on the hotspot page). If the radio already broadcasts a network, that network is kept and the hotspot is added as a second network on the same radio. If the radio is switched off, it is switched on for the hotspot. With a wired port, both are put in one bridge, <code>hypbits-hs-bridge</code>. The radio restarts for a few seconds while the network is added, so devices on its existing network reconnect briefly.</small>
                                                    <div class="row">
                                                        <div class="col-md-6 form-group mb-0">
                                                            <label for="radio"><b>Radio</b></label>
                                                            <select class="form-control" name="radio" id="radio">
                                                                @foreach ($radios as $radio)
                                                                    <option value="{{ $radio['name'] }}">{{ $radio['name'] }}{{ $radio['band'] ? ' (' . $radio['band'] . ')' : '' }} — {{ $radio['in_use'] ? 'in use: "' . $radio['ssid'] . '", hotspot added as a second network' : 'off, will be switched on' }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 form-group mb-0">
                                                            <label for="ssid"><b>Wi-Fi network name</b></label>
                                                            <input class="form-control" type="text" name="ssid" id="ssid" maxlength="32" placeholder="e.g. MyISP Free WiFi">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group mb-1">
                                                <label class="mb-0"><input type="checkbox" name="allow_dns" value="1" {{ $detect['dns_remote_requests'] ? 'disabled checked' : 'checked' }}> Let the router answer DNS for hotspot customers</label>
                                                <small class="d-block text-muted">Changes the router's DNS setting (<code>allow-remote-requests=yes</code>, and sets 8.8.8.8 / 1.1.1.1 if no DNS servers are set). The hotspot login page does not work without it. Make sure your firewall blocks DNS from the internet side.{{ $detect['dns_remote_requests'] ? ' Already on.' : '' }}</small>
                                            </div>
                                            @php
                                                $covered = false;
                                                foreach ($detect['masquerades'] as $masq) {
                                                    if ($masq['src-address'] === '' ) { $covered = true; }
                                                }
                                            @endphp
                                            <div class="form-group">
                                                <label class="mb-0"><input type="checkbox" name="add_masquerade" value="1" {{ $covered ? '' : 'checked' }}> Add internet sharing (NAT masquerade) for the hotspot network</label>
                                                <small class="d-block text-muted">{{ $covered ? 'The router already has a masquerade rule for all addresses, so this is usually not needed.' : 'The router has no masquerade rule covering all addresses, so customers need this to reach the internet.' }} The rule is tagged <code>hypbits-hotspot</code>.</small>
                                            </div>
                                            @php $btnText = "<i class=\"ft-eye\"></i> Preview commands (dry run)"; @endphp
                                            <x-button :btnText="$btnText" btnType="info" btnSize="sm" type="submit" btnId="hotspot_dry_run" otherAttributes="name=dry_run value=1" otherClasses="mr-1" :readOnly="$disabled" />
                                            @php $btnText = "<i class=\"ft-play\"></i> Set up hotspot"; @endphp
                                            <x-button :btnText="$btnText" btnType="primary" btnSize="sm" type="submit" btnId="hotspot_setup" :readOnly="$disabled" />
                                            <small class="d-block text-muted mt-1">Preview lists every command the setup would send to the router, without changing anything.</small>
                                        </form>
                                    @endif
                                </div></div>
                            </div>
                        @else
                            <div class="card"><div class="card-content"><div class="card-body">
                                <p class="text-danger mb-0">The router is offline ({{ $offline_reason }}). Setup and adopt need the router online. Reload this page when it is back.</p>
                            </div></div></div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-activity"></i> Activity</h4></div>
                            <div class="card-content"><div class="card-body" id="hotspot_activity" data-url="{{ route('hotspot.router_activity', $router->router_id) }}">
                                @include("hotspot.partials.router-activity", ["activities" => $activities])
                            </div></div>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="hotspot_stop_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('hotspot.router_stop', $router->router_id) }}">
                                @csrf
                                <div class="modal-header bg-warning white">
                                    <h4 class="modal-title white">Stop managing the hotspot on {{ $router->router_name }}?</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">Hypbits stops using this router's hotspot for sales. <b>Nothing is changed on the router</b>: the hotspot keeps running as it is. You can adopt or set it up again at any time.</p>
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-pause\"></i> Stop managing"; @endphp
                                    <x-button :btnText="$btnText" btnType="warning" btnSize="sm" type="submit" btnId="hotspot_stop" :readOnly="$readonly ? 'disabled' : ''" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="hotspot_adopt_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('hotspot.router_adopt', $router->router_id) }}">
                                @csrf
                                <input type="hidden" name="server" id="hotspot_adopt_server" value="">
                                <div class="modal-header bg-success white">
                                    <h4 class="modal-title white">Adopt hotspot server <span id="hotspot_adopt_name"></span>?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Hypbits will sell access on this hotspot server (on <b id="hotspot_adopt_interface"></b>). It only adds a walled-garden entry so customers can reach the payment portal; the server, its profile and your existing users are not changed.</p>
                                    <p class="mb-0">"Remove from router" later removes only that walled-garden entry and leaves this server in place.</p>
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-check\"></i> Adopt"; @endphp
                                    <x-button :btnText="$btnText" btnType="success" btnSize="sm" type="submit" btnId="hotspot_adopt" :readOnly="$disabled" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="hotspot_delete_server_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('hotspot.router_delete_server', $router->router_id) }}">
                                @csrf
                                <input type="hidden" name="server" id="hotspot_delete_server" value="">
                                <div class="modal-header bg-danger white">
                                    <h4 class="modal-title white">Delete hotspot server <span id="hotspot_delete_name"></span>?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>This deletes the hotspot server <b id="hotspot_delete_name_2"></b> (on <b id="hotspot_delete_interface"></b>) from {{ $router->router_name }}. Customers connected to it are disconnected and it stops showing a login page.</p>
                                    <p>Only the server is deleted. Its profile, address pool, DHCP server and users stay on the router, because they may be used by something else; remove them in Winbox if you no longer need them.</p>
                                    <label for="hotspot_delete_confirm">Type the server name <b id="hotspot_delete_name_3"></b> to confirm</label>
                                    <input class="form-control" type="text" name="confirm" id="hotspot_delete_confirm" autocomplete="off">
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-trash-2\"></i> Delete server"; @endphp
                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" type="submit" btnId="hotspot_delete_server_submit" :readOnly="$disabled" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade text-left" id="hotspot_remove_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('hotspot.router_remove', $router->router_id) }}">
                                @csrf
                                <div class="modal-header bg-danger white">
                                    <h4 class="modal-title white">Remove the Hypbits hotspot from {{ $router->router_name }}</h4>
                                </div>
                                <div class="modal-body">
                                    <p>This deletes only what Hypbits created on the router: the <code>hypbits-hs</code> hotspot server and profile, its DHCP server, pool, address, walled-garden entries and NAT rule. Your PPPoE, bridges, queues and other settings are not touched, and an adopted hotspot server is left in place. Connected hotspot customers are disconnected, and if the hotspot has a Wi-Fi network, the radio restarts for a few seconds so devices on its other networks reconnect briefly.</p>
                                    <label for="confirm_remove">Type <b>REMOVE</b> to confirm</label>
                                    <input class="form-control" type="text" name="confirm" id="confirm_remove" autocomplete="off">
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-trash-2\"></i> Remove"; @endphp
                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" type="submit" btnId="hotspot_remove" :readOnly="$disabled" />
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
        // fill the adopt modal with the server whose Adopt button was clicked
        $("#hotspot_adopt_modal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            $("#hotspot_adopt_server").val(button.data("server"));
            $("#hotspot_adopt_name").text(button.data("server"));
            $("#hotspot_adopt_interface").text(button.data("interface"));
        });
        // fill the delete modal with the server whose Delete button was clicked
        $("#hotspot_delete_server_modal").on("show.bs.modal", function (event) {
            var button = $(event.relatedTarget);
            $("#hotspot_delete_server").val(button.data("server"));
            $("#hotspot_delete_name, #hotspot_delete_name_2, #hotspot_delete_name_3").text(button.data("server"));
            $("#hotspot_delete_interface").text(button.data("interface"));
            $("#hotspot_delete_confirm").val("");
        });
    </script>
    <script>
        // refresh the activity log while a task is queued or running
        (function () {
            var box = document.getElementById("hotspot_activity");
            function busy() {
                return box.querySelector('[data-status="queued"], [data-status="running"]') !== null;
            }
            function refresh() {
                if (!busy()) {
                    return;
                }
                fetch(box.getAttribute("data-url") + "?html=1", {headers: {"Accept": "text/html"}})
                    .then(function (response) { return response.text(); })
                    .then(function (html) {
                        var wasBusy = busy();
                        var details = document.getElementById("hotspot_activity_details");
                        var wasOpen = details && details.classList.contains("show");
                        box.innerHTML = html;
                        if (wasOpen && document.getElementById("hotspot_activity_details")) {
                            document.getElementById("hotspot_activity_details").classList.add("show");
                        }
                        if (wasBusy && !busy()) {
                            window.location.reload();
                        }
                    });
            }
            setInterval(refresh, 3000);
        })();
    </script>
</body>

</html>
