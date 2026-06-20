<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\login;

class EmailTemplates extends Controller
{
    public static function getDefaults(): array { return self::DEFAULTS; }

    private const DEFAULTS = [
        'new_client_welcome' => [
            'label'   => 'New Client Welcome',
            'subject' => 'Welcome to [org_name] – Your Account is Ready',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Welcome to <strong>[org_name]</strong>! Your internet account has been created successfully.</p>
<p><strong>Account Details</strong><br>
Account Number: <strong>[account_number]</strong><br>
Monthly Plan: <strong>[monthly_fees]</strong><br>
Expiry Date: <strong>[exp_date]</strong></p>
<p>If you have any questions, do not hesitate to contact us.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'payment_received' => [
            'label'   => 'Payment Received',
            'subject' => 'Payment Received – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>We have received your payment of <strong>[trans_amount]</strong>. Thank you!</p>
<p><strong>Account Summary</strong><br>
Account: <strong>[account_number]</strong><br>
Wallet Balance: <strong>[wallet_balance]</strong><br>
Expiry Date: <strong>[exp_date]</strong></p>
<p>[receipt]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'payment_below_minimum' => [
            'label'   => 'Payment Below Minimum',
            'subject' => 'Payment Received – Below Minimum Threshold',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>We have received your payment of <strong>[trans_amount]</strong>. However, your payment is below the minimum required amount of <strong>[min_amount]</strong>.</p>
<p>Please top up the remaining balance to restore full service.</p>
<p><strong>Account:</strong> [account_number]<br>
<strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'payment_wrong_account' => [
            'label'   => 'Payment – Wrong Account',
            'subject' => 'Payment Received for Unrecognised Account',
            'body'    => '<p>Dear Customer,</p>
<p>We have received a payment of <strong>[trans_amount]</strong> referencing an account number that does not exist in our system.</p>
<p>Please contact us so we can resolve this for you.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_renewed' => [
            'label'   => 'Account Renewed',
            'subject' => 'Your Account Has Been Renewed – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account has been renewed successfully.</p>
<p><strong>Account:</strong> [account_number]<br>
<strong>New Expiry Date:</strong> [exp_date]<br>
<strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Enjoy uninterrupted internet access!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_extended' => [
            'label'   => 'Account Extended',
            'subject' => 'Your Account Has Been Extended – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account has been extended.</p>
<p><strong>Account:</strong> [account_number]<br>
<strong>New Expiry Date:</strong> [exp_date]<br>
<strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_deactivated' => [
            'label'   => 'Account Deactivated',
            'subject' => 'Account Deactivated – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) has been deactivated.</p>
<p>To reactivate your account, please make a payment or contact us for assistance.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_frozen' => [
            'label'   => 'Account Frozen',
            'subject' => 'Your Account Has Been Frozen – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) has been frozen for <strong>[days_frozen]</strong>.</p>
<p>Your account will be automatically restored on <strong>[unfreeze_date]</strong>.</p>
<p>If you have any queries, please contact us.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_freeze_scheduled' => [
            'label'   => 'Account Freeze Scheduled',
            'subject' => 'Upcoming Account Freeze Notice – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>This is a notice that your account (<strong>[account_number]</strong>) is scheduled to be frozen on <strong>[freeze_date]</strong> for <strong>[days_frozen]</strong>.</p>
<p>It will be automatically restored on <strong>[unfreeze_date]</strong>.</p>
<p>Contact us if you have any questions.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'account_unfrozen' => [
            'label'   => 'Account Unfrozen',
            'subject' => 'Your Account Has Been Reactivated – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Great news! Your internet account (<strong>[account_number]</strong>) has been unfrozen and is now active.</p>
<p><strong>Expiry Date:</strong> [exp_date]</p>
<p>Welcome back!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'referral_commission' => [
            'label'   => 'Referral Commission',
            'subject' => 'Referral Commission Credited – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>A referral commission of <strong>[trans_amount]</strong> has been credited to your wallet.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Thank you for referring new clients to us!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'payment_reminder_day_before' => [
            'label'   => 'Payment Reminder – Day Before Expiry',
            'subject' => 'Your Account Expires Tomorrow – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) expires <strong>tomorrow</strong>.</p>
<p>Please make a payment to avoid service interruption.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
        'payment_reminder_day_after' => [
            'label'   => 'Payment Reminder – Day After Expiry',
            'subject' => 'Your Account Has Expired – [org_name]',
            'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) expired <strong>yesterday</strong>.</p>
<p>Please make a payment to restore your service.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
        ],
    ];

    public function index()
    {
        $change_db = new login();
        $change_db->change_db();

        $rows = DB::connection('mysql2')->table('email_templates')->get()->keyBy('name');

        $templates = [];
        foreach (self::DEFAULTS as $name => $def) {
            $row = $rows->get($name);
            $templates[] = [
                'name'       => $name,
                'label'      => $def['label'],
                'subject'    => $row ? $row->subject : $def['subject'],
                'updated_at' => $row ? $row->updated_at : null,
            ];
        }

        return view('email_tpl_list', ['templates' => $templates]);
    }

    public function edit(string $name)
    {
        if (!array_key_exists($name, self::DEFAULTS)) {
            abort(404);
        }

        $change_db = new login();
        $change_db->change_db();

        $row = DB::connection('mysql2')->table('email_templates')->where('name', $name)->first();
        $def = self::DEFAULTS[$name];

        $template = [
            'name'    => $name,
            'label'   => $def['label'],
            'subject' => $row ? $row->subject : $def['subject'],
            'body'    => $row ? $row->html_body : $def['body'],
        ];

        $variables = [
            '[client_name]'    => 'Client Full Name',
            '[client_f_name]'  => 'Client First Name',
            '[account_number]' => 'Account Number',
            '[monthly_fees]'   => 'Monthly Fees',
            '[exp_date]'       => 'Expiry Date',
            '[wallet_balance]' => 'Wallet Balance',
            '[trans_amount]'   => 'Transaction Amount',
            '[min_amount]'     => 'Minimum Payment',
            '[days_frozen]'    => 'Days Frozen',
            '[unfreeze_date]'  => 'Unfreeze Date',
            '[freeze_date]'    => 'Freeze Date',
            '[org_name]'       => 'Organisation Name',
            '[receipt]'        => 'Attach PDF Receipt',
        ];

        return view('email_tpl_edit', compact('template', 'variables'));
    }

    public function save(Request $req, string $name)
    {
        if (!array_key_exists($name, self::DEFAULTS)) {
            abort(404);
        }

        $req->validate([
            'subject'  => 'required|string|max:255',
            'html_body' => 'required|string',
        ]);

        $change_db = new login();
        $change_db->change_db();

        $exists = DB::connection('mysql2')->table('email_templates')->where('name', $name)->exists();

        if ($exists) {
            DB::connection('mysql2')->table('email_templates')->where('name', $name)->update([
                'subject'    => $req->input('subject'),
                'html_body'  => $req->input('html_body'),
                'updated_at' => now(),
            ]);
        } else {
            DB::connection('mysql2')->table('email_templates')->insert([
                'name'       => $name,
                'label'      => self::DEFAULTS[$name]['label'],
                'subject'    => $req->input('subject'),
                'html_body'  => $req->input('html_body'),
                'updated_at' => now(),
            ]);
        }

        session()->flash('success', self::DEFAULTS[$name]['label'] . ' template saved successfully.');
        return redirect('/email-templates');
    }

    public function reset(string $name)
    {
        if (!array_key_exists($name, self::DEFAULTS)) {
            abort(404);
        }

        $change_db = new login();
        $change_db->change_db();

        DB::connection('mysql2')->table('email_templates')->where('name', $name)->delete();

        session()->flash('success', self::DEFAULTS[$name]['label'] . ' template reset to default.');
        return redirect('/email-templates');
    }
}
