<?php

namespace App\Http\Middleware;

use App\Http\Controllers\login;
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
        // change db
        $change_db = new login();
        $change_db->change_db();
        $unvalidated = DB::connection("mysql2")->select("SELECT * FROM client_tables WHERE validated = '0';");

        // check if the user has been given rights to manage clients
        $privilleged = session("priviledges");
        $show_option = $this->showOption($privilleged, "My Clients");
        if ($show_option) {
            session(["unvalidated" => $unvalidated, "unvalidated_users" => count($unvalidated)]);
        }else{
            session(["unvalidated" => [], "unvalidated_users" => 0]);
        }
        return $next($request);
    }

    function showOption($priviledges,$name){
        if ($this->isJson($priviledges)) {
            $priviledges = json_decode($priviledges);
            for ($index=0; $index < count($priviledges); $index++) { 
                if ($priviledges[$index]->option == $name) {
                    if ($priviledges[$index]->view) {
                        return true;
                    }else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}
