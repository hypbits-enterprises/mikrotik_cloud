<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - Edit Email Template</title>
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
                    <h3 class="content-header-title"><i class="ft-mail"></i> {{ $template['label'] }}</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/sms">SMS</a></li>
                                <li class="breadcrumb-item"><a href="/email-templates">Email Templates</a></li>
                                <li class="breadcrumb-item active">{{ $template['label'] }}</li>
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
                                <h4 class="card-title">{{ $template['label'] }}</h4>
                                <p class="card-text text-muted mb-0">Click a variable chip to insert it at the cursor position in the editor.</p>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="mb-2">
                                        <a href="/email-templates" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to list</a>
                                    </div>

                                    @if($template['description'])
                                    <div class="alert alert-info mb-3" role="alert">
                                        <strong><i class="ft-info mr-1"></i> When is this sent?</strong>
                                        {{ $template['description'] }}
                                    </div>
                                    @endif

                                    {{-- Variable chips --}}
                                    <div class="mb-3">
                                        <label class="form-control-label font-weight-bold">Insert Variable</label><br>
                                        @foreach($variables as $placeholder => $label)
                                            <span class="badge badge-info var-chip mr-1 mb-1"
                                                  style="cursor:pointer;font-size:0.8rem;padding:5px 10px"
                                                  data-var="{{ $placeholder }}">
                                                {{ $label }} <small class="text-white-50">{{ $placeholder }}</small>
                                            </span>
                                        @endforeach
                                    </div>

                                    <form action="/email-templates/{{ $template['name'] }}" method="POST" onsubmit="tinymce.triggerSave()">
                                        @csrf

                                        @if($errors->any())
                                            <div class="alert alert-danger">
                                                @foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="subject" class="form-control-label">Subject Line</label>
                                            <input type="text" name="subject" id="subject"
                                                   class="form-control"
                                                   value="{{ old('subject', $template['subject']) }}"
                                                   placeholder="Email subject" required>
                                            <small class="text-muted">Variables like <code>[client_name]</code> work here too.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-control-label">Body</label>
                                            <textarea name="html_body" id="html_body" class="form-control" rows="20">{{ old('html_body', $template['body']) }}</textarea>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            @php
                                                $btnText = "Cancel";
                                                $otherClasses = "mr-1";
                                                $btnLink = "/email-templates";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="secondary" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                            @php $btnText = "Save Template"; $otherClasses = ""; @endphp
                                            <x-button btnText="{{ $btnText }}" btnType="primary" btnSize="sm" type="submit" :readOnly="$readonly" />
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
    <script src="/theme-assets/vendors/js/editors/tinymce/tinymce.min.js"></script>
    <script>
      tinymce.init({
          selector: '#html_body',
          height: 500,
          menubar: false,
          plugins: ['link', 'lists', 'table', 'code', 'image', 'paste'],
          toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright | bullist numlist | link image table | code',
          paste_as_text: false,
          content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; } p { margin: 0 0 10px; }',
          setup: function(editor) {
              window._tinyEditor = editor;
          }
      });

      document.querySelectorAll('.var-chip').forEach(function(chip) {
          chip.addEventListener('click', function() {
              var variable = this.getAttribute('data-var');
              if (window._tinyEditor) {
                  window._tinyEditor.insertContent(variable);
              } else {
                  var subj = document.getElementById('subject');
                  var pos = subj.selectionStart;
                  subj.value = subj.value.slice(0, pos) + variable + subj.value.slice(subj.selectionEnd);
                  subj.selectionStart = subj.selectionEnd = pos + variable.length;
                  subj.focus();
              }
          });
      });

      var milli_seconds = 1200;
      setInterval(function() { if (milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
