<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Hypbits - WhatsApp Templates</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <x-css></x-css>
    <style>
        .wa-tag { cursor: pointer; font-size: 0.78rem; padding: 4px 8px; margin: 2px; display: inline-block; }
        .wa-tag:hover { opacity: 0.8; }
        .var-badge { font-size: 0.75rem; padding: 3px 7px; margin: 2px; display: inline-block; }
        .tpl-validation { font-size: 0.8rem; margin-top: 4px; }
        .char-counter { font-size: 0.75rem; color: #999; float: right; }
        .char-counter.warn  { color: #ff9f43; }
        .char-counter.error { color: #ea5455; }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="whatsapp"></x-menu>

    @php
        $priviledges = session("priviledges");
        $readonly = readOnly($priviledges, "SMS");
        $activeTemplates = collect($templates)->where('is_active', 1)->count();

        $metaStatuses = [
            'not_submitted' => ['label' => 'Not Submitted', 'color' => 'secondary'],
            'pending'       => ['label' => 'Pending Approval', 'color' => 'warning'],
            'approved'      => ['label' => 'Approved',         'color' => 'success'],
            'rejected'      => ['label' => 'Rejected',         'color' => 'danger'],
        ];

        $availableVars = [
            'name'       => 'Full Name',
            'first_name' => 'First Name',
            'account_no' => 'Account No',
            'phone'      => 'Phone',
            'amount'     => 'Monthly Fee',
            'username'   => 'Username',
            'address'    => 'Address',
            'exp_date'   => 'Expiry Date',
            'wallet'     => 'Wallet Balance',
        ];
    @endphp

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title"><i class="ft-file-text"></i> WhatsApp Templates</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="/whatsapp/chats">WhatsApp Chats</a></li>
                                <li class="breadcrumb-item active">Templates</li>
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
                        $btnText = "<i class='ft-send'></i> Bulk Send";
                        $otherClasses = "ml-1";
                        $btnLink = "/whatsapp/bulk";
                        $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="info" btnSize="sm" toolTip="Bulk WhatsApp Send" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />

                    @php
                        $btnText = "<i class='ft-refresh-cw'></i> Sync Status from Meta";
                        $otherClasses = "ml-1";
                        $btnLink = "/whatsapp/templates/sync";
                        $otherAttributes = "";
                    @endphp
                    <x-button-link btnType="primary" btnSize="sm" toolTip="Pull latest approval status from Meta API" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                </div>

                @if(session('success_wa'))
                    <div class="alert alert-success">{{ session('success_wa') }}</div>
                @endif
                @if(session('error_wa'))
                    <div class="alert alert-danger">{{ session('error_wa') }}</div>
                @endif

                <div class="row">
                    {{-- Add / Edit Template Form --}}
                    @php $atLimit = count($templates) >= $maxTemplates; @endphp
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="form_title">
                                    @if($atLimit)
                                        <i class="ft-lock text-danger"></i> Template limit reached
                                    @else
                                        Add Template
                                    @endif
                                </h4>
                                <small class="{{ $atLimit ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                    {{ count($templates) }} / {{ $maxTemplates }} templates used
                                </small>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @if($atLimit)
                                        <div class="alert alert-warning py-2 mb-0" style="font-size:.85rem;">
                                            <i class="ft-alert-triangle"></i>
                                            You have used all <strong>{{ $maxTemplates }}</strong> available templates.
                                            <strong>Delete</strong> an existing template to create a new one.
                                        </div>
                                    @endif
                                    <form method="POST" action="/whatsapp/templates/save" id="tpl_form" @if($atLimit) style="pointer-events:none;opacity:.5;" @endif>
                                        @csrf
                                        <input type="hidden" name="id" id="tpl_id">

                                        <div class="form-group">
                                            <label>Display Name</label>
                                            <input type="text" name="name" id="tpl_name" class="form-control"
                                                placeholder="e.g. Service Disruption Notice" required {{ $readonly }}>
                                        </div>

                                        <div class="form-group">
                                            <label>Meta Template Name
                                                <small class="text-muted">(lowercase letters, numbers and underscores only)</small>
                                            </label>
                                            <input type="text" name="template_name" id="tpl_template_name"
                                                class="form-control" placeholder="e.g. service_disruption_hypbits" required {{ $readonly }}>
                                            <small class="text-info">
                                                <i class="ft-info"></i>
                                                Include your company name to avoid conflicts with other businesses on Meta &mdash;
                                                e.g. <code>service_disruption_hypbits</code>, <code>maintenance_notice_ladybird</code>.
                                            </small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label>Category
                                                        <small class="text-muted">(service → Utility on Meta)</small>
                                                    </label>
                                                    <select name="category" id="tpl_category" class="form-control" {{ $readonly }}>
                                                        @foreach($categories as $key => $label)
                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Language</label>
                                                    <select name="language" id="tpl_language" class="form-control" {{ $readonly }}>
                                                        <option value="en">English (en)</option>
                                                        <option value="en_US">English US (en_US)</option>
                                                        <option value="en_GB">English UK (en_GB)</option>
                                                        <option value="sw">Swahili (sw)</option>
                                                        <option value="fr">French (fr)</option>
                                                        <option value="ar">Arabic (ar)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- Clickable variable tags --}}
                                        <div class="form-group mb-1">
                                            <label>
                                                Template Body
                                                <small class="text-muted">(use @{{1}}, @{{2}}&hellip; for variables)</small>
                                                <span class="char-counter" id="char_counter">0 / 1024</span>
                                            </label>
                                            <div class="mb-1">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="ft-tag"></i> Click a field to insert it at your cursor:
                                                </small>
                                                @foreach($availableVars as $key => $label)
                                                    <span class="badge badge-pill badge-light border wa-tag"
                                                          data-var="{{ $key }}"
                                                          title="Insert {{ $label }}">{{ $label }}</span>
                                                @endforeach
                                            </div>
                                            <textarea name="body_text" id="tpl_body" class="form-control" rows="4"
                                                placeholder="Dear @{{1}}, we are currently experiencing a service disruption in @{{2}}. Our team is working to restore connectivity as soon as possible. We apologize for the inconvenience."
                                                required {{ $readonly }}></textarea>

                                            {{-- Real-time validation output --}}
                                            <div id="tpl_validation" class="tpl-validation mt-1"></div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                Variable Mapping
                                                <small class="text-muted">(auto-filled by clicking tags above)</small>
                                            </label>

                                            {{-- Visual variable sequence --}}
                                            <div id="var_sequence" class="mb-1 p-2 border rounded bg-light" style="min-height:32px;">
                                                <small class="text-muted" id="var_seq_empty">No variables added yet.</small>
                                            </div>

                                            {{-- Hidden input stores comma-separated keys --}}
                                            <input type="hidden" name="variables" id="tpl_variables">

                                            <small class="text-muted">
                                                Each position maps to one client field.
                                                @{{1}} = 1st field below, @{{2}} = 2nd, etc.
                                            </small>
                                        </div>

                                        <div class="d-flex" style="gap:6px;">
                                            @php
                                                $btnText = "<i class='ft-save'></i> Save Template";
                                                $otherClasses = "";
                                                $btn_id = "save_tpl_btn";
                                                $otherAttributes = "";
                                                $saveReadOnly = ($atLimit && !request('id')) ? "disabled" : $readonly;
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="Save template" btnType="success" type="submit" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$saveReadOnly" />

                                            @php
                                                $btnText = "Clear";
                                                $otherClasses = "";
                                                $btn_id = "clear_form_btn";
                                                $otherAttributes = "onclick=\"clearForm()\"";
                                            @endphp
                                            <x-button :otherAttributes="$otherAttributes" :btnText="$btnText" toolTip="Clear form" btnType="secondary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" readOnly="" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ISP Example Templates --}}
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="ft-zap text-warning"></i> ISP Template Examples</h4>
                                <small class="text-muted">Click <strong>Use this</strong> to load into the form</small>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body py-1" style="font-size:0.82rem;">

                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>Service Disruption Notice</strong>
                                                <span class="badge badge-secondary">Utility</span><br>
                                                <span class="text-muted" style="font-size:.78rem;">
                                                    Dear <em>@{{1}}</em>, we are currently experiencing a service disruption in <em>@{{2}}</em>. Our team is working to restore connectivity as soon as possible. We apologize for the inconvenience.
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ml-2 flex-shrink-0" style="padding:3px;" onclick="useExample('disruption')"><span class="d-inline-block border border-white w-100" style="border-radius:2px;padding:5px;">Use this</span></button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>Scheduled Maintenance Alert</strong>
                                                <span class="badge badge-secondary">Utility</span><br>
                                                <span class="text-muted" style="font-size:.78rem;">
                                                    Dear <em>@{{1}}</em>, we will be carrying out scheduled maintenance on <em>@{{2}}</em>. Your internet service may be temporarily unavailable. We expect to complete by <em>@{{3}}</em>. Thank you for your patience.
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ml-2 flex-shrink-0" style="padding:3px;" onclick="useExample('maintenance')"><span class="d-inline-block border border-white w-100" style="border-radius:2px;padding:5px;">Use this</span></button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>Service Restored</strong>
                                                <span class="badge badge-success">Utility</span><br>
                                                <span class="text-muted" style="font-size:.78rem;">
                                                    Dear <em>@{{1}}</em>, we are glad to inform you that internet service has been fully restored in your area. We apologize for the disruption and thank you for your patience.
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ml-2 flex-shrink-0" style="padding:3px;" onclick="useExample('restored')"><span class="d-inline-block border border-white w-100" style="border-radius:2px;padding:5px;">Use this</span></button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>Subscription Expiry Reminder</strong>
                                                <span class="badge badge-warning">Utility</span><br>
                                                <span class="text-muted" style="font-size:.78rem;">
                                                    Dear <em>@{{1}}</em>, your internet subscription (Account: <em>@{{2}}</em>) expires on <em>@{{3}}</em>. Renew now to avoid service interruption. Contact us for any assistance.
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ml-2 flex-shrink-0" style="padding:3px;" onclick="useExample('expiry')"><span class="d-inline-block border border-white w-100" style="border-radius:2px;padding:5px;">Use this</span></button>
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>Welcome New Client</strong>
                                                <span class="badge badge-info">Utility</span><br>
                                                <span class="text-muted" style="font-size:.78rem;">
                                                    Welcome <em>@{{1}}</em>! Your internet connection is now active. Your account number is <em>@{{2}}</em> and your monthly plan is KES <em>@{{3}}</em>. Contact us anytime for support.
                                                </span>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ml-2 flex-shrink-0" style="padding:3px;" onclick="useExample('welcome')"><span class="d-inline-block border border-white w-100" style="border-radius:2px;padding:5px;">Use this</span></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Meta Rules Reference --}}
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="ft-info"></i> Meta Template Rules</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body py-1" style="font-size:0.82rem;">
                                    <ul class="pl-3 mb-0">
                                        <li>Body max <strong>1,024 characters</strong>.</li>
                                        <li>Variables must be <strong>numbered sequentially</strong>: @{{1}}, @{{2}}&hellip; — no skipping.</li>
                                        <li>Max <strong>10 variables</strong> per template.</li>
                                        <li>Number of @{{N}} placeholders must <strong>exactly match</strong> declared variables.</li>
                                        <li>Template name: <strong>lowercase, numbers, underscores</strong> only. Include your company name to avoid conflicts with other businesses &mdash; e.g. <code>payment_reminder_hypbits</code>.</li>
                                        <li>Templates must be <strong>submitted and approved</strong> in Meta Business Manager before sending.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Templates List --}}
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    Saved Templates
                                    <small class="text-muted font-small-2">
                                        ({{ count($templates) }} shown
                                        @if($deletedCount > 0)
                                            , {{ $deletedCount }} soft-deleted
                                        @endif
                                        )
                                    </small>
                                </h4>
                                @php
                                    $btnText = "<i class='ft-refresh-cw'></i> Sync Status from Meta";
                                    $otherClasses = "";
                                    $btnLink = "/whatsapp/templates/sync";
                                    $otherAttributes = "";
                                @endphp
                                <x-button-link btnType="primary" btnSize="sm" toolTip="Pull latest approval status from Meta API" :otherAttributes="$otherAttributes" :btnText="$btnText" :btnLink="$btnLink" :otherClasses="$otherClasses" readOnly="" />
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    @forelse($templates as $tpl)
                                        @php
                                            $ms  = $tpl->meta_status ?? 'not_submitted';
                                            $msInfo = $metaStatuses[$ms] ?? $metaStatuses['not_submitted'];
                                        @endphp
                                        <div class="border rounded p-2 mb-2 {{ $tpl->is_active ? '' : 'bg-light' }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div style="flex:1; min-width:0;">
                                                    <strong>{{ $tpl->name }}</strong>
                                                    <span class="badge badge-{{ match($tpl->category) { 'marketing' => 'warning', 'utility' => 'info', 'authentication' => 'primary', default => 'secondary' } }}">
                                                        {{ ucfirst($tpl->category) }}
                                                    </span>
                                                    @if($tpl->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                    <span class="badge badge-{{ $msInfo['color'] }}">
                                                        <i class="ft-{{ $ms === 'approved' ? 'check' : ($ms === 'rejected' ? 'x' : ($ms === 'pending' ? 'clock' : 'upload')) }}"></i>
                                                        {{ $msInfo['label'] }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">Meta name: <code>{{ $tpl->template_name }}</code></small>
                                                    <p class="mt-1 mb-1" style="font-size:0.85rem; word-break:break-word;">{{ $tpl->body_text }}</p>
                                                    @php
                                                        $vars = json_decode($tpl->variables ?? '[]', true) ?: [];
                                                    @endphp
                                                    @if(!empty($vars))
                                                        <div>
                                                            @foreach($vars as $i => $v)
                                                                <span class="badge badge-light border var-badge">
                                                                    {{ '{' . '{' . ($i + 1) . '}' . '}' }} = {{ $v }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if($ms !== 'approved' && $tpl->is_active)
                                                        <small class="text-warning">
                                                            <i class="ft-alert-triangle"></i>
                                                            Not yet approved by Meta — sending may fail.
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column ml-2" style="gap:4px; min-width:120px;">
                                                    @php
                                                        $editAttrs = "onclick=\"editTemplate(" . $tpl->id . ")\"";
                                                        $btn_id = "";
                                                        $otherClasses = "";
                                                    @endphp
                                                    <x-button :otherAttributes="$editAttrs" :btnText="'<i class=\'ft-edit\'></i> Edit'" toolTip="Edit template" btnType="primary" type="button" btnSize="sm" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />

                                                    @php
                                                        $canSubmit    = in_array($ms, ['not_submitted', 'rejected']);
                                                        $submitLink   = "/whatsapp/templates/submit/" . $tpl->id;
                                                        $submitText   = $ms === 'rejected' ? "<i class='ft-refresh-cw'></i> Resubmit" : "<i class='ft-upload'></i> Submit to Meta";
                                                        $submitType   = $ms === 'rejected' ? "warning" : "info";
                                                        $submitAttrs  = "onclick=\"return confirm('Submit this template to Meta for approval?')\"";
                                                        $otherClasses = "";
                                                    @endphp
                                                    @if($canSubmit)
                                                        <x-button-link btnType="{{ $submitType }}" btnSize="sm" toolTip="Submit template to Meta for approval" :otherAttributes="$submitAttrs" :btnText="$submitText" :btnLink="$submitLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                    @endif

                                                    @php
                                                        $toggleLink   = "/whatsapp/templates/toggle/" . $tpl->id;
                                                        $toggleType   = $tpl->is_active ? "warning" : "success";
                                                        $toggleText   = $tpl->is_active ? "<i class='ft-pause'></i> Deactivate" : "<i class='ft-play'></i> Activate";
                                                        $otherClasses = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button-link btnType="{{ $toggleType }}" btnSize="sm" toolTip="{{ $tpl->is_active ? 'Deactivate' : 'Activate' }} template" :otherAttributes="$otherAttributes" :btnText="$toggleText" :btnLink="$toggleLink" :otherClasses="$otherClasses" :readOnly="$readonly" />

                                                    @php
                                                        $deleteLink  = "/whatsapp/templates/delete/" . $tpl->id;
                                                        $deleteMsg   = !empty($tpl->meta_template_id) ? 'Delete this template locally AND from Meta?' : 'Delete this template?';
                                                        $deleteAttrs = "onclick=\"return confirm('{$deleteMsg}')\"";
                                                        $otherClasses = "";
                                                    @endphp
                                                    <x-button-link btnType="danger" btnSize="sm" toolTip="Delete template" :otherAttributes="$deleteAttrs" :btnText="'<i class=\'ft-trash\'></i> Delete'" :btnLink="$deleteLink" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center">No templates yet. Add one on the left.</p>
                                    @endforelse
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
    (function () {
        var bodyField  = document.getElementById('tpl_body');
        var varsField  = document.getElementById('tpl_variables');
        var seqBox     = document.getElementById('var_sequence');
        var seqEmpty   = document.getElementById('var_seq_empty');
        var counter    = document.getElementById('char_counter');
        var validation = document.getElementById('tpl_validation');

        @php
            $orgName  = session('organization')->organization_name ?? '';
            $orgSlug  = strtolower(preg_replace('/[^a-z0-9]+/i', '_', trim($orgName)));
            $orgSlug  = trim($orgSlug, '_');
        @endphp
        var ORG_SLUG = '{{ $orgSlug }}';

        var varLabels    = @json($availableVars);
        var templateData = @json($templates);
        // Split to prevent Blade from parsing these as template expressions
        var OB = '{' + '{';
        var CB = '}' + '}';

        // ── Helpers ────────────────────────────────────────────────────────────

        function getVarArray() {
            var v = varsField.value.trim();
            return v ? v.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
        }

        function setVarArray(arr) {
            varsField.value = arr.join(',');
            renderSequence(arr);
            validate();
        }

        function renderSequence(arr) {
            if (arr.length === 0) {
                seqBox.innerHTML = '<small class="text-muted" id="var_seq_empty">No variables added yet.</small>';
                return;
            }
            var html = '';
            for (var i = 0; i < arr.length; i++) {
                var label = varLabels[arr[i]] || arr[i];
                html += '<span class="badge badge-pill badge-info var-badge">'
                      + OB + (i + 1) + CB + ' = ' + label
                      + ' <span style="cursor:pointer;margin-left:4px;" onclick="removeVar(' + i + ')">&times;</span>'
                      + '</span>';
            }
            seqBox.innerHTML = html;
        }

        window.removeVar = function(index) {
            var arr  = getVarArray();
            var removed = arr.splice(index, 1)[0];

            // Remove ALL occurrences of index+1 from body and shift higher numbers down
            var body = bodyField.value;
            var removedNum = index + 1;

            // Remove the placeholder for the removed variable
            body = body.split(OB + removedNum + CB).join('');

            // Shift all higher placeholders down by 1
            for (var n = removedNum + 1; n <= arr.length + 1; n++) {
                body = body.split(OB + n + CB).join(OB + (n - 1) + CB);
            }

            bodyField.value = body;
            setVarArray(arr);
        };

        // ── Tag click: insert placeholder at cursor ───────────────────────────

        document.querySelectorAll('.wa-tag').forEach(function(tag) {
            tag.addEventListener('click', function() {
                var varKey = this.getAttribute('data-var');
                var arr    = getVarArray();

                var pos;
                if (arr.includes(varKey)) {
                    // Already in list — just insert the same placeholder again
                    pos = arr.indexOf(varKey) + 1;
                } else {
                    arr.push(varKey);
                    pos = arr.length;
                }

                var placeholder = OB + pos + CB;

                // Insert at cursor
                var start = bodyField.selectionStart;
                var end   = bodyField.selectionEnd;
                var text  = bodyField.value;
                bodyField.value = text.substring(0, start) + placeholder + text.substring(end);
                bodyField.selectionStart = bodyField.selectionEnd = start + placeholder.length;
                bodyField.focus();

                setVarArray(arr);
            });
        });

        // ── Real-time validation ───────────────────────────────────────────────

        function validate() {
            var body   = bodyField.value;
            var arr    = getVarArray();
            var errors = [];
            var warns  = [];

            // Character counter
            var len = body.length;
            counter.textContent = len + ' / 1024';
            counter.className   = 'char-counter' + (len > 1024 ? ' error' : (len > 900 ? ' warn' : ''));
            if (len > 1024) errors.push('Body exceeds 1024 characters (' + len + ').');

            // Extract numeric placeholders
            var re   = /\{\{(\d+)\}\}/g;
            var m, nums = [];
            while ((m = re.exec(body)) !== null) {
                nums.push(parseInt(m[1]));
            }
            var unique = nums.filter(function(v, i, a) { return a.indexOf(v) === i; }).sort(function(a,b){return a-b;});

            // Sequential check
            if (unique.length > 0) {
                for (var i = 0; i < unique.length; i++) {
                    if (unique[i] !== i + 1) {
                        errors.push('Variables must be sequential from ' + OB + '1' + CB + ' — found gap or wrong start (saw ' + OB + unique[i] + CB + ' at position ' + (i+1) + ').');
                        break;
                    }
                }
            }

            var maxNum = unique.length > 0 ? Math.max.apply(null, unique) : 0;

            // Count mismatch
            if (maxNum !== arr.length) {
                errors.push(
                    'Body has ' + maxNum + ' placeholder' + (maxNum !== 1 ? 's' : '') +
                    ' but ' + arr.length + ' variable' + (arr.length !== 1 ? 's' : '') + ' declared. They must match.'
                );
            }

            // Max variables
            if (maxNum > 10) errors.push('Meta allows max 10 variables (found ' + maxNum + ').');

            // Show output
            if (errors.length > 0) {
                validation.innerHTML = errors.map(function(e) {
                    return "<span class='text-danger'><i class='ft-alert-circle'></i> " + e + "</span>";
                }).join('<br>');
            } else if (maxNum > 0) {
                validation.innerHTML = "<span class='text-success'><i class='ft-check-circle'></i> " + maxNum + " variable" + (maxNum !== 1 ? 's' : '') + " — looks good.</span>";
            } else {
                validation.innerHTML = '';
            }
        }

        bodyField.addEventListener('input', validate);
        bodyField.addEventListener('keyup', validate);
        validate();

        // ── Edit / Clear ───────────────────────────────────────────────────────

        window.editTemplate = function(id) {
            var tpl = templateData.find(function(t) { return t.id == id; });
            if (!tpl) return;

            document.getElementById('tpl_id').value            = tpl.id;
            document.getElementById('tpl_name').value          = tpl.name;
            document.getElementById('tpl_template_name').value = tpl.template_name;
            document.getElementById('tpl_category').value      = tpl.category;
            document.getElementById('tpl_language').value      = tpl.language || 'en';
            bodyField.value = tpl.body_text;

            var vars = tpl.variables;
            if (typeof vars === 'string') { try { vars = JSON.parse(vars); } catch(e) { vars = []; } }
            if (!Array.isArray(vars)) vars = [];
            setVarArray(vars);

            document.getElementById('form_title').textContent = 'Edit Template';
            window.scrollTo(0, 0);
        };

        var ISP_EXAMPLES = {
            disruption: {
                name: 'Service Disruption Notice',
                tplName: 'service_disruption',
                category: 'utility',
                body: 'Dear ' + OB + '1' + CB + ', we are currently experiencing a service disruption in ' + OB + '2' + CB + '. Our team is working to restore connectivity as soon as possible. We apologize for the inconvenience.',
                vars: ['name', 'address']
            },
            maintenance: {
                name: 'Scheduled Maintenance Alert',
                tplName: 'maintenance_alert',
                category: 'utility',
                body: 'Dear ' + OB + '1' + CB + ', we will be carrying out scheduled maintenance on ' + OB + '2' + CB + '. Your internet service may be temporarily unavailable. We expect to complete by ' + OB + '3' + CB + '. Thank you for your patience.',
                vars: ['name', 'address', 'exp_date']
            },
            restored: {
                name: 'Service Restored',
                tplName: 'service_restored',
                category: 'utility',
                body: 'Dear ' + OB + '1' + CB + ', we are glad to inform you that internet service has been fully restored in your area. We apologize for the disruption and thank you for your patience.',
                vars: ['name']
            },
            expiry: {
                name: 'Subscription Expiry Reminder',
                tplName: 'expiry_reminder',
                category: 'utility',
                body: 'Dear ' + OB + '1' + CB + ', your internet subscription (Account: ' + OB + '2' + CB + ') expires on ' + OB + '3' + CB + '. Renew now to avoid service interruption. Contact us for any assistance.',
                vars: ['name', 'account_no', 'exp_date']
            },
            welcome: {
                name: 'Welcome New Client',
                tplName: 'welcome_new_client',
                category: 'utility',
                body: 'Welcome ' + OB + '1' + CB + '! Your internet connection is now active. Your account number is ' + OB + '2' + CB + ' and your monthly plan is KES ' + OB + '3' + CB + '. Contact us anytime for support.',
                vars: ['name', 'account_no', 'amount']
            }
        };

        window.useExample = function(key) {
            var ex = ISP_EXAMPLES[key];
            if (!ex) return;
            var tplName = ORG_SLUG ? ex.tplName + '_' + ORG_SLUG : ex.tplName;
            document.getElementById('tpl_id').value            = '';
            document.getElementById('tpl_name').value          = ex.name;
            document.getElementById('tpl_template_name').value = tplName;
            document.getElementById('tpl_category').value      = ex.category;
            document.getElementById('tpl_language').value      = 'en';
            bodyField.value = ex.body;
            setVarArray(ex.vars);
            document.getElementById('form_title').textContent = 'Add Template';
            window.scrollTo(0, 0);
        };

        window.clearForm = function() {
            ['tpl_id','tpl_name','tpl_template_name'].forEach(function(id) {
                document.getElementById(id).value = '';
            });
            document.getElementById('tpl_category').value     = 'service';
            document.getElementById('tpl_language').value = 'en';
            bodyField.value = '';
            setVarArray([]);
            document.getElementById('form_title').textContent = 'Add Template';
            validation.innerHTML = '';
            counter.textContent  = '0 / 1024';
            counter.className    = 'char-counter';
        };

    })();

    var milli_seconds = 1200;
    setInterval(function() { if(milli_seconds-- === 0) window.location.href = "/"; }, 1000);
    </script>
</body>
</html>
