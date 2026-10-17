<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Hotspot mode switch, the hotspot-enabled check and hotspot privileges (Stage 2).
 * Runs against the dev DBs (org 1 = LADYBIRD SYSTEMS, admin 1). Org 1's org_hotspot_settings row
 * is saved before each test and put back afterwards.
 *
 * Several views (components/menu.blade.php, the admin views) declare global functions, so a page can
 * render only once per PHP process: every test runs in its own process and makes one page request.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotModeTest extends TestCase
{
    private const ORG_ID = 1;
    private const ADMIN_ID = 1;

    private $savedSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedSettings = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
    }

    protected function tearDown(): void
    {
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        if ($this->savedSettings) {
            DB::table('org_hotspot_settings')->insert((array) $this->savedSettings);
        }
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        parent::tearDown();
    }

    private function enableHotspot(): void
    {
        DB::table('org_hotspot_settings')->insert([
            'organization_id' => self::ORG_ID,
            'hotspot_enabled' => 1,
            'date_created' => now()->format('YmdHis'),
        ]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
    }

    private function privileges(array $overrides = [], bool $owner = true): string
    {
        $list = [
            ['option' => 'My Clients', 'view' => true, 'readonly' => false],
            ['option' => 'Account and Profile', 'view' => $owner, 'readonly' => false],
        ];
        foreach ($overrides as $option => $view) {
            $list[] = ['option' => $option, 'view' => $view, 'readonly' => false, 'expiry' => 'indefinate_expiry', 'expiry_date' => ''];
        }
        return json_encode($list);
    }

    private function adminSession(string $privileges): array
    {
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        return [
            'auth' => 'admin',
            'Userid' => self::ADMIN_ID,
            'Usernames' => 'Test Admin',
            'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID,
            'organization' => $organization,
            'priviledges' => $privileges,
        ];
    }

    public function test_hotspot_pages_return_403_when_hotspot_is_disabled()
    {
        $this->withSession($this->adminSession($this->privileges()))
            ->get('/hotspot/dashboard')
            ->assertStatus(403)
            ->assertSee('Hotspot is not enabled for your account');
    }

    public function test_isp_page_has_no_mode_switch_when_hotspot_is_disabled()
    {
        $this->withSession($this->adminSession($this->privileges()))
            ->get('/Regions')
            ->assertStatus(200)
            ->assertDontSee('billing_mode_hotspot');
    }

    public function test_owner_sees_hotspot_dashboard_and_switch_when_enabled()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges()))
            ->get('/hotspot/dashboard')
            ->assertStatus(200)
            ->assertSee("Today's Sales", false)
            ->assertSee('Active Users')
            ->assertSee('Tickets Sold Today')
            ->assertSee('Hotspot Routers')
            ->assertSee('test_mpesa_credentials')
            ->assertSee('billing_mode_hotspot')
            ->assertSee(route('hotspot.dashboard'));
    }

    public function test_isp_page_shows_switch_and_keeps_isp_menu_when_enabled()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges()))
            ->get('/Regions')
            ->assertStatus(200)
            ->assertSee('billing_mode_hotspot')
            ->assertSee('My Routers');
    }

    public function test_explicit_denied_privilege_returns_403()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges(['Hotspot Dashboard' => false])))
            ->get('/hotspot/dashboard')
            ->assertStatus(403)
            ->assertSee("You don't have access to this hotspot page", false);
    }

    public function test_non_owner_without_hotspot_entries_is_denied()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges([], false)))
            ->get('/hotspot/dashboard')
            ->assertStatus(403);
    }

    public function test_non_owner_with_explicit_grant_is_allowed()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges(['Hotspot Dashboard' => true], false)))
            ->get('/hotspot/dashboard')
            ->assertStatus(200);
    }

    public function test_edit_admin_hides_hotspot_privileges_when_disabled()
    {
        $this->withSession($this->adminSession($this->privileges()))->get('/Admin/View/' . self::ADMIN_ID)
            ->assertStatus(200)->assertDontSee('hotspot_privilege_view');
    }

    public function test_edit_admin_shows_hotspot_privileges_when_enabled()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges()))->get('/Admin/View/' . self::ADMIN_ID)
            ->assertStatus(200)->assertSee('hotspot_privilege_view')->assertSee('Hotspot Reports');
    }

    public function test_add_admin_shows_hotspot_privileges_when_enabled()
    {
        $this->enableHotspot();

        $this->withSession($this->adminSession($this->privileges()))->get('/Accounts/add')
            ->assertStatus(200)->assertSee('hotspot_privilege_view');
    }

    public function test_mpesa_test_reports_missing_keys_without_showing_values()
    {
        $this->enableHotspot();
        $session = $this->adminSession($this->privileges());
        (new \App\Http\Controllers\login())->change_db($session['database_name']);
        $secret = DB::connection('mysql2')->table('settings')->where('keyword', 'consumer_secret')->first();
        $key = DB::connection('mysql2')->table('settings')->where('keyword', 'consumer_key')->value('value');

        // hide consumer_secret for this request only, so no live Daraja call is made
        if ($secret) {
            DB::connection('mysql2')->table('settings')->where('id', $secret->id)->update(['deleted' => 1]);
        }
        try {
            $response = $this->withSession($session)->postJson('/hotspot/mpesa/test');
        } finally {
            if ($secret) {
                DB::connection('mysql2')->table('settings')->where('id', $secret->id)->update(['deleted' => $secret->deleted]);
            }
        }

        $response->assertStatus(200)->assertJson(['ok' => false]);
        $this->assertStringContainsString('consumer_secret', $response->json('message'));
        if ($key) {
            $this->assertStringNotContainsString($key, $response->getContent());
        }
    }

    public function test_access_rules()
    {
        $owner = $this->privileges();
        $staff = $this->privileges([], false);

        $this->assertTrue(HotspotAccess::can($owner, 'Hotspot Plans'));
        $this->assertFalse(HotspotAccess::readOnly($owner, 'Hotspot Plans'));
        $this->assertFalse(HotspotAccess::can($staff, 'Hotspot Plans'));
        $this->assertFalse(HotspotAccess::can($this->privileges(['Hotspot Plans' => false]), 'Hotspot Plans'));
        $this->assertTrue(HotspotAccess::can(null, 'Hotspot Plans'), 'non-JSON privileges keep full access');

        $expired = json_encode([
            ['option' => 'Account and Profile', 'view' => true, 'readonly' => false],
            ['option' => 'Hotspot Plans', 'view' => true, 'readonly' => false, 'expiry' => 'definate_expiry', 'expiry_date' => '2020-01-01T00:00'],
        ]);
        $this->assertFalse(HotspotAccess::can($expired, 'Hotspot Plans'));
    }
}
