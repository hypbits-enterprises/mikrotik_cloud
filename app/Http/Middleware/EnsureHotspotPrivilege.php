<?php

namespace App\Http\Middleware;

use App\Classes\HotspotAccess;
use Closure;
use Illuminate\Http\Request;

/**
 * Server-side check of one hotspot privilege, e.g. ->middleware('hotspotCan:Hotspot Plans').
 */
class EnsureHotspotPrivilege
{
    public function handle(Request $request, Closure $next, string $option)
    {
        if (!HotspotAccess::can(session('priviledges'), $option)) {
            return response()->view('hotspot.disabled', ['reason' => 'privilege', 'option' => $option], 403);
        }

        return $next($request);
    }
}
