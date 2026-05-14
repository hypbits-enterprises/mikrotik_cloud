<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - Bulk WhatsApp Send</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="whatsapp"></x-menu>

    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges, "SMS");
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><i class="ft-send"></i> Bulk WhatsApp Send</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/whatsapp/chats">WhatsApp Chats</a></li>
                                <li class="breadcrumb-item active">Bulk Send</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">

                {{-- Navigation --}}
                <div class="mb-2">
                    @php
                        $btnText = "<i class='fas fa-arrow-left'></i> Back to Chats";
                        $otherClasses = "";
                        $btnLink = "/whatsapp/chats";
                        $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="secondary" btnSize="sm" toolTip="Back to WhatsApp Chats" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />

                    @php
                        $btnText = "<i class='ft-file-text'></i> Manage Templates";
                        $otherClasses = "ml-1";
                        $btnLink = "/whatsapp/templates";
                        $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="outline-secondary" btnSize="sm" toolTip="Manage Templates" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                </div>

                @if(session('success_wa'))
                    <div class="alert alert-success">{{ session('success_wa') }}</div>
                @endif
                @if(session('error_wa'))
                    <div class="alert alert-danger">{{ session('error_wa') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Send Bulk Template Message</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @if(count($templates) === 0)
                                        <div class="alert alert-warning">
                                            No active templates found.
                                            @php
                                                $btnText = "<i class='ft-file-text'></i> Add a template";
                                                $otherClasses = "ml-1";
                                                $btnLink = "/whatsapp/templates";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link btnType="warning" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                        </div>
                                    @else
                                        <form method="POST" action="/whatsapp/bulk">
                                            @csrf
                                            <div class="form-group">
                                                <label>Select Template</label>
                                                <select name="template_id" id="template_select" class="form-control" required {{ $readonly }}>
                                                    <option value="" hidden>Select a template</option>
                                                    @foreach($templates as $tpl)
                                                        <option value="{{ $tpl->id }}"
                                                            data-body="{{ $tpl->body_text }}"
                                                            data-vars="{{ $tpl->variables }}"
                                                            data-category="{{ $tpl->category }}">
                                                            {{ $tpl->name }} — {{ ucfirst($tpl->category) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Template preview --}}
                                            <div id="tpl_preview_box" class="border rounded p-2 mb-2 bg-light d-none">
                                                <small class="text-muted d-block mb-1"><strong>Template Preview</strong></small>
                                                <p id="tpl_preview_body" class="mb-1" style="font-size:0.9rem;"></p>
                                                <small class="text-muted">Variables: <span id="tpl_preview_vars"></span></small><br>
                                                <small class="text-info">Dynamic data (name, account_no, amount, phone, username) will be filled per client automatically.</small>
                                            </div>

                                            <div class="form-group">
                                                <label>Send To</label>
                                                <select name="client_group" class="form-control" required {{ $readonly }}>
                                                    <option value="" hidden>Select client group</option>
                                                    <option value="active">All Active Clients</option>
                                                    <option value="inactive">All Inactive Clients</option>
                                                    <option value="all">All Clients</option>
                                                </select>
                                            </div>

                                            <div class="alert alert-warning py-1">
                                                <i class="ft-alert-triangle"></i>
                                                Bulk send uses template messages only. All recipients will receive the
                                                selected template with their own dynamic data filled in.
                                            </div>

                                            <div class="d-flex" style="gap:6px;">
                                                @php
                                                    $btnText = "<i class='ft-send'></i> Send Bulk Message";
                                                    $otherClasses = "";
                                                    $btn_id = "";
                                                    $confirmAttr = "onclick=\"return confirm('Send this template to the selected group?')\"";
                                                @endphp
                                                <x-button :otherAttributes="$confirmAttr" :btnText="$btnText" toolTip="Send bulk WhatsApp message" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                @php
                                                    $btnText = "<i class='ft-x'></i> Cancel";
                                                    $otherClasses = "";
                                                    $btnLink = "/whatsapp/chats";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button-link btnType="danger" btnSize="sm" toolTip="Cancel" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Variable reference --}}
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dynamic Variable Reference</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <p class="text-muted" style="font-size:0.85rem;">
                                        These variables are automatically replaced with client-specific data when sending:
                                    </p>
                                    <table class="table table-sm">
                                        <thead><tr><th>Variable</th><th>Data</th></tr></thead>
                                        <tbody>
                                            <tr><td><code>name</code></td><td>Client full name</td></tr>
                                            <tr><td><code>account_no</code></td><td>Account number</td></tr>
                                            <tr><td><code>phone</code></td><td>Phone number</td></tr>
                                            <tr><td><code>amount</code></td><td>Monthly payment</td></tr>
                                            <tr><td><code>username</code></td><td>PPPoE username</td></tr>
                                        </tbody>
                                    </table>
                                    <small class="text-muted">In your template body, use <code>@{{1}}</code>, <code>@{{2}}</code> etc. as positional placeholders.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer style="margin-bottom: 0% !important" class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits Enterprises</span>
        </div>
    </footer>

    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        document.getElementById('template_select').addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            if (!opt.value) return;
            var body = opt.getAttribute('data-body');
            var vars = opt.getAttribute('data-vars');
            document.getElementById('tpl_preview_body').textContent = body;
            try { vars = JSON.parse(vars); } catch(e) {}
            document.getElementById('tpl_preview_vars').textContent = Array.isArray(vars) ? vars.join(', ') : (vars || 'none');
            document.getElementById('tpl_preview_box').classList.remove('d-none');
        });
        var milli_seconds = 1200;
        setInterval(function() { if(milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
