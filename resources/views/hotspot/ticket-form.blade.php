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
    <title>Hypbits - New Ticket</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_tickets"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">New Ticket</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotspot.tickets') }}">Tickets</a></li>
                                <li class="breadcrumb-item"><a href="#">New</a></li>
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
                        @php $btnText = "<i class=\"ft-arrow-left\"></i> Back to Tickets"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.tickets')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-tag"></i> New manual ticket</h4></div>
                            <div class="card-content"><div class="card-body">
                                <p class="text-muted">For customers who pay you in cash. Each ticket is a code the customer types on the hotspot login page.</p>
                                @if (count($plans) == 0 || count($routers) == 0)
                                    <p class="text-warning mb-0">{{ count($plans) == 0 ? 'Create a plan first (Hotspot → Plans).' : 'Set up the hotspot on a router first (Hotspot → Routers).' }}</p>
                                @else
                                <form method="POST" action="{{ route('hotspot.ticket_store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="plan_id"><b>Plan</b></label>
                                            <select class="form-control" name="plan_id" id="plan_id" required>
                                                @foreach ($plans as $plan)
                                                    <option value="{{ $plan->plan_id }}" {{ old('plan_id') == $plan->plan_id ? 'selected' : '' }}>{{ $plan->plan_name }} — Kes {{ number_format($plan->price) }} ({{ \App\Classes\HotspotPlans::durationText($plan->duration_minutes) }}){{ $plan->is_active ? '' : ', inactive' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="router_id"><b>Router</b></label>
                                            <select class="form-control" name="router_id" id="router_id" required>
                                                @foreach ($routers as $router)
                                                    <option value="{{ $router->router_id }}" {{ old('router_id') == $router->router_id ? 'selected' : '' }}>{{ $router->router_name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">The code works only on this router's hotspot.</small>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="quantity"><b>How many</b></label>
                                            <input class="form-control" type="number" name="quantity" id="quantity" min="1" max="20" step="1" value="{{ old('quantity', 1) }}" required>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <label for="phone"><b>Customer phone</b> <span class="text-muted">(optional, one ticket only)</span></label>
                                            <input class="form-control" type="text" name="phone" id="phone" placeholder="e.g. 0712345678" value="{{ old('phone') }}">
                                            <label class="mt-1 mb-0"><input type="checkbox" name="send_sms" value="1" {{ old('send_sms', 1) ? 'checked' : '' }}> Send the code to this phone by SMS</label>
                                        </div>
                                    </div>
                                    @php $btnText = "<i class=\"ft-plus\"></i> Create ticket(s)"; @endphp
                                    <x-button :btnText="$btnText" btnType="primary" btnSize="sm" type="submit" btnId="hotspot_ticket_create" :readOnly="$readonly ? 'disabled' : ''" />
                                </form>
                                @endif
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
