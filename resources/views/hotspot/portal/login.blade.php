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
        @php $paybill = $payments['shortcode'] !== '' && $payments['shortcode_type'] === 'paybill'; @endphp
        @if (count($plans) > 0 && ($payments['stk'] || $paybill))
            <label class="field" for="phone">M-Pesa phone number</label>
            <input class="text" id="phone" type="tel" inputmode="numeric" autocomplete="tel" placeholder="07XX XXX XXX">
            @if ($payments['stk'] && $paybill)
                <label class="plan selected" style="margin-top: 10px;"><input type="radio" name="method" value="stk" checked><div><div class="name">M-Pesa prompt</div><div class="info">We send the payment request to your phone</div></div></label>
                <label class="plan"><input type="radio" name="method" value="paybill"><div><div class="name">Pay to Paybill {{ $payments['shortcode'] }}</div><div class="info">You pay from the M-Pesa menu with an account number we give you</div></div></label>
            @else
                <input type="hidden" name="method" value="{{ $payments['stk'] ? 'stk' : 'paybill' }}">
            @endif
            <div id="pay_msg" class="msg error hidden" style="margin: 12px 0 0;"></div>
            <button type="button" class="btn" id="pay_btn">Pay with M-Pesa</button>
        @elseif (count($plans) > 0)
            <div class="msg info" style="margin: 12px 0 0;">Paying online is not set up yet. Ask for a ticket code and enter it below.</div>
        @endif
    </div>

    {{-- shown while a payment is in progress --}}
    <div class="card hidden" id="pay_wait">
        <h2 id="wait_title">Waiting for your payment</h2>
        <div id="wait_paybill" class="hidden">
            <p style="margin: 0 0 10px;">On your phone open <b>M-Pesa → Lipa na M-Pesa → Pay Bill</b> and enter:</p>
            <div class="rows">
                <div><span>Business number</span><span id="wait_shortcode"></span></div>
                <div><span>Account number</span><span id="wait_account" style="letter-spacing: 2px;"></span></div>
                <div><span>Amount</span><span id="wait_amount"></span></div>
            </div>
        </div>
        <div id="wait_msg" class="msg info" style="margin: 12px 0 0;"></div>
        <button type="button" class="btn light hidden" id="wait_retry">Choose again</button>
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

    var plans = document.querySelectorAll('.plan[data-plan]');
    for (var i = 0; i < plans.length; i++) {
        plans[i].addEventListener('change', function () {
            for (var j = 0; j < plans.length; j++) {
                plans[j].className = plans[j].querySelector('input').checked ? 'plan selected' : 'plan';
            }
        });
    }

    var payUrl = @json(route('hs.pay', $key));
    var statusBase = @json(url('/hs/' . $key . '/pay'));

    function request(method, url, body, done) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader('Accept', 'application/json');
        if (body) { xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); }
        xhr.onload = function () {
            var reply = null;
            try { reply = JSON.parse(xhr.responseText); } catch (e) {}
            if (xhr.status === 429) { reply = { ok: false, state: 'failed', message: 'Too many tries. Wait a few minutes and try again.' }; }
            done(reply);
        };
        xhr.onerror = function () { done(null); };
        xhr.send(body);
    }

    // after payment: log in on the router with the new code (same as "I have a ticket code")
    function loginWith(code) {
        if (!loginUrl) {
            show(document.getElementById('wait_msg'), 'Paid. Your ticket code is ' + code + '. Connect to the WiFi and enter it below.', 'ok');
            return;
        }
        document.getElementById('rl_user').value = code;
        document.getElementById('rl_pass').value = code;
        document.getElementById('router_login').submit();
    }

    function waitFor(payment) {
        var started = new Date().getTime();
        var waitMsg = document.getElementById('wait_msg');
        var limit = payment.method === 'paybill' ? 10 * 60000 : 3 * 60000;
        function poll() {
            request('GET', statusBase + '/' + payment.payment_id + '?t=' + encodeURIComponent(payment.token), null, function (reply) {
                var state = reply ? reply.state : 'waiting';
                if (state === 'ready') {
                    document.getElementById('wait_title').textContent = 'Paid';
                    show(waitMsg, 'Payment received. Connecting you now…', 'ok');
                    loginWith(reply.code);
                    return;
                }
                if (state === 'failed') {
                    show(waitMsg, reply.message || 'The payment did not go through.', 'error');
                    document.getElementById('wait_retry').className = 'btn light';
                    return;
                }
                if (state === 'activating') {
                    document.getElementById('wait_title').textContent = 'Paid';
                    show(waitMsg, 'Payment received. Setting up your ticket ' + (reply.code || '') + '…', 'ok');
                } else if (new Date().getTime() - started > limit) {
                    show(waitMsg, 'We have not received the payment yet. If you have paid, your code will come by SMS: enter it below when it arrives.', 'info');
                    document.getElementById('wait_retry').className = 'btn light';
                    return;
                }
                setTimeout(poll, 4000);
            });
        }
        setTimeout(poll, 4000);
    }

    var payBtn = document.getElementById('pay_btn');
    if (payBtn) {
        var methods = document.querySelectorAll('input[name=method][type=radio]');
        for (var m = 0; m < methods.length; m++) {
            methods[m].addEventListener('change', function () {
                for (var n = 0; n < methods.length; n++) {
                    methods[n].parentNode.className = methods[n].checked ? 'plan selected' : 'plan';
                }
            });
        }
        payBtn.addEventListener('click', function () {
            var msg = document.getElementById('pay_msg');
            var plan = document.querySelector('input[name=plan_id]:checked');
            var phone = document.getElementById('phone').value;
            var method = document.querySelector('input[name=method]:checked') || document.querySelector('input[name=method]');
            if (!plan) { show(msg, 'Choose a package first.', 'error'); return; }
            if (phone.replace(/\D/g, '').length < 9) { show(msg, 'Enter your M-Pesa phone number.', 'error'); return; }
            if (preview) { show(msg, 'Preview: no payment is started here.', 'info'); return; }
            payBtn.disabled = true;
            payBtn.textContent = 'Starting…';
            request('POST', payUrl, 'plan_id=' + encodeURIComponent(plan.value) + '&phone=' + encodeURIComponent(phone) + '&method=' + encodeURIComponent(method.value) + '&mac=' + encodeURIComponent(mac || ''), function (reply) {
                payBtn.disabled = false;
                payBtn.textContent = 'Pay with M-Pesa';
                if (!reply || !reply.ok) {
                    show(msg, reply && reply.message ? reply.message : 'Could not reach the server. Try again.', 'error');
                    return;
                }
                msg.className = 'msg error hidden';
                var wait = document.getElementById('pay_wait');
                wait.className = 'card';
                if (reply.method === 'paybill') {
                    document.getElementById('wait_paybill').className = '';
                    document.getElementById('wait_shortcode').textContent = reply.shortcode;
                    document.getElementById('wait_account').textContent = reply.account;
                    document.getElementById('wait_amount').textContent = 'KES ' + reply.amount;
                    show(document.getElementById('wait_msg'), 'Waiting for your payment… This page connects you as soon as it arrives.', 'info');
                } else {
                    show(document.getElementById('wait_msg'), 'Check your phone and enter your M-Pesa PIN to pay KES ' + reply.amount + '. Waiting…', 'info');
                }
                wait.scrollIntoView();
                waitFor(reply);
            });
        });
        document.getElementById('wait_retry').addEventListener('click', function () {
            document.getElementById('pay_wait').className = 'card hidden';
            document.getElementById('wait_paybill').className = 'hidden';
            document.getElementById('wait_retry').className = 'btn light hidden';
            document.getElementById('wait_title').textContent = 'Waiting for your payment';
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
