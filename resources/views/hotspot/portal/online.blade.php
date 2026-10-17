@extends('hotspot.portal.layout')

@section('subtitle')
    <p>You're online</p>
@endsection

@section('content')
    <div class="card">
        <div class="msg ok" style="margin-bottom: 12px;">You are connected to the internet.</div>
        @if ($ticket && $plan)
            <div class="rows">
                <div><span>Package</span><span>{{ $plan->plan_name }}</span></div>
                @if ($plan->validity_type == 'elapsed' && $ticket->expires_at)
                    <div><span>Valid until</span><span>{{ date('D jS M g:i A', strtotime($ticket->expires_at)) }}</span></div>
                @endif
                <div><span>Time left</span><span>{{ $usage['time_left'] }}</span></div>
                <div><span>Data used</span><span>{{ $usage['data_used'] }}</span></div>
                @if ($plan->data_limit_mb)
                    <div><span>Data left</span><span>{{ $usage['data_left'] }}</span></div>
                @endif
                <div><span>Devices</span><span>{{ $plan->shared_users }}</span></div>
                <div><span>Ticket code</span><span>{{ $ticket->ticket_code }}</span></div>
            </div>
            <p class="foot" style="margin: 10px 0 0;">
                {{ $plan->validity_type == 'paused' ? 'Time only counts while you are connected.' : 'The time runs from your first login.' }}
                Updated every minute.
            </p>
        @endif
        @if ($dst)
            <a class="btn" href="{{ $dst }}">Continue</a>
        @endif
        <a class="btn light" href="{{ route('hs.portal', $key) }}">Buy more time</a>
        @if ($logout_url)
            <a class="btn light" href="{{ $logout_url }}">Log out</a>
        @endif
    </div>
@endsection
