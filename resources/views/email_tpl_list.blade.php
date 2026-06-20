<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - Email Templates</title>
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
                    <h3 class="content-header-title"><i class="ft-mail"></i> Email Templates</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/sms">SMS</a></li>
                                <li class="breadcrumb-item active">Email Templates</li>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Automated Email Templates</h4>
                                <p class="card-text text-muted mb-0">Customise the HTML email sent to clients for each automated event. Changes apply immediately.</p>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="mb-2">
                                        @php $btnText = '<i class="fas fa-arrow-left"></i> Back to SMS'; $btnLink = "/sms"; $otherClasses = ""; $otherAttributes = ""; @endphp
                                        <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" btnId="" />
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Template</th>
                                                    <th>Description</th>
                                                    <th>Last Modified</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($templates as $tpl)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $tpl['label'] }}</strong><br>
                                                        <small class="text-muted">{{ $tpl['name'] }}</small>
                                                    </td>
                                                    <td><small class="text-muted">{{ $tpl['description'] }}</small></td>
                                                    <td>
                                                        @if($tpl['updated_at'])
                                                            {{ date('d M Y H:i', strtotime($tpl['updated_at'])) }}
                                                        @else
                                                            <span class="badge badge-secondary">Default</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $btnText = "Edit";
                                                            $otherClasses = "mr-1";
                                                            $btnLink = "/email-templates/{$tpl['name']}/edit";
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button-link btnType="primary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                        @if($tpl['updated_at'])
                                                            @php
                                                                $btnText = "Reset";
                                                                $otherClasses = "confirm-reset";
                                                                $otherAttributes = 'data-label="' . e($tpl['label']) . '" data-url="/email-templates/reset/' . $tpl['name'] . '"';
                                                            @endphp
                                                            <x-button btnText="Reset" btnType="danger" btnSize="sm" type="button" toolTip="" :otherAttributes="$otherAttributes" :otherClasses="$otherClasses" :readOnly="$readonly" btnId="" />
                                                        @endif
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

    {{-- Reset confirmation modal --}}
    <div class="modal fade text-left" id="reset_confirm_modal" tabindex="-1" role="dialog" aria-modal="true" style="background-color:rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger white">
                    <h4 class="modal-title text-white">Reset Template</h4>
                    <button type="button" class="close text-white" id="close_reset_modal_1" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Reset <strong id="reset_template_label"></strong> to its default content?</p>
                    <p class="text-danger mb-0"><small>Your customisations will be permanently lost.</small></p>
                </div>
                <div class="modal-footer">
                    @php $btnText = "Cancel"; $otherClasses = ""; $otherAttributes = ""; @endphp
                    <x-button btnText="Cancel" btnType="secondary" btnSize="sm" type="button" toolTip="" :otherAttributes="$otherAttributes" :otherClasses="$otherClasses" :readOnly="false" btnId="close_reset_modal_2" />
                    @php $btnText = "Yes, Reset"; $btnLink = "#"; $otherClasses = ""; $otherAttributes = ""; @endphp
                    <x-button-link btnText="Yes, Reset" btnType="danger" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :otherClasses="$otherClasses" :readOnly="false" btnLink="#" btnId="reset_confirm_link" />
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
      document.querySelectorAll('.confirm-reset').forEach(function(btn) {
          btn.addEventListener('click', function() {
              document.getElementById('reset_template_label').textContent = this.getAttribute('data-label');
              document.getElementById('reset_confirm_link').setAttribute('href', this.getAttribute('data-url'));
              $('#reset_confirm_modal').modal('show');
          });
      });
      document.getElementById('close_reset_modal_1').addEventListener('click', function() { $('#reset_confirm_modal').modal('hide'); });
      document.getElementById('close_reset_modal_2').addEventListener('click', function() { $('#reset_confirm_modal').modal('hide'); });
      var milli_seconds = 1200;
      setInterval(function() { if (milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
