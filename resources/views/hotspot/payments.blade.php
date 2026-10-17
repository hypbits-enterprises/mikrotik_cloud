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
    <title>Hypbits - Hotspot Payments</title>
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
                    <h3 class="content-header-title">Payments</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="#">Payments</a></li>
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
                @php
                    $disabled = $readonly ? 'disabled' : '';
                    $badges = ["matched" => "success", "unmatched" => "warning", "review" => "info", "rejected" => "secondary"];
                    $sources = ["stk" => "M-Pesa prompt", "billing_c2b" => "Paybill (shared)", "hotspot_c2b" => "Paybill (hotspot)", "assigned" => "Assigned"];
                    $attemptBadges = ["pending" => "warning", "failed" => "danger", "cancelled" => "secondary", "expired" => "dark"];
                @endphp
                <div class="row mb-1">
                    <div class="col-12">
                        @php $btnText = "<i class=\"ft-settings\"></i> Payment settings"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.payment_settings')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if (count($reviews) > 0 || count($unmatched) > 0)
                            <div class="card border-warning">
                                <div class="card-header"><h4 class="card-title text-warning"><i class="ft-alert-circle"></i> Needs attention</h4></div>
                                <div class="card-content"><div class="card-body">
                                    @if (count($reviews) > 0)
                                        <p class="mb-1"><b>Customers waiting for approval</b>: they gave an M-Pesa code on the login page but the phone did not match the payer (or SMS is off). Check the payment, then approve to send the ticket code to their phone.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead><tr><th>Sent</th><th>M-Pesa code</th><th>Phone given</th><th>Package</th><th>Why</th><th></th></tr></thead>
                                                <tbody>
                                                    @foreach ($reviews as $review)
                                                        <tr>
                                                            <td>{{ date('D jS M Y g:i:sA', strtotime($review->date_created)) }}</td>
                                                            <td><b>{{ $review->mpesa_receipt }}</b></td>
                                                            <td>{{ $review->phone }}</td>
                                                            <td>{{ $review->plan_name }} (Kes {{ number_format($review->amount) }})</td>
                                                            <td><small>{{ $review->result_desc }}</small></td>
                                                            <td class="text-nowrap">
                                                                @php
                                                                    $btnText = "<i class=\"ft-check\"></i> Approve";
                                                                    $attributes = "data-toggle=modal data-target=#approve_modal data-url=\"" . route('hotspot.payment_approve', $review->payment_id) . "\" data-code=\"" . e($review->mpesa_receipt) . "\" data-phone=\"" . e($review->phone) . "\"";
                                                                @endphp
                                                                <x-button :btnText="$btnText" btnType="success" btnSize="sm" :readOnly="$disabled" :otherAttributes="$attributes" otherClasses="hs-approve mr-1" />
                                                                @php
                                                                    $btnText = "<i class=\"ft-x\"></i> Reject";
                                                                    $attributes = "data-toggle=modal data-target=#reject_modal data-kind=claim data-id=" . $review->payment_id . " data-code=\"" . e($review->mpesa_receipt) . "\"";
                                                                @endphp
                                                                <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :readOnly="$disabled" :otherAttributes="$attributes" otherClasses="hs-reject" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                    @if (count($unmatched) > 0)
                                        <p class="mb-1"><b>Hotspot Paybill payments without a valid account number</b>: assign each to a package and the customer's phone, or reject it.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead><tr><th>Received</th><th>M-Pesa code</th><th>Amount</th><th>Payer</th><th>Account typed</th><th></th></tr></thead>
                                                <tbody>
                                                    @foreach ($unmatched as $row)
                                                        <tr>
                                                            <td>{{ date('D jS M Y g:i:sA', strtotime($row->date_created)) }}</td>
                                                            <td><b>{{ $row->trans_id }}</b></td>
                                                            <td>Kes {{ number_format($row->amount, 2) }}</td>
                                                            <td>{{ $row->payer_name }} {{ strlen((string) $row->phone) > 15 ? '' : $row->phone }}</td>
                                                            <td>{{ $row->bill_ref ?: '—' }}@if ($row->note)<br><small class="text-muted">{{ $row->note }}</small>@endif</td>
                                                            <td class="text-nowrap">
                                                                @php
                                                                    $btnText = "<i class=\"ft-tag\"></i> Assign";
                                                                    $attributes = "data-toggle=modal data-target=#assign_modal data-id=" . $row->transaction_id . " data-code=\"" . e($row->trans_id) . "\" data-amount=\"" . e(number_format($row->amount, 2)) . "\" data-phone=\"" . e(strlen((string) $row->phone) > 15 ? '' : $row->phone) . "\"";
                                                                @endphp
                                                                <x-button :btnText="$btnText" btnType="success" btnSize="sm" :readOnly="$disabled" :otherAttributes="$attributes" otherClasses="hs-assign mr-1" />
                                                                @php
                                                                    $btnText = "<i class=\"ft-x\"></i> Reject";
                                                                    $attributes = "data-toggle=modal data-target=#reject_modal data-kind=transaction data-id=" . $row->transaction_id . " data-code=\"" . e($row->trans_id) . "\"";
                                                                @endphp
                                                                <x-button :btnText="$btnText" btnType="danger" btnSize="sm" :readOnly="$disabled" :otherAttributes="$attributes" otherClasses="hs-reject" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div></div>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-dollar-sign"></i> Hotspot earnings</h4></div>
                            <div class="card-content"><div class="card-body">
                                <p class="text-muted">Money received for hotspot packages. These payments are kept apart from your PPPoE client transactions. Customers pay {{ $config['stk'] ? 'with the M-Pesa prompt or ' : '' }}to {{ $config['shortcode_type'] === 'till' ? 'Till' : 'Paybill' }} <b>{{ $config['shortcode'] ?: 'not set' }}</b>{{ $config['mode'] === 'shared' ? ' (the same Paybill as your billing)' : '' }} with the account number the login page gives them.</p>
                                <form method="GET" action="{{ route('hotspot.payments') }}" class="row">
                                    <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>From</small></label><input class="form-control form-control-sm" type="date" name="from" value="{{ $filters['from'] }}"></div>
                                    <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>To</small></label><input class="form-control form-control-sm" type="date" name="to" value="{{ $filters['to'] }}"></div>
                                    <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Status</small></label>
                                        <select class="form-control form-control-sm" name="status"><option value="">All</option>
                                            @foreach (array_keys($badges) as $status)<option value="{{ $status }}" {{ $filters['status'] == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach
                                        </select></div>
                                    <div class="col-md-3 col-6 form-group"><label class="mb-0"><small>M-Pesa code, account or phone</small></label><input class="form-control form-control-sm" type="text" name="q" value="{{ $filters['q'] }}"></div>
                                    <div class="col-md-3 col-12 form-group d-flex align-items-end">
                                        @php $btnText = "<i class=\"ft-filter\"></i> Filter"; @endphp
                                        <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" type="submit" btnId="hotspot_payments_filter" otherClasses="mr-1" />
                                        @php $btnText = "<i class=\"ft-download\"></i> CSV"; @endphp
                                        <x-button-link :btnText="$btnText" btnType="info" btnSize="sm" :btnLink="route('hotspot.payments_export', $filters)" />
                                    </div>
                                </form>
                                <p class="mb-1">
                                    @foreach ($badges as $status => $badge)
                                        <span class="badge badge-{{ $badge }} mr-1">{{ ucfirst($status) }}: {{ isset($totals[$status]) ? $totals[$status]->n : 0 }}</span>
                                    @endforeach
                                    <span class="ml-1">Received: Kes <b>{{ number_format(isset($totals['matched']) ? $totals['matched']->total : 0, 2) }}</b> from {{ date('D jS M Y', strtotime($filters['from'])) }} to {{ date('D jS M Y', strtotime($filters['to'])) }}</span>
                                </p>
                                @if (count($rows) == 0)
                                    <p class="text-muted mb-0">No hotspot payments in this period.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead><tr><th>Date</th><th>M-Pesa code</th><th>Amount</th><th>Phone</th><th>Account</th><th>Package</th><th>Ticket</th><th>Via</th><th>Status</th></tr></thead>
                                            <tbody>
                                                @foreach ($rows as $row)
                                                    <tr>
                                                        <td>{{ $row->date_created ? date('D jS M Y g:i:sA', strtotime($row->date_created)) : '' }}</td>
                                                        <td>{{ str_starts_with($row->trans_id, 'STK-') ? 'awaiting receipt' : $row->trans_id }}</td>
                                                        <td>Kes {{ number_format($row->amount, 2) }}</td>
                                                        <td>{{ $row->customer_phone ?: (strlen((string) $row->phone) > 15 ? 'hidden by M-Pesa' : $row->phone) }}</td>
                                                        <td>{{ $row->bill_ref }}</td>
                                                        <td>{{ $row->plan_name }}</td>
                                                        <td>{!! $row->ticket ? '<a href="' . route('hotspot.ticket', $row->ticket) . '">' . e($row->ticket_code) . '</a>' : '—' !!}</td>
                                                        <td>{{ $sources[$row->source] ?? $row->source }}</td>
                                                        <td><span class="badge badge-{{ $badges[$row->status] ?? 'secondary' }}">{{ ucfirst($row->status) }}</span>@if ($row->note)<br><small class="text-muted">{{ $row->note }}</small>@endif</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if (count($rows) >= $limit)<p class="text-muted mb-0"><small>Showing the latest {{ $limit }}. Narrow the dates to see more.</small></p>@endif
                                @endif
                            </div></div>
                        </div>
                        @include("hotspot.partials.payment-modals")
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-clock"></i> Payment attempts not completed</h4></div>
                            <div class="card-content"><div class="card-body">
                                @if (count($attempts) == 0)
                                    <p class="text-muted mb-0">None in this period.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead><tr><th>Started</th><th>Phone</th><th>Package</th><th>Amount</th><th>Method</th><th>Account</th><th>Status</th></tr></thead>
                                            <tbody>
                                                @foreach ($attempts as $attempt)
                                                    <tr>
                                                        <td>{{ date('D jS M Y g:i:sA', strtotime($attempt->date_created)) }}</td>
                                                        <td>{{ $attempt->phone }}</td>
                                                        <td>{{ $attempt->plan_name }}</td>
                                                        <td>Kes {{ number_format($attempt->amount, 2) }}</td>
                                                        <td>{{ $attempt->method === 'paybill' ? 'Paybill' : 'M-Pesa prompt' }}</td>
                                                        <td>{{ $attempt->account_code }}</td>
                                                        <td><span class="badge badge-{{ $attemptBadges[$attempt->status] ?? 'secondary' }}">{{ ucfirst($attempt->status) }}</span>@if ($attempt->result_desc)<br><small class="text-muted">{{ $attempt->result_desc }}</small>@endif</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
</body>

</html>
