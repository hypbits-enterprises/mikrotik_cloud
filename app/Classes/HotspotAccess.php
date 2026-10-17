<?php

namespace App\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Who can use the hotspot module.
 *
 * - The org's flag lives in the manager DB (org_hotspot_settings.hotspot_enabled), off by default.
 * - Hotspot privileges use the same admin JSON as the ISP options. An explicit entry always wins.
 *   An option missing from the JSON is allowed only for owners: admins whose "Account and Profile"
 *   option has view on, read-only off and has not expired. Non-JSON privileges keep today's
 *   meaning (full access).
 */
class HotspotAccess
{
    public static function enabledForOrg($organizationId): bool
    {
        if (!$organizationId) {
            return false;
        }
        return Cache::remember(self::cacheKey($organizationId), config('hotspot.enabled_cache_seconds', 300), function () use ($organizationId) {
            return DB::table('org_hotspot_settings')->where('organization_id', $organizationId)->value('hotspot_enabled') == 1;
        });
    }

    /**
     * Sidebar/top-bar state for components/menu.blade.php (an anonymous component, so it is
     * computed here). Mode comes from the URL so two tabs can be in different modes; an org
     * without hotspot keeps the ISP sidebar even on /hotspot URLs (the "not enabled" page).
     */
    public static function menuState(): array
    {
        $billingMode = request()->is('hotspot', 'hotspot/*') ? 'hotspot' : 'isp';
        $hotspotMenu = [];
        if (session('auth') == 'admin' && self::enabledForOrg(session('organization_id'))) {
            foreach (config('hotspot.menu', []) as $item) {
                if (Route::has($item['route']) && self::can(session('priviledges'), $item['privilege'])) {
                    $hotspotMenu[] = $item;
                }
            }
        }
        $hotspotEnabled = count($hotspotMenu) > 0;

        return [
            'billingMode' => $hotspotEnabled ? $billingMode : 'isp',
            'hotspotEnabled' => $hotspotEnabled,
            'hotspotMenu' => $hotspotMenu,
        ];
    }

    public static function cacheKey($organizationId): string
    {
        return "hotspot_enabled_{$organizationId}";
    }

    public static function options(): array
    {
        return config('hotspot.privileges', []);
    }

    public static function can($privileges, string $option): bool
    {
        $list = self::decode($privileges);
        if ($list === null) {
            return true;
        }
        $entry = self::find($list, $option);
        if ($entry !== null) {
            return !empty($entry->view) && !self::expired($entry);
        }
        return self::ownerFromList($list);
    }

    public static function readOnly($privileges, string $option): bool
    {
        if (session('block_edits') == 'true') {
            return true;
        }
        $list = self::decode($privileges);
        if ($list === null) {
            return false;
        }
        $entry = self::find($list, $option);
        if ($entry !== null) {
            return !empty($entry->readonly);
        }
        return !self::ownerFromList($list);
    }

    public static function canAny($privileges): bool
    {
        foreach (self::options() as $option) {
            if (self::can($privileges, $option)) {
                return true;
            }
        }
        return false;
    }

    public static function isOwner($privileges): bool
    {
        $list = self::decode($privileges);
        return $list === null ? true : self::ownerFromList($list);
    }

    private static function ownerFromList(array $list): bool
    {
        $entry = self::find($list, 'Account and Profile');
        return $entry !== null && !empty($entry->view) && empty($entry->readonly) && !self::expired($entry);
    }

    private static function decode($privileges): ?array
    {
        if (!is_string($privileges)) {
            return null;
        }
        $list = json_decode($privileges);
        return is_array($list) ? $list : null;
    }

    private static function find(array $list, string $option)
    {
        foreach ($list as $entry) {
            if (isset($entry->option) && $entry->option == $option) {
                return $entry;
            }
        }
        return null;
    }

    // same rule as showOption() in components/menu.blade.php
    private static function expired($entry): bool
    {
        if (isset($entry->expiry) && $entry->expiry == 'definate_expiry') {
            return date('YmdHis') > date('YmdHis', strtotime($entry->expiry_date ?? ''));
        }
        return false;
    }
}
