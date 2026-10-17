<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use App\Classes\HotspotAgent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Hotspot Routers pages (Stage 4). Runs against the dev DBs and the GNS3 lab: router 23 (CHR-ISP)
 * must be online, router 22 has no router behind it. Only READ commands reach a router; the agent
 * call is faked, so nothing is ever written to a router. Org 1's hotspot settings row, router 23's
 * hotspot columns and the activity rows a test adds are put back afterwards.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotRoutersTest extends TestCase
{
    private const ORG_ID = 1;
    private const LAB_ROUTER = 23;
    private const OFFLINE_ROUTER = 22;

    private $savedSettings;
    private $savedRouter;
    private $lastActivityId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedSettings = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        DB::table('org_hotspot_settings')->insert(['organization_id' => self::ORG_ID, 'hotspot_enabled' => 1, 'date_created' => now()->format('YmdHis')]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));

        $this->useOrgDb();
        $this->savedRouter = DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->first();
        $this->lastActivityId = (int) DB::connection('mysql2')->table('hotspot_router_activity')->max('activity_id');
        config(['hotspot.router_write_allowlist' => 'mikrotik_cloud:' . self::LAB_ROUTER, 'hotspot.agent_url' => 'http://agent.test/hotspot_router_agent.php']);
    }

    protected function tearDown(): void
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_router_activity')->where('activity_id', '>', $this->lastActivityId)->delete();
        if ($this->savedRouter) {
            DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->update([
                'hotspot_enabled' => $this->savedRouter->hotspot_enabled,
                'date_changed' => $this->savedRouter->date_changed,
            ]);
        }
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        if ($this->savedSettings) {
            DB::table('org_hotspot_settings')->insert((array) $this->savedSettings);
        }
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        parent::tearDown();
    }

    private function useOrgDb(): void
    {
        (new \App\Http\Controllers\login())->change_db('mikrotik_cloud');
    }

    private function adminSession(bool $owner = true): array
    {
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        return [
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin',
            'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode([['option' => 'Account and Profile', 'view' => $owner, 'readonly' => false]]),
        ];
    }

    private function newActivityRows()
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('hotspot_router_activity')->where('activity_id', '>', $this->lastActivityId)->get();
    }

    public function test_routers_page_lists_routers_with_live_status_links()
    {
        $this->withSession($this->adminSession())->get('/hotspot/routers')
            ->assertStatus(200)
            ->assertSee(route('hotspot.router_status', self::LAB_ROUTER))
            ->assertSee(route('hotspot.router', self::LAB_ROUTER));
    }

    public function test_lab_router_status_is_online()
    {
        $this->withSession($this->adminSession())->getJson('/hotspot/routers/' . self::LAB_ROUTER . '/status')
            ->assertStatus(200)->assertJson(['online' => true])->assertJsonStructure(['version', 'hotspot_servers']);
    }

    public function test_router_without_vpn_session_is_offline_quickly()
    {
        $start = microtime(true);
        $this->withSession($this->adminSession())->getJson('/hotspot/routers/' . self::OFFLINE_ROUTER . '/status')
            ->assertStatus(200)->assertJson(['online' => false]);
        $this->assertLessThan(10, microtime(true) - $start);
    }

    public function test_router_page_shows_detection_and_wizard()
    {
        $this->withSession($this->adminSession())->get('/hotspot/routers/' . self::LAB_ROUTER)
            ->assertStatus(200)
            ->assertSee('Set up a new hotspot')
            ->assertSee('RouterOS');
    }

    public function test_setup_on_the_wan_interface_is_rejected()
    {
        Http::fake();
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/setup', ['interface' => 'ether1', 'subnet' => '10.5.50.0/24'])
            ->assertSessionHas('error_hotspot');
        Http::assertNothingSent();
        $this->assertCount(0, $this->newActivityRows());
    }

    public function test_setup_needs_the_router_on_the_allowlist()
    {
        Http::fake();
        config(['hotspot.router_write_allowlist' => '']);
        session($this->adminSession());
        $result = HotspotAgent::queue(self::LAB_ROUTER, 'setup', ['interface' => 'ether5']);
        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('allowlist', $result['message']);
        Http::assertNothingSent();
        $this->assertCount(0, $this->newActivityRows());
    }

    public function test_queue_creates_a_queued_task_and_calls_the_agent()
    {
        Http::fake(['agent.test/*' => Http::response(['success' => true, 'message' => 'Agent started.'])]);
        session($this->adminSession());
        $result = HotspotAgent::queue(self::LAB_ROUTER, 'setup', ['target' => 'test', 'interface' => 'ether5', 'subnet' => '10.5.50.0/24']);

        $this->assertTrue($result['ok']);
        $rows = $this->newActivityRows();
        $this->assertCount(1, $rows);
        $this->assertEquals('queued', $rows[0]->status);
        $this->assertEquals('ether5', json_decode($rows[0]->message, true)['params']['interface']);
        Http::assertSent(function ($request) use ($rows) {
            return str_contains($request->url(), 'db=mikrotik_cloud') && str_contains($request->url(), 'activity=' . $rows[0]->activity_id) && str_contains($request->url(), 'background=1');
        });
    }

    public function test_agent_that_cannot_start_marks_the_task_failed()
    {
        Http::fake(['agent.test/*' => Http::response('Server ip address not allowed', 200)]);
        session($this->adminSession());
        $result = HotspotAgent::queue(self::LAB_ROUTER, 'remove', ['target' => 'test']);

        $this->assertFalse($result['ok']);
        $this->assertEquals('failed', $this->newActivityRows()[0]->status);
    }

    public function test_stop_managing_changes_nothing_on_the_router()
    {
        Http::fake();
        $this->useOrgDb();
        DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->update(['hotspot_enabled' => 1]);

        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/stop')
            ->assertSessionHas('success_hotspot');

        Http::assertNothingSent();
        $this->useOrgDb();
        $this->assertEquals(0, DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->value('hotspot_enabled'));
        $this->assertEquals('stop', $this->newActivityRows()[0]->action);
    }

    public function test_remove_needs_typed_confirmation()
    {
        Http::fake();
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/remove', ['confirm' => 'yes'])
            ->assertSessionHas('error_hotspot');
        Http::assertNothingSent();
    }

    public function test_https_mode_needs_a_login_page_name()
    {
        Http::fake();
        config(['hotspot.login_https' => true]);
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/setup', ['interface' => 'ether5', 'subnet' => '10.5.50.0/24', 'dns_name' => ''])
            ->assertSessionHas('error_hotspot', function ($message) {
                return str_contains($message, 'HTTPS');
            });
        Http::assertNothingSent();
    }

    public function test_main_domain_style_names_are_checked()
    {
        Http::fake();
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/setup', ['interface' => 'ether5', 'subnet' => '10.5.50.0/24', 'dns_name' => 'not a domain'])
            ->assertSessionHas('error_hotspot');
        Http::assertNothingSent();
    }

    public function test_wizard_explains_the_login_page_name_in_test_mode()
    {
        config(['hotspot.login_https' => false]);
        $response = $this->withSession($this->adminSession())->get('/hotspot/routers/' . self::LAB_ROUTER)->assertStatus(200);
        // the notice is shown only when the wizard form is (a free interface exists)
        if (str_contains($response->getContent(), 'name="dns_name"')) {
            $response->assertSee('dedicated subdomain')->assertSee('Test mode');
        } else {
            $response->assertSee('No free interface');
        }
    }

    public function test_delete_server_needs_the_name_typed()
    {
        Http::fake();
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/delete-server', ['server' => 'other-hs', 'confirm' => 'yes'])
            ->assertSessionHas('error_hotspot');
        Http::assertNothingSent();
        $this->assertCount(0, $this->newActivityRows());
    }

    public function test_delete_server_refuses_the_hypbits_server()
    {
        Http::fake();
        $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/delete-server', ['server' => 'hypbits-hs', 'confirm' => 'hypbits-hs'])
            ->assertSessionHas('error_hotspot', function ($message) {
                return str_contains($message, 'Remove from router');
            });
        Http::assertNothingSent();
    }

    public function test_delete_server_refuses_a_managed_server()
    {
        Http::fake();
        $this->useOrgDb();
        DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->update(['hotspot_enabled' => 1, 'hotspot_server_name' => 'adopted-hs']);
        try {
            $this->withSession($this->adminSession())->post('/hotspot/routers/' . self::LAB_ROUTER . '/delete-server', ['server' => 'adopted-hs', 'confirm' => 'adopted-hs'])
                ->assertSessionHas('error_hotspot', function ($message) {
                    return str_contains($message, 'Stop managing');
                });
        } finally {
            $this->useOrgDb();
            DB::connection('mysql2')->table('remote_routers')->where('router_id', self::LAB_ROUTER)->update(['hotspot_server_name' => $this->savedRouter->hotspot_server_name]);
        }
        Http::assertNothingSent();
    }

    public function test_admin_without_hotspot_routers_privilege_is_denied()
    {
        $this->withSession($this->adminSession(false))->get('/hotspot/routers')->assertStatus(403);
    }
}
