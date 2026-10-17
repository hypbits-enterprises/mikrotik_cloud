{{-- Router file error.html: downloaded onto the router (hypbits-hs html directory) by the crontab
     agent; RouterOS fills the $(…) values. It only forwards the phone to the Hypbits portal. --}}
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WiFi</title>
</head>
<body>
<form name="hs" action="{{ $portal }}" method="get">
<input type="hidden" name="mac" value="$(mac)">
<input type="hidden" name="login" value="$(link-login-only)">
<input type="hidden" name="error" value="$(error)">
<noscript><input type="submit" value="Continue"></noscript>
</form>
<script>document.hs.submit();</script>
</body>
</html>
