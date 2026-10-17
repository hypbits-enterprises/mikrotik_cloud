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
    <title>Hypbits - Hotspot Dashboard</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_dashboard"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Hotspot Dashboard</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Dashboard</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card bg-gradient-x-blue-cyan">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-top">
                                            <i class="ft-dollar-sign text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right align-self-bottom mt-3">
                                            <span class="d-block mb-1 font-medium-1">Today's Sales</span>
                                            <h1 class="text-white mb-0">Kes {{ number_format($todays_sales) }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card bg-gradient-x-blue-green">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-top">
                                            <i class="ft-users text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right align-self-bottom mt-3">
                                            <span class="d-block mb-1 font-medium-1">Active Users</span>
                                            <h1 class="text-white mb-0">{{ number_format($active_users) }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card bg-gradient-x-orange-yellow">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-top">
                                            <i class="ft-tag text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right align-self-bottom mt-3">
                                            <span class="d-block mb-1 font-medium-1">Tickets Sold Today</span>
                                            <h1 class="text-white mb-0">{{ number_format($tickets_today) }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card bg-gradient-x-purple-blue">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="align-self-top">
                                            <i class="ft-wifi text-white font-large-2 float-left"></i>
                                        </div>
                                        <div class="media-body text-white text-right align-self-bottom mt-3">
                                            <span class="d-block mb-1 font-medium-1">Hotspot Routers</span>
                                            <h1 class="text-white mb-0">{{ number_format($hotspot_routers) }}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (\App\Classes\HotspotAccess::can(session("priviledges"), "Hotspot Payments"))
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="ft-credit-card"></i> M-Pesa</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p>Hotspot customers pay into your own Paybill/Till using the M-Pesa keys in your account settings. Check that M-Pesa accepts them before you start selling.</p>
                                    @php $btnText = "<i class=\"ft-check-circle\"></i> Test M-Pesa credentials"; @endphp
                                    <x-button :btnText="$btnText" btnType="primary" btnSize="sm" btnId="test_mpesa_credentials" />
                                    <p class="mt-1 mb-0" id="test_mpesa_result"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Getting started</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p>The hotspot module is being set up for your account. Plans, tickets, payments and router setup will appear in the menu as they become available.</p>
                                    <p class="mb-0">Use the dashboard selector at the top (<b>Hotspot</b> &#9662;) and pick <b>ISP Billing</b> to go back to your ISP billing dashboard at any time.</p>
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
        (function () {
            var button = document.getElementById("test_mpesa_credentials");
            if (!button) {
                return;
            }
            var result = document.getElementById("test_mpesa_result");
            button.addEventListener("click", function () {
                button.disabled = true;
                result.className = "mt-1 mb-0 text-muted";
                result.textContent = "Contacting M-Pesa...";
                fetch("{{ route('hotspot.mpesa_test') }}", {
                    method: "POST",
                    headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json"}
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    result.className = "mt-1 mb-0 " + (data.ok ? "text-success" : "text-danger");
                    result.textContent = data.message;
                }).catch(function () {
                    result.className = "mt-1 mb-0 text-danger";
                    result.textContent = "The test could not be run. Reload the page and try again.";
                }).finally(function () {
                    button.disabled = false;
                });
            });
        })();
    </script>
</body>

</html>
