<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class clientValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $allow  "allow_admin" also lets logged-in admins through (e.g. STK push from the client profile)
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $allow = null)
    {
        if(!session()->has("database_name")){
            // this means that the database name is not set in the session and it is not provided as a parameter, we redirect to the login page
            return redirect("/Client-Login")->with("error", "Login and try again!!");
        }

        if($allow == "allow_admin" && session("auth") == "admin"){
            return $next($request);
        }

        if(session("auth") != "client"){
            // this means that the user is not a client and it is not provided as a parameter, we redirect to the login page
            return redirect("/Client-Login")->with("error", "Login and try again!!");
        }
        return $next($request);
    }
}
