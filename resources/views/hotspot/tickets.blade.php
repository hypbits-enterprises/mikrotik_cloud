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
    <title>Hypbits - Hotspot Tickets</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">

    {{-- CSS COMPONENT --}}
    <x-css></x-css>
</head>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <x-menu active="hotspot_tickets"></x-menu>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">Hotspot Tickets</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="#">Tickets</a></li>
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
                @php $badges = ["pending" => "warning", "active" => "success", "used" => "secondary", "expired" => "dark", "failed" => "danger"]; @endphp
                @if (session("created_codes"))
                    @php $created = session("created_codes"); @endphp
                    <div class="card border-success">
                        <div class="card-content"><div class="card-body">
                            <h4 class="text-success"><i class="ft-check-circle"></i> {{ count($created["codes"]) }} ticket(s) for {{ $created["plan"] }} on {{ $created["router"] }}</h4>
                            <p class="mb-1">Give each customer one code. They connect to the WiFi and type it on the login page.</p>
                            @foreach ($created["codes"] as $code)
                                <span class="badge badge-primary mr-1 mb-1" style="font-size: 1.2rem; letter-spacing: 2px;">{{ $code }}</span>
                            @endforeach
                        </div></div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="ft-tag"></i> Hotspot Tickets</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="mb-1">
                                        @php $btnText = "<i class=\"ft-plus\"></i> New manual ticket"; @endphp
                                        <x-button-link :btnText="$btnText" btnType="primary" btnSize="sm" :btnLink="route('hotspot.ticket_new')" :readOnly="$readonly ? 'disabled' : ''" />
                                    </div>
                                    <form method="GET" action="{{ route('hotspot.tickets') }}" class="row">
                                        <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Date</small></label><input class="form-control form-control-sm" type="date" name="date" value="{{ $filters['date'] }}"></div>
                                        <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Status</small></label>
                                            <select class="form-control form-control-sm" name="status"><option value="">All</option>
                                                @foreach (array_keys($badges) as $status)<option value="{{ $status }}" {{ $filters['status'] == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach
                                            </select></div>
                                        <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Router</small></label>
                                            <select class="form-control form-control-sm" name="router_id"><option value="">All</option>
                                                @foreach ($routers as $router)<option value="{{ $router->router_id }}" {{ $filters['router_id'] == $router->router_id ? 'selected' : '' }}>{{ $router->router_name }}</option>@endforeach
                                            </select></div>
                                        <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Plan</small></label>
                                            <select class="form-control form-control-sm" name="plan_id"><option value="">All</option>
                                                @foreach ($plans as $plan)<option value="{{ $plan->plan_id }}" {{ $filters['plan_id'] == $plan->plan_id ? 'selected' : '' }}>{{ $plan->plan_name }}</option>@endforeach
                                            </select></div>
                                        <div class="col-md-1 col-6 form-group"><label class="mb-0"><small>Source</small></label>
                                            <select class="form-control form-control-sm" name="source"><option value="">All</option>
                                                @foreach (["stk" => "M-Pesa prompt", "paybill" => "Paybill", "manual" => "Manual"] as $value => $label)<option value="{{ $value }}" {{ $filters['source'] == $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                                            </select></div>
                                        <div class="col-md-2 col-6 form-group"><label class="mb-0"><small>Code or phone</small></label><input class="form-control form-control-sm" type="text" name="q" value="{{ $filters['q'] }}"></div>
                                        <div class="col-md-1 col-12 form-group d-flex align-items-end">
                                            @php $btnText = "<i class=\"ft-filter\"></i> Filter"; @endphp
                                            <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" type="submit" btnId="hotspot_tickets_filter" />
                                        </div>
                                    </form>
                                    <p class="mb-1">
                                        @php $total_count = 0; $total_amount = 0; @endphp
                                        @foreach ($badges as $status => $badge)
                                            @php $n = isset($counts[$status]) ? $counts[$status]->n : 0; $total_count += $n; $total_amount += isset($counts[$status]) ? $counts[$status]->total : 0; @endphp
                                            <span class="badge badge-{{ $badge }} mr-1">{{ ucfirst($status) }}: {{ $n }}</span>
                                        @endforeach
                                        <span class="ml-1"><b>{{ $total_count }}</b> ticket(s), Kes <b>{{ number_format($total_amount) }}</b> on {{ date('D jS M Y', strtotime($filters['date'])) }}</span>
                                    </p>
                                    @if (count($tickets) == 0)
                                        <p class="text-muted mb-0">No tickets for this day and filter.</p>
                                    @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead><tr><th>Code</th><th>Plan</th><th>Router</th><th>Phone</th><th>Source</th><th>Amount</th><th>Status</th><th>On router</th><th>First login</th><th>Time used / left</th><th>Data used</th><th></th></tr></thead>
                                            <tbody>
                                                @foreach ($tickets as $ticket)
                                                    @php $usage = \App\Classes\HotspotTickets::usage($ticket, (object) ['duration_minutes' => $ticket->duration_minutes, 'validity_type' => $ticket->validity_type, 'data_limit_mb' => $ticket->data_limit_mb]); @endphp
                                                    <tr>
                                                        <td><b style="letter-spacing: 1px;">{{ $ticket->ticket_code }}</b></td>
                                                        <td>{{ $ticket->plan_name }}</td>
                                                        <td>{{ $ticket->router_name }}</td>
                                                        <td>{{ $ticket->phone ?: '—' }}</td>
                                                        <td>{{ ['stk' => 'M-Pesa prompt', 'paybill' => 'Paybill', 'manual' => 'Manual'][$ticket->source] ?? $ticket->source }}</td>
                                                        <td>Kes {{ number_format($ticket->amount) }}</td>
                                                        <td><span class="badge badge-{{ $badges[$ticket->status] ?? 'secondary' }}">{{ $ticket->status }}</span></td>
                                                        <td>{!! $ticket->provision_status == 'done' ? '<span class="text-success">Yes</span>' : ($ticket->provision_status == 'failed' ? '<span class="text-danger">Failed</span>' : '<span class="text-warning">Waiting</span>') !!}</td>
                                                        <td>{{ $ticket->activated_at ? date('D jS M g:i:sA', strtotime($ticket->activated_at)) : '—' }}</td>
                                                        <td>{{ $usage['time_used'] }} / {{ $usage['time_left'] }}</td>
                                                        <td>{{ $usage['data_used'] }}</td>
                                                        <td>
                                                            @php $btnText = "<i class=\"ft-eye\"></i> View"; @endphp
                                                            <x-button-link :btnText="$btnText" btnType="primary" btnSize="sm" :btnLink="route('hotspot.ticket', $ticket->ticket_id)" />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if (count($tickets) >= $limit)
                                        <p class="text-muted mb-0">Showing the latest {{ $limit }} tickets. Narrow the filter to see others.</p>
                                    @endif
                                    @endif
                                </div>
                            </div>
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
