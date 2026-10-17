{{-- Router file redirect.html: downloaded onto the router (hypbits-hs html directory) by the crontab
     agent. Same as the RouterOS default: forwards to $(link-redirect). --}}
<html>
<head>
<meta http-equiv="refresh" content="0; url=$(link-redirect)">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<title>WiFi</title>
</head>
<body>
<a href="$(link-redirect)">Continue</a>
</body>
</html>
