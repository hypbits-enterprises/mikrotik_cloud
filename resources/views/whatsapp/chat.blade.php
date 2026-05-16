<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - Chat: {{ ucwords(strtolower($client->client_name)) }}</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <x-css></x-css>
    <style>
        /* ── Chat application styles (Chameleon pattern) ── */
        .chat-window-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 230px);
            min-height: 420px;
        }
        .chat-window-card .card-content { flex: 1; overflow: hidden; }

        .chats {
            height: 100%;
            overflow-y: auto;
            padding: 16px 12px;
            background: #f9f9f9;
        }

        /* Outbound (.chat) = right; Inbound (.chat-left) = left */
        .chat {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            margin-bottom: 14px;
        }
        .chat.chat-left {
            flex-direction: row;
            justify-content: flex-start;
        }

        .chat-avatar {
            flex-shrink: 0;
        }
        .chat .chat-avatar   { order: 2; margin-left: 8px; }
        .chat-left .chat-avatar { order: 1; margin-right: 8px; }

        .chat .chat-body    { order: 1; }
        .chat-left .chat-body { order: 2; }

        .chat-body { max-width: 68%; }

        .chat-content {
            border-radius: 12px 12px 0 12px;
            padding: 9px 14px;
            margin-bottom: 3px;
            font-size: 0.875rem;
            line-height: 1.5;
            box-shadow: 0 1px 2px rgba(0,0,0,.08);
            background: #dcf8c6;
            color: #1a1a1a;
        }
        .chat-left .chat-content {
            border-radius: 12px 12px 12px 0;
            background: #ffffff;
            border: 1px solid #e8e8e8;
        }
        .chat-content p { margin: 0; }
        .chat-content .chat-time {
            font-size: 0.68rem;
            color: #888;
            text-align: right;
            margin-top: 4px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
        }
        .chat-left .chat-content .chat-time { justify-content: flex-start; }
        .chat-content .tpl-label {
            font-size: 0.68rem;
            color: #666;
            margin-bottom: 3px;
        }

        .chat-avatar .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
        }
        .avatar-outbound { background: #25d366; }
        .avatar-inbound  { background: #5a67d8; }

        /* Date separator */
        .chat-date-sep {
            text-align: center;
            margin: 10px 0;
            font-size: 0.72rem;
            color: #aaa;
            position: relative;
        }
        .chat-date-sep::before, .chat-date-sep::after {
            content: '';
            display: inline-block;
            width: 30%;
            height: 1px;
            background: #ddd;
            vertical-align: middle;
            margin: 0 6px;
        }

        /* Window alert */
        .window-alert {
            border-left: 4px solid #f90;
            background: #fffbf0;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        /* Compose footer */
        .chat-compose {
            border-top: 1px solid #e8e8e8;
            background: #fff;
        }
        .chat-compose .compose-tabs .nav-link {
            padding: 8px 14px;
            font-size: 0.82rem;
            border-radius: 0;
        }
        .chat-compose .compose-body {
            padding: 12px 14px;
        }
        .chat-compose textarea, .chat-compose select {
            border-radius: 6px;
            font-size: 0.87rem;
        }

        /* Client sidebar */
        .client-info-card { position: sticky; top: 16px; }
        .client-avatar-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #5a67d8;
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="whatsapp"></x-menu>

    @php
        $priviledges   = session("priviledges");
        $readonly      = readOnly($priviledges, "SMS");
        $clientInitial = strtoupper(substr(trim($client->client_name), 0, 1));
        $clientName    = ucwords(strtolower($client->client_name));
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">
                        <i class="fa-brands fa-whatsapp text-success"></i> {{ $clientName }}
                    </h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/whatsapp/chats">WhatsApp Chats</a></li>
                                <li class="breadcrumb-item active">{{ $clientName }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">

                {{-- Top nav buttons --}}
                <div class="mb-2">
                    @php
                        $btnText = "<i class='fas fa-arrow-left'></i> Back";
                        $otherClasses = ""; $btnLink = "/whatsapp/chats"; $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="secondary" btnSize="sm" toolTip="Back to Chats" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />

                    @php
                        $btnText = "<i class='ft-user'></i> Client Profile";
                        $otherClasses = "ml-1"; $btnLink = "/Clients/View/" . $client->client_id; $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="outline-primary" btnSize="sm" toolTip="View Client Profile" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />

                    @php
                        $btnText = "<i class='ft-file-text'></i> Templates";
                        $otherClasses = "ml-1"; $btnLink = "/whatsapp/templates"; $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="outline-secondary" btnSize="sm" toolTip="Manage Templates" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                </div>

                <div class="row">

                    {{-- ── Client info sidebar ── --}}
                    <div class="col-md-3 d-none d-lg-block">
                        <div class="card client-info-card">
                            <div class="card-body text-center py-3">
                                <div class="client-avatar-circle">{{ $clientInitial }}</div>
                                <h6 class="mb-0 font-weight-bold">{{ $clientName }}</h6>
                                <p class="text-muted mb-0 font-small-2">{{ $client->clients_contacts }}</p>
                                <p class="text-muted mb-2 font-small-2">Acc: {{ $client->client_account }}</p>

                                @if($withinWindow)
                                    <span class="badge badge-pill badge-success mb-2">
                                        <i class="ft-check-circle"></i> Window Open
                                    </span>
                                @else
                                    <span class="badge badge-pill badge-warning mb-2">
                                        <i class="ft-clock"></i> Window Closed
                                    </span>
                                @endif

                                @php
                                    $btnText = "<i class='ft-user'></i> View Profile";
                                    $otherClasses = "btn-block mt-1"; $btnLink = "/Clients/View/" . $client->client_id; $otherAttributes = "";
                                @endphp
                                <x-button-link btnType="outline-primary" btnSize="sm" toolTip="View Profile" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                            </div>
                            <div class="card-footer py-2 px-3" style="font-size:0.8rem;">
                                <div class="d-flex justify-content-between text-muted">
                                    <span><i class="ft-message-circle"></i> Messages</span>
                                    <strong>{{ count($messages) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Chat window ── --}}
                    <div class="col-md-12 col-lg-9">
                        <div class="card chat-window-card">

                            {{-- Header --}}
                            <div class="card-header py-2" style="flex-shrink:0;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2" style="width:34px;height:34px;border-radius:50%;background:#5a67d8;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;">
                                        {{ $clientInitial }}
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">{{ $clientName }}</h5>
                                        <small class="text-muted">{{ $client->clients_contacts }}</small>
                                    </div>
                                    <div class="ml-auto">
                                        @if($withinWindow)
                                            <span class="badge badge-success"><i class="ft-check-circle"></i> Window open</span>
                                        @else
                                            <span class="badge badge-warning"><i class="ft-clock"></i> Template only</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Flash messages --}}
                            @if(session('success_wa'))
                                <div class="alert alert-success mx-2 mt-2 mb-0 py-1 font-small-3">{{ session('success_wa') }}</div>
                            @endif
                            @if(session('error_wa'))
                                <div class="alert alert-danger mx-2 mt-2 mb-0 py-1 font-small-3">{{ session('error_wa') }}</div>
                            @endif

                            {{-- ── Chat messages ── --}}
                            <div class="card-content">
                                <div class="chats" id="chat-thread">
                                    @forelse($messages as $msg)
                                        @php
                                            $dir        = $msg->direction ?? 'outbound';
                                            $isOutbound = $dir === 'outbound';

                                            $statusIcon = match($msg->delivery_status ?? 'sent') {
                                                'delivered' => '✓✓',
                                                'read'      => '<span style="color:#34b7f1">✓✓</span>',
                                                'failed'    => '<span class="text-danger">✗</span>',
                                                default     => '✓',
                                            };

                                            $ds   = $msg->date_sent ?? '';
                                            $date = (strlen($ds) >= 12)
                                                ? date('d M H:i', mktime(
                                                    (int)substr($ds,8,2), (int)substr($ds,10,2), 0,
                                                    (int)substr($ds,4,2), (int)substr($ds,6,2), (int)substr($ds,0,4)
                                                  ))
                                                : '';
                                        @endphp

                                        <div class="chat {{ $isOutbound ? '' : 'chat-left' }}">
                                            <div class="chat-avatar">
                                                <span class="avatar-circle {{ $isOutbound ? 'avatar-outbound' : 'avatar-inbound' }}"
                                                      data-toggle="tooltip"
                                                      title="{{ $isOutbound ? 'System' : $clientName }}">
                                                    @if($isOutbound)
                                                        <i class="fa-brands fa-whatsapp" style="font-size:16px;"></i>
                                                    @else
                                                        {{ $clientInitial }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    @if($msg->template_name)
                                                        <div class="tpl-label">
                                                            <i class="ft-file-text"></i> {{ $msg->template_name }}
                                                        </div>
                                                    @endif
                                                    <p>{{ $msg->sms_content }}</p>
                                                    <div class="chat-time">
                                                        <span>{{ $date }}</span>
                                                        @if($isOutbound)
                                                            {!! $statusIcon !!}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-5">
                                            <i class="fa-brands fa-whatsapp" style="font-size:48px;color:#ccc;"></i>
                                            <p class="mt-2">No messages yet. Send the first message below.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- ── Compose area ── --}}
                            <div class="chat-compose" style="flex-shrink:0;">

                                @if(!$withinWindow)
                                    <div class="window-alert mx-3 mt-2">
                                        <i class="ft-clock text-warning"></i>
                                        <strong>24-hour window closed.</strong>
                                        The client hasn't replied in 24 hours — only template messages can be sent.
                                    </div>
                                @endif

                                @if($withinWindow)
                                    {{-- Free-form compose --}}
                                    <form method="POST" action="/whatsapp/send" id="free-form">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->client_id }}">
                                        <div class="compose-body">
                                            <div class="row no-gutters align-items-end" style="gap:6px;">
                                                <div class="col">
                                                    <div class="input-group">
                                                        <textarea name="message" id="free-msg" class="form-control" rows="1"
                                                            placeholder="Type a message…" required {{ $readonly }}
                                                            style="resize:none; border-radius:20px 0 0 20px; padding-left:14px;"></textarea>
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-success {{ $readonly }}"
                                                                style="border-radius:0 20px 20px 0; padding: 0 18px;">
                                                                <i class="ft-send"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif

                                {{-- Template send --}}
                                @if(count($templates) > 0)
                                    <form method="POST" action="/whatsapp/send-template" class="compose-body {{ $withinWindow ? 'border-top pt-2' : 'pt-2' }}">
                                        @csrf
                                        <input type="hidden" name="client_id" value="{{ $client->client_id }}">
                                        <div class="row no-gutters align-items-center" style="gap:6px;">
                                            <div class="col-auto">
                                                <small class="text-muted"><i class="ft-file-text"></i> Template:</small>
                                            </div>
                                            <div class="col">
                                                <select name="template_id" class="form-control form-control-sm" required>
                                                    <option value="" hidden>Select a template…</option>
                                                    @foreach($templates as $tpl)
                                                        <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst($tpl->category) }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                @php
                                                    $btnText = "<i class='ft-send'></i> Send";
                                                    $otherClasses = ""; $btn_id = ""; $otherAttributes = "";
                                                @endphp
                                                <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="Send template" btnType="outline-success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="compose-body border-top pt-2">
                                        <small class="text-muted">
                                            No active templates.
                                            @php $btnText = "Add templates"; $otherClasses = ""; $btnLink = "/whatsapp/templates"; $otherAttributes = ""; @endphp
                                            <x-button-link btnType="link" btnSize="sm" toolTip="" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                                        </small>
                                    </div>
                                @endif

                                <div class="px-3 pb-2" style="font-size:0.7rem; color:#bbb;">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp Business Cloud API
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer style="margin-bottom:0 !important" class="footer footer-static footer-light navbar-border navbar-shadow">
        <div class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block"><?php echo date('Y'); ?> &copy; Copyright Hypbits Enterprises</span>
        </div>
    </footer>

    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        // Scroll thread to bottom on load
        var thread = document.getElementById('chat-thread');
        if (thread) thread.scrollTop = thread.scrollHeight;

        // Auto-expand textarea
        var ta = document.getElementById('free-msg');
        if (ta) {
            ta.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }

        $('[data-toggle="tooltip"]').tooltip();

        var milli_seconds = 1200;
        setInterval(function () { if (milli_seconds-- === 0) window.location.href = '/'; }, 1000);

        // ── Message auto-poll (every 5 s) ─────────────────────────────────────
        var POLL_CLIENT_ID = {{ $client->client_id }};
        var lastMsgId = {{ count($messages) ? $messages[count($messages) - 1]->sms_id : 0 }};
        var MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        function escHtml(s) {
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function fmtDs(ds) {
            if (!ds || ds.length < 12) return '';
            var dy = ds.substr(6,2), mo = parseInt(ds.substr(4,2)), hr = ds.substr(8,2), mn = ds.substr(10,2);
            return dy + ' ' + MONTHS[mo - 1] + ' ' + hr + ':' + mn;
        }

        function renderThread(messages, client) {
            var container = document.getElementById('chat-thread');
            if (!messages || !messages.length) return;
            var initial = (client.client_name || '?').charAt(0).toUpperCase();
            var html = '', lastDate = '';

            messages.forEach(function (msg) {
                var isOut = (msg.direction || 'outbound') === 'outbound';
                var ds    = msg.date_sent || '';
                var dateLabel = ds.length >= 8 ? ds.substr(0,4)+'-'+ds.substr(4,2)+'-'+ds.substr(6,2) : '';

                if (dateLabel && dateLabel !== lastDate) {
                    lastDate = dateLabel;
                    var d = new Date(dateLabel);
                    html += '<div class="chat-date-sep">'
                          + d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear()
                          + '</div>';
                }

                var statusIcon = '';
                if (isOut) {
                    var s = msg.delivery_status || 'sent';
                    statusIcon = s === 'read'      ? '<span style="color:#34b7f1">✓✓</span>'
                               : s === 'delivered' ? '✓✓'
                               : s === 'failed'    ? '<span class="text-danger">✗</span>'
                               : '✓';
                }

                var tplLabel = msg.template_name
                    ? '<div class="tpl-label"><i class="ft-file-text"></i> ' + escHtml(msg.template_name) + '</div>'
                    : '';

                html += '<div class="chat ' + (isOut ? '' : 'chat-left') + '">';
                html += '<div class="chat-avatar"><span class="avatar-circle '
                      + (isOut ? 'avatar-outbound' : 'avatar-inbound') + '">'
                      + (isOut ? '<i class="fa-brands fa-whatsapp" style="font-size:16px;"></i>' : escHtml(initial))
                      + '</span></div>';
                html += '<div class="chat-body"><div class="chat-content">'
                      + tplLabel
                      + '<p>' + escHtml(msg.sms_content) + '</p>'
                      + '<div class="chat-time"><span>' + fmtDs(ds) + '</span>'
                      + (isOut ? statusIcon : '') + '</div>'
                      + '</div></div>';
                html += '</div>';
            });

            container.innerHTML = html;
        }

        setInterval(function () {
            fetch('/whatsapp/messages/' + POLL_CLIENT_ID)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.error || !data.messages || !data.messages.length) return;
                    var msgs  = data.messages;
                    var newId = msgs[msgs.length - 1].sms_id;
                    if (newId === lastMsgId) return;
                    lastMsgId = newId;
                    var atBottom = thread.scrollHeight - thread.scrollTop - thread.clientHeight < 120;
                    renderThread(msgs, data.client);
                    if (atBottom) thread.scrollTop = thread.scrollHeight;
                });
        }, 5000);
    </script>
</body>
</html>
