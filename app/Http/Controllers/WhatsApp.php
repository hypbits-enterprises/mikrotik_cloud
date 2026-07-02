<?php

namespace App\Http\Controllers;

use App\Models\sms_table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsApp extends Controller
{
    // ─── Dashboard / Chat List ───────────────────────────────────────────────

    public function index()
    {
        $this->switchDb();

        // Latest message per client that has a WhatsApp conversation
        $chats = DB::connection('mysql2')->select("
            SELECT m.*, c.client_name, c.clients_contacts, c.client_account,
                   wa.direction, wa.delivery_status, wa.received_at,
                   (SELECT COUNT(*) FROM sms_tables s2
                    JOIN whatsapp_chats w2 ON w2.message_id = s2.sms_id
                    WHERE s2.account_id = m.account_id
                      AND s2.channel = 'whatsapp' AND s2.deleted = '0') AS total_messages
            FROM sms_tables m
            JOIN whatsapp_chats wa ON wa.message_id = m.sms_id
            JOIN client_tables c ON c.client_id = m.account_id
            WHERE m.deleted = '0' AND m.channel = 'whatsapp'
              AND m.sms_id IN (
                  SELECT MAX(s3.sms_id) FROM sms_tables s3
                  WHERE s3.channel = 'whatsapp' AND s3.deleted = '0'
                  GROUP BY s3.account_id
              )
            ORDER BY m.sms_id DESC
            LIMIT 500
        ");

        $stats     = $this->getWhatsAppStats();
        $templates = $this->fetchTemplates();

        return view('whatsapp.chats', compact('chats', 'stats', 'templates'));
    }

    // ─── AJAX: Sidebar chats list (for polling) ───────────────────────────────

    public function getChatsJson()
    {
        $this->switchDb();

        $chats = DB::connection('mysql2')->select("
            SELECT m.sms_id, m.account_id, m.sms_content, m.date_sent,
                   c.client_name, c.clients_contacts, c.client_account,
                   wa.direction, wa.delivery_status
            FROM sms_tables m
            JOIN whatsapp_chats wa ON wa.message_id = m.sms_id
            JOIN client_tables c ON c.client_id = m.account_id
            WHERE m.deleted = '0' AND m.channel = 'whatsapp'
              AND m.sms_id IN (
                  SELECT MAX(s3.sms_id) FROM sms_tables s3
                  WHERE s3.channel = 'whatsapp' AND s3.deleted = '0'
                  GROUP BY s3.account_id
              )
            ORDER BY m.sms_id DESC
            LIMIT 500
        ");

        return response()->json($chats);
    }

    // ─── Clients list for "Start Chat" picker ────────────────────────────────

    public function getClientsForChat()
    {
        $this->switchDb();

        $clients = DB::connection('mysql2')->select(
            "SELECT client_id, client_name, clients_contacts, client_account, client_status
             FROM client_tables WHERE deleted = '0' ORDER BY client_name ASC"
        );

        return response()->json($clients);
    }

    // ─── Delete a conversation ────────────────────────────────────────────────

    public function deleteChat($client_id)
    {
        $this->switchDb();

        DB::connection('mysql2')->update(
            "UPDATE `sms_tables` SET `deleted` = '1'
             WHERE `account_id` = ? AND `channel` = 'whatsapp'",
            [$client_id]
        );

        return response()->json(['success' => true]);
    }

    // ─── AJAX: Messages for a client ─────────────────────────────────────────

    public function getChatMessages($client_id)
    {
        $this->switchDb();

        $client = DB::connection('mysql2')->select(
            "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ?",
            [$client_id]
        );

        if (empty($client)) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $client = $client[0];

        $messages = DB::connection('mysql2')->select("
            SELECT m.sms_id, m.sms_content, m.date_sent, m.account_id,
                   wa.direction, wa.delivery_status, wa.template_name
            FROM sms_tables m
            JOIN whatsapp_chats wa ON wa.message_id = m.sms_id
            WHERE m.deleted = '0' AND m.channel = 'whatsapp'
              AND m.account_id = ?
            ORDER BY m.sms_id ASC
        ", [$client_id]);
        $client->next_expiration_date = date("D dS M Y h:i A", strtotime($client->next_expiration_date));
        $client->clients_reg_date = date("D dS M Y h:i A", strtotime($client->clients_reg_date));
        $client->last_seen = isset($client->last_seen) ? date("D dS M Y h:i A", strtotime($client->last_seen)) : "-";

        // get the router name
        $select = "SELECT * FROM remote_routers WHERE router_id = ?";
        $router = DB::connection('mysql2')->select($select, [$client->router_name]);
        $client->router_name = $router ? $router[0]->router_name : 'Unknown';
        return response()->json([
            'client'       => $client,
            'messages'     => $messages,
            'withinWindow' => $this->isWithin24hrWindow((int) $client_id),
            'templates'    => $this->fetchTemplates(),
        ]);
    }

    // ─── Send Free-Form Message (within 24hr window) ──────────────────────────

    public function sendMessage(Request $req)
    {
        $req->validate([
            'client_id' => 'required|integer',
            'message'   => 'required|string|max:4096',
        ]);

        $this->switchDb();

        if (!$this->isWithin24hrWindow($req->client_id)) {
            return back()->with('error_wa', 'The 24-hour reply window has closed. Please use a template message.');
        }

        $client = DB::connection('mysql2')->select(
            "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ?",
            [$req->client_id]
        );

        if (empty($client)) {
            return back()->with('error_wa', 'Client not found.');
        }

        $phone  = $this->formatKenyanPhone($client[0]->clients_contacts);
        $result = $this->sendWhatsAppMessage($phone, $req->message, 'service');

        $status = isset($result['messages'][0]['id']) ? 1 : 0;
        $waId   = $result['messages'][0]['id'] ?? null;

        $this->logWhatsAppMessage(
            $req->client_id,
            $req->message,
            $phone,
            $status,
            'service',
            'outbound',
            $waId
        );

        if ($req->wantsJson()) {
            return response()->json($status
                ? ['success' => true]
                : ['success' => false, 'error' => 'Failed to send message.']
            );
        }
        if ($status) {
            return back()->with('success_wa', 'Message sent successfully.');
        }
        return back()->with('error_wa', 'Failed to send message. Please try again.');
    }

    // ─── Send Template Message ────────────────────────────────────────────────

    public function sendTemplate(Request $req)
    {
        $req->validate([
            'client_id'     => 'required|integer',
            'template_id'   => 'required|integer',
        ]);

        $this->switchDb();

        $template = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `id` = ? AND `is_active` = 1 AND `deleted` = '0'",
            [$req->template_id]
        );

        if (empty($template)) {
            return back()->with('error_wa', 'Template not found.');
        }

        $template = $template[0];

        $client = DB::connection('mysql2')->select(
            "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ?",
            [$req->client_id]
        );

        if (empty($client)) {
            return back()->with('error_wa', 'Client not found.');
        }

        $phone     = $this->formatKenyanPhone($client[0]->clients_contacts);
        $variables = $this->resolveTemplateVariables($template, $client[0]);
        $result    = $this->sendWhatsAppTemplate($phone, $template->template_name, $variables, $template->category);

        $status = isset($result['messages'][0]['id']) ? 1 : 0;
        $waId   = $result['messages'][0]['id'] ?? null;

        $body = $this->interpolateTemplate($template->body_text, $variables);

        $this->logWhatsAppMessage(
            $req->client_id,
            $body,
            $phone,
            $status,
            $template->category,
            'outbound',
            $waId,
            $template->template_name
        );

        if ($req->wantsJson()) {
            return response()->json($status
                ? ['success' => true]
                : ['success' => false, 'error' => 'Failed to send template.']
            );
        }
        if ($status) {
            return back()->with('success_wa', 'Template message sent.');
        }
        return back()->with('error_wa', 'Failed to send template. Please try again.');
    }

    // ─── Bulk Template Send ───────────────────────────────────────────────────

    public function bulkView()
    {
        $this->switchDb();
        $templates = $this->fetchTemplates();
        return view('whatsapp.bulk', compact('templates'));
    }

    public function sendBulk(Request $req)
    {
        $req->validate([
            'template_id'    => 'required|integer',
            'client_group'   => 'required|in:active,inactive,all',
        ]);

        $this->switchDb();

        $template = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `id` = ? AND `is_active` = 1 AND `deleted` = '0' AND `meta_status` = 'approved'",
            [$req->template_id]
        );

        if (empty($template)) {
            return back()->with('error_wa', 'Template not found or not approved.');
        }

        $template = $template[0];

        $statusFilter = match($req->client_group) {
            'active'   => "AND `client_status` = 1",
            'inactive' => "AND `client_status` = 0",
            default    => "",
        };

        $clients = DB::connection('mysql2')->select(
            "SELECT * FROM `client_tables` WHERE `deleted` = '0' {$statusFilter}"
        );

        $sent = 0;
        $failed = 0;

        foreach ($clients as $client) {
            $phone     = $this->formatKenyanPhone($client->clients_contacts);
            $variables = $this->resolveTemplateVariables($template, $client);
            $result    = $this->sendWhatsAppTemplate($phone, $template->template_name, $variables, $template->category);

            $status = isset($result['messages'][0]['id']) ? 1 : 0;
            $waId   = $result['messages'][0]['id'] ?? null;
            $body   = $this->interpolateTemplate($template->body_text, $variables);

            $this->logWhatsAppMessage(
                $client->client_id,
                $body,
                $phone,
                $status,
                $template->category,
                'outbound',
                $waId,
                $template->template_name
            );

            $status ? $sent++ : $failed++;
        }

        return back()->with('success_wa', "Bulk send complete. Sent: {$sent}, Failed: {$failed}.");
    }

    // ─── Send from Compose Form ───────────────────────────────────────────────

    public function sendFromCompose(Request $req)
    {
        $this->switchDb();

        $selectRecipient = $req->input('select_recipient');
        $rawPhone        = $req->input('phone_number') ?: $req->input('phone_numbers', '');
        $templateId      = (int) $req->input('template_id');

        if (!$templateId) {
            return redirect('/sms/compose')->with('error_sms', 'Please select a template before sending.');
        }

        $template = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `id` = ? AND `is_active` = 1 AND `deleted` = '0' AND `meta_status` = 'approved'",
            [$templateId]
        );

        if (empty($template)) {
            return redirect('/sms/compose')->with('error_sms', 'Selected template not found or not approved.');
        }
        $template = $template[0];

        // Resolve phone numbers and build a phone→client map where possible
        $phones    = [];
        $clientMap = [];

        if ($selectRecipient == '1' || $selectRecipient == '5') {
            $cleaned = str_replace(' ', '', $rawPhone);
            if ($cleaned === '') {
                return redirect('/sms/compose')->with('error_sms', 'Please provide a number to send the message.');
            }
            $phones = explode(',', $cleaned);
        } elseif ($selectRecipient == '2') {
            $clients = DB::connection('mysql2')->select(
                "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = 1"
            );
            if (empty($clients)) {
                return redirect('/sms/compose')->with('error_sms', 'No active clients at the moment.');
            }
            foreach ($clients as $c) { $phones[] = $c->clients_contacts; $clientMap[$c->clients_contacts] = $c; }
        } elseif ($selectRecipient == '3') {
            $clients = DB::connection('mysql2')->select(
                "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = 0"
            );
            if (empty($clients)) {
                return redirect('/sms/compose')->with('error_sms', 'No inactive clients at the moment.');
            }
            foreach ($clients as $c) { $phones[] = $c->clients_contacts; $clientMap[$c->clients_contacts] = $c; }
        } elseif ($selectRecipient == '4') {
            $clients = DB::connection('mysql2')->select(
                "SELECT * FROM `client_tables` WHERE `deleted` = '0'"
            );
            if (empty($clients)) {
                return redirect('/sms/compose')->with('error_sms', 'No clients to send messages at the moment.');
            }
            foreach ($clients as $c) { $phones[] = $c->clients_contacts; $clientMap[$c->clients_contacts] = $c; }
        } else {
            return redirect('/sms/compose')->with('error_sms', 'Please select a recipient option.');
        }

        $sent   = 0;
        $failed = 0;

        foreach ($phones as $phone) {
            $phone = trim($phone);
            if ($phone === '') continue;

            $client = $clientMap[$phone] ?? null;
            if (!$client) {
                $rows   = DB::connection('mysql2')->select(
                    "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_contacts` = ?",
                    [$phone]
                );
                $client = $rows[0] ?? null;
            }

            $variables = $client ? $this->resolveTemplateVariables($template, $client) : [];
            $waPhone   = $this->formatKenyanPhone($phone);
            $result    = $this->sendWhatsAppTemplate($waPhone, $template->template_name, $variables, $template->category);
            $status    = isset($result['messages'][0]['id']) ? 1 : 0;
            $waId      = $result['messages'][0]['id'] ?? null;
            $body      = $this->interpolateTemplate($template->body_text, $variables);
            $clientId  = $client->client_id ?? 0;

            $this->logWhatsAppMessage($clientId, $body, $waPhone, $status, $template->category, 'outbound', $waId, $template->template_name);

            $status ? $sent++ : $failed++;
        }

        return redirect('/sms/compose')->with(
            'message_success',
            "WhatsApp: Sent {$sent}" . ($failed ? ", {$failed} failed." : '.')
        );
    }

    // ─── Send Verification Code ───────────────────────────────────────────────

    public function sendVerificationCode(Request $req)
    {
        $req->validate([
            'phone'   => 'required|string',
            'code'    => 'required|string|max:10',
            'client_id' => 'nullable|integer',
        ]);

        $phone  = $this->formatKenyanPhone($req->phone);
        $body   = "Your verification code is: *{$req->code}*. Valid for 10 minutes.";
        $result = $this->sendWhatsAppMessage($phone, $body, 'authentication');

        $status = isset($result['messages'][0]['id']) ? 1 : 0;
        $waId   = $result['messages'][0]['id'] ?? null;

        if ($req->client_id) {
            $this->logWhatsAppMessage(
                $req->client_id,
                $body,
                $phone,
                $status,
                'authentication',
                'outbound',
                $waId
            );
        }

        return response()->json(['success' => (bool) $status]);
    }

    // ─── Template Management ─────────────────────────────────────────────────

    public function getTemplates()
    {
        $this->switchDb();

        $templates = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `deleted` = '0' ORDER BY `id` ASC"
        );

        $deletedCount = DB::connection('mysql2')->select(
            "SELECT COUNT(*) AS total FROM `whatsapp_templates` WHERE `deleted` = '1'"
        )[0]->total ?? 0;

        $maxTemplates = config('messaging.max_templates', 3);
        $categories   = config('messaging.message_categories');

        return view('whatsapp.templates', compact('templates', 'maxTemplates', 'categories', 'deletedCount'));
    }

    public function saveTemplate(Request $req)
    {
        $req->validate([
            'id'            => 'nullable|integer',
            'name'          => 'required|string|max:255',
            'template_name' => 'required|string|max:255|regex:/^[a-z0-9_]+$/',
            'category'      => 'required|in:service,utility,authentication,marketing',
            'body_text'     => 'required|string|max:1024',
            'variables'     => 'nullable|string',
            'language'      => 'nullable|string|max:10',
        ]);

        // ── Meta template rules ──────────────────────────────────────────────
        $body = $req->body_text;

        // Extract all {{N}} placeholders
        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
        $placeholderNums = array_map('intval', $matches[1]);
        $uniquePlaceholders = array_unique($placeholderNums);
        sort($uniquePlaceholders);

        // Must be sequential starting from 1 with no gaps
        if (!empty($uniquePlaceholders)) {
            $expected = range(1, count($uniquePlaceholders));
            if ($uniquePlaceholders !== $expected) {
                return back()->withInput()->with(
                    'error_wa',
                    'Variables must be numbered sequentially starting from {{1}} with no gaps (e.g. {{1}}, {{2}}, {{3}}).'
                );
            }
        }

        $maxVar = !empty($uniquePlaceholders) ? max($uniquePlaceholders) : 0;

        if ($maxVar > 10) {
            return back()->withInput()->with('error_wa', 'Meta allows a maximum of 10 variables per template.');
        }

        // Parse declared variables
        $rawVars    = trim($req->variables ?? '');
        $varArray   = $rawVars !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $rawVars))))
            : [];

        $allowedVars = ['name', 'first_name', 'account_no', 'phone', 'amount', 'username', 'address', 'exp_date', 'wallet'];
        foreach ($varArray as $v) {
            if (!in_array($v, $allowedVars)) {
                return back()->withInput()->with(
                    'error_wa',
                    "Unknown variable \"{$v}\". Allowed: " . implode(', ', $allowedVars) . '.'
                );
            }
        }

        // Count of declared variables must match max placeholder number
        if (count($varArray) !== $maxVar) {
            return back()->withInput()->with(
                'error_wa',
                "Body has {$maxVar} placeholder(s) but " . count($varArray) . " variable(s) declared. They must match."
            );
        }

        $variables = !empty($varArray) ? json_encode($varArray) : '[]';
        $language  = $req->language ?? 'en';

        $this->switchDb();

        $totalCount = DB::connection('mysql2')->select(
            "SELECT COUNT(*) AS total FROM `whatsapp_templates` WHERE `deleted` = '0'"
        )[0]->total;

        $maxTemplates = config('messaging.max_templates', 3);

        if (!$req->id && $totalCount >= $maxTemplates) {
            return back()->withInput()->with('error_wa', "You have reached the limit of {$maxTemplates} templates. Delete an existing template to add a new one.");
        }

        $now = date('YmdHis');

        // For edits, fetch current Meta template ID so we can delete it before resubmitting
        $existingMetaId = null;
        if ($req->id) {
            $existing = DB::connection('mysql2')->select(
                "SELECT `meta_template_id` FROM `whatsapp_templates` WHERE `id` = ? AND `deleted` = '0'",
                [$req->id]
            );
            $existingMetaId = $existing[0]->meta_template_id ?? null;

            DB::connection('mysql2')->update(
                "UPDATE `whatsapp_templates` SET `name` = ?, `template_name` = ?, `category` = ?,
                 `body_text` = ?, `variables` = ?, `language` = ?, `date_changed` = ?
                 WHERE `id` = ? AND `deleted` = '0'",
                [$req->name, $req->template_name, $req->category, $body, $variables, $language, $now, $req->id]
            );
            $savedId = $req->id;
        } else {
            DB::connection('mysql2')->insert(
                "INSERT INTO `whatsapp_templates`
                 (`name`, `template_name`, `category`, `body_text`, `variables`, `meta_status`, `language`, `is_active`, `date_changed`, `deleted`)
                 VALUES (?, ?, ?, ?, ?, 'not_submitted', ?, 1, ?, '0')",
                [$req->name, $req->template_name, $req->category, $body, $variables, $language, $now]
            );
            $savedId = DB::connection('mysql2')->getPdo()->lastInsertId();
        }

        // ── Auto-submit to Meta ──────────────────────────────────────────────
        $cfg = $this->getWhatsAppSettings();

        if (!$cfg || empty($cfg['business_account_id'])) {
            return back()->with('success_wa', 'Template saved locally. (Meta submission skipped — WHATSAPP_BUSINESS_ACCOUNT_ID not configured.)');
        }

        // If editing a previously submitted template, delete the old version on Meta first
        if ($existingMetaId) {
            $delUrl = "{$cfg['base_url']}/{$cfg['api_version']}/{$cfg['business_account_id']}/message_templates"
                    . '?hsm_id=' . urlencode($existingMetaId)
                    . '&name=' . urlencode($req->template_name);
            $this->whatsappApiCall($delUrl, $cfg['access_token'], null, 'DELETE');
        }

        $payload = $this->buildMetaTemplatePayload($req->template_name, $language, $req->category, $body, $varArray);
        $result  = $this->submitMetaTemplate($req->template_name, $payload, $cfg);

        if (isset($result['id'])) {
            DB::connection('mysql2')->update(
                "UPDATE `whatsapp_templates` SET `meta_status` = 'pending', `meta_template_id` = ? WHERE `id` = ?",
                [$result['id'], $savedId]
            );
            return back()->with('success_wa', 'Template saved and submitted to Meta. Approval is usually within a few minutes — use Sync Status to check.');
        }

        return back()->with('error_wa', 'Template saved locally but Meta submission failed: ' . $this->metaErrorMessage($result));
    }

    public function deleteTemplate($id)
    {
        $this->switchDb();

        $tpl = DB::connection('mysql2')->select(
            "SELECT `meta_template_id`, `template_name` FROM `whatsapp_templates` WHERE `id` = ? AND `deleted` = '0'",
            [$id]
        );

        // Delete from Meta if it was submitted there
        if (!empty($tpl) && !empty($tpl[0]->meta_template_id)) {
            $cfg = $this->getWhatsAppSettings();
            if ($cfg && !empty($cfg['business_account_id'])) {
                $url = "{$cfg['base_url']}/{$cfg['api_version']}/{$cfg['business_account_id']}/message_templates"
                     . '?hsm_id=' . urlencode($tpl[0]->meta_template_id)
                     . '&name=' . urlencode($tpl[0]->template_name);
                $this->whatsappApiCall($url, $cfg['access_token'], null, 'DELETE');
            }
        }

        DB::connection('mysql2')->update(
            "UPDATE `whatsapp_templates` SET `deleted` = '1' WHERE `id` = ?",
            [$id]
        );
        return redirect('/whatsapp/templates')->with('success_wa', 'Template deleted.');
    }

    // ─── Submit Template to Meta for Approval ────────────────────────────────

    public function submitToMeta($id)
    {
        $this->switchDb();

        $tpl = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `id` = ? AND `deleted` = '0'",
            [$id]
        );

        if (empty($tpl)) {
            return back()->with('error_wa', 'Template not found.');
        }

        $tpl = $tpl[0];
        $cfg = $this->getWhatsAppSettings();

        if (!$cfg || empty($cfg['business_account_id'])) {
            return back()->with('error_wa', 'WHATSAPP_BUSINESS_ACCOUNT_ID is not set in your .env file.');
        }

        $vars    = json_decode($tpl->variables ?? '[]', true) ?: [];
        $payload = $this->buildMetaTemplatePayload($tpl->template_name, $tpl->language ?? 'en_US', $tpl->category, $tpl->body_text, $vars);
        $result  = $this->submitMetaTemplate($tpl->template_name, $payload, $cfg);

        if (isset($result['id'])) {
            DB::connection('mysql2')->update(
                "UPDATE `whatsapp_templates` SET `meta_status` = 'pending', `meta_template_id` = ?, `date_changed` = ? WHERE `id` = ?",
                [$result['id'], date('YmdHis'), $id]
            );
            return back()->with('success_wa', 'Template submitted to Meta. Approval usually takes a few minutes — use Sync Status to check.');
        }

        return back()->with('error_wa', 'Meta API error: ' . $this->metaErrorMessage($result));
    }

    // ─── Sync All Template Statuses from Meta ─────────────────────────────────

    public function syncMetaStatus()
    {
        $this->switchDb();

        $cfg = $this->getWhatsAppSettings();
        if (!$cfg || empty($cfg['business_account_id'])) {
            return back()->with('error_wa', 'WHATSAPP_BUSINESS_ACCOUNT_ID is not set in your .env file.');
        }

        $url    = "{$cfg['base_url']}/{$cfg['api_version']}/{$cfg['business_account_id']}/message_templates?fields=id,name,status&limit=100";
        $result = $this->whatsappApiCall($url, $cfg['access_token'], null, 'GET');

        if (!isset($result['data'])) {
            $errMsg = $result['error']['message'] ?? 'Could not fetch templates from Meta.';
            return back()->with('error_wa', "Sync failed: {$errMsg}");
        }

        $statusMap = [
            'APPROVED'         => 'approved',
            'PENDING'          => 'pending',
            'IN_APPEAL'        => 'pending',
            'REJECTED'         => 'rejected',
            'PAUSED'           => 'rejected',
            'DISABLED'         => 'rejected',
            'PENDING_DELETION' => 'rejected',
        ];

        $updated    = 0;
        $matchedNames = [];
        foreach ($result['data'] as $metaTpl) {
            $metaStatus = $statusMap[strtoupper($metaTpl['status'] ?? '')] ?? 'pending';
            $affected   = DB::connection('mysql2')->update(
                "UPDATE `whatsapp_templates`
                 SET `meta_status` = ?, `meta_template_id` = ?, `date_changed` = ?
                 WHERE `template_name` = ? AND `deleted` = '0'",
                [$metaStatus, $metaTpl['id'], date('YmdHis'), $metaTpl['name']]
            );
            if ($affected > 0) {
                $matchedNames[] = $metaTpl['name'] . ' [' . strtoupper($metaTpl['status'] ?? '?') . ']';
                $updated += $affected;
            }
        }

        $metaList = empty($matchedNames)
            ? 'No matching local templates were found.'
            : implode(', ', $matchedNames) . '.';

        return back()->with('success_wa', "Sync complete. {$updated} local template(s) updated: {$metaList}");
    }

    public function toggleTemplate($id)
    {
        $this->switchDb();
        $tpl = DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `id` = ? AND `deleted` = '0'",
            [$id]
        );

        if (empty($tpl)) {
            return back()->with('error_wa', 'Template not found.');
        }

        $newStatus = $tpl[0]->is_active ? 0 : 1;

        if ($newStatus == 1) {
            $activeCount = DB::connection('mysql2')->select(
                "SELECT COUNT(*) AS total FROM `whatsapp_templates` WHERE `is_active` = 1 AND `deleted` = '0'"
            )[0]->total;

            $maxTemplates = config('messaging.max_templates', 3);

            if ($activeCount >= $maxTemplates) {
                return back()->with('error_wa', "Maximum of {$maxTemplates} active templates allowed.");
            }
        }

        DB::connection('mysql2')->update(
            "UPDATE `whatsapp_templates` SET `is_active` = ? WHERE `id` = ?",
            [$newStatus, $id]
        );

        return back()->with('success_wa', 'Template updated.');
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    public function stats()
    {
        $this->switchDb();
        $stats = $this->getWhatsAppStats();
        return view('whatsapp.stats', compact('stats'));
    }

    // ─── Webhook ──────────────────────────────────────────────────────────────

    public function verifyWebhook(Request $req)
    {
        $mode      = $req->query('hub_mode');
        $token     = $req->query('hub_verify_token');
        $challenge = $req->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('messaging.whatsapp.webhook_verify_token')) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function webhook(Request $req)
    {
        $payload = $req->all();

        \Log::info('WhatsApp webhook payload', $payload);

        if (!isset($payload['entry'])) {
            return response()->json(['status' => 'ok']);
        }

        $ownPhoneNumberId = config('messaging.whatsapp.phone_number_id');

        foreach ($payload['entry'] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                // Only process events destined for our registered WABA number
                $incomingPhoneNumberId = $value['metadata']['phone_number_id'] ?? null;
                if ($incomingPhoneNumberId && $incomingPhoneNumberId !== $ownPhoneNumberId) {
                    continue;
                }

                foreach ($value['messages'] ?? [] as $message) {
                    $this->handleInboundMessage($message, $value['contacts'][0] ?? null);
                }

                foreach ($value['statuses'] ?? [] as $status) {
                    $this->handleStatusUpdate($status);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // ─── Resolve org DB by searching for the sender's phone across all orgs ──

    private function resolveOrgByPhone(string $localPhone): void
    {
        $orgs = DB::select("SELECT organization_database FROM organizations");

        foreach ($orgs as $org) {
            $this->applyOrgDb($org->organization_database);

            $hit = DB::connection('mysql2')->select(
                "SELECT client_id FROM client_tables WHERE deleted = '0' AND clients_contacts LIKE ? LIMIT 1",
                ["%{$localPhone}%"]
            );

            if (!empty($hit)) return; // stay on this org's DB
        }

        // Sender is not a registered client — default to the first org
        if (!empty($orgs)) {
            $this->applyOrgDb($orgs[0]->organization_database);
        }
    }

    private function resolveOrgByWaMessageId(string $waId): void
    {
        $orgs = DB::select("SELECT organization_database FROM organizations");

        foreach ($orgs as $org) {
            $this->applyOrgDb($org->organization_database);

            $hit = DB::connection('mysql2')->select(
                "SELECT id FROM whatsapp_chats WHERE wa_message_id = ? LIMIT 1",
                [$waId]
            );

            if (!empty($hit)) return;
        }

        if (!empty($orgs)) {
            $this->applyOrgDb($orgs[0]->organization_database);
        }
    }

    private function applyOrgDb(string $dbName): void
    {
        config(['database.connections.mysql2.database' => $dbName]);
        DB::purge('mysql2');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function handleInboundMessage(array $message, ?array $contact)
    {
        $phone = $message['from'] ?? null;
        if (!$phone) return;

        $localPhone = $this->toLocalPhone($phone);

        // Find which org this sender belongs to, then stay on that DB
        $this->resolveOrgByPhone($localPhone);

        // If the phone is shared by multiple clients, use whichever was registered first
        $client = DB::connection('mysql2')->select(
            "SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_contacts` LIKE ? ORDER BY `client_id` ASC LIMIT 1",
            ["%{$localPhone}%"]
        );

        $body = $message['text']['body']
            ?? $message['image']['caption']
            ?? '[Media message]';

        $waId = $message['id'] ?? null;
        $now  = now()->format('YmdHis');

        // Unknown number — save to the primary DB support inbox and stop
        if (empty($client)) {
            DB::table('unknown_wa_chats')->insert([
                'phone'          => $phone,
                'wa_message_id'  => $waId,
                'direction'      => 'inbound',
                'message'        => $body,
                'delivery_status'=> 'received',
                'date_sent'      => $now,
            ]);
            return;
        }

        $clientId = $client[0]->client_id;

        $msgId = DB::connection('mysql2')->table('sms_tables')->insertGetId([
            'sms_content'    => $body,
            'date_sent'      => $now,
            'recipient_phone'=> $phone,
            'sms_status'     => 1,
            'account_id'     => $clientId,
            'sms_type'       => 2,
            'channel'        => 'whatsapp',
            'message_category' => 'service',
            'deleted'        => '0',
        ]);

        DB::connection('mysql2')->table('whatsapp_chats')->insert([
            'message_id'       => $msgId,
            'direction'        => 'inbound',
            'wa_message_id'    => $waId,
            'billing_category' => 'service',
            'billable'         => 0, // receiving a message is never charged
            'message_category' => 'service',
            'delivery_status'  => 'received',
            'window_open'      => 1,
            'received_at'      => $now,
        ]);
    }

    private function handleStatusUpdate(array $status)
    {
        $waId      = $status['id'] ?? null;
        $newStatus = $status['status'] ?? null;

        if (!$waId || !$newStatus) return;

        // Check primary-DB unknown inbox first
        $unknownUpdate = ['delivery_status' => $newStatus];
        $affected = DB::table('unknown_wa_chats')
            ->where('wa_message_id', $waId)
            ->update($unknownUpdate);

        if ($affected > 0) return;

        // Find which org's DB holds this message, then update it there.
        // Billing (conversation_id/billing_category/billable) is decided at send time in
        // logWhatsAppMessage() and is not touched here — Meta's webhook is unreliable and
        // overwriting a self-assigned conversation_id for just one row in a window would
        // break the grouping of the rest of that window's messages.
        $this->resolveOrgByWaMessageId($waId);

        DB::connection('mysql2')->update(
            'UPDATE `whatsapp_chats` SET delivery_status = ? WHERE `wa_message_id` = ?',
            [$newStatus, $waId]
        );
    }

    private function fetchTemplates(): array
    {
        return DB::connection('mysql2')->select(
            "SELECT * FROM `whatsapp_templates` WHERE `is_active` = 1 AND `deleted` = '0' AND `meta_status` = 'approved' ORDER BY `id` ASC"
        );
    }

    private function resolveTemplateVariables($template, $client): array
    {
        $vars      = json_decode($template->variables ?? '[]', true) ?: [];
        $fullName  = ucwords(strtolower($client->client_name ?? ''));
        $firstName = explode(' ', $fullName)[0];

        $expRaw = $client->next_expiration_date ?? '';
        $expDate = $expRaw ? date('d M Y', strtotime($expRaw)) : '';

        $map = [
            'name'       => $fullName,
            'first_name' => $firstName,
            'account_no' => $client->client_account ?? '',
            'phone'      => $client->clients_contacts ?? '',
            'amount'     => $client->monthly_payment ?? '',
            'username'   => $client->client_secret ?? $client->client_account ?? '',
            'address'    => $client->client_address ?? '',
            'exp_date'   => $expDate,
            'wallet'     => $client->wallet_amount ?? '',
        ];

        $resolved = [];
        foreach ($vars as $var) {
            $resolved[] = $map[$var] ?? '';
        }
        return $resolved;
    }

    private function interpolateTemplate(string $body, array $variables): string
    {
        foreach ($variables as $i => $value) {
            $body = str_replace('{{' . ($i + 1) . '}}', $value, $body);
        }
        return $body;
    }

    private function getWhatsAppStats(): array
    {
        $today     = date('Ymd') . '000000';
        $yesterday = date('Ymd', strtotime('-1 day')) . '000000';
        $lastWeek  = date('Ymd', strtotime('-7 days')) . '000000';
        $lastMonth = date('Ymd', strtotime('-30 days')) . '000000';

        $base = "SELECT COUNT(*) AS total FROM `sms_tables` WHERE `channel` = 'whatsapp' AND `deleted` = '0'";

        $total      = DB::connection('mysql2')->select("{$base}")[0]->total;
        $today_c    = DB::connection('mysql2')->select("{$base} AND `date_sent` >= '{$today}'")[0]->total;
        $week_c     = DB::connection('mysql2')->select("{$base} AND `date_sent` >= '{$lastWeek}'")[0]->total;
        $month_c    = DB::connection('mysql2')->select("{$base} AND `date_sent` >= '{$lastMonth}'")[0]->total;

        $byCategory = DB::connection('mysql2')->select(
            "SELECT `message_category`, COUNT(*) AS total FROM `sms_tables`
             WHERE `channel` = 'whatsapp' AND `deleted` = '0' GROUP BY `message_category`"
        );

        $byStatus = DB::connection('mysql2')->select(
            "SELECT wc.`delivery_status`, COUNT(*) AS total FROM `whatsapp_chats` wc
             JOIN `sms_tables` s ON s.sms_id = wc.message_id
             WHERE s.`channel` = 'whatsapp' AND s.`deleted` = '0'
             GROUP BY wc.`delivery_status`"
        );

        return compact('total', 'today_c', 'week_c', 'month_c', 'byCategory', 'byStatus');
    }

    private function buildMetaTemplatePayload(string $templateName, string $language, string $category, string $body, array $varArray): string
    {
        $categoryMap   = ['marketing' => 'MARKETING', 'utility' => 'UTILITY', 'authentication' => 'AUTHENTICATION', 'service' => 'UTILITY'];
        $exampleValues = [
            'name' => 'John Doe', 'first_name' => 'John', 'account_no' => 'ACC001',
            'phone' => '0712345678', 'amount' => '1500', 'username' => 'john.doe',
            'address' => '123 Main St', 'exp_date' => '31 Dec 2025', 'wallet' => '500',
        ];

        $bodyComponent = ['type' => 'BODY', 'text' => $body];
        if (!empty($varArray)) {
            $bodyComponent['example'] = [
                'body_text' => [array_values(array_map(fn($v) => $exampleValues[$v] ?? 'sample', $varArray))],
            ];
        }

        return json_encode([
            'name'                  => $templateName,
            'language'              => $language,
            'category'              => $categoryMap[$category] ?? 'UTILITY',
            'allow_category_change' => true,
            'components'            => [$bodyComponent],
        ]);
    }

    /**
     * Submit a template payload to Meta, with one automatic retry if the name already exists.
     * Returns the Meta API result array.
     */
    private function submitMetaTemplate(string $templateName, string $payload, array $cfg): array
    {
        $submitUrl = "{$cfg['base_url']}/{$cfg['api_version']}/{$cfg['business_account_id']}/message_templates";
        $result    = $this->whatsappApiCall($submitUrl, $cfg['access_token'], $payload);

        // If Meta says the name already exists, delete it by name and retry once
        if (isset($result['error'])) {
            $errDetails = strtolower(
                ($result['error']['error_data']['details'] ?? '') . ' ' .
                ($result['error']['error_user_msg']         ?? '') . ' ' .
                ($result['error']['message']                ?? '')
            );

            if (str_contains($errDetails, 'already') || str_contains($errDetails, 'duplicate')) {
                $delUrl = "{$cfg['base_url']}/{$cfg['api_version']}/{$cfg['business_account_id']}/message_templates"
                        . '?name=' . urlencode($templateName);
                $this->whatsappApiCall($delUrl, $cfg['access_token'], null, 'DELETE');

                // Retry the submission after deletion
                $result = $this->whatsappApiCall($submitUrl, $cfg['access_token'], $payload);
            }
        }

        return $result;
    }

    private function metaErrorMessage(array $result): string
    {
        $err     = $result['error'] ?? [];
        $message = $err['message'] ?? 'Unknown error';
        $details = $err['error_data']['details'] ?? ($err['error_user_msg'] ?? '');
        return $details ? "{$message} — {$details}" : $message;
    }

    private function switchDb()
    {
        $db_name = session('database_name');
        if ($db_name) {
            config(['database.connections.mysql2.database' => $db_name]);
            DB::purge('mysql2');
        }
    }

    private function toLocalPhone(string $internationalPhone): string
    {
        if (str_starts_with($internationalPhone, '254')) {
            return '0' . substr($internationalPhone, 3);
        }
        return $internationalPhone;
    }
}
