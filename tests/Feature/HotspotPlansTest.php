<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use App\Classes\HotspotPlans;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Hotspot plans (Stage 5). Dev DBs, org 1. The agent call is faked, so nothing reaches a router.
 * Plans and activity rows a test adds are deleted afterwards; org 1's hotspot settings are restored.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotPlansTest extends TestCase
{
    private const ORG_ID = 1;

    private $savedSettings;
    private $lastPlanId;
    private $lastActivityId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedSettings = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        DB::table('org_hotspot_settings')->insert(['organization_id' => self::ORG_ID, 'hotspot_enabled' => 1, 'date_created' => now()->format('YmdHis')]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        $this->useOrgDb();
        $this->lastPlanId = (int) DB::connection('mysql2')->table('hotspot_plans')->max('plan_id');
        $this->lastActivityId = (int) DB::connection('mysql2')->table('hotspot_router_activity')->max('activity_id');
        config(['hotspot.agent_url' => 'http://agent.test/hotspot_router_agent.php']);
        Http::fake(['agent.test/*' => Http::response(['success' => true, 'message' => 'Agent started.'])]);
    }

    protected function tearDown(): void
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', '>', $this->lastPlanId)->delete();
        DB::connection('mysql2')->table('hotspot_router_activity')->where('activity_id', '>', $this->lastActivityId)->delete();
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

    private function adminSession(array $extra = [], bool $owner = true): array
    {
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        $privileges = [['option' => 'Account and Profile', 'view' => $owner, 'readonly' => false]];
        foreach ($extra as $entry) {
            $privileges[] = $entry;
        }
        return [
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin',
            'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode($privileges),
        ];
    }

    private function planInput(array $overrides = []): array
    {
        return array_merge([
            'plan_name' => 'Test plan ' . uniqid(),
            'price' => '50',
            'duration_value' => '6', 'duration_unit' => 'hours',
            'validity_type' => 'elapsed',
            'upload_speed' => '2', 'upload_unit' => 'Mbps',
            'download_speed' => '5', 'download_unit' => 'Mbps',
            'data_limit' => '1', 'data_unit' => 'GB',
            'shared_users' => '2', 'sort_order' => '1', 'is_active' => '1',
        ], $overrides);
    }

    private function newPlans()
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', '>', $this->lastPlanId)->get();
    }

    private function newSyncTasks()
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('hotspot_router_activity')->where('activity_id', '>', $this->lastActivityId)->where('action', 'sync_profiles')->get();
    }

    private function managedAllowedRouters()
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('remote_routers')->where('deleted', 0)->where('hotspot_enabled', 1)->get()
            ->filter(function ($router) {
                return \App\Classes\HotspotAgent::writeAllowed('mikrotik_cloud', $router->router_id);
            });
    }

    public function test_create_converts_units_and_queues_a_sync_per_managed_router()
    {
        $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput(['plan_name' => '6 hours']))
            ->assertRedirect(route('hotspot.plans'))->assertSessionHas('success_hotspot');

        $plan = $this->newPlans()->first();
        $this->assertEquals('6 hours', $plan->plan_name);
        $this->assertEquals(360, $plan->duration_minutes);
        $this->assertEquals('2M', $plan->upload_speed);
        $this->assertEquals('5M', $plan->download_speed);
        $this->assertEquals('2M/5M', $plan->rate_limit);
        $this->assertEquals(1024, $plan->data_limit_mb);
        $this->assertEquals(2, $plan->shared_users);
        $this->assertCount($this->managedAllowedRouters()->count(), $this->newSyncTasks());
    }

    public function test_unlimited_speed_and_data_are_stored_empty()
    {
        $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput(['upload_speed' => '', 'download_speed' => '', 'data_limit' => '', 'duration_value' => '30', 'duration_unit' => 'minutes']));
        $plan = $this->newPlans()->first();
        $this->assertNull($plan->rate_limit);
        $this->assertNull($plan->data_limit_mb);
        $this->assertEquals(30, $plan->duration_minutes);
    }

    public function test_zero_or_negative_price_and_duration_are_rejected()
    {
        foreach ([['price' => '0'], ['price' => '-5'], ['duration_value' => '0'], ['duration_value' => '-1'], ['duration_value' => '1.5']] as $bad) {
            $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput($bad))->assertSessionHas('error_hotspot');
        }
        $this->assertCount(0, $this->newPlans());
        $this->assertCount(0, $this->newSyncTasks());
    }

    public function test_duplicate_names_are_rejected()
    {
        $input = $this->planInput(['plan_name' => 'Same name ' . uniqid()]);
        $this->withSession($this->adminSession())->post('/hotspot/plans', $input);
        $this->withSession($this->adminSession())->post('/hotspot/plans', $input)->assertSessionHas('error_hotspot');
        $this->assertCount(1, $this->newPlans());
    }

    public function test_deactivated_plan_is_hidden_from_the_portal_list()
    {
        $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput());
        $plan = $this->newPlans()->first();
        $this->useOrgDb();
        $this->assertTrue(HotspotPlans::forPortal()->pluck('plan_id')->contains($plan->plan_id));

        $this->withSession($this->adminSession())->post('/hotspot/plans/' . $plan->plan_id . '/toggle');
        $this->useOrgDb();
        $this->assertFalse(HotspotPlans::forPortal()->pluck('plan_id')->contains($plan->plan_id));
        $this->assertEquals(0, DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan->plan_id)->value('is_active'));
    }

    public function test_delete_is_a_soft_delete_and_queues_a_sync()
    {
        $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput());
        $plan = $this->newPlans()->first();
        $routers = $this->managedAllowedRouters()->count();
        $this->assertCount($routers, $this->newSyncTasks());

        $this->withSession($this->adminSession())->post('/hotspot/plans/' . $plan->plan_id . '/delete')->assertSessionHas('success_hotspot');
        $this->useOrgDb();
        $this->assertEquals(1, DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan->plan_id)->value('deleted'));
        $this->assertCount(2 * $routers, $this->newSyncTasks());
        $this->assertGreaterThan(0, $routers, 'the dev DB should have managed, allow-listed routers (23, 28)');
    }

    public function test_edit_updates_the_plan()
    {
        $this->withSession($this->adminSession())->post('/hotspot/plans', $this->planInput());
        $plan = $this->newPlans()->first();
        $this->withSession($this->adminSession())->post('/hotspot/plans/' . $plan->plan_id, $this->planInput(['plan_name' => $plan->plan_name, 'download_speed' => '10', 'upload_speed' => '512', 'upload_unit' => 'Kbps']));
        $this->useOrgDb();
        $this->assertEquals('512k/10M', DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan->plan_id)->value('rate_limit'));
    }

    public function test_read_only_admin_cannot_save()
    {
        $session = $this->adminSession([['option' => 'Hotspot Plans', 'view' => true, 'readonly' => true, 'expiry' => 'indefinate_expiry', 'expiry_date' => '']], false);
        $this->withSession($session)->post('/hotspot/plans', $this->planInput())->assertSessionHas('error_hotspot');
        $this->assertCount(0, $this->newPlans());
    }

    public function test_admin_without_the_privilege_is_denied()
    {
        $this->withSession($this->adminSession([], false))->get('/hotspot/plans')->assertStatus(403);
    }

    public function test_plans_page_lists_plans()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_plans')->insert([
            'plan_name' => 'Listed plan', 'price' => 20, 'duration_minutes' => 60, 'validity_type' => 'paused',
            'shared_users' => 1, 'is_active' => 1, 'sort_order' => 0, 'date_created' => now()->format('YmdHis'), 'date_changed' => now()->format('YmdHis'), 'deleted' => 0,
        ]);
        $this->withSession($this->adminSession())->get('/hotspot/plans')
            ->assertStatus(200)->assertSee('Listed plan')->assertSee('1 hour')->assertSee('while connected');
    }

    public function test_helpers()
    {
        $this->assertEquals([6, 'hours'], HotspotPlans::durationParts(360));
        $this->assertEquals([1, 'days'], HotspotPlans::durationParts(1440));
        $this->assertEquals('90 minutes', HotspotPlans::durationText(90));
        $this->assertEquals('1 week', HotspotPlans::durationText(10080));
        $this->assertEquals('2.5M', HotspotPlans::speed('2.5', 'Mbps'));
        $this->assertEquals('512 Kbps', HotspotPlans::speedText('512k'));
        $this->assertEquals('1536 MB', HotspotPlans::dataText(1536));
        $this->assertEquals('Unlimited', HotspotPlans::dataText(null));
        $this->assertEquals('2 GB', HotspotPlans::dataText(2048));
    }
}
