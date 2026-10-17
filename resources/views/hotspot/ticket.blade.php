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
    <title>Hypbits - Hotspot Ticket</title>
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
                    <h3 class="content-header-title">Hotspot Ticket</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/hotspot/dashboard">Hotspot</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotspot.tickets') }}">Tickets</a></li>
                                <li class="breadcrumb-item"><a href="#">{{ $ticket->ticket_code }}</a></li>
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
                @php $badges = ["pending" => "warning", "active" => "success", "used" => "secondary", "expired" => "dark", "failed" => "danger"]; $can_end = in_array($ticket->status, ['pending', 'active']); @endphp
                <div class="row mb-1">
                    <div class="col-12">
                        @php $btnText = "<i class=\"ft-arrow-left\"></i> Back to Tickets"; @endphp
                        <x-button-link :btnText="$btnText" btnType="secondary" btnSize="sm" :btnLink="route('hotspot.tickets', ['date' => date('Y-m-d', strtotime($ticket->date_created))])" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-tag"></i> Ticket <span style="letter-spacing: 2px;">{{ $ticket->ticket_code }}</span></h4></div>
                            <div class="card-content"><div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr><th style="width: 45%">Status</th><td><span class="badge badge-{{ $badges[$ticket->status] ?? 'secondary' }}">{{ $ticket->status }}</span>{{ $ticket->last_error && in_array($ticket->status, ['expired', 'failed']) ? ' — ' . $ticket->last_error : '' }}</td></tr>
                                    <tr><th>Plan</th><td>{{ $plan->plan_name ?? '—' }}{{ $plan ? ' — ' . \App\Classes\HotspotPlans::durationText($plan->duration_minutes) . ', ' . ($plan->validity_type == 'paused' ? 'while connected' : 'from first login') : '' }}</td></tr>
                                    <tr><th>Router</th><td>{{ $ticket->router_name }}</td></tr>
                                    <tr><th>Sold</th><td>{{ $ticket->source == 'stk' ? 'M-Pesa' : 'Manual (cash)' }}, Kes {{ number_format($ticket->amount) }}, {{ date('D jS M Y g:i:sA', strtotime($ticket->date_created)) }}{{ $created_by ? ' by ' . $created_by : '' }}</td></tr>
                                    <tr><th>Phone</th><td>{{ $ticket->phone ?: '—' }}</td></tr>
                                    <tr><th>On the router</th><td>{{ $ticket->provision_status == 'done' ? 'Yes, since ' . date('D jS M g:i:sA', strtotime($ticket->provisioned_at)) : ($ticket->provision_status == 'failed' ? 'Failed: ' . $ticket->last_error : 'Waiting (tries so far: ' . $ticket->provision_attempts . ')' . ($ticket->last_error ? ' — ' . $ticket->last_error : '')) }}</td></tr>
                                    <tr><th>First login</th><td>{{ $ticket->activated_at ? date('D jS M Y g:i:sA', strtotime($ticket->activated_at)) : 'Not used yet' }}</td></tr>
                                    @if ($ticket->expires_at)
                                        <tr><th>Ends</th><td>{{ date('D jS M Y g:i:sA', strtotime($ticket->expires_at)) }}</td></tr>
                                    @endif
                                    <tr><th>Time used / left</th><td>{{ $usage['time_used'] }} / {{ $usage['time_left'] }}</td></tr>
                                    <tr><th>Data used / left</th><td>{{ $usage['data_used'] }} / {{ $usage['data_left'] }}</td></tr>
                                </table>
                                @if ($ticket->phone)
                                    <form method="POST" action="{{ route('hotspot.ticket_sms', $ticket->ticket_id) }}" class="d-inline">
                                        @csrf
                                        @php $btnText = "<i class=\"ft-message-square\"></i> Resend code by SMS"; @endphp
                                        <x-button :btnText="$btnText" btnType="info" btnSize="sm" type="submit" otherClasses="mr-1" :readOnly="$readonly ? 'disabled' : ''" />
                                    </form>
                                @endif
                                @if ($can_end)
                                    @php $btnText = "<i class=\"ft-x-circle\"></i> End ticket now"; @endphp
                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" otherAttributes="data-toggle=modal data-target=#hotspot_ticket_end_modal" :readOnly="$readonly ? 'disabled' : ''" />
                                @endif
                            </div></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title"><i class="ft-activity"></i> Usage readings</h4></div>
                            <div class="card-content"><div class="card-body">
                                @if (count($snapshots) == 0)
                                    <p class="text-muted mb-0">No readings yet. They are taken every 5 minutes while the ticket is in use.</p>
                                @else
                                    <table class="table table-sm">
                                        <thead><tr><th>Time</th><th>Connected time</th><th>Download</th><th>Upload</th></tr></thead>
                                        <tbody>
                                            @foreach ($snapshots as $snapshot)
                                                <tr>
                                                    <td>{{ date('D jS M g:i:sA', strtotime($snapshot->captured_at)) }}</td>
                                                    <td>{{ \App\Classes\HotspotTickets::seconds($snapshot->uptime_seconds) }}</td>
                                                    <td>{{ round($snapshot->bytes_out / 1048576, 1) }} MB</td>
                                                    <td>{{ round($snapshot->bytes_in / 1048576, 1) }} MB</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div></div>
                        </div>
                    </div>
                </div>
                @if ($can_end)
                <div class="modal fade text-left" id="hotspot_ticket_end_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('hotspot.ticket_expire', $ticket->ticket_id) }}">
                                @csrf
                                <div class="modal-header bg-danger white">
                                    <h4 class="modal-title white">End ticket {{ $ticket->ticket_code }} now?</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">The customer is disconnected and the code stops working straight away. This can't be undone; the remaining time is not refunded automatically.</p>
                                </div>
                                <div class="modal-footer">
                                    @php $btnText = "Cancel"; @endphp
                                    <x-button :btnText="$btnText" btnType="secondary" btnSize="sm" otherAttributes="data-dismiss=modal" />
                                    @php $btnText = "<i class=\"ft-x-circle\"></i> End ticket"; @endphp
                                    <x-button :btnText="$btnText" btnType="danger" btnSize="sm" type="submit" btnId="hotspot_ticket_end" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
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
