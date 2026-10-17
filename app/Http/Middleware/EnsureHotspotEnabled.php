<?php

namespace App\Http\Middleware;

use App\Classes\HotspotAccess;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks /hotspot/* unless an admin is logged in and the org has hotspot enabled in the manager DB.
 */
class EnsureHotspotEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (session('auth') != 'admin' || !session()->has('organization_id')) {
            return redirect('/Login')->with('error', 'Login and try again!');
        }

        if (!HotspotAccess::enabledForOrg(session('organization_id'))) {
            return response()->view('hotspot.disabled', ['reason' => 'disabled'], 403);
        }

        return $next($request);
    }
}
