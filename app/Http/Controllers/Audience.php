<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Audience extends Controller
{
    // Live match count + full client list for the shared audience-builder component's
    // (client-side paginated) preview table. Used by both the SMS/WhatsApp compose
    // page and the WhatsApp bulk page while the user is adjusting filters, before
    // anything is actually sent.
    function preview(Request $req)
    {
        $change_db = new login();
        $change_db->change_db();

        $filters = $req->only([
            'client_status',
            'router_id',
            'region',
            'subregion',
            'assignment',
            'client_profile',
            'preferred_channel',
            'payments_status',
        ]);

        $clients = $this->getFilteredAudience($filters);

        $subregion_regions = $this->getSubregionRegionMap();
        $rows = array_map(function ($c) use ($subregion_regions) {
            return [
                'client_name'      => $c->client_name,
                'clients_contacts' => $c->clients_contacts,
                'region'           => $subregion_regions[$c->region ?? ''] ?? '',
                'subregion'        => $c->region,
                'router_name'      => $c->router_display_name,
            ];
        }, $clients);

        return response()->json([
            'count'   => count($rows),
            'clients' => $rows,
        ]);
    }
}
