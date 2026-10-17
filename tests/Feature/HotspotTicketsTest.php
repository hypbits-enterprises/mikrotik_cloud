<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use App\Classes\HotspotTickets;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Hotspot tickets (Stage 6). Dev DBs, org 1, managed routers 23/28. The agent call is faked, so no
 * router is touched. Plans, tickets, activity and SMS rows a test adds are deleted afterwards; org
 * 1's hotspot settings and send_sms are restored.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotTicketsTest extends TestCase
{
    private const ORG_ID = 1;
    private const ROUTER = 28;

    private $saved = [];
    private $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saved['settings'] = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        $this->saved['send_sms'] = DB::table('organizations')->where('organization_id', self::ORG_ID)->value('send_sms');
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        DB::table('org_hotspot_settings')->insert(['organization_id' => self::ORG_ID, 'hotspot_enabled' => 1, 'date_created' => now()->format('YmdHis')]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        $this->useOrgDb();
        foreach (['hotspot_plans' => 'plan_id', 'hotspot_tickets' => 'ticket_id', 'hotspot_router_activity' => 'activity_id', 'sms_tables' => 'sms_id'] as $table => $key) {
            $this->saved['max_' . $table] = (int) DB::connection('mysql2')->table($table)->max($key);
        }
        $now = now()->format('YmdHis');
        $plan_id = DB::connection('mysql2')->table('hotspot_plans')->insertGetId([
            'plan_name' => 'Ticket test ' . uniqid(), 'price' => 20, 'duration_minutes' => 60, 'validity_type' => 'elapsed',
            'shared_users' => 1, 'is_active' => 1, 'sort_order' => 0, 'date_created' => $now, 'date_changed' => $now, 'deleted' => 0,
        ]);
        $this->plan = DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan_id)->first();
        config(['hotspot.agent_url' => 'http://agent.test/hotspot_router_agent.php']);
        Http::fake(['agent.test/*' => Http::response(['success' => true, 'message' => 'Agent started.'])]);
    }

    protected function tearDown(): void
    {
        $this->useOrgDb();
        foreach (['hotspot_plans' => 'plan_id', 'hotspot_tickets' => 'ticket_id', 'hotspot_router_activity' => 'activity_id', 'sms_tables' => 'sms_id'] as $table => $key) {
            DB::connection('mysql2')->table($table)->where($key, '>', $this->saved['max_' . $table])->delete();
        }
        DB::table('organizations')->where('organization_id', self::ORG_ID)->update(['send_sms' => $this->saved['send_sms']]);
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
        $privileges = array_merge([['option' => 'Account and Profile', 'view' => $owner, 'readonly' => false]], $extra);
        return [
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin',
            'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode($privileges),
        ];
    }

    private function newRows($table, $key)
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table($table)->where($key, '>', $this->saved['max_' . $table])->orderBy($key)->get();
    }

    public function test_codes_are_six_readable_characters_and_unique()
    {
        $this->useOrgDb();
        $codes = [];
        for ($i = 0; $i < 200; $i++) {
            $code = HotspotTickets::newCode();
            $this->assertMatchesRegularExpression('/^[ABCDEFGHJKMNPQRSTUVWXYZ23456789]{6}$/', $code);
            $codes[] = $code;
        }
        $this->assertCount(200, array_unique($codes));
    }

    public function test_manual_ticket_is_created_and_queued_for_the_router()
    {
        $this->withSession($this->adminSession())->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'quantity' => 1])
            ->assertRedirect(route('hotspot.tickets'))->assertSessionHas('created_codes');

        $tickets = $this->newRows('hotspot_tickets', 'ticket_id');
        $this->assertCount(1, $tickets);
        $this->assertEquals('manual', $tickets[0]->source);
        $this->assertEquals(20, (float) $tickets[0]->amount);
        $this->assertEquals('pending', $tickets[0]->status);
        $this->assertEquals('pending', $tickets[0]->provision_status);
        $this->assertEquals(1, $tickets[0]->created_by);

        $tasks = $this->newRows('hotspot_router_activity', 'activity_id');
        $this->assertCount(1, $tasks);
        $this->assertEquals('provision_tickets', $tasks[0]->action);
        $this->assertEquals([$tickets[0]->ticket_id], json_decode($tasks[0]->message, true)['params']['ticket_ids']);
    }

    public function test_quantity_creates_several_tickets_in_one_task()
    {
        $this->withSession($this->adminSession())->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'quantity' => 3]);
        $this->assertCount(3, $this->newRows('hotspot_tickets', 'ticket_id'));
        $tasks = $this->newRows('hotspot_router_activity', 'activity_id');
        $this->assertCount(1, $tasks);
        $this->assertCount(3, json_decode($tasks[0]->message, true)['params']['ticket_ids']);
    }

    public function test_deleted_plan_and_unmanaged_router_are_rejected()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $this->plan->plan_id)->update(['deleted' => 1]);
        $this->withSession($this->adminSession())->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'quantity' => 1])
            ->assertSessionHas('error_hotspot');
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $this->plan->plan_id)->update(['deleted' => 0]);
        $this->withSession($this->adminSession())->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => 22, 'quantity' => 1])
            ->assertSessionHas('error_hotspot');
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_bad_quantity_and_phone_are_rejected()
    {
        foreach ([['quantity' => 0], ['quantity' => 21], ['quantity' => 2, 'phone' => '0712345678'], ['quantity' => 1, 'phone' => '12345']] as $bad) {
            $this->withSession($this->adminSession())->post('/hotspot/tickets', array_merge(['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER], $bad))
                ->assertSessionHas('error_hotspot');
        }
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_sms_is_not_sent_when_sms_is_off_for_the_org()
    {
        DB::table('organizations')->where('organization_id', self::ORG_ID)->update(['send_sms' => 0]);
        $this->withSession($this->adminSession())->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'quantity' => 1, 'phone' => '0712345678', 'send_sms' => 1])
            ->assertSessionHas('success_hotspot', function ($message) {
                return str_contains($message, 'SMS is turned off');
            });
        $this->assertEquals('254712345678', $this->newRows('hotspot_tickets', 'ticket_id')[0]->phone);
        $this->assertCount(0, $this->newRows('sms_tables', 'sms_id'));
    }

    public function test_tickets_page_lists_and_counts()
    {
        $this->useOrgDb();
        $router = DB::connection('mysql2')->table('remote_routers')->where('router_id', self::ROUTER)->first();
        HotspotTickets::issue($this->plan, $router, null, null, 'manual', null, 1);
        $id = HotspotTickets::issue($this->plan, $router, null, null, 'manual', null, 1);
        DB::connection('mysql2')->table('hotspot_tickets')->where('ticket_id', $id)->update(['status' => 'active']);
        $code = DB::connection('mysql2')->table('hotspot_tickets')->where('ticket_id', $id)->value('ticket_code');

        $this->withSession($this->adminSession())->get('/hotspot/tickets?plan_id=' . $this->plan->plan_id)
            ->assertStatus(200)->assertSee($code)->assertSee('Pending: 1')->assertSee('Active: 1');
    }

    public function test_ending_a_ticket_queues_revoke()
    {
        $this->useOrgDb();
        $router = DB::connection('mysql2')->table('remote_routers')->where('router_id', self::ROUTER)->first();
        $id = HotspotTickets::issue($this->plan, $router, null, null, 'manual', null, 1);
        $this->withSession($this->adminSession())->post('/hotspot/tickets/' . $id . '/expire')->assertSessionHas('success_hotspot');
        $task = $this->newRows('hotspot_router_activity', 'activity_id')->last();
        $this->assertEquals('revoke_ticket', $task->action);
        $this->assertEquals($id, json_decode($task->message, true)['params']['ticket_id']);
    }

    public function test_one_payment_gives_one_ticket()
    {
        $this->useOrgDb();
        $router = DB::connection('mysql2')->table('remote_routers')->where('router_id', self::ROUTER)->first();
        $first = HotspotTickets::issue($this->plan, $router, null, null, 'stk', 987654321);
        $second = HotspotTickets::issue($this->plan, $router, null, null, 'stk', 987654321);
        $this->assertEquals($first, $second);
        $this->assertCount(1, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_read_only_and_missing_privilege()
    {
        $readonly = $this->adminSession([['option' => 'Hotspot Tickets', 'view' => true, 'readonly' => true, 'expiry' => 'indefinate_expiry', 'expiry_date' => '']], false);
        $this->withSession($readonly)->post('/hotspot/tickets', ['plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'quantity' => 1])->assertSessionHas('error_hotspot');
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_admin_without_privilege_is_denied()
    {
        $this->withSession($this->adminSession([], false))->get('/hotspot/tickets')->assertStatus(403);
    }

    public function test_usage_helper()
    {
        $plan = (object) ['duration_minutes' => 60, 'validity_type' => 'paused', 'data_limit_mb' => 100];
        $ticket = (object) ['status' => 'active', 'expires_at' => null, 'uptime_used_seconds' => 1500, 'bytes_in' => 10 * 1048576, 'bytes_out' => 40 * 1048576];
        $usage = HotspotTickets::usage($ticket, $plan);
        $this->assertEquals('25m', $usage['time_used']);
        $this->assertEquals('35m', $usage['time_left']);
        $this->assertEquals('50 MB', $usage['data_used']);
        $this->assertEquals('50 MB', $usage['data_left']);
    }
}
