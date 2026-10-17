<?php

namespace Tests\Feature;

use App\Classes\HotspotAccess;
use App\Classes\HotspotPayments;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Hotspot payments (Stage 8a). Dev DBs, org 1 (Paybill 4061913), router 28. Daraja and the router
 * agent are faked (a token is put in the array cache, so no request reaches Safaricom); org SMS is
 * switched off so nothing is sent. Rows a test adds are deleted afterwards and the org's settings
 * are restored.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HotspotPaymentsTest extends TestCase
{
    private const ORG_ID = 1;
    private const ROUTER = 28;
    private const KEY = 'd271083fb314807b';
    private const ORG_KEY = 'testorgkey12345678';
    private const TOKEN = 'test-callback-token-0123456789abcdef0123456789';
    private const SHORTCODE = '4061913';

    private $saved = [];
    private $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saved['settings'] = DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->first();
        $this->saved['send_sms'] = DB::table('organizations')->where('organization_id', self::ORG_ID)->value('send_sms');
        DB::table('organizations')->where('organization_id', self::ORG_ID)->update(['send_sms' => 0]);
        DB::table('org_hotspot_settings')->where('organization_id', self::ORG_ID)->delete();
        DB::table('org_hotspot_settings')->insert(['organization_id' => self::ORG_ID, 'hotspot_enabled' => 1, 'org_key' => self::ORG_KEY, 'callback_token' => self::TOKEN, 'date_created' => now()->format('YmdHis')]);
        Cache::forget(HotspotAccess::cacheKey(self::ORG_ID));
        $this->useOrgDb();
        foreach (['hotspot_payments' => 'payment_id', 'hotspot_tickets' => 'ticket_id', 'hotspot_transactions' => 'transaction_id', 'hotspot_router_activity' => 'activity_id', 'transaction_tables' => 'transaction_id', 'sms_tables' => 'sms_id', 'hotspot_plans' => 'plan_id'] as $table => $key) {
            $this->saved['max_' . $table] = (int) DB::connection('mysql2')->table($table)->max($key);
        }
        $this->saved['customers'] = DB::connection('mysql2')->table('hotspot_customers')->pluck('phone')->all();
        $this->saved['hotspot_settings'] = DB::connection('mysql2')->table('settings')->where('keyword', 'like', 'hotspot\\_%')->get();
        $this->saved['sms_key_rows'] = DB::connection('mysql2')->table('settings')->where('keyword', 'sms_api_key')->where('deleted', '0')->pluck('id')->all();
        $this->saved['max_settings'] = (int) DB::connection('mysql2')->table('settings')->max('id');
        $now = now()->format('YmdHis');
        $plan_id = DB::connection('mysql2')->table('hotspot_plans')->insertGetId([
            'plan_name' => 'Pay test ' . uniqid(), 'price' => 20, 'duration_minutes' => 60, 'validity_type' => 'elapsed',
            'shared_users' => 1, 'is_active' => 1, 'sort_order' => 0, 'date_created' => $now, 'date_changed' => $now, 'deleted' => 0,
        ]);
        $this->plan = DB::connection('mysql2')->table('hotspot_plans')->where('plan_id', $plan_id)->first();

        // Daraja token in the cache (MpesaService hotspot mode), Daraja calls and the agent faked
        $keys = DB::connection('mysql2')->table('settings')->where('deleted', '0')->whereIn('keyword', ['consumer_key', 'consumer_secret'])->pluck('value', 'keyword');
        Cache::put('mpesa_token_v1_' . sha1(trim($keys['consumer_key']) . ':' . trim($keys['consumer_secret'])), 'fake-token', 600);
        config(['hotspot.agent_url' => 'http://agent.test/hotspot_router_agent.php']);
        Http::fake([
            'agent.test/*' => Http::response(['success' => true, 'message' => 'Agent started.']),
            'api.safaricom.co.ke/mpesa/stkpush/*' => Http::response(['MerchantRequestID' => 'M-' . uniqid(), 'CheckoutRequestID' => 'ws_CO_' . date('dmYHis') . substr(str_replace('.', '', (string) microtime(true)), -7) . '254712345678', 'ResponseCode' => '0', 'CustomerMessage' => 'Success']),
            'api.safaricom.co.ke/mpesa/stkpushquery/*' => Http::response(['ResponseCode' => '0', 'ResultCode' => '0', 'ResultDesc' => 'The service request is processed successfully.']),
        ]);
    }

    protected function tearDown(): void
    {
        $this->useOrgDb();
        foreach (['hotspot_payments' => 'payment_id', 'hotspot_tickets' => 'ticket_id', 'hotspot_transactions' => 'transaction_id', 'hotspot_router_activity' => 'activity_id', 'transaction_tables' => 'transaction_id', 'sms_tables' => 'sms_id', 'hotspot_plans' => 'plan_id'] as $table => $key) {
            DB::connection('mysql2')->table($table)->where($key, '>', $this->saved['max_' . $table])->delete();
        }
        DB::connection('mysql2')->table('hotspot_customers')->whereNotIn('phone', $this->saved['customers'] ?: [''])->delete();
        DB::connection('mysql2')->table('settings')->where('keyword', 'like', 'hotspot\\_%')->delete();
        foreach ($this->saved['hotspot_settings'] as $row) {
            DB::connection('mysql2')->table('settings')->insert((array) $row);
        }
        DB::connection('mysql2')->table('settings')->whereIn('id', $this->saved['sms_key_rows'] ?: [0])->update(['deleted' => '0']);
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

    private function pay(array $values = [])
    {
        return $this->post('/hs/' . self::KEY . '/pay', array_merge(['plan_id' => $this->plan->plan_id, 'phone' => '0712345678', 'method' => 'stk', 'mac' => 'AA:BB:CC:DD:EE:' . substr(uniqid(), -2)], $values));
    }

    private function payment()
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('hotspot_payments')->where('payment_id', '>', $this->saved['max_hotspot_payments'])->orderBy('payment_id', 'DESC')->first();
    }

    private function newRows($table, $key)
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table($table)->where($key, '>', $this->saved['max_' . $table])->get();
    }

    private function stkCallback($payment, $code = 0, $receipt = 'TJK1234567')
    {
        $callback = ['MerchantRequestID' => $payment->merchant_request_id, 'CheckoutRequestID' => $payment->checkout_request_id, 'ResultCode' => $code, 'ResultDesc' => $code ? 'Request cancelled by user' : 'The service request is processed successfully.'];
        if ($code === 0) {
            $callback['CallbackMetadata'] = ['Item' => [
                ['Name' => 'Amount', 'Value' => 20], ['Name' => 'MpesaReceiptNumber', 'Value' => $receipt],
                ['Name' => 'TransactionDate', 'Value' => 20261017200000], ['Name' => 'PhoneNumber', 'Value' => 254712345678],
            ]];
        }
        return ['Body' => ['stkCallback' => $callback]];
    }

    private function c2b($account, $trans_id, $amount = 20)
    {
        return [
            'TransactionType' => 'Pay Bill', 'TransID' => $trans_id, 'TransTime' => '20261017200500', 'TransAmount' => (string) $amount,
            'BusinessShortCode' => self::SHORTCODE, 'BillRefNumber' => $account, 'InvoiceNumber' => '', 'OrgAccountBalance' => '',
            'ThirdPartyTransID' => '', 'MSISDN' => '254712345678', 'FirstName' => 'TEST',
        ];
    }

    public function test_pay_checks_the_phone_and_the_package()
    {
        $this->pay(['phone' => '12345'])->assertJson(['ok' => false]);
        $this->pay(['plan_id' => 999999])->assertJson(['ok' => false]);
        $this->assertNull($this->payment());
    }

    public function test_stk_pay_starts_a_payment_with_an_account_code()
    {
        $reply = $this->pay()->assertJson(['ok' => true, 'method' => 'stk'])->json();
        $payment = $this->payment();
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('254712345678', $payment->phone);
        $this->assertMatchesRegularExpression('/^[A-Z2-9]{6}$/', $payment->account_code);
        $this->assertStringStartsWith('ws_CO_', $payment->checkout_request_id);
        $this->assertEquals($payment->public_token, $reply['token']);
        Http::assertSent(function ($request) use ($payment) {
            return str_contains($request->url(), 'stkpush/v1/processrequest') && $request['AccountReference'] === $payment->account_code
                && $request['Amount'] === 20 && str_contains($request['CallBackURL'], '/hs-callback/stk/' . self::ORG_KEY . '/');
        });
    }

    public function test_stk_callback_completes_the_payment_once()
    {
        $this->pay();
        $payment = $this->payment();
        $url = '/hs-callback/stk/' . self::ORG_KEY . '/' . self::TOKEN;
        $this->postJson($url, $this->stkCallback($payment))->assertJson(['ResultCode' => 0]);
        $this->postJson($url, $this->stkCallback($payment))->assertJson(['ResultCode' => 0]);

        $payment = $this->payment();
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals('TJK1234567', $payment->mpesa_receipt);
        $tickets = $this->newRows('hotspot_tickets', 'ticket_id');
        $this->assertCount(1, $tickets);
        $this->assertEquals($payment->account_code, $tickets[0]->ticket_code);
        $this->assertEquals('stk', $tickets[0]->source);
        $transactions = $this->newRows('hotspot_transactions', 'transaction_id');
        $this->assertCount(1, $transactions);
        $this->assertEquals('TJK1234567', $transactions[0]->trans_id);
        $this->assertEquals($tickets[0]->ticket_id, $transactions[0]->ticket_id);
        $this->assertEquals('provision_tickets', $this->newRows('hotspot_router_activity', 'activity_id')->last()->action);
        $this->assertEquals(1, DB::connection('mysql2')->table('hotspot_customers')->where('phone', '254712345678')->value('purchase_count') >= 1 ? 1 : 0);
    }

    public function test_callback_with_a_wrong_token_is_rejected()
    {
        $this->pay();
        $payment = $this->payment();
        $this->postJson('/hs-callback/stk/' . self::ORG_KEY . '/wrong-token', $this->stkCallback($payment))->assertStatus(403);
        $this->assertEquals('pending', $this->payment()->status);
    }

    public function test_cancelled_prompt_shows_as_failed_on_the_portal()
    {
        $reply = $this->pay()->json();
        $payment = $this->payment();
        $this->postJson('/hs-callback/stk/' . self::ORG_KEY . '/' . self::TOKEN, $this->stkCallback($payment, 1032));
        $this->assertEquals('cancelled', $this->payment()->status);
        $this->get('/hs/' . self::KEY . '/pay/' . $payment->payment_id . '?t=' . $reply['token'])->assertJson(['state' => 'failed']);
    }

    public function test_status_needs_the_payment_token()
    {
        $this->pay();
        $payment = $this->payment();
        $this->get('/hs/' . self::KEY . '/pay/' . $payment->payment_id . '?t=wrong')->assertStatus(404);
    }

    public function test_status_waits_then_reports_the_code_once_on_the_router()
    {
        $reply = $this->pay()->json();
        $payment = $this->payment();
        $status = '/hs/' . self::KEY . '/pay/' . $payment->payment_id . '?t=' . $reply['token'];
        $this->get($status)->assertJson(['state' => 'waiting']);
        $this->postJson('/hs-callback/stk/' . self::ORG_KEY . '/' . self::TOKEN, $this->stkCallback($payment));
        $this->get($status)->assertJson(['state' => 'activating']);
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_tickets')->where('payment_id', $payment->payment_id)->update(['provision_status' => 'done']);
        $this->get($status)->assertJson(['state' => 'ready', 'code' => $payment->account_code]);
    }

    public function test_stk_query_completes_and_the_c2b_confirmation_adds_the_receipt()
    {
        $reply = $this->pay()->json();
        $payment = $this->payment();
        DB::connection('mysql2')->table('hotspot_payments')->where('payment_id', $payment->payment_id)->update(['date_created' => now()->subMinute()->format('YmdHis')]);
        $this->get('/hs/' . self::KEY . '/pay/' . $payment->payment_id . '?t=' . $reply['token']);
        $this->assertEquals('paid', $this->payment()->status);
        $this->assertStringStartsWith('STK-', $this->newRows('hotspot_transactions', 'transaction_id')[0]->trans_id);

        // the STK payment's own C2B confirmation reaches /Transact (shared Paybill)
        $this->post('/Transact', $this->c2b(strtolower($payment->account_code), 'TJK7654321'));
        $transactions = $this->newRows('hotspot_transactions', 'transaction_id');
        $this->assertCount(1, $transactions);
        $this->assertEquals('TJK7654321', $transactions[0]->trans_id);
        $this->assertEquals('TJK7654321', $this->payment()->mpesa_receipt);
        $this->assertCount(1, $this->newRows('hotspot_tickets', 'ticket_id'));
        $this->assertCount(0, $this->newRows('transaction_tables', 'transaction_id'));
    }

    public function test_paybill_payment_through_transact_is_a_hotspot_payment()
    {
        $this->pay(['method' => 'paybill'])->assertJson(['ok' => true, 'method' => 'paybill', 'shortcode' => self::SHORTCODE]);
        $payment = $this->payment();
        $this->assertNull($payment->checkout_request_id);
        $this->post('/Transact', $this->c2b($payment->account_code, 'TJP1111111'))->assertSee('Hotspot payment received');

        $this->assertEquals('paid', $this->payment()->status);
        $ticket = $this->newRows('hotspot_tickets', 'ticket_id')->first();
        $this->assertEquals($payment->account_code, $ticket->ticket_code);
        $this->assertEquals('paybill', $ticket->source);
        $this->assertEquals('billing_c2b', $this->newRows('hotspot_transactions', 'transaction_id')[0]->source);
        $this->assertCount(0, $this->newRows('transaction_tables', 'transaction_id'));
        $this->assertCount(0, $this->newRows('sms_tables', 'sms_id'));
    }

    public function test_paying_less_than_the_package_is_kept_for_review()
    {
        $this->pay(['method' => 'paybill']);
        $payment = $this->payment();
        $this->post('/Transact', $this->c2b($payment->account_code, 'TJP2222222', 10));
        $this->assertEquals('pending', $this->payment()->status);
        $this->assertEquals('review', $this->newRows('hotspot_transactions', 'transaction_id')[0]->status);
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_other_paybill_payments_go_through_transact_as_before()
    {
        $this->post('/Transact', $this->c2b('NOSUCHACC', 'TJX3333333'));
        $rows = $this->newRows('transaction_tables', 'transaction_id');
        $this->assertCount(1, $rows);
        $this->assertEquals('0', $rows[0]->transaction_status);
        $this->assertCount(0, $this->newRows('hotspot_transactions', 'transaction_id'));
    }

    public function test_reconcile_expires_abandoned_paybill_payments()
    {
        $this->pay(['method' => 'paybill']);
        $payment = $this->payment();
        DB::connection('mysql2')->table('hotspot_payments')->where('payment_id', $payment->payment_id)->update(['date_created' => now()->subMinutes(15)->format('YmdHis')]);
        $this->artisan('hotspot:reconcile-payments', ['--org' => self::ORG_ID])->assertExitCode(0);
        $this->assertEquals('expired', $this->payment()->status);

        // a late payment still gets its ticket
        $this->post('/Transact', $this->c2b($payment->account_code, 'TJP4444444'));
        $this->assertEquals('paid', $this->payment()->status);
        $this->assertCount(1, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    // SMS "on" for the org but its SMS key hidden, so no SMS can actually be sent
    private function smsOnButSilent(): void
    {
        DB::table('organizations')->where('organization_id', self::ORG_ID)->update(['send_sms' => 1]);
        $this->useOrgDb();
        DB::connection('mysql2')->table('settings')->whereIn('id', $this->saved['sms_key_rows'] ?: [0])->update(['deleted' => '1']);
    }

    private function billingTransaction($mpesa_code, $phone = '254712345678', $amount = 20)
    {
        $this->useOrgDb();
        return DB::connection('mysql2')->table('transaction_tables')->insertGetId([
            'transaction_mpesa_id' => $mpesa_code, 'transaction_date' => '20261017200000', 'transacion_amount' => $amount,
            'phone_transacting' => $phone, 'transaction_account' => 'WRONGACC', 'transaction_acc_id' => 0, 'transaction_status' => '0',
            'transaction_short_code' => self::SHORTCODE, 'fullnames' => 'TEST', 'date_changed' => now()->format('YmdHis'), 'deleted' => '0',
        ]);
    }

    private function claim($code, $phone = '0712345678')
    {
        return $this->post('/hs/' . self::KEY . '/claim', ['plan_id' => $this->plan->plan_id, 'mpesa_code' => $code, 'phone' => $phone, 'mac' => 'AA:BB:CC:DD:EE:' . substr(uniqid(), -2)])->json();
    }

    private function admin(): array
    {
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        return [
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin', 'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode([['option' => 'Account and Profile', 'view' => true, 'readonly' => false]]),
        ];
    }

    public function test_claim_with_the_payer_phone_sells_the_package()
    {
        $this->smsOnButSilent();
        $billing_id = $this->billingTransaction('TJC1000001');
        $reply = $this->claim('tjc1000001');
        $this->assertEquals('ok', $reply['state'], $reply['message']);
        $ticket = $this->newRows('hotspot_tickets', 'ticket_id')->first();
        $this->assertEquals('254712345678', $ticket->phone);
        $moved = DB::connection('mysql2')->table('transaction_tables')->where('transaction_id', $billing_id)->first();
        $this->assertEquals('1', (string) $moved->deleted);
        $row = $this->newRows('hotspot_transactions', 'transaction_id')->first();
        $this->assertEquals('TJC1000001', $row->trans_id);
        $this->assertEquals('assigned', $row->source);
        $this->assertEquals($ticket->ticket_id, $row->ticket_id);

        // the same M-Pesa code can't be used again
        $this->assertEquals('error', $this->claim('TJC1000001')['state']);
    }

    public function test_claim_matches_a_hashed_payer_phone()
    {
        $this->smsOnButSilent();
        $this->billingTransaction('TJC2000002', hash('sha256', '254712345678'));
        $this->assertEquals('ok', $this->claim('TJC2000002')['state']);
    }

    public function test_claim_with_another_phone_waits_for_approval()
    {
        $this->smsOnButSilent();
        $this->billingTransaction('TJC3000003');
        $this->assertEquals('review', $this->claim('TJC3000003', '0799999999')['state']);
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
        $review = $this->payment();
        $this->assertEquals('review', $review->status);

        $this->withSession($this->admin())->post('/hotspot/payments/review/' . $review->payment_id . '/approve')->assertSessionHas('success_hotspot');
        $this->assertEquals('paid', $this->payment()->status);
        $this->assertEquals('254799999999', $this->newRows('hotspot_tickets', 'ticket_id')->first()->phone);
    }

    public function test_claim_of_too_little_or_unknown_payment_is_refused()
    {
        $this->smsOnButSilent();
        $this->billingTransaction('TJC4000004', '254712345678', 5);
        $this->assertEquals('error', $this->claim('TJC4000004')['state']);
        $this->assertEquals('error', $this->claim('TJC9999999')['state']);
        $this->assertCount(0, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_isp_assigns_a_billing_transaction_to_hotspot()
    {
        $billing_id = $this->billingTransaction('TJA5000005');
        $this->withSession($this->admin())->post('/hotspot/payments/assign', [
            'source' => 'billing', 'back' => 'billing', 'transaction_id' => $billing_id,
            'plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'phone' => '0712345678',
        ])->assertRedirect(route('hotspot.payments'))->assertSessionHas('success_hotspot');
        $this->assertCount(1, $this->newRows('hotspot_tickets', 'ticket_id'));
        $this->assertEquals('1', (string) DB::connection('mysql2')->table('transaction_tables')->where('transaction_id', $billing_id)->value('deleted'));
    }

    public function test_transaction_page_offers_assign_to_hotspot()
    {
        $billing_id = $this->billingTransaction('TJA6000006');
        $this->withSession($this->admin())->get('/Transactions/View/' . $billing_id)->assertStatus(200)->assertSee('Assign to hotspot');
    }

    public function test_separate_paybill_settings_and_its_c2b_link()
    {
        $this->withSession($this->admin())->post('/hotspot/payments/settings', [
            'mode' => 'separate', 'shortcode' => '5551234', 'shortcode_type' => 'paybill',
            'hotspot_consumer_key' => 'ck-test', 'hotspot_consumer_secret' => 'cs-test', 'hotspot_passkey' => 'pk-test',
        ])->assertSessionHas('success_hotspot');
        $this->useOrgDb();
        $config = HotspotPayments::config();
        $this->assertEquals('separate', $config['mode']);
        $this->assertEquals('5551234', $config['shortcode']);
        $this->assertTrue($config['stk']);

        // a payment with its account code, and one without
        $this->pay(['method' => 'paybill'])->assertJson(['shortcode' => '5551234']);
        $payment = $this->payment();
        $url = '/hs-callback/c2b/' . self::ORG_KEY . '/' . self::TOKEN;
        $this->postJson($url, array_merge($this->c2b($payment->account_code, 'TJS7000007'), ['BusinessShortCode' => '5551234']))->assertJson(['ResultCode' => 0]);
        $this->assertEquals('paid', $this->payment()->status);
        $this->postJson($url, array_merge($this->c2b('typo', 'TJS8000008'), ['BusinessShortCode' => '5551234']));
        $unmatched = DB::connection('mysql2')->table('hotspot_transactions')->where('trans_id', 'TJS8000008')->first();
        $this->assertEquals('unmatched', $unmatched->status);
        $this->postJson('/hs-callback/c2b/' . self::ORG_KEY . '/wrong', $this->c2b('x', 'TJS9000009'))->assertStatus(403);

        // the ISP assigns the unmatched one
        $this->withSession($this->admin())->post('/hotspot/payments/assign', [
            'source' => 'hotspot', 'transaction_id' => $unmatched->transaction_id,
            'plan_id' => $this->plan->plan_id, 'router_id' => self::ROUTER, 'phone' => '0712345678',
        ])->assertSessionHas('success_hotspot');
        $this->assertEquals('matched', DB::connection('mysql2')->table('hotspot_transactions')->where('trans_id', 'TJS8000008')->value('status'));
        $this->assertCount(2, $this->newRows('hotspot_tickets', 'ticket_id'));
    }

    public function test_settings_never_show_the_keys()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('settings')->insert(['keyword' => 'hotspot_consumer_secret', 'value' => 'very-secret-value', 'status' => 1, 'deleted' => 0]);
        $this->withSession($this->admin())->get('/hotspot/payments/settings')->assertStatus(200)->assertDontSee('very-secret-value');
    }

    public function test_rejecting_an_unmatched_payment()
    {
        $this->useOrgDb();
        DB::connection('mysql2')->table('hotspot_transactions')->insert(['trans_id' => 'TJR1000001', 'source' => 'hotspot_c2b', 'amount' => 20, 'status' => 'unmatched', 'date_created' => now()->format('YmdHis'), 'deleted' => 0]);
        $id = DB::connection('mysql2')->table('hotspot_transactions')->where('trans_id', 'TJR1000001')->value('transaction_id');
        $this->withSession($this->admin())->post('/hotspot/payments/reject', ['kind' => 'transaction', 'id' => $id])->assertSessionHas('success_hotspot');
        $this->assertEquals('rejected', DB::connection('mysql2')->table('hotspot_transactions')->where('transaction_id', $id)->value('status'));
    }

    public function test_payments_page_lists_hotspot_earnings()
    {
        $this->pay(['method' => 'paybill']);
        $payment = $this->payment();
        $this->post('/Transact', $this->c2b($payment->account_code, 'TJP5555555'));
        $organization = DB::table('organizations')->where('organization_id', self::ORG_ID)->first();
        $this->withSession([
            'auth' => 'admin', 'Userid' => 1, 'Usernames' => 'Test Admin', 'database_name' => $organization->organization_database,
            'organization_id' => self::ORG_ID, 'organization' => $organization,
            'priviledges' => json_encode([['option' => 'Account and Profile', 'view' => true, 'readonly' => false]]),
        ])->get('/hotspot/payments')->assertStatus(200)->assertSee('TJP5555555')->assertSee($payment->account_code)->assertSee('Hotspot earnings');
    }
}
