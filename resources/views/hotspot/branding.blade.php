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
    <title>Hypbits - Hotspot Branding</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
    <style>
        .portal-preview { width: 100%; max-width: 390px; height: 720px; border: 1px solid #e3e8ee; border-radius: 12px; display: block; margin: 0 auto; background: #f2f5f8; }
        .color-pick { width: 46px; padding: 2px; }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_branding"></x-menu>

    @php
        $value = function ($field) use ($page) {
            return old($field, $page ? $page->$field : '');
        };
        $disabled = $readonly ? 'disabled' : '';
        $scopeQuery = $router_id ? ['router_id' => $router_id] : [];
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Branding</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="#">Branding</a></li>
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
                    <div class="col-xl-7 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-image"></i> Login page branding</h4></div>
                            <div class="card-content"><div class="card-body">
                                <p class="text-muted">What customers see when they join your hotspot WiFi: the package list, the ticket code box and the "You're online" page. Changes show straight away on every router; nothing is changed on the routers.</p>
                                <div class="form-group">
                                    <label for="branding_scope"><b>Applies to</b></label>
                                    <select class="form-control" id="branding_scope" data-url="{{ route('hotspot.branding') }}">
                                        <option value="" {{ $router_id ? '' : 'selected' }}>All routers (default)</option>
                                        @foreach ($routers as $one)
                                            <option value="{{ $one->router_id }}" {{ $router_id == $one->router_id ? 'selected' : '' }}>{{ $one->router_name }}{{ in_array($one->router_id, $overrides) ? ' (own branding)' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ $router_id ? 'Fields left empty use the default branding.' : 'Pick a router to give it its own branding. Fields left empty use the text shown in grey.' }}</small>
                                </div>
                                <form method="POST" action="{{ route('hotspot.branding_save') }}" enctype="multipart/form-data" id="branding_form">
                                    @csrf
                                    <input type="hidden" name="router_id" value="{{ $router_id }}">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="title"><b>Title</b></label>
                                            <input class="form-control preview-field" type="text" name="title" id="title" maxlength="150" value="{{ $value('title') }}" placeholder="{{ $defaults['title'] }}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="support_phone"><b>Support phone</b></label>
                                            <input class="form-control preview-field" type="text" name="support_phone" id="support_phone" maxlength="20" value="{{ $value('support_phone') }}" placeholder="{{ $defaults['support_phone'] }}">
                                        </div>
                                        <div class="col-12 form-group">
                                            <label for="welcome_text"><b>Welcome text</b></label>
                                            <textarea class="form-control preview-field" name="welcome_text" id="welcome_text" rows="2" maxlength="500" placeholder="{{ $defaults['welcome_text'] }}">{{ $value('welcome_text') }}</textarea>
                                        </div>
                                        @foreach (['primary_color' => 'Main colour (buttons)', 'secondary_color' => 'Header colour'] as $field => $label)
                                            <div class="col-md-6 form-group">
                                                <label for="{{ $field }}"><b>{{ $label }}</b></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <input class="form-control color-pick" type="color" id="{{ $field }}_pick" value="{{ $value($field) ?: $defaults[$field] }}" data-target="{{ $field }}">
                                                    </div>
                                                    <input class="form-control preview-field" type="text" name="{{ $field }}" id="{{ $field }}" maxlength="7" value="{{ $value($field) }}" placeholder="{{ $defaults[$field] }}">
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-md-6 form-group">
                                            <label for="logo"><b>Logo</b></label>
                                            <input class="form-control" type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/webp">
                                            <small class="text-muted">PNG, JPG or WEBP up to 2 MB. It is shrunk for phones. {{ $page && $page->logo_path ? '' : ($router_id ? 'Empty: the default logo.' : 'Empty: your organization logo.') }}</small>
                                            @if ($page && $page->logo_path)
                                                <div class="mt-1"><img src="{{ asset($page->logo_path) }}" alt="" style="max-height: 40px;" class="mr-1"><label class="mb-0"><input type="checkbox" name="remove_logo" value="1" id="remove_logo"> Remove this logo</label></div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="redirect_url"><b>Page after login</b> <span class="text-muted">(optional)</span></label>
                                            <input class="form-control" type="url" name="redirect_url" id="redirect_url" maxlength="255" value="{{ $value('redirect_url') }}" placeholder="{{ $defaults['redirect_url'] ?: 'e.g. https://yourisp.co.ke' }}">
                                            <small class="text-muted">Empty: customers see the "You're online" page with their time and data left.</small>
                                        </div>
                                    </div>
                                    @php $btnText = "<i class=\"ft-save\"></i> Save branding"; @endphp
                                    <x-button :btnText="$btnText" btnType="primary" btnSize="sm" type="submit" btnId="branding_save" :readOnly="$disabled" />
                                    @if ($router_id && $page)
                                        @php $btnText = "<i class=\"ft-rotate-ccw\"></i> Use the default branding"; @endphp
                                        <x-button :btnText="$btnText" btnType="warning" btnSize="sm" btnId="branding_delete_open" :readOnly="$disabled" otherAttributes="data-toggle=modal data-target=#branding_delete_modal" />
                                    @endif
                                </form>
                            </div></div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-smartphone"></i> Preview</h4></div>
                            <div class="card-content"><div class="card-body">
                                <iframe class="portal-preview" id="branding_preview" title="Login page preview" src="{{ route('hotspot.branding_preview', $scopeQuery) }}" data-url="{{ route('hotspot.branding_preview') }}"></iframe>
                                @if ($preview_router && $preview_router->hotspot_key)
                                    <p class="text-center mt-1 mb-0"><a href="{{ route('hs.portal', $preview_router->hotspot_key) }}" target="_blank">Open the live portal for {{ $preview_router->router_name }}</a></p>
                                @endif
                            </div></div>
                        </div>
                    </div>
                </div>
                @if ($router_id && $page)
                    <div class="modal fade text-left" id="branding_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('hotspot.branding_delete') }}">
                                    @csrf
                                    <input type="hidden" name="router_id" value="{{ $router_id }}">
                                    <div class="modal-header bg-warning white">
                                        <h4 class="modal-title white">Use the default branding on {{ $router->router_name }}?</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">This router's own title, text, colours and logo are deleted, and its login page shows the default branding again.</p>
                                    </div>
                                    <div class="modal-footer">
                                        @php $btnText = "Cancel"; @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                        @php $btnText = "<i class=\"ft-rotate-ccw\"></i> Use the default"; @endphp
                                        <x-button :btnText="$btnText" btnType="warning" btnSize="sm" type="submit" btnId="branding_delete" :readOnly="$disabled" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
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
            var scope = document.getElementById('branding_scope');
            scope.addEventListener('change', function () {
                window.location = scope.dataset.url + (scope.value ? '?router_id=' + encodeURIComponent(scope.value) : '');
            });

            var frame = document.getElementById('branding_preview');
            var routerId = @json($router_id);
            var chosenLogo = null;
            var timer = null;
            function refresh() {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    var params = [];
                    if (routerId) { params.push('router_id=' + encodeURIComponent(routerId)); }
                    var fields = document.querySelectorAll('.preview-field');
                    for (var i = 0; i < fields.length; i++) {
                        params.push(fields[i].name + '=' + encodeURIComponent(fields[i].value));
                    }
                    var remove = document.getElementById('remove_logo');
                    if (remove && remove.checked) { params.push('remove_logo=1'); }
                    frame.src = frame.dataset.url + '?' + params.join('&');
                }, 400);
            }
            // a newly chosen logo is shown in the preview before it is uploaded
            frame.addEventListener('load', function () {
                if (chosenLogo) {
                    frame.contentWindow.postMessage({ logo: chosenLogo }, window.location.origin);
                }
            });

            var fields = document.querySelectorAll('.preview-field');
            for (var i = 0; i < fields.length; i++) {
                fields[i].addEventListener('input', refresh);
            }
            var picks = document.querySelectorAll('.color-pick');
            for (var j = 0; j < picks.length; j++) {
                picks[j].addEventListener('input', function (e) {
                    document.getElementById(e.target.dataset.target).value = e.target.value;
                    refresh();
                });
            }
            var remove = document.getElementById('remove_logo');
            if (remove) { remove.addEventListener('change', refresh); }
            document.getElementById('logo').addEventListener('change', function (e) {
                var file = e.target.files[0];
                if (!file) { chosenLogo = null; refresh(); return; }
                var reader = new FileReader();
                reader.onload = function () {
                    chosenLogo = reader.result;
                    frame.contentWindow.postMessage({ logo: chosenLogo }, window.location.origin);
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</body>

</html>
