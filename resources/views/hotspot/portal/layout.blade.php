<!DOCTYPE html>
{{-- Captive portal layout: the one place outside the Chameleon admin theme. Kept tiny (phones on a
     hotspot with no internet yet): inline CSS, no frameworks, no web fonts. --}}
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $branding['title'] }}</title>
    <style>
        :root { --primary: {{ $branding['primary_color'] }}; --secondary: {{ $branding['secondary_color'] }}; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f2f5f8; color: #263238; font-size: 16px; line-height: 1.4; }
        .top { background: var(--secondary); color: #fff; text-align: center; padding: 20px 16px 52px; }
        .top img { max-width: 160px; max-height: 80px; display: block; margin: 0 auto 10px; }
        .top h1 { margin: 0; font-size: 1.35rem; }
        .top p { margin: 6px 0 0; opacity: .85; font-size: .95rem; }
        .wrap { max-width: 460px; margin: -36px auto 24px; padding: 0 12px; }
        .card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 16px; margin-bottom: 14px; }
        .card h2 { font-size: 1.05rem; margin: 0 0 12px; }
        .plan { display: flex; align-items: center; border: 2px solid #e3e8ee; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; cursor: pointer; }
        .plan input { margin: 0 10px 0 0; accent-color: var(--primary); width: 18px; height: 18px; }
        .plan.selected { border-color: var(--primary); background: #f7fbff; }
        .plan .name { font-weight: 600; }
        .plan .info { font-size: .82rem; color: #607d8b; }
        .plan .price { margin-left: auto; font-weight: 700; color: var(--primary); white-space: nowrap; padding-left: 8px; }
        label.field { display: block; font-size: .9rem; margin: 10px 0 4px; color: #455a64; }
        input.text { width: 100%; padding: 12px; font-size: 1.05rem; border: 1px solid #cfd8dc; border-radius: 8px; }
        input.code { text-transform: uppercase; letter-spacing: 3px; text-align: center; font-weight: 700; }
        .btn { display: block; width: 100%; padding: 13px; margin-top: 12px; border: 0; border-radius: 8px; background: var(--primary); color: #fff; font-size: 1.05rem; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer; }
        .btn.light { background: #eceff1; color: #263238; }
        .btn[disabled] { opacity: .6; }
        .msg { border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; font-size: .95rem; }
        .msg.error { background: #fdecea; color: #b71c1c; }
        .msg.info { background: #e8f4fd; color: #0d47a1; }
        .msg.ok { background: #e8f5e9; color: #1b5e20; }
        .hidden { display: none; }
        .rows div { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eef1f4; }
        .rows div:last-child { border-bottom: 0; }
        .rows span:last-child { font-weight: 600; }
        .foot { text-align: center; font-size: .85rem; color: #78909c; }
        .foot a { color: var(--primary); }
    </style>
</head>
<body>
    <div class="top">
        @if ($branding['logo_path'])
            <img id="portal_logo" src="{{ asset($branding['logo_path']) }}" alt="">
        @elseif ($preview ?? false)
            <img id="portal_logo" class="hidden" alt="">
        @endif
        <h1>{{ $branding['title'] }}</h1>
        @yield('subtitle')
    </div>
    <div class="wrap">
        @yield('content')
        @if ($branding['support_phone'])
            <p class="foot">Need help? Call <a href="tel:{{ $branding['support_phone'] }}">{{ $branding['support_phone'] }}</a></p>
        @endif
    </div>
    @yield('scripts')
    @if ($preview ?? false)
        {{-- Branding page preview: show a logo chosen in the form before it is uploaded --}}
        <script>
            window.addEventListener('message', function (e) {
                if (e.origin !== window.location.origin || !e.data || typeof e.data.logo !== 'string') { return; }
                var img = document.getElementById('portal_logo');
                img.src = e.data.logo;
                img.className = '';
            });
        </script>
    @endif
</body>
</html>
