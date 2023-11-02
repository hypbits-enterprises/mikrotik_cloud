<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        '/Transact',
        '/connect_router',
        '/remove_interface_bridge',
        '/add_bridge',
        '/remove_bridge',
        '/change_bridge',
        '/get_setting',
        '/set_dynamic',
        '/set_static_access',
        '/set_pppoe_assignment',
        '/set_pool',
        '/add_pppoe_profile',
        '/save_ppoe_server',
        '/add_security',
        '/save_ssid',
        '/get_interface_supply',
        '/get_wireless',
        '/get_interface_config',
        "/get_internet_access",
        '/get_supply_method',
        'wireless_settings',
        '/Client-due-demographics'
    ];
}
