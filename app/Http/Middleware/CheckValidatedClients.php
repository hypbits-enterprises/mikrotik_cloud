<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckValidatedClients
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $unvalidated = DB::connection("mysql2")->select("SELECT * FROM client_tables WHERE validated = '0';");
        session(["unvalidated" => $unvalidated, "unvalidated_users" => count($unvalidated)]);
        return $next($request);
    }
}
