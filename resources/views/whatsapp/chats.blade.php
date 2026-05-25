<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hypbits - WhatsApp Chats</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <x-css></x-css>
    <style>
        /* ── Two-panel chat layout ── */
        .wa-app {
            display: flex;
            height: calc(100vh - 260px);
            min-height: 480px;
            background: #fff;
            overflow: hidden;
        }

        /* ── Left: contacts sidebar ── */
        .wa-sidebar {
            width: 300px;
            min-width: 300px;
            border-right: 1px solid #e8e8e8;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .wa-sidebar-header {
            padding: 12px 14px 8px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
            flex-shrink: 0;
        }
        .wa-sidebar-header h6 { margin: 0 0 8px; font-size: 0.9rem; }
        .wa-search {
            width: 100%;
            padding: 6px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            font-size: 0.82rem;
            outline: none;
            background: #f5f5f5;
        }
        .wa-search:focus { background: #fff; border-color: #25d366; }

        .wa-contacts {
            flex: 1;
            overflow-y: auto;
        }
        .wa-contact-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            transition: background .15s;
            text-decoration: none;
            color: inherit;
        }
        .wa-contact-item:hover { background: #f9f9f9; text-decoration: none; color: inherit; }
        .wa-contact-item.active { background: #e8f5e9; border-left: 3px solid #25d366; }
        .wa-contact-item.active:hover { background: #dff0e0; }

        .wa-contact-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .wa-contact-info { flex: 1; min-width: 0; }
        .wa-contact-name {
            font-size: 0.84rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        .wa-contact-preview {
            font-size: 0.73rem;
            color: #888;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        .wa-contact-meta {
            font-size: 0.68rem;
            color: #aaa;
            text-align: right;
            flex-shrink: 0;
            margin-left: 6px;
        }

        /* ── Right: chat window ── */
        .wa-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: #f0f0f0;
        }

        /* Chat top bar (client info) */
        .wa-chat-header {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
            flex-shrink: 0;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }
        .wa-chat-header:hover { background: #f9f9f9; }
        .wa-chat-header .header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .wa-chat-header .header-info { flex: 1; min-width: 0; }
        .wa-chat-header .header-info h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wa-chat-header .header-info small { color: #888; font-size: 0.72rem; }
        .wa-chat-header .header-actions { display: flex; gap: 6px; }

        /* Chat messages area */
        .wa-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
            background: #e5ddd5 url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Chat bubbles */
        .wa-bubble-row {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-end;
        }
        .wa-bubble-row.outbound { justify-content: flex-end; }
        .wa-bubble-row.inbound  { justify-content: flex-start; }

        .wa-bubble {
            max-width: 65%;
            padding: 7px 12px 5px;
            border-radius: 8px;
            font-size: 0.875rem;
            line-height: 1.45;
            box-shadow: 0 1px 2px rgba(0,0,0,.12);
            word-break: break-word;
        }
        .wa-bubble-row.outbound .wa-bubble {
            background: #dcf8c6;
            border-radius: 8px 0 8px 8px;
        }
        .wa-bubble-row.inbound .wa-bubble {
            background: #fff;
            border-radius: 0 8px 8px 8px;
        }

        .bubble-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .wa-bubble-row.outbound .bubble-avatar { margin-left: 6px; }
        .wa-bubble-row.inbound  .bubble-avatar { margin-right: 6px; }

        .bubble-meta {
            font-size: 0.67rem;
            color: #888;
            text-align: right;
            margin-top: 3px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 3px;
        }
        .inbound .bubble-meta { justify-content: flex-start; }

        .bubble-tpl-label {
            font-size: 0.67rem;
            color: #666;
            margin-bottom: 3px;
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding-bottom: 3px;
        }

        /* Date separator */
        .wa-date-sep {
            text-align: center;
            margin: 12px 0 8px;
            font-size: 0.72rem;
            color: #666;
        }
        .wa-date-sep span {
            background: rgba(255,255,255,.85);
            padding: 3px 12px;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,.1);
        }

        /* Compose area */
        .wa-compose {
            background: #f0f0f0;
            border-top: 1px solid #d0d0d0;
            padding: 10px 14px;
            flex-shrink: 0;
        }
        .wa-compose .window-closed-notice {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        .wa-compose .compose-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            margin-bottom: 6px;
        }
        .wa-compose textarea {
            flex: 1;
            resize: none;
            border-radius: 20px;
            border: 1px solid #ddd;
            padding: 8px 14px;
            font-size: 0.875rem;
            line-height: 1.4;
            background: #fff;
            outline: none;
            max-height: 100px;
        }
        .wa-compose textarea:focus { border-color: #25d366; }
        .wa-compose .send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #25d366;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .2s;
        }
        .wa-compose .send-btn:hover { background: #1da851; }
        .wa-compose .send-btn:disabled { background: #a8d5b5; cursor: default; }
        .wa-compose .tpl-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-top: 6px;
            border-top: 1px solid #e0e0e0;
        }
        .wa-compose .tpl-row select {
            flex: 1;
            font-size: 0.82rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 5px 10px;
        }

        /* Welcome screen */
        .wa-welcome {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f8f8;
            color: #999;
            gap: 12px;
        }
        .wa-welcome i { font-size: 64px; color: #25d366; opacity: .6; }
        .wa-welcome h5 { color: #555; margin: 0; }

        /* Loading */
        .wa-loading {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
            color: #888;
        }

        /* Avatar colour palette (cycle by modulo) */
        .av-0 { background: #5a67d8; }
        .av-1 { background: #e53e3e; }
        .av-2 { background: #38a169; }
        .av-3 { background: #d69e2e; }
        .av-4 { background: #805ad5; }
        .av-5 { background: #2b6cb0; }
        .av-6 { background: #c05621; }
        .av-7 { background: #2c7a7b; }
        .av-wa { background: #25d366; }

        /* Variable insert chips */
        .var-chip {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #c8e6c9;
            background: #f1f8f2;
            color: #2e7d32;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
            user-select: none;
        }
        .var-chip:hover { background: #c8e6c9; }
        .var-chip i { font-size: 0.65rem; }

        /* Stats strip */
        .wa-stats-strip {
            display: flex;
            gap: 1px;
            border-bottom: 1px solid #f0f0f0;
            background: #f5f5f5;
        }
        .wa-stat {
            flex: 1;
            text-align: center;
            padding: 5px 4px;
            font-size: 0.7rem;
            color: #777;
            background: #fff;
        }
        .wa-stat strong { display: block; font-size: 1rem; font-weight: 700; }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="whatsapp"></x-menu>

    @php
        $priviledges = session("priviledges");
        $readonly    = readOnly($priviledges, "SMS");
        $colors      = ['av-0','av-1','av-2','av-3','av-4','av-5','av-6','av-7'];
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">
                        <i class="fa-brands fa-whatsapp text-success"></i> WhatsApp Chats
                    </h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">WhatsApp Chats</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">

                @if(session('success_wa'))
                    <div class="alert alert-success py-1 mb-1">{{ session('success_wa') }}</div>
                @endif
                @if(session('error_wa'))
                    <div class="alert alert-danger py-1 mb-1">{{ session('error_wa') }}</div>
                @endif

                <div class="row">
                <div class="col-12">
                <div class="card" style="position:relative;">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fa-brands fa-whatsapp text-success"></i> Conversations
                            <span class="badge badge-pill badge-secondary ml-1" style="font-size:.7rem;">{{ count($chats) }}</span>
                        </h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li>
                                    @php $btnText = "<i class='ft-file-text'></i> Templates"; $btnLink = "/whatsapp/templates"; $otherClasses = ""; $otherAttributes = ""; @endphp
                                    <x-button-link btnType="secondary" btnSize="sm" toolTip="Manage Templates" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                </li>
                                <li>
                                    @php $btnText = "<i class='ft-send'></i> Bulk Send"; $btnLink = "/whatsapp/bulk"; $otherClasses = "ml-1"; $otherAttributes = ""; @endphp
                                    <x-button-link btnType="info" btnSize="sm" toolTip="Bulk Send" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                    <div class="card-body p-0">

                <div class="wa-app">

                    {{-- ════════════════════════════════════════
                         LEFT SIDEBAR — contacts list
                    ════════════════════════════════════════ --}}
                    <div class="wa-sidebar">

                        {{-- Sidebar header --}}
                        <div class="wa-sidebar-header">
                            <input type="text" id="contact-search" class="wa-search" placeholder="&#xf002;  Search contacts…">
                        </div>

                        {{-- Stats strip --}}
                        <div class="wa-stats-strip">
                            <div class="wa-stat"><strong class="text-primary">{{ $stats['today_c'] }}</strong>Today</div>
                            <div class="wa-stat"><strong class="text-info">{{ $stats['week_c'] }}</strong>Week</div>
                            <div class="wa-stat"><strong class="text-success">{{ $stats['month_c'] }}</strong>Month</div>
                            <div class="wa-stat"><strong class="text-secondary">{{ $stats['total'] }}</strong>Total</div>
                        </div>

                        {{-- Contacts list --}}
                        <div class="wa-contacts" id="contacts-list">
                            @forelse($chats as $idx => $chat)
                                @php
                                    $name    = ucwords(strtolower($chat->client_name));
                                    $initial = strtoupper(substr(trim($chat->client_name), 0, 1));
                                    $color   = $colors[$chat->account_id % 8];
                                    $ds      = $chat->date_sent ?? '';
                                    $chatTime = (strlen($ds) >= 12)
                                        ? date('H:i', mktime((int)substr($ds,8,2),(int)substr($ds,10,2),0,(int)substr($ds,4,2),(int)substr($ds,6,2),(int)substr($ds,0,4)))
                                        : '';
                                    $preview = mb_strlen($chat->sms_content) > 38
                                        ? mb_substr($chat->sms_content, 0, 38) . '…'
                                        : $chat->sms_content;
                                    $dirIcon = $chat->direction === 'inbound'
                                        ? '<i class="ft-corner-down-left" style="color:#25d366;font-size:.7rem;"></i> '
                                        : '<i class="ft-corner-up-right" style="color:#888;font-size:.7rem;"></i> ';
                                @endphp
                                <a class="wa-contact-item"
                                   id="contact-{{ $chat->account_id }}"
                                   data-client-id="{{ $chat->account_id }}"
                                   data-name="{{ $name }}"
                                   data-phone="{{ $chat->clients_contacts }}"
                                   data-account="{{ $chat->client_account }}"
                                   data-color="{{ $color }}"
                                   data-initial="{{ $initial }}"
                                   data-sms-id="{{ $chat->sms_id }}"
                                   href="#">
                                    <div class="wa-contact-avatar {{ $color }}">{{ $initial }}</div>
                                    <div class="wa-contact-info">
                                        <p class="wa-contact-name">{{ $name }}</p>
                                        <p class="wa-contact-preview">{!! $dirIcon !!}{{ $preview }}</p>
                                    </div>
                                    <div class="wa-contact-meta">{{ $chatTime }}</div>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4" style="font-size:.85rem;">
                                    <i class="fa-brands fa-whatsapp" style="font-size:32px;color:#ccc;"></i>
                                    <p class="mt-1 mb-0">No conversations yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ════════════════════════════════════════
                         RIGHT PANEL — chat window
                    ════════════════════════════════════════ --}}
                    <div class="wa-main" id="wa-main">

                        {{-- Welcome state --}}
                        <div class="wa-welcome" id="wa-welcome">
                            <i class="fa-brands fa-whatsapp"></i>
                            <h5>WhatsApp Messages</h5>
                            <p style="font-size:.85rem;">Select a conversation from the left panel</p>
                        </div>

                        {{-- Loading state --}}
                        <div class="wa-loading" id="wa-loading" style="display:none;">
                            <div class="spinner-border text-success" role="status" style="width:32px;height:32px;"></div>
                            <small>Loading conversation…</small>
                        </div>

                        {{-- Active chat (hidden until loaded) --}}
                        <div id="wa-chat" style="display:none; flex:1; min-height:0; flex-direction:column; display:none;">

                            {{-- Top bar: client info --}}
                            <div class="wa-chat-header" id="chat-header">
                                <div class="header-avatar av-0" id="header-avatar">A</div>
                                <div class="header-info">
                                    <h6 id="header-name">—</h6>
                                    <small id="header-sub">—</small>
                                </div>
                                <div class="header-actions">
                                    <span id="header-window-badge" class="badge badge-success" style="font-size:.72rem;"></span>
                                    <a id="header-profile-link" href="#" class="btn btn-sm btn-primary" style="padding:3px 8px;" data-toggle="tooltip" title="View Client Profile">
                                        <i class="ft-user"></i>
                                    </a>
                                    @if(!$readonly)
                                    <button id="delete-chat-btn" class="btn btn-sm btn-danger" style="padding:3px 8px;" data-toggle="tooltip" title="Delete Conversation">
                                        <i class="ft-trash-2"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Messages --}}
                            <div class="wa-messages" id="chat-messages"></div>

                            {{-- Compose --}}
                            <div class="wa-compose" id="chat-compose">
                                {{-- Free-form (shown/hidden by JS based on window status) --}}
                                <div id="compose-freeform" style="display:none;">
                                    {{-- Client variable chips --}}
                                    <div id="var-chips" style="display:flex;flex-wrap:wrap;gap:4px;padding:4px 0 6px;border-bottom:1px solid #e8e8e8;margin-bottom:6px;"></div>
                                    <div class="compose-row">
                                        <textarea id="compose-text" rows="1" placeholder="Type a message…" {{ $readonly }}></textarea>
                                        <button class="send-btn" id="send-btn" title="Send message" {{ $readonly }}>
                                            <i class="ft-navigation" style="font-size:14px;"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Window closed notice --}}
                                <div class="window-closed-notice" id="window-closed-notice" style="display:none;">
                                    <i class="ft-clock text-warning"></i>
                                    <strong>24-hour window closed.</strong>
                                    Client hasn't replied in 24 h — only template messages can be sent.
                                </div>

                                {{-- Template row (always shown when templates exist) --}}
                                <div class="tpl-row" id="compose-template-row" style="display:none;">
                                    <i class="ft-file-text text-muted" style="font-size:.8rem;"></i>
                                    <select id="tpl-select" style="flex:1;font-size:.82rem;border-radius:8px;border:1px solid #ddd;padding:5px 10px;">
                                        <option value="">Select template…</option>
                                    </select>
                                    <button class="send-btn" id="send-tpl-btn" title="Send template" {{ $readonly }}>
                                        <i class="ft-navigation" style="font-size:14px;"></i>
                                    </button>
                                </div>

                                {{-- No templates notice --}}
                                <div id="no-templates-notice" style="display:none;font-size:.78rem;color:#aaa;padding-top:4px;">
                                    No active templates. <a href="/whatsapp/templates">Manage templates</a>
                                </div>

                                <div style="font-size:.65rem;color:#ccc;margin-top:4px;">
                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp Business Cloud API
                                </div>
                            </div>
                        </div>

                    </div>
                </div>{{-- /.wa-app --}}

                    </div>{{-- /.card-body --}}

{{-- ══ Client Info Modal ══ --}}
<div class="modal fade" id="clientInfoModal" tabindex="-1" role="dialog" aria-labelledby="clientInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8f9fa;border-bottom:1px solid #e8e8e8;">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div class="header-avatar av-0" id="modal-avatar" style="width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;flex-shrink:0;">A</div>
                    <div>
                        <h6 class="modal-title mb-0 font-weight-bold" id="clientInfoModalLabel" style="font-size:.95rem;"></h6>
                        <small class="text-muted" id="modal-account"></small>
                    </div>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="margin-left:auto;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row" style="font-size:.84rem;row-gap:8px;" id="modal-fields"></div>
            </div>
            <div class="modal-footer py-2" style="border-top:1px solid #f0f0f0;">
                @php
                    $btnText = "<i class='ft-user'></i> View Full Profile";
                    $btnLink = "#"; $otherClasses = ""; $otherAttributes = "";
                @endphp
                <x-button-link btnType="primary" btnSize="sm" toolTip="Open client profile" btnId="modal-profile-link" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" :otherAttributes="$otherAttributes" :readOnly="$readonly" />
                @php
                    $btnText = "Close"; $btn_id = ""; $otherClasses = ""; $otherAttributes = "data-dismiss='modal'";
                @endphp
                <x-button btnType="secondary" btnSize="sm" toolTip="" type="button" :btnText="$btnText" :btnId="$btn_id" :otherClasses="$otherClasses" :otherAttributes="$otherAttributes" :readOnly="$readonly" />
            </div>
        </div>
    </div>
</div>
                    </div>{{-- /.card-content --}}
                </div>{{-- /.card --}}
                </div>{{-- /.col-12 --}}
                </div>{{-- /.row --}}

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
    (function () {
        var csrfToken       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var activeId        = null;
        var activeColor     = 'av-0';
        var activeInitial   = 'A';
        var clientTemplates = [];
        var activeClient    = null;
        var lastMsgId       = 0;
        var msgPollTimer    = null;
        var sidebarTimer    = null;
        var unreadMap       = {};

        // ── Avatar colour helpers ────────────────────────────────────────────
        var COLORS = ['av-0','av-1','av-2','av-3','av-4','av-5','av-6','av-7'];
        function colorFor(id) { return COLORS[parseInt(id) % 8]; }

        function escHtml(s) {
            return String(s)
                .replace(/&/g,'&amp;')
                .replace(/</g,'&lt;')
                .replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;');
        }

        function formatDate(ds) {
            if (!ds || ds.length < 12) return '';
            var yr = ds.substr(0,4), mo = ds.substr(4,2), dy = ds.substr(6,2);
            var hr = ds.substr(8,2), mn = ds.substr(10,2);
            return dy + ' ' + ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][parseInt(mo)-1] + ' ' + hr + ':' + mn;
        }

        // ── State switches ───────────────────────────────────────────────────
        function showState(state) {
            document.getElementById('wa-welcome').style.display = state === 'welcome' ? '' : 'none';
            document.getElementById('wa-loading').style.display = state === 'loading' ? '' : 'none';
            var chatEl = document.getElementById('wa-chat');
            chatEl.style.display = state === 'chat' ? 'flex' : 'none';
            chatEl.style.flexDirection = 'column';
        }

        // ── Load a conversation ──────────────────────────────────────────────
        function openChat(clientId) {
            if (activeId === clientId) return;

            // Remove unread dot from this contact
            var dotEl = document.querySelector('#contact-' + clientId + ' .unread-dot');
            if (dotEl) dotEl.remove();

            // Highlight contact
            document.querySelectorAll('.wa-contact-item').forEach(function (el) {
                el.classList.remove('active');
            });
            var contactEl = document.getElementById('contact-' + clientId);
            if (contactEl) {
                contactEl.classList.add('active');
                activeColor   = contactEl.getAttribute('data-color') || 'av-0';
                activeInitial = contactEl.getAttribute('data-initial') || '?';
            }

            activeId  = clientId;
            lastMsgId = 0;
            showState('loading');

            fetch('/whatsapp/messages/' + clientId)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.error) { showState('welcome'); return; }
                    lastMsgId = data.messages.length ? data.messages[data.messages.length - 1].sms_id : 0;
                    renderHeader(data.client, data.withinWindow);
                    renderMessages(data.messages, data.client);
                    renderCompose(data.withinWindow, data.templates);
                    showState('chat');
                    scrollDown();
                    startMsgPoll();
                })
                .catch(function () { showState('welcome'); });
        }

        // ── Render top bar ───────────────────────────────────────────────────
        function renderHeader(client, withinWindow) {
            activeClient = client;

            var name    = toTitle(client.client_name || '');
            var initial = name.charAt(0).toUpperCase();
            var color   = colorFor(client.client_id);

            var av = document.getElementById('header-avatar');
            av.className = 'header-avatar ' + color;
            av.textContent = initial;

            document.getElementById('header-name').textContent = name;
            document.getElementById('header-sub').textContent  =
                client.clients_contacts + '  ·  Acc: ' + client.client_account;

            var badge = document.getElementById('header-window-badge');
            if (withinWindow) {
                badge.className   = 'badge badge-success';
                badge.textContent = '● Window open';
            } else {
                badge.className   = 'badge badge-warning';
                badge.textContent = '● Template only';
            }

            document.getElementById('header-profile-link').href = '/Clients/View/' + client.client_id;
        }

        // ── Date formatter — MySQL "YYYY-MM-DD HH:MM:SS" → "Mon 10th Jan 2026 10:00:10AM" ──
        var DAYS   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        var MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        function fmtDatetime(s) {
            if (!s) return '—';
            var d = new Date(String(s).replace(' ', 'T'));
            if (isNaN(d)) return escHtml(String(s));
            var pad = function (n) { return n < 10 ? '0' + n : String(n); };
            var ordinal = function (n) {
                var v = n % 100;
                return n + (['th','st','nd','rd'][(v - 20) % 10] || ['th','st','nd','rd'][v] || 'th');
            };
            var hr   = d.getHours();
            var ampm = hr >= 12 ? 'PM' : 'AM';
            hr = hr % 12 || 12;
            return DAYS[d.getDay()] + ' ' + ordinal(d.getDate()) + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear()
                 + ' ' + pad(hr) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds()) + ampm;
        }

        // ── Client info modal ─────────────────────────────────────────────────
        function showClientModal() {
            if (!activeClient) return;
            var c     = activeClient;
            var name  = toTitle(c.client_name || '');
            var color = colorFor(c.client_id);

            var av = document.getElementById('modal-avatar');
            av.className = 'header-avatar ' + color;
            av.style.cssText = 'width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;flex-shrink:0;';
            av.textContent = name.charAt(0).toUpperCase();

            document.getElementById('clientInfoModalLabel').textContent = name;
            document.getElementById('modal-account').textContent = 'Acc: ' + (c.client_account || '—');
            document.getElementById('modal-profile-link').href = '/Clients/View/' + c.client_id;

            var statusHtml = c.client_status == 1
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';

            var assignRaw  = String(c.assignment || '').toLowerCase();
            var assignHtml = assignRaw === 'static'
                ? '<span class="badge badge-info">Static</span>'
                : assignRaw === 'pppoe'
                    ? '<span class="badge badge-primary">PPPoE</span>'
                    : escHtml(c.assignment || '—');

            var fields = [
                ['Phone',       escHtml(c.clients_contacts || '—')],
                ['Status',      statusHtml],
                ['Account No.', escHtml(c.client_account   || '—')],
                ['Assignment',  assignHtml],
                ['Monthly',     c.monthly_payment ? 'KES ' + escHtml(String(c.monthly_payment)) : '—'],
                ['Wallet',      c.wallet_amount != null ? 'KES ' + escHtml(String(c.wallet_amount)) : '—'],
                ['Router',      escHtml(c.router_name      || '—')],
                ['Address',     escHtml(c.client_address   || '—')],
                ['Expiry',      fmtDatetime(c.next_expiration_date)],
                ['Registered',  fmtDatetime(c.clients_reg_date)],
                ['Last Seen',   escHtml(c.last_seen        || '—')],
            ];

            var html = '';
            fields.forEach(function (f) {
                html += '<div class="col-6" style="margin-bottom:8px;">'
                    + '<div style="font-size:.68rem;color:#aaa;text-transform:uppercase;letter-spacing:.04em;">' + f[0] + '</div>'
                    + '<div style="font-size:.83rem;font-weight:600;">' + f[1] + '</div>'
                    + '</div>';
            });
            document.getElementById('modal-fields').innerHTML = html;

            $('#clientInfoModal').modal('show');
        }

        // Header click → modal; profile link and delete button stop propagation
        document.getElementById('chat-header').addEventListener('click', function (e) {
            if (e.target.closest('#header-profile-link')) return;
            if (e.target.closest('#delete-chat-btn')) return;
            showClientModal();
        });
        document.getElementById('header-profile-link').addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // ── Delete conversation ───────────────────────────────────────────────
        var deleteChatBtn = document.getElementById('delete-chat-btn');
        if (deleteChatBtn) {
            deleteChatBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (!activeId) return;
                var name = document.getElementById('header-name').textContent || 'this contact';
                if (!confirm('Delete all messages with ' + name + '? This cannot be undone.')) return;

                var btn = this;
                btn.disabled = true;

                fetch('/whatsapp/chat/delete/' + activeId, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) { alert('Failed to delete conversation.'); return; }
                    // Remove contact from sidebar
                    var contactEl = document.getElementById('contact-' + activeId);
                    if (contactEl) contactEl.remove();
                    delete unreadMap[activeId];
                    // Reset active state and show welcome screen
                    activeId  = null;
                    lastMsgId = 0;
                    if (msgPollTimer) { clearInterval(msgPollTimer); msgPollTimer = null; }
                    showState('welcome');
                })
                .catch(function () { alert('Failed to delete conversation.'); })
                .finally(function () { btn.disabled = false; });
            });
        }

        // ── Render messages ──────────────────────────────────────────────────
        function renderMessages(messages, client) {
            var container = document.getElementById('chat-messages');
            if (!messages.length) {
                container.innerHTML = '<div style="text-align:center;color:#aaa;padding-top:60px;font-size:.85rem;">'
                    + '<i class="fa-brands fa-whatsapp" style="font-size:40px;color:#ccc;display:block;margin-bottom:10px;"></i>'
                    + 'No messages yet. Send the first one below.</div>';
                return;
            }

            var clientInitial = (client.client_name || '?').charAt(0).toUpperCase();
            var clientColor   = colorFor(client.client_id);
            var html = '';
            var lastDate = '';

            messages.forEach(function (msg) {
                var isOut = (msg.direction || 'outbound') === 'outbound';
                var dir   = isOut ? 'outbound' : 'inbound';

                // Date separator
                var ds = msg.date_sent || '';
                var dateLabel = ds.length >= 8 ? ds.substr(0,4) + '-' + ds.substr(4,2) + '-' + ds.substr(6,2) : '';
                if (dateLabel && dateLabel !== lastDate) {
                    lastDate = dateLabel;
                    var d = new Date(dateLabel);
                    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    html += '<div class="wa-date-sep"><span>' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() + '</span></div>';
                }

                var statusIcon = '';
                if (isOut) {
                    var s = msg.delivery_status || 'sent';
                    statusIcon = s === 'read'      ? '<span style="color:#34b7f1;">✓✓</span>'
                               : s === 'delivered' ? '✓✓'
                               : s === 'failed'    ? '<span style="color:#e53e3e;">✗</span>'
                               : '✓';
                }

                var tplLabel = msg.template_name
                    ? '<div class="bubble-tpl-label"><i class="ft-file-text"></i> ' + escHtml(msg.template_name) + '</div>'
                    : '';

                var avatarHtml = isOut
                    ? '<div class="bubble-avatar av-wa"><i class="fa-brands fa-whatsapp" style="font-size:11px;"></i></div>'
                    : '<div class="bubble-avatar ' + clientColor + '">' + clientInitial + '</div>';

                html += '<div class="wa-bubble-row ' + dir + '">';
                if (!isOut) html += avatarHtml;
                html += '<div class="wa-bubble">'
                    + tplLabel
                    + '<div>' + escHtml(msg.sms_content) + '</div>'
                    + '<div class="bubble-meta"><span>' + formatDate(ds) + '</span>' + statusIcon + '</div>'
                    + '</div>';
                if (isOut) html += avatarHtml;
                html += '</div>';
            });

            container.innerHTML = html;
        }

        // ── Client variable chips ────────────────────────────────────────────
        function buildVarChips(client) {
            var container = document.getElementById('var-chips');
            container.innerHTML = '';
            if (!client) return;

            var name = toTitle(client.client_name || '');
            var expiry = client.next_expiration_date || '—';
            var vars = [
                { label: 'Name',     icon: 'ft-user',       value: name },
                { label: 'Phone',    icon: 'ft-phone',       value: client.clients_contacts || '—' },
                { label: 'Account',  icon: 'ft-hash',        value: client.client_account   || '—' },
                { label: 'Monthly',  icon: 'ft-dollar-sign', value: client.monthly_payment ? 'KES ' + client.monthly_payment : '—' },
                { label: 'Wallet',   icon: 'ft-credit-card', value: client.wallet_amount != null ? 'KES ' + client.wallet_amount : '—' },
                { label: 'Expiry',   icon: 'ft-clock',       value: expiry },
                { label: 'Router',   icon: 'ft-wifi',        value: client.router_name      || '—' },
                { label: 'Address',  icon: 'ft-map-pin',     value: client.client_address   || '—' },
            ];

            vars.forEach(function (v) {
                if (!v.value || v.value === '—') return;
                var chip = document.createElement('span');
                chip.className = 'var-chip';
                chip.title = 'Insert: ' + v.value;
                chip.innerHTML = '<i class="' + v.icon + '"></i>' + v.label;
                chip.addEventListener('click', function () {
                    insertAtCursor(document.getElementById('compose-text'), v.value);
                });
                container.appendChild(chip);
            });
        }

        function insertAtCursor(el, text) {
            el.focus();
            var start = el.selectionStart;
            var end   = el.selectionEnd;
            var val   = el.value;
            el.value  = val.substring(0, start) + text + val.substring(end);
            var pos   = start + text.length;
            el.setSelectionRange(pos, pos);
            // Trigger auto-expand
            el.dispatchEvent(new Event('input'));
        }

        // ── Render compose area ───────────────────────────────────────────────
        function renderCompose(withinWindow, templates) {
            clientTemplates = templates || [];

            buildVarChips(activeClient);
            document.getElementById('compose-freeform').style.display       = withinWindow ? '' : 'none';
            document.getElementById('window-closed-notice').style.display   = withinWindow ? 'none' : '';

            var tplRow     = document.getElementById('compose-template-row');
            var noTplNote  = document.getElementById('no-templates-notice');
            var tplSelect  = document.getElementById('tpl-select');

            if (clientTemplates.length > 0) {
                tplRow.style.display    = '';
                noTplNote.style.display = 'none';
                tplSelect.innerHTML = '<option value="">Select template…</option>';
                clientTemplates.forEach(function (t) {
                    var opt = document.createElement('option');
                    opt.value       = t.id;
                    opt.textContent = t.name + ' (' + capitalise(t.category) + ')';
                    tplSelect.appendChild(opt);
                });
            } else {
                tplRow.style.display    = 'none';
                noTplNote.style.display = '';
            }
        }

        // ── Send free-form message ───────────────────────────────────────────
        document.getElementById('send-btn').addEventListener('click', function () {
            if (!activeId) return;
            var txt = document.getElementById('compose-text').value.trim();
            if (!txt) return;

            this.disabled = true;
            var self = this;

            fetch('/whatsapp/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ client_id: activeId, message: txt })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    document.getElementById('compose-text').value = '';
                    fetchMessages(true);
                    pollSidebar();
                } else {
                    alert(data.error || 'Failed to send message.');
                }
            })
            .finally(function () { self.disabled = false; });
        });

        // Enter key sends in textarea
        document.getElementById('compose-text').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('send-btn').click();
            }
        });

        // Auto-expand textarea
        document.getElementById('compose-text').addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // ── Send template ────────────────────────────────────────────────────
        document.getElementById('send-tpl-btn').addEventListener('click', function () {
            if (!activeId) return;
            var tplId = document.getElementById('tpl-select').value;
            if (!tplId) { alert('Please select a template.'); return; }

            this.disabled = true;
            var self = this;

            fetch('/whatsapp/send-template', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ client_id: activeId, template_id: tplId })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    fetchMessages(true);
                    pollSidebar();
                } else {
                    alert(data.error || 'Failed to send template.');
                }
            })
            .finally(function () { self.disabled = false; });
        });

        // ── Fetch & render messages ──────────────────────────────────────────
        // forceScroll=true: always scroll to bottom (use after sending)
        // forceScroll=false: skip render if no new messages, scroll only if near bottom
        function fetchMessages(forceScroll) {
            if (!activeId) return;
            fetch('/whatsapp/messages/' + activeId)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.error || !data.messages) return;
                    var msgs  = data.messages;
                    var newId = msgs.length ? msgs[msgs.length - 1].sms_id : 0;
                    if (!forceScroll && newId === lastMsgId) return;
                    var hadMessages = lastMsgId > 0;
                    lastMsgId = newId;
                    renderMessages(msgs, data.client);
                    if (hadMessages && msgs.length && msgs[msgs.length - 1].direction === 'inbound') {
                        playNotification();
                    }
                    var el = document.getElementById('chat-messages');
                    if (forceScroll || el.scrollHeight - el.scrollTop - el.clientHeight < 120) scrollDown();
                });
        }

        function scrollDown() {
            var el = document.getElementById('chat-messages');
            if (el) el.scrollTop = el.scrollHeight;
        }

        function playNotification() {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                [[880, 0, 0.12], [1100, 0.13, 0.18]].forEach(function (note) {
                    var osc  = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.value = note[0];
                    gain.gain.setValueAtTime(0, ctx.currentTime + note[1]);
                    gain.gain.linearRampToValueAtTime(0.18, ctx.currentTime + note[1] + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + note[1] + note[2] + 0.25);
                    osc.start(ctx.currentTime + note[1]);
                    osc.stop(ctx.currentTime + note[1] + note[2] + 0.3);
                });
            } catch (e) {}
        }

        // ── Auto-poll: active chat messages every 5 s ────────────────────────
        function startMsgPoll() {
            if (msgPollTimer) clearInterval(msgPollTimer);
            msgPollTimer = setInterval(function () { fetchMessages(false); }, 5000);
        }

        // ── Auto-poll: sidebar contacts every 15 s ───────────────────────────
        function startSidebarPoll() {
            if (sidebarTimer) clearInterval(sidebarTimer);
            sidebarTimer = setInterval(pollSidebar, 15000);
        }

        function chatTimeFromDs(ds) {
            if (!ds || ds.length < 12) return '';
            return ds.substr(8, 2) + ':' + ds.substr(10, 2);
        }

        function pollSidebar() {
            fetch('/whatsapp/chats-json')
                .then(function (r) { return r.json(); })
                .then(function (chats) {
                    if (!Array.isArray(chats)) return;
                    chats.forEach(function (chat) {
                        var id   = String(chat.account_id);
                        var curr = parseInt(chat.sms_id) || 0;
                        var prev = unreadMap[id]          || 0;
                        var el   = document.getElementById('contact-' + id);

                        if (el) {
                            // Update preview snippet and time
                            var prevEl = el.querySelector('.wa-contact-preview');
                            var metaEl = el.querySelector('.wa-contact-meta');
                            if (prevEl) {
                                var dirIcon = chat.direction === 'inbound'
                                    ? '<i class="ft-corner-down-left" style="color:#25d366;font-size:.7rem;"></i> '
                                    : '<i class="ft-corner-up-right" style="color:#888;font-size:.7rem;"></i> ';
                                var snippet = (chat.sms_content || '').substr(0, 38)
                                            + ((chat.sms_content || '').length > 38 ? '…' : '');
                                prevEl.innerHTML = dirIcon + escHtml(snippet);
                            }
                            if (metaEl) {
                                var dot = metaEl.querySelector('.unread-dot');
                                metaEl.textContent = chatTimeFromDs(chat.date_sent || '');
                                if (dot) metaEl.appendChild(dot); // re-attach dot after textContent wipe
                            }
                            // Show unread dot if this is NOT the active chat and a new message arrived
                            if (String(activeId) !== id && curr > prev) {
                                if (!el.querySelector('.unread-dot')) {
                                    var dot2 = document.createElement('span');
                                    dot2.className = 'unread-dot';
                                    dot2.style.cssText = 'display:inline-block;width:8px;height:8px;background:#25d366;border-radius:50%;margin-left:4px;flex-shrink:0;';
                                    el.querySelector('.wa-contact-meta').appendChild(dot2);
                                }
                                // Bubble the contact to the top
                                var list = document.getElementById('contacts-list');
                                list.insertBefore(el, list.firstChild);
                            }
                        } else if (curr > 0) {
                            // Entirely new conversation — add to sidebar
                            prependContact(chat);
                        }

                        if (curr > prev) unreadMap[id] = curr;
                    });
                });
        }

        function prependContact(chat) {
            var id       = String(chat.account_id);
            var name     = toTitle(chat.client_name || '');
            var initial  = name.charAt(0).toUpperCase();
            var color    = colorFor(chat.account_id);
            var dirIcon  = chat.direction === 'inbound'
                ? '<i class="ft-corner-down-left" style="color:#25d366;font-size:.7rem;"></i> '
                : '<i class="ft-corner-up-right" style="color:#888;font-size:.7rem;"></i> ';
            var snippet  = (chat.sms_content || '').substr(0, 38)
                         + ((chat.sms_content || '').length > 38 ? '…' : '');
            var chatTime = chatTimeFromDs(chat.date_sent || '');

            var a = document.createElement('a');
            a.className = 'wa-contact-item';
            a.id        = 'contact-' + id;
            a.setAttribute('data-client-id', id);
            a.setAttribute('data-name', name);
            a.setAttribute('data-phone', chat.clients_contacts || '');
            a.setAttribute('data-account', chat.client_account || '');
            a.setAttribute('data-color', color);
            a.setAttribute('data-initial', initial);
            a.setAttribute('data-sms-id', chat.sms_id || 0);
            a.href = '#';
            a.innerHTML =
                '<div class="wa-contact-avatar ' + color + '">' + escHtml(initial) + '</div>'
              + '<div class="wa-contact-info">'
              +   '<p class="wa-contact-name">' + escHtml(name) + '</p>'
              +   '<p class="wa-contact-preview">' + dirIcon + escHtml(snippet) + '</p>'
              + '</div>'
              + '<div class="wa-contact-meta">' + chatTime
              +   '<span class="unread-dot" style="display:inline-block;width:8px;height:8px;'
              +   'background:#25d366;border-radius:50%;margin-left:4px;flex-shrink:0;"></span>'
              + '</div>';
            a.addEventListener('click', function (e) {
                e.preventDefault();
                openChat(id);
            });

            var list  = document.getElementById('contacts-list');
            var empty = list.querySelector('.text-center.text-muted');
            if (empty) empty.remove();
            list.insertBefore(a, list.firstChild);
            unreadMap[id] = parseInt(chat.sms_id) || 0;
        }

        // ── Contact click ────────────────────────────────────────────────────
        document.querySelectorAll('.wa-contact-item').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                openChat(this.getAttribute('data-client-id'));
            });
        });

        // ── Contact search ───────────────────────────────────────────────────
        document.getElementById('contact-search').addEventListener('input', function () {
            var val = this.value.toLowerCase();
            document.querySelectorAll('.wa-contact-item').forEach(function (el) {
                var name  = (el.getAttribute('data-name')  || '').toLowerCase();
                var phone = (el.getAttribute('data-phone') || '').toLowerCase();
                el.style.display = (name.includes(val) || phone.includes(val)) ? '' : 'none';
            });
        });

        // ── Utilities ────────────────────────────────────────────────────────
        function toTitle(s) {
            return s.toLowerCase().replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        }
        function capitalise(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        }

        // ── Seed unreadMap from server-rendered contacts ──────────────────────
        document.querySelectorAll('.wa-contact-item[data-sms-id]').forEach(function (el) {
            var id  = el.getAttribute('data-client-id');
            var sid = parseInt(el.getAttribute('data-sms-id')) || 0;
            if (id) unreadMap[id] = sid;
        });

        // ── Auto-open from URL ?open=id ───────────────────────────────────────
        var params = new URLSearchParams(window.location.search);
        var openId = params.get('open');
        if (openId) openChat(openId);

        // ── Start sidebar poll ────────────────────────────────────────────────
        startSidebarPoll();

        // ── Session timeout ───────────────────────────────────────────────────
        var milli_seconds = 1200;
        setInterval(function () { if (milli_seconds-- === 0) window.location.href = '/'; }, 1000);

        $('[data-toggle="tooltip"]').tooltip();
    }());
    </script>
</body>
</html>
