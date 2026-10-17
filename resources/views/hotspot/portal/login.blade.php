@extends('hotspot.portal.layout')

@section('subtitle')
    @if ($branding['welcome_text'])
        <p>{{ $branding['welcome_text'] }}</p>
    @endif
@endsection

@section('content')
    @if ($logged_out)
        <div class="msg ok">You have logged out. {{ $code ? 'To continue on ticket ' . $code . ', tap Connect (it works until its time or data runs out).' : 'Enter your ticket code to connect again, or buy a package.' }}</div>
    @endif
    @if ($error)
        <div class="msg error">{{ $error }}</div>
    @endif

    <div class="card">
        <h2>Choose a package</h2>
        @forelse ($plans as $plan)
            <label class="plan" data-plan="{{ $plan->plan_id }}">
                <input type="radio" name="plan_id" value="{{ $plan->plan_id }}">
                <div>
                    <div class="name">{{ $plan->plan_name }}</div>
                    <div class="info">
                        {{ \App\Classes\HotspotPlans::durationText($plan->duration_minutes) }}{{ $plan->validity_type == 'paused' ? ' of connected time' : '' }}
                        @if ($plan->download_speed) · up to {{ \App\Classes\HotspotPlans::speedText($plan->download_speed) }} @endif
                        @if ($plan->data_limit_mb) · {{ \App\Classes\HotspotPlans::dataText($plan->data_limit_mb) }} @endif
                        @if ($plan->shared_users > 1) · {{ $plan->shared_users }} devices @endif
                    </div>
                </div>
                <div class="price">KES {{ rtrim(rtrim(number_format($plan->price, 2), '0'), '.') }}</div>
            </label>
        @empty
            <p>No packages are on sale right now.</p>
        @endforelse
        @if (count($plans) > 0)
            <label class="field" for="phone">M-Pesa phone number</label>
            <input class="text" id="phone" type="tel" inputmode="numeric" autocomplete="tel" placeholder="07XX XXX XXX">
            <div id="pay_msg" class="msg info hidden" style="margin: 12px 0 0;"></div>
            <button type="button" class="btn" id="pay_btn">Pay with M-Pesa</button>
        @endif
    </div>

    <div class="card">
        <h2>I have a ticket code</h2>
        <div id="code_msg" class="msg error hidden"></div>
        <input class="text code" id="code" type="text" maxlength="12" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="ABC123" value="{{ $code ?? '' }}">
        <button type="button" class="btn" id="code_btn">Connect</button>
    </div>

    {{-- filled and submitted to the router's login link once the code is accepted --}}
    <form id="router_login" method="post" action="{{ $login_url ?? '' }}" class="hidden">
        <input type="hidden" name="username" id="rl_user">
        <input type="hidden" name="password" id="rl_pass">
        <input type="hidden" name="dst" value="{{ $dst ?? '' }}">
        <input type="hidden" name="popup" value="false">
    </form>
@endsection

@section('scripts')
<script>
(function () {
    var preview = {{ $preview ? 'true' : 'false' }};
    var loginUrl = @json($login_url);
    var codeUrl = @json(route('hs.code', $key));
    var mac = @json($mac);

    function show(el, text, kind) {
        el.textContent = text;
        el.className = 'msg ' + kind;
    }

    var plans = document.querySelectorAll('.plan');
    for (var i = 0; i < plans.length; i++) {
        plans[i].addEventListener('change', function () {
            for (var j = 0; j < plans.length; j++) {
                plans[j].className = plans[j].querySelector('input').checked ? 'plan selected' : 'plan';
            }
        });
    }

    var payBtn = document.getElementById('pay_btn');
    if (payBtn) {
        payBtn.addEventListener('click', function () {
            var msg = document.getElementById('pay_msg');
            if (!document.querySelector('.plan input:checked')) {
                show(msg, 'Choose a package first.', 'error');
                return;
            }
            show(msg, 'Paying online is not available yet. Ask for a ticket code and enter it below.', 'info');
        });
    }

    var codeBtn = document.getElementById('code_btn');
    var codeInput = document.getElementById('code');
    var codeMsg = document.getElementById('code_msg');
    codeInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { codeBtn.click(); }
    });
    codeBtn.addEventListener('click', function () {
        var code = codeInput.value.replace(/\s+/g, '').toUpperCase();
        if (code === '') {
            show(codeMsg, 'Enter your ticket code.', 'error');
            return;
        }
        if (preview) {
            show(codeMsg, 'Preview: the code is not checked here.', 'info');
            return;
        }
        if (!loginUrl) {
            show(codeMsg, 'Connect to the WiFi network first, then open any website to get back to this page.', 'error');
            return;
        }
        codeBtn.disabled = true;
        codeBtn.textContent = 'Checking…';
        var xhr = new XMLHttpRequest();
        xhr.open('POST', codeUrl);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function () {
            var reply = null;
            try { reply = JSON.parse(xhr.responseText); } catch (e) {}
            if (xhr.status === 429) {
                reply = { ok: false, message: 'Too many tries. Wait a minute and try again.' };
            }
            if (reply && reply.ok) {
                codeBtn.textContent = 'Connecting…';
                document.getElementById('rl_user').value = reply.code;
                document.getElementById('rl_pass').value = reply.code;
                document.getElementById('router_login').submit();
                return;
            }
            codeBtn.disabled = false;
            codeBtn.textContent = 'Connect';
            show(codeMsg, reply && reply.message ? reply.message : 'Something went wrong. Try again.', 'error');
        };
        xhr.onerror = function () {
            codeBtn.disabled = false;
            codeBtn.textContent = 'Connect';
            show(codeMsg, 'Could not reach the server. Try again.', 'error');
        };
        xhr.send('code=' + encodeURIComponent(code) + '&mac=' + encodeURIComponent(mac || ''));
    });
})();
</script>
@endsection
