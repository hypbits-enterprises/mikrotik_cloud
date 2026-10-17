{{-- Router file alogin.html: downloaded onto the router (hypbits-hs html directory) by the crontab
     agent; RouterOS fills the $(…) values. It only forwards the phone to the Hypbits portal. --}}
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WiFi</title>
</head>
<body>
<form name="hs" action="{{ $portal }}/online" method="get">
<input type="hidden" name="user" value="$(username)">
<input type="hidden" name="mac" value="$(mac)">
<input type="hidden" name="logout" value="$(link-logout)">
<input type="hidden" name="dst" value="$(link-redirect)">
<noscript><input type="submit" value="Continue"></noscript>
</form>
<script>document.hs.submit();</script>
</body>
</html>
