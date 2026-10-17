<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use App\Classes\HotspotPortal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Captive portal and branding (Stage 7). Dev DBs, org 1, managed router 28 (key below) and 23.
 * Nothing reaches a router. Tickets, login pages and key rows a test adds are removed afterwards,
 * the branding rows are put back as they were, and org 1's hotspot settings are restored.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotPortalTest extends TestCase
{
    private const ORG_ID = 1;
    private const ROUTER = 28;
    private const KEY = 'd271083fb314807b';
    private const UNMANAGED_KEY = 'aaaaaaaaaaaaaaaa';

    private $saved = [];
    private $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saved['settings'] = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        DB::table('org_hotspot_settings')->insert(['organization_id' => self::ORG_ID, 'hotspot_enabled' => 1, 'date_created' => now()->format('YmdHis')]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        $this->useOrgDb();
        $this->saved['pages'] = DB::connection('mysql2')->table('hotspot_login_pages')->get();
        $this->saved['max_ticket'] = (int) DB::connection('mysql2')->table('hotspot_tickets')->max('ticket_id');
        $this->saved['max_plan'] = (int) DB::connection('mysql2')->table('hotspot_plans')->max('plan_id');
        $this->saved['logos'] = glob(public_path(HotspotPortal::LOGO_DIR . '/*')) ?: [];
        $now = now()->format('YmdHis');
        $plan_id = DB::connection('mysql2')->table('hotspot_plans')->insertGetId([
            'plan_name' => 'Portal test ' . uniqid(), 'price' => 30, 'duration_minutes' => 120, 'validity_type' => 'paused',
            'download_speed' => '5M', 'data_limit_mb' => 500, 'shared_users' => 2, 'is_active' => 1, 'sort_order' => 0,
            'date_created' => $now, 'date_changed' => $now, 'deleted' => 0,
        ]);
        $this->plan = DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan_id)->first();
    }

    protected function tearDown(): void
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_tickets')->where('ticket_id', '>', $this->saved['max_ticket'])->delete();
        DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', '>', $this->saved['max_plan'])->delete();
        DB::connection('mysql2')->table('hotspot_login_pages')->delete();
        foreach ($this->saved['pages'] as $page) {
            DB::connection('mysql2')->table('hotspot_login_pages')->insert((array) $page);
        }
        foreach (glob(public_path(HotspotPortal::LOGO_DIR . '/*')) ?: [] as $file) {
            if (!in_array($file, $this->saved['logos'], true) && !str_contains(basename($file), 'org-')) {
                @unlink($file);
            }
        }
        DB::table('hotspot_router_keys')->where('hotspot_key', self::UNMANAGED_KEY)->delete();
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        if ($this->saved['settings']) {
            DB::table('org_hotspot_settings')->insert((array) $this->saved['settings']);
        }
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        parent::tearDown();
    }

    private function useOrgDb(): void
    {
        (new \App\Http\Controllers\login())->change_db('mikrotik_cloud');
    }

    private function adminSession(array $extra = [], bool $owner = true): array
    {
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        return [
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin',
            'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode(array_merge([['option' => 'Account and Profile', 'view' => $owner, 'readonly' => false]], $extra)),
        ];
    }

    private function ticket(array $values = [])
    {
        $this->useOrgDb();
        $now = now()->format('YmdHis');
        $code = 'T' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
        DB::connection('mysql2')->table('hotspot_tickets')->insert(array_merge([
            'ticket_code' => $code, 'plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'source' => 'manual',
            'amount' => 30, 'status' => 'pending', 'provision_status' => 'done', 'provision_attempts' => 1,
            'date_created' => $now, 'date_changed' => $now, 'deleted' => 0,
        ], $values));
        return $code;
    }

    private function page(array $values)
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_login_pages')->insert(array_merge(['date_created' => now()->format('YmdHis'), 'deleted' => 0], $values));
    }

    public function test_unknown_key_shows_the_unavailable_page()
    {
        $this->get('/hs/0000000000000000')->assertStatus(404)->assertSee('not available');
    }

    public function test_org_with_hotspot_off_shows_the_unavailable_page()
    {
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->update(['hotspot_enabled' => 0]);
        $this->get('/hs/' . self::KEY)->assertStatus(404);
    }

    public function test_key_of_an_unmanaged_router_shows_the_unavailable_page()
    {
        DB::table('hotspot_router_keys')->insert(['hotspot_key' => self::UNMANAGED_KEY, 'organization_id' => self::ORG_ID, 'router_id' => 22, 'date_created' => now()->format('YmdHis')]);
        $this->get('/hs/' . self::UNMANAGED_KEY)->assertStatus(404);
    }

    public function test_portal_shows_active_plans_and_router_branding_over_the_default()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_plans')->insert([
            'plan_name' => 'Hidden plan ' . uniqid(), 'price' => 5, 'duration_minutes' => 30, 'validity_type' => 'elapsed',
            'shared_users' => 1, 'is_active' => 0, 'sort_order' => 0, 'date_created' => now()->format('YmdHis'), 'deleted' => 0,
        ]);
        DB::connection('mysql2')->table('hotspot_login_pages')->delete();
        $this->page(['router_id' => null, 'title' => 'Default title', 'welcome_text' => 'Default welcome', 'support_phone' => '0711000000']);
        $this->page(['router_id' => self::ROUTER, 'title' => 'Router title', 'primary_color' => '#ff5500']);

        $this->get('/hs/' . self::KEY . '?mac=AA:BB:CC:DD:EE:FF&login=http://10.5.50.1/login')
            ->assertStatus(200)
            ->assertSee($this->plan->plan_name)->assertSee('KES 30')->assertSee('2 hours of connected time')->assertSee('500 MB')->assertSee('2 devices')
            ->assertDontSee('Hidden plan')
            ->assertSee('Router title')->assertDontSee('Default title')
            ->assertSee('Default welcome')->assertSee('0711000000')->assertSee('--primary: #ff5500', false)
            ->assertSee('"http:\/\/10.5.50.1\/login"', false);
    }

    public function test_after_logout_the_code_is_filled_in()
    {
        $this->get('/hs/' . self::KEY . '?logged_out=1&user=ABC123&login=http://10.5.50.1/login')
            ->assertStatus(200)->assertSee('You have logged out')->assertSee('value="ABC123"', false);
    }

    public function test_login_link_to_another_host_is_ignored()
    {
        $this->get('/hs/' . self::KEY . '?login=http://evil.example.com/login')
            ->assertStatus(200)->assertDontSee('evil.example.com');
    }

    public function test_login_url_rules()
    {
        $router = (object) ['hotspot_dns_name' => 'wifi.isp.co.ke'];
        $this->assertTrue(HotspotPortal::loginUrlAllowed('http://10.5.50.1/login', $router));
        $this->assertTrue(HotspotPortal::loginUrlAllowed('https://wifi.isp.co.ke/login', $router));
        $this->assertFalse(HotspotPortal::loginUrlAllowed('http://8.8.8.8/login', $router));
        $this->assertFalse(HotspotPortal::loginUrlAllowed('http://10.5.50.1/other', $router));
        $this->assertFalse(HotspotPortal::loginUrlAllowed('javascript:alert(1)', $router));
        $this->assertFalse(HotspotPortal::loginUrlAllowed('http://user:pass@10.5.50.1/login', $router));
        $this->assertTrue(HotspotPortal::logoutUrlAllowed('http://10.5.50.1/logout', $router));
        $this->assertFalse(HotspotPortal::logoutUrlAllowed('http://evil.com/logout', $router));
        $this->assertNull(HotspotPortal::safeLink('javascript:alert(1)'));
        $this->assertEquals('http://example.com/', HotspotPortal::safeLink('http://example.com/'));
    }

    public function test_code_check_answers_for_each_ticket_state()
    {
        $good = $this->ticket();
        $used = $this->ticket(['status' => 'used']);
        $expired = $this->ticket(['status' => 'active', 'expires_at' => now()->subMinute()->format('YmdHis')]);
        $other = $this->ticket(['router_id' => 23]);
        $waiting = $this->ticket(['provision_status' => 'pending']);

        $check = function ($code) {
            return $this->post('/hs/' . self::KEY . '/code', ['code' => $code, 'mac' => 'AA:BB:CC:DD:EE:0' . substr(md5($code), 0, 1)])->json();
        };
        $this->assertEquals(['ok' => true, 'code' => $good], $check(strtolower($good)));
        $this->assertStringContainsString('used up', $check($used)['message']);
        $this->assertStringContainsString('expired', $check($expired)['message']);
        $this->assertStringContainsString('another hotspot', $check($other)['message']);
        $this->assertStringContainsString('being activated', $check($waiting)['message']);
        $this->assertStringContainsString('not found', $check('ZZZZZZ')['message']);
    }

    public function test_code_check_is_rate_limited_per_device()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/hs/' . self::KEY . '/code', ['code' => 'ZZZZZZ', 'mac' => 'AA:BB:CC:DD:EE:01'])->assertStatus(200);
        }
        $this->post('/hs/' . self::KEY . '/code', ['code' => 'ZZZZZZ', 'mac' => 'AA:BB:CC:DD:EE:01'])->assertStatus(429);
        $this->post('/hs/' . self::KEY . '/code', ['code' => 'ZZZZZZ', 'mac' => 'AA:BB:CC:DD:EE:02'])->assertStatus(200);
    }

    public function test_online_page_shows_the_ticket_usage()
    {
        $code = $this->ticket(['status' => 'active', 'uptime_used_seconds' => 1800, 'bytes_in' => 50 * 1048576, 'bytes_out' => 50 * 1048576]);
        $this->get('/hs/' . self::KEY . '/online?user=' . $code . '&logout=http://10.5.50.1/logout&dst=http://example.com/')
            ->assertStatus(200)->assertSee("You're online", false)->assertSee($this->plan->plan_name)
            ->assertSee('1h 30m')->assertSee('100 MB')->assertSee('400 MB')
            ->assertSee('http://10.5.50.1/logout')->assertSee('href="http://example.com', false);
    }

    public function test_online_page_follows_the_redirect_link()
    {
        $this->page(['router_id' => self::ROUTER, 'redirect_url' => 'https://isp.example.com/']);
        $this->get('/hs/' . self::KEY . '/online?user=ABC123')->assertRedirect('https://isp.example.com/');
    }

    public function test_router_files_point_at_the_portal()
    {
        $this->get('/hs/' . self::KEY . '/router-files/login.html')
            ->assertStatus(200)
            ->assertSee(config('hotspot.portal_url') . '/hs/' . self::KEY, false)
            ->assertSee('$(link-login-only)', false)
            ->assertSee('/ping?t=', false);
    }

    public function test_only_known_router_files_are_served()
    {
        $this->get('/hs/' . self::KEY . '/router-files/secret.php')->assertStatus(404);
    }

    public function test_ping_is_a_small_image()
    {
        $this->get('/hs/' . self::KEY . '/ping')->assertStatus(200)->assertHeader('Content-Type', 'image/gif');
    }

    public function test_saving_default_branding_with_a_logo()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_login_pages')->delete();
        $this->withSession($this->adminSession())->post('/hotspot/branding', [
            'router_id' => '', 'title' => 'My WiFi', 'primary_color' => '#112233', 'support_phone' => '0700111222',
            'redirect_url' => '', 'logo' => UploadedFile::fake()->image('logo.png', 1200, 600),
        ])->assertSessionHas('success_hotspot');

        $this->useOrgDb();
        $page = DB::connection('mysql2')->table('hotspot_login_pages')->whereNull('router_id')->first();
        $this->assertEquals('My WiFi', $page->title);
        $this->assertEquals('#112233', $page->primary_color);
        $this->assertNull($page->redirect_url);
        [$width, $height] = getimagesize(public_path($page->logo_path));
        $this->assertLessThanOrEqual(320, $width);
        $this->assertLessThanOrEqual(160, $height);
    }

    public function test_bad_branding_values_are_rejected()
    {
        foreach ([['primary_color' => 'red'], ['redirect_url' => 'javascript:alert(1)'], ['support_phone' => 'call me']] as $bad) {
            $this->withSession($this->adminSession())->post('/hotspot/branding', array_merge(['router_id' => ''], $bad))->assertSessionHas('error_hotspot');
        }
    }

    public function test_read_only_admin_cannot_save_branding()
    {
        $session = $this->adminSession([['option' => 'Hotspot Branding', 'view' => true, 'readonly' => true, 'expiry' => 'indefinate_expiry', 'expiry_date' => '']], false);
        $this->withSession($session)->post('/hotspot/branding', ['router_id' => '', 'title' => 'Nope'])->assertSessionHas('error_hotspot');
    }

    public function test_admin_without_the_privilege_is_denied()
    {
        $this->withSession($this->adminSession([], false))->get('/hotspot/branding')->assertStatus(403);
    }

    public function test_branding_page_loads()
    {
        $this->withSession($this->adminSession())->get('/hotspot/branding?router_id=' . self::ROUTER)
            ->assertStatus(200)->assertSee('Login page branding')->assertSee('Preview');
    }

    public function test_preview_uses_the_form_values()
    {
        $this->withSession($this->adminSession())->get('/hotspot/branding/preview?title=Preview+title&primary_color=%23abcdef')
            ->assertStatus(200)->assertSee('Preview title')->assertSee('--primary: #abcdef', false);
    }
}
