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
        $btnText = ""; $otherClasses = "";
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">Email Templates</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Home</a></li>
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
                                <p class="card-text text-muted mb-0">Customise the HTML email sent to clients for each automated event. Changes apply immediately — no restart needed.</p>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Template</th>
                                                    <th>Subject</th>
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
                                                    <td>{{ $tpl['subject'] }}</td>
                                                    <td>
                                                        @if($tpl['updated_at'])
                                                            {{ date('d M Y H:i', strtotime($tpl['updated_at'])) }}
                                                        @else
                                                            <span class="badge badge-secondary">Default</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-button-link href="/email-templates/{{ $tpl['name'] }}/edit" btnText="Edit" btnType="primary" btnSize="sm" :readOnly="$readonly" />
                                                        @if($tpl['updated_at'])
                                                        <x-button-link href="/email-templates/reset/{{ $tpl['name'] }}" btnText="Reset" btnType="danger" btnSize="sm" :readOnly="$readonly" otherClasses="confirm-reset" />
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

    <x-footer></x-footer>
    <x-js></x-js>
    <script>
      document.querySelectorAll('.confirm-reset').forEach(function(btn) {
          btn.addEventListener('click', function(e) {
              if (!confirm('Reset this template to its default content? Your customisations will be lost.')) {
                  e.preventDefault();
              }
          });
      });
      var milli_seconds = 1200;
      setInterval(function() { if (milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
