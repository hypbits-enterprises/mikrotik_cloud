<?php

/*
|--------------------------------------------------------------------------
| Hotspot module
|--------------------------------------------------------------------------
| privileges: options stored in admin_tables.priviledges (same JSON as the ISP options).
|   An option missing from an admin's JSON is allowed only for owners (admins with
|   "Account and Profile" view on and read-only off), see App\Classes\HotspotAccess.
|
| menu: hotspot sidebar, shown when the URL is /hotspot/*. An item is rendered only when its
|   route exists and the admin has its privilege, so a later stage's page appears in the menu
|   as soon as its route is added.
*/

return [

    'privileges' => [
        'Hotspot Dashboard',
        'Hotspot Plans',
        'Hotspot Tickets',
        'Hotspot Payments',
        'Hotspot Routers',
        'Hotspot Branding',
        'Hotspot Reports',
    ],

    'menu' => [
        ['label' => 'Dashboard', 'icon' => 'ft-home',        'route' => 'hotspot.dashboard', 'privilege' => 'Hotspot Dashboard', 'active' => 'hotspot_dashboard'],
        ['label' => 'Plans',     'icon' => 'ft-package',     'route' => 'hotspot.plans',     'privilege' => 'Hotspot Plans',     'active' => 'hotspot_plans'],
        ['label' => 'Tickets',   'icon' => 'ft-tag',         'route' => 'hotspot.tickets',   'privilege' => 'Hotspot Tickets',   'active' => 'hotspot_tickets'],
        ['label' => 'Payments',  'icon' => 'ft-dollar-sign', 'route' => 'hotspot.payments',  'privilege' => 'Hotspot Payments',  'active' => 'hotspot_payments'],
        ['label' => 'Routers',   'icon' => 'ft-wifi',        'route' => 'hotspot.routers',   'privilege' => 'Hotspot Routers',   'active' => 'hotspot_routers'],
        ['label' => 'Branding',  'icon' => 'ft-image',       'route' => 'hotspot.branding',  'privilege' => 'Hotspot Branding',  'active' => 'hotspot_branding'],
        ['label' => 'Reports',   'icon' => 'ft-bar-chart-2', 'route' => 'hotspot.reports',   'privilege' => 'Hotspot Reports',   'active' => 'hotspot_reports'],
    ],

    // seconds the org's hotspot_enabled flag is cached
    'enabled_cache_seconds' => 300,

    // crontab hotspot router agent (router writes), called like the SMS agent
    'agent_url' => env('HOTSPOT_AGENT_URL'),

    // host the routers must reach for the captive portal (walled garden). Defaults to APP_URL's host.
    'portal_host' => env('HOTSPOT_PORTAL_HOST', parse_url(env('APP_URL', ''), PHP_URL_HOST)),

    // router hotspot login page over HTTPS: false while testing, TRUE ON THE LIVE SERVER.
    // When on, the setup wizard needs a login page name and the router gets a Let's Encrypt
    // certificate for it (needs a public DNS record for the name and port 80 reachable).
    'login_https' => env('HOTSPOT_LOGIN_HTTPS', false),

    // outside production, router writes only on these routers: <org_db>:<router_id>,...
    'router_write_allowlist' => env('HOTSPOT_ROUTER_WRITE_ALLOWLIST', ''),
];
