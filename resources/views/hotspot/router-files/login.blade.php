{{-- Router file login.html: downloaded onto the router (hypbits-hs html directory) by the crontab
     agent; RouterOS fills the $(…) values. It forwards the phone to the Hypbits portal. If the
     portal does not answer within a few seconds (our server or the ISP's uplink is down), it shows
     a plain ticket-code form that logs in on the router directly, so customers with a code can
     still get online. Title and colour are copied in when the file is downloaded. --}}
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>
<style>
body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f2f5f8; color: #263238; }
#fallback { display: none; max-width: 380px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
h1 { font-size: 1.2rem; margin: 0 0 6px; }
p { margin: 0 0 12px; color: #607d8b; font-size: .95rem; }
.error { color: #b71c1c; }
input.code { width: 100%; box-sizing: border-box; padding: 12px; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 3px; text-align: center; border: 1px solid #cfd8dc; border-radius: 8px; }
button { width: 100%; margin-top: 12px; padding: 13px; border: 0; border-radius: 8px; background: {{ $color }}; color: #fff; font-size: 1.05rem; font-weight: 600; }
#wait { text-align: center; margin-top: 60px; color: #607d8b; }
</style>
</head>
<body>
<div id="wait">Loading…</div>
<form name="hs" action="{{ $portal }}" method="get">
<input type="hidden" name="mac" value="$(mac)">
<input type="hidden" name="ip" value="$(ip)">
<input type="hidden" name="login" value="$(link-login-only)">
<input type="hidden" name="dst" value="$(link-orig)">
<input type="hidden" name="error" value="$(error)">
<noscript><input type="submit" value="Continue"></noscript>
</form>
<div id="fallback">
<h1>{{ $title }}</h1>
<p>The payment page can't be reached right now. If you have a ticket code, enter it to connect.</p>
$(if error)<p class="error">$(error)</p>$(endif)
<form name="local" action="$(link-login-only)" method="post" onsubmit="return send();">
<input class="code" name="username" type="text" maxlength="12" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="Ticket code">
<input type="hidden" name="password" value="">
<input type="hidden" name="dst" value="$(link-orig)">
<input type="hidden" name="popup" value="false">
<button type="submit">Connect</button>
</form>
</div>
<script>
function send() {
    var f = document.local;
    f.username.value = f.username.value.replace(/\s+/g, '').toUpperCase();
    f.password.value = f.username.value;
    return f.username.value !== '';
}
(function () {
    var done = false;
    function fallback() {
        if (done) { return; }
        done = true;
        document.getElementById('wait').style.display = 'none';
        document.getElementById('fallback').style.display = 'block';
    }
    // a tiny image from the portal: loads -> go to the portal, fails or too slow -> local form
    var probe = new Image();
    probe.onload = function () {
        if (done) { return; }
        done = true;
        document.hs.submit();
    };
    probe.onerror = fallback;
    setTimeout(fallback, 6000);
    probe.src = '{{ $portal }}/ping?t=' + new Date().getTime();
})();
</script>
</body>
</html>
