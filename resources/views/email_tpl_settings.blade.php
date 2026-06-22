<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - Email Settings</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="sms"></x-menu>

    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges, "SMS");
        $btnText = ""; $otherClasses = ""; $btnLink = ""; $otherAttributes = "";
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><i class="ft-settings"></i> Email Settings</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/sms">SMS</a></li>
                                <li class="breadcrumb-item"><a href="/email-templates">Email Templates</a></li>
                                <li class="breadcrumb-item active">Email Settings</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12 col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">SMTP Configuration</h4>
                                <p class="card-text text-muted mb-0">Configure the outgoing mail server used to send automated email notifications to clients.</p>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="mb-2">
                                        @php $btnText = '<i class="fas fa-arrow-left"></i> Back to Email Templates'; $btnLink = "/email-templates"; $otherClasses = ""; $otherAttributes = ""; @endphp
                                        <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="false" btnId="" />
                                    </div>

                                    <form action="/email-templates/settings" method="POST">
                                        @csrf

                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label class="form-control-label">SMTP HOST</label>
                                                <input type="text" name="email_host" class="form-control" value="{{ $es['host'] ?? '' }}" placeholder="e.g. smtp.gmail.com">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-control-label">PORT</label>
                                                <input type="number" name="email_port" class="form-control" value="{{ $es['port'] ?? 587 }}" placeholder="587">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-control-label">ENCRYPTION</label>
                                                <select name="email_encryption" class="form-control">
                                                    <option value="tls" {{ ($es['encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (STARTTLS)</option>
                                                    <option value="ssl" {{ ($es['encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    <option value="none" {{ ($es['encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <label class="form-control-label">FROM NAME</label>
                                                <input type="text" name="email_from_name" class="form-control" value="{{ $es['from_name'] ?? '' }}" placeholder="Your company name">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-control-label">USERNAME / EMAIL ADDRESS</label>
                                                <input type="email" name="email_username" class="form-control" value="{{ $es['username'] ?? '' }}" placeholder="you@example.com">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-control-label">PASSWORD / APP PASSWORD</label>
                                                <input type="password" name="email_password" class="form-control" placeholder="Leave blank to keep current">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <small class="text-muted">If left blank, the system default email is used. Required when the preferred notification channel is set to Email.</small>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            @php $btnText = '<i class="ft-save"></i> Save Settings'; $otherClasses = ""; $otherAttributes = ""; @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="" btnType="primary" type="submit" btnSize="" :otherClasses="$otherClasses" :readOnly="$readonly" btnId="" />
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer style="margin-bottom:0%!important" class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits Enterprises</span>
        </div>
    </footer>

    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
      var milli_seconds = 1200;
      setInterval(function() { if (milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
