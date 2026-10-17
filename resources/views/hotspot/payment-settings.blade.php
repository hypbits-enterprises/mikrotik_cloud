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
    <title>Hypbits - Payment Settings</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_payments"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Payment Settings</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotspot.payments') }}">Payments</a></li>
                                <li class="breadcrumb-item"><a href="#">Settings</a></li>
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
                @php $disabled = $readonly ? 'disabled' : ''; @endphp
                <div class="row mb-1">
                    <div class="col-12">
                        @php $btnText = "<i class=\"ft-arrow-left\"></i> Back to Payments"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.payments')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-settings"></i> How customers pay for hotspot</h4></div>
                            <div class="card-content"><div class="card-body">
                                <form method="POST" action="{{ route('hotspot.payment_settings_save') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="d-block"><input type="radio" name="mode" value="shared" {{ $mode === 'shared' ? 'checked' : '' }}> <b>Same Paybill as billing</b> ({{ $billing_paybill ?: 'not set' }})</label>
                                        <small class="text-muted d-block mb-1">Hotspot customers pay to your billing Paybill with the account number the login page gives them. Those payments are recognised and kept apart from your PPPoE client payments. A payment with a wrong account number can be moved to hotspot from its transaction page.</small>
                                        <label class="d-block"><input type="radio" name="mode" value="separate" {{ $mode === 'separate' ? 'checked' : '' }}> <b>Separate Paybill or Till for hotspot</b></label>
                                        <small class="text-muted d-block">Hotspot money goes to its own number, with its own M-Pesa (Daraja) keys.</small>
                                    </div>
                                    <div id="separate_fields" class="{{ $mode === 'separate' ? '' : 'd-none' }}">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="shortcode"><b>Hotspot Paybill / Till number</b></label>
                                                <input class="form-control" type="text" name="shortcode" id="shortcode" value="{{ $shortcode }}" inputmode="numeric" maxlength="8">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="shortcode_type"><b>Type</b></label>
                                                <select class="form-control" name="shortcode_type" id="shortcode_type">
                                                    <option value="paybill" {{ $shortcode_type === 'paybill' ? 'selected' : '' }}>Paybill (customers type an account number)</option>
                                                    <option value="till" {{ $shortcode_type === 'till' ? 'selected' : '' }}>Till (Buy Goods, no account number)</option>
                                                </select>
                                            </div>
                                            @foreach (['hotspot_consumer_key' => 'Consumer key', 'hotspot_consumer_secret' => 'Consumer secret', 'hotspot_passkey' => 'Passkey'] as $field => $label)
                                                <div class="col-md-4 form-group">
                                                    <label for="{{ $field }}"><b>{{ $label }}</b> {!! $keys_set[$field] ? '<span class="badge badge-success">set</span>' : '<span class="badge badge-warning">not set</span>' !!}</label>
                                                    <input class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" autocomplete="new-password" placeholder="{{ $keys_set[$field] ? 'Leave empty to keep' : '' }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted d-block mb-1">The keys are stored for your account only and are never shown again. A Till takes payments by M-Pesa prompt; customers who pay it from the M-Pesa menu enter their M-Pesa code on the login page.</small>
                                    </div>
                                    @php $btnText = "<i class=\"ft-save\"></i> Save"; @endphp
                                    <x-button :btnText="$btnText" btnType="primary" btnSize="sm" type="submit" btnId="payment_settings_save" :readOnly="$disabled" />
                                </form>
                            </div></div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-link"></i> Hotspot C2B link</h4></div>
                            <div class="card-content"><div class="card-body">
                                @if ($config['mode'] !== 'separate')
                                    <p class="text-muted mb-0">Only needed for a separate hotspot Paybill or Till. With the same Paybill, payments keep arriving through your billing link.</p>
                                @elseif (!$links)
                                    <p class="text-warning mb-0">Your account has no hotspot callback keys yet. Ask Hypbits to save your hotspot settings in the manager.</p>
                                @else
                                    <p>Safaricom sends every payment to {{ $config['shortcode'] }} to this link, which records it as a hotspot payment:</p>
                                    <p><small><code>{{ $links['confirmation'] }}</code></small></p>
                                    <form method="POST" action="{{ route('hotspot.payment_register_c2b') }}">
                                        @csrf
                                        @php $btnText = "<i class=\"ft-upload-cloud\"></i> Register with Safaricom"; @endphp
                                        <x-button :btnText="$btnText" btnType="info" btnSize="sm" type="submit" btnId="payment_register_c2b" :readOnly="$disabled" />
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
    <script>
        (function () {
            var radios = document.querySelectorAll('input[name=mode]');
            for (var i = 0; i < radios.length; i++) {
                radios[i].addEventListener('change', function (e) {
                    document.getElementById('separate_fields').className = e.target.value === 'separate' ? '' : 'd-none';
                });
            }
        })();
    </script>
</body>

</html>
