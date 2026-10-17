# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

```bash
# Start development server
php artisan serve

# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run a single test method
php artisan test --filter=test_method_name

# Build frontend assets
npm run dev

# Build for production
npm run prod

# Watch for frontend changes
npm run watch

# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan view:clear

# Run migrations
php artisan migrate

# Tinker (interactive shell)
php artisan tinker
```

## Three-System Architecture

This project (`mikrotik_cloud`) is the **child/per-organization system**. Each ISP organization uses this system to run their business. It is one of three tightly linked systems:

- **`mikrotik_cloud_manager`** — located at `/home/hila/Documents/laravel/mikrotik_cloud_manager`. This is the master control plane that provisions organizations, sets billing rates, and manages the central database.
- **`crontab`** — located at `/opt/lampp/htdocs/crontab`. This is a standalone PHP cron job system that runs background tasks affecting **both** `mikrotik_cloud` and `mikrotik_cloud_manager`. It is not a Laravel project — it is plain PHP served by LAMPP/Apache. Its own `CLAUDE.md` documents the scripts and conventions in detail.

### Cross-Project Sync Points

Changes here sometimes require corresponding changes in the other two systems:

- **Database schema changes on org DBs** — any new table or `ALTER TABLE` introduced here must be communicated to `mikrotik_cloud_manager` so it can apply the change across all existing org databases during provisioning or upgrades. The `crontab` scripts also query org DB tables directly (`client_tables`, `settings`, `sms_tables`, etc.) — if you add or rename columns those scripts may need updating too.
- **SMS templates / message variables** — the `crontab` system reads message templates from the `settings` table (`keyword='Messages'`) and substitutes variables like `[client_name]`, `[exp_date]`, etc. Any new template variable added here should also be added to `shared_functions.php` in `crontab`.
- **Client activation/deactivation** — `activate_deactivate_clients.php` and `freeze_clients.php` in `crontab` are the canonical source of truth for toggling client status on the MikroTik router. This system triggers them via the `https://billing.hypbits.com/activate/{client_id}/{db_name}` API; do not duplicate that logic here.
- **WhatsApp billing rates** — the `whatsapp_billing_rates` table lives in the `mikrotik_cloud_manager` DB. This system reads rates from there; never store or manage rates here.
- **Unknown sender inbox** — this system's WhatsApp webhook writes unknown senders to `unknown_wa_chats` in the primary `mikrotik_cloud_manager` DB. Do not build management UI for that table here; it belongs in the manager system.
- **Packages / service tiers** — stored in the central DB (`mikrotik_cloud_manager`) and read by this system for client plan assignments. Package CRUD is managed there, not here.
- **Organization context** — this system reads its own org record (database name, wallet, expiry, package) from the central DB. That record is created and maintained by `mikrotik_cloud_manager`.

When you introduce a new shared table, a new column on org DBs, or a new config value that the manager needs to display or bill — update `mikrotik_cloud_manager`'s CLAUDE.md and implement the manager-side work there. When changes affect background job behaviour (expiry, freeze, activation, SMS sending), also check and update the relevant `crontab` scripts.

## Architecture Overview

This is an **ISP Management System** for managing MikroTik routers, client accounts, billing, SMS, and payments (M-Pesa). Built on Laravel 8 with a multi-tenant, dual-database architecture.

### Dual-Database Architecture

Two MySQL connections are defined in `config/database.php`:
- `mysql` — primary database (`mikrotik_cloud_manager`): stores admin users, organizations, shared config
- `mysql2` — secondary connection: dynamically points to per-client/organization databases at runtime

Most client data (clients, routers, transactions, SMS) lives in per-organization databases accessed via the `mysql2` connection. Controllers typically switch the `mysql2` connection credentials based on the logged-in organization before querying.

### Two Authentication Flows

- **Admin/Organization login** — `/Hypbits` → `login.php` controller → session-based auth for ISP operators
- **Client login** — `/Client-Login` → same controller, different session scope for end-customers

Three middleware classes guard routes:
- `checkAccount` — validates organization account status, payment, expiry, and client count limits
- `validated` (CheckValidatedClients) — validates client status
- `clientValidate` — client-specific login guard

### Key Controllers

| Controller | Responsibility |
|---|---|
| `Clients.php` (451KB) | Core client lifecycle — create, edit, activate, deactivate PPPoE/Static clients |
| `Transaction.php` (90KB) | Billing, invoices, payments, M-Pesa callbacks |
| `Sms.php` (79KB) | SMS composition, bulk sending, scheduling, provider management |
| `Router.php` (80KB) | MikroTik router CRUD, secret management, bandwidth monitoring |
| `Router_Cloud.php` (60KB) | Cloud router operations separate from on-premise routers |
| `Expenses.php` (31KB) | Business expense tracking and reporting |
| `export_client.php` (36KB) | CSV/Excel export for client data |
| `login.php` (31KB) | Auth, OTP verification, password reset |
| `admin.php` (23KB) | Admin/organization user management |
| `SharedTables.php` (26KB) | Cross-organization shared data tables |
| `billsms_manager.php` (28KB) | Automated billing SMS workflows |
| `Region.php` | Region & subregion CRUD — groups clients by area, used for targeted messaging |

Controllers do not extend a resource base class — `Controller.php` (the base) provides shared utility methods like `getPPPSecrets($router_id)` and `convertBits($bits, $type)`.

### Regions & Subregions

Regions and subregions let an org group clients by area for targeted messaging: a **region** is a broad area (e.g. "Nairobi North") and is split into **subregions** (town, estate, zone). Reachable from `/Accounts` → the "Regions" button next to "Shared Tables" (not in the main sidebar menu — it's a setup-once admin tool, same placement rationale as Shared Tables/Manage Admin). `/Regions` (`Region.php`, `regions.blade.php`) manages both lists on one page.

- **Storage** — same convention as expense categories: JSON arrays in the org DB's `settings` table. `keyword = 'Regions'` → `[{"name","index"}]`; `keyword = 'Subregions'` → `[{"name","index","region"}]` where `region` is the parent region's **name**. No dedicated tables. Read through `Controller::getRegionsList()`, `getSubregionsList()`, `getSubregionRegionMap()` (subregion → region) and `getSubregionsGrouped()` (region → subregions, for `<optgroup>` dropdowns).
- **Clients are only ever assigned a subregion.** `client_tables.region` (`VARCHAR(150) NULL`, already on all org DBs) holds the **subregion name** despite the column name; a client's region is always looked up from its subregion, never stored. Set from the new-client forms (all four: `new_client_static`, `new_client_pppoe`, `newClient`, `newPPOEclient`) and from the client profile's "Region" row (`components/client/dash.blade.php`) via `POST /change_client_region` → `Clients::change_client_region()`, which validates against the subregion list.
- **Rules** — subregion names are unique across the whole org (case-insensitive); every subregion must belong to a region; a region with subregions cannot be deleted; a subregion with (non-deleted) clients cannot be deleted. Renaming a subregion cascades to `client_tables.region`; renaming a region only updates the `region` field inside the `Subregions` JSON; moving a subregion to another region touches no clients.
- **One-time migration (per org, automatic)** — before subregions existed, the `Regions` row held what are now subregions. The first time any region helper runs for an org with no `Subregions` row, `Controller::migrateRegionsToSubregions()` moves the old list into `Subregions` under a default region named **"General"** and rewrites `Regions` to `[General]`. The existence of the `Subregions` row is the "already migrated" marker. Client rows need no change.
- **Audience builder / messaging** — `getFilteredAudience()` takes a `region` filter (expands to `client_tables.region IN (<that region's subregions>)`) and a `subregion` filter (`client_tables.region = ?`). The builder's Subregion dropdown narrows to the chosen region. Message tags: `[region]` = the client's region, `[subregion]` = the client's subregion.
- **Cross-system** — no schema change was needed for subregions. Neither `mikrotik_cloud_manager` nor `crontab` reads regions; if crontab billing messages ever need `[region]`/`[subregion]`, add them to `shared_functions.php` there with the same lookup.

### MikroTik RouterOS Integration

`app/Classes/routeros_api.php` is a PHP socket-based API client (v1.4) that communicates directly with MikroTik routers over the RouterOS API protocol. It is used by `Router.php` and `Router_Cloud.php` to manage PPPoE secrets, bandwidth queues, and live statistics.

Stats are collected at multiple granularities via models: `one_minute_stats`, `five_minute_stats`, `thirty_minute_stats`, `two_hour_stats`, `one_day_stats`.

### M-Pesa Payment Integration

`app/Classes/MpesaService.php` handles STK push payments. Credentials are loaded from the database per organization. The callback endpoint is handled in `Transaction.php`.

### SMS System

SMS functionality lives in both `Sms.php` (operator-facing composition/sending) and `billsms_manager.php` (automated billing triggers). Related models: `sms_table`, `sms_client`, `sms_clients_package`.

### Routes

All application routes are in `routes/web.php` (26KB). The API routes (`routes/api.php`) only expose Laravel Sanctum token endpoints; all business logic goes through `web.php` with session auth.

### Frontend

Blade templates in `resources/views/` (89 files). Assets compiled via Laravel Mix (`webpack.mix.js`). No SPA framework — server-rendered with jQuery/Axios for dynamic interactions. Public JS scripts also live in `public/scripts/` and `public/ajax/`.

**UI Theme: Chameleon Admin v1.0.0** by ThemeSelection (Bootstrap 4). All frontend work must remain consistent with this theme — use its existing CSS classes, components, colour variables, and layout patterns. Do not introduce styles, component libraries, or layout structures from other themes or frameworks unless explicitly instructed.

**Blade Template Rules:** Every UI build must strictly follow Laravel Blade syntax:
- Use `@{{ expression }}` to output literal `{{ }}` curly braces (e.g. `@{{1}}` renders as `{{1}}`). Never use PHP string concatenation or nested Blade expressions to escape curly braces.
- All buttons and links must use the existing `<x-button>` and `<x-button-link>` components — never raw `<button>` or `<a class="btn">` elements. This applies everywhere: page bodies, card headers, modal footers, inline forms. Set a `btnId` when the element's `href` or state needs to be updated by JavaScript.
- Every view that navigates beyond what the menu shows must include back-navigation buttons at the top of the content body using `<x-button-link>`.
- Never use `btnType="outline-*"`. Always use the solid variant: `primary`, `secondary`, `info`, `success`, `warning`, `danger`.

**Date formatting:** `client_tables` date fields (`next_expiration_date`, `clients_reg_date`, `last_seen`, etc.) are stored as MySQL datetime strings (`YYYY-MM-DD HH:MM:SS`). In PHP format them with `date('D jS M Y g:i:sA', strtotime($value))`. In JavaScript use the `fmtDatetime()` helper (defined in `chats.blade.php`) which calls `new Date(s.replace(' ','T'))` and formats as `"Ddd Dth Mon YYYY H:MM:SSam/pm"` (e.g. `"Mon 10th Jan 2026 10:00:10AM"`).

### Test Database

Tests run against a real MySQL database (not SQLite in-memory) — see `phpunit.xml`. Ensure the test database is available and `DB_DATABASE` is set before running tests.

The organisation database used for development and testing is `mikrotik_cloud` (the `mysql2` / secondary connection). When testing features that query per-org tables (`sms_tables`, `client_tables`, `settings`, `whatsapp_chats`, etc.), ensure `DB_DATABASE_CLIENT=mikrotik_cloud` is set in `.env`.

### WhatsApp Module

`app/Http/Controllers/WhatsApp.php` handles the full WhatsApp Business Cloud API flow:
- Chat list and per-client message threads (`/whatsapp/chats`, `/whatsapp/chat/{id}`)
- Outbound free-form and template messages
- Bulk template sends
- Template CRUD with Meta API auto-submission and status sync
- Inbound webhook (`GET /whatsapp/webhook` for verification, `POST /whatsapp/webhook` for events)

**Multi-tenant webhook routing:** The webhook has no user session, so it cannot use `switchDb()`. Instead `resolveOrgByPhone()` searches the sender's phone number across all org databases in the primary `organizations` table, then stays on the matching org's `mysql2` connection. Status updates use `resolveOrgByWaMessageId()` which searches `whatsapp_chats` across all orgs.

**Webhook payload logging:** Every POST from Meta is written to `storage/logs/laravel.log` via `\Log::info('WhatsApp webhook payload', $payload)`.

**App timezone:** `config/app.php` timezone is `Africa/Nairobi`. All timestamp writes use `now()->format('YmdHis')` — do not use bare `date()` in WhatsApp handlers.

**ngrok for local testing:** Run `ngrok http 8000` to get a public HTTPS URL, then register `https://<subdomain>.ngrok-free.app/whatsapp/webhook` in Meta's webhook settings. The verify token is in `.env` as `WHATSAPP_WEBHOOK_VERIFY_TOKEN`.

**Views:** `resources/views/whatsapp/` — `chats.blade.php` (inbox + client info modal), `chat.blade.php` (single thread), `templates.blade.php`, `bulk.blade.php`.

**Config:** `config/messaging.php` holds WhatsApp settings, max templates, and message categories.

### WhatsApp Billing Model

Meta charges per **conversation** (a 24-hour window), not per message. Four categories exist, each priced differently: `service`, `utility`, `marketing`, `authentication`.

**Key rules:**
- Multiple messages within the same 24-hour window share one `conversation_id` — only 1 charge regardless of message count
- `service` conversations (client messages you first) are free up to 1,000/month per WABA, then billed
- Business-initiated conversations (template sends) are always billed per conversation
- Bulk sends: each recipient gets their own conversation window — billing is per recipient, not per send
- If a service window is already open and you send a template, Meta opens a **separate** conversation for the template category — they do not share windows across categories
- Meta reports `pricing.billable = false` for free-tier conversations — this must be stored to avoid overbilling

**Billing data flow:**
1. Meta sends `pricing.billable`, `pricing.category`, and `conversation.id` inside **`sent`** status webhooks only (not `delivered` or `read`)
2. This system captures those three fields and writes them to `whatsapp_chats` columns: `conversation_id`, `billing_category`, `billable`
3. **Billing rates are set in `mikrotik_cloud_manager` by the main system admin — individual orgs cannot change them**
4. The billing/manager system reads `whatsapp_chats` from each org DB and runs:

```sql
SELECT account_id, billing_category,
       COUNT(DISTINCT conversation_id)              AS conversations,
       COUNT(DISTINCT conversation_id) * r.rate     AS cost
FROM whatsapp_chats wc
JOIN whatsapp_billing_rates r ON r.category = wc.billing_category
WHERE wc.billable = 1
  AND wc.date_sent BETWEEN '20260501000000' AND '20260531235959'
GROUP BY account_id, billing_category;
```

**`whatsapp_chats` billing columns (added via ALTER TABLE on each org DB):**
```sql
ALTER TABLE `whatsapp_chats`
  ADD COLUMN `conversation_id`  VARCHAR(100) NULL AFTER `wa_message_id`,
  ADD COLUMN `billing_category` VARCHAR(20)  NULL AFTER `conversation_id`,
  ADD COLUMN `billable`         TINYINT(1)   NULL AFTER `billing_category`,
  ADD INDEX  `idx_conversation_id` (`conversation_id`);
```

**`whatsapp_billing_rates` table lives in `mikrotik_cloud_manager` (not in org DBs):**
```sql
CREATE TABLE `whatsapp_billing_rates` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category`   VARCHAR(20) NOT NULL UNIQUE,  -- service|utility|marketing|authentication
    `rate`       DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    `currency`   VARCHAR(5) NOT NULL DEFAULT 'KES',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Unknown sender inbox:**
Messages from numbers not registered as clients in any org are saved to `unknown_wa_chats` in `mikrotik_cloud_manager` (primary DB). The UI to manage these is built in the manager system, not here. This system only writes to that table via the webhook handler.

```sql
CREATE TABLE `unknown_wa_chats` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `phone`           VARCHAR(30)  NOT NULL,
    `wa_message_id`   VARCHAR(100) DEFAULT NULL,
    `direction`       ENUM('inbound','outbound') NOT NULL DEFAULT 'inbound',
    `message`         TEXT NOT NULL,
    `delivery_status` VARCHAR(20) NOT NULL DEFAULT 'received',
    `date_sent`       VARCHAR(14) NOT NULL,
    `deleted`         TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_phone` (`phone`),
    KEY `idx_wa_message_id` (`wa_message_id`),
    KEY `idx_deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Bulk SMS Sending Agent (built 2026-10-01)

Bulk SMS from `/sms/compose` (Insert Number, Select Client, Filter Clients) is no longer sent inside the HTTP request. `Sms::sendsms()` personalizes each recipient's `[tags]`, **queues** one `sms_tables` row per recipient via `Sms::queueSms()`, then `Sms::startSmsAgent()` calls the agent link and returns at once ("N message(s) queued and are being sent").

- **The agent** is `send_queued_sms.php` in the `crontab` project, called as a link with the org database: `{SMS_AGENT_URL}?db=<organization_database>` (also runnable as `php send_queued_sms.php <db>`). It is **not** scheduled in the system crontab — it only runs when this app calls it after queueing. `SMS_AGENT_URL` lives in `.env` and is read through `config('messaging.sms_agent_url')`; if unset, messages are queued but not sent and the operator sees an error.
- **Fire-and-forget**: `startSmsAgent()` calls the link with `&background=1` (5s connect / 10s total timeout). In that mode the agent replies `{"success":true,"message":"Agent started."}` right after validating the org, closes the connection, and keeps sending (`ignore_user_abort(true)` + `set_time_limit(0)`). Without `background=1` (browser/manual run) it returns the full send summary. If the reply isn't that JSON, the compose page shows the reason including the start of the reply text, and it's logged as `SMS agent could not be started` in `storage/logs/laravel.log`.
- **Live server IP allowlist**: on the live server `crontab/allowed_ip.php`'s IP check is enabled (it's commented out in the repo copy). The mikrotik_cloud server's IP must be in that allowlist or every agent call returns HTTP 200 with `Server ip address not allowed "..."` and messages stay queued. Running from the CLI (`php send_queued_sms.php <db>`) bypasses the check — so a working CLI run plus a failing compose-page send points at the allowlist.
- **Queue markers**: a queued row has `processing_id = 0`, `sent_status = 0`, `sms_status = 0`. `sent_status` **defaults to `1`**, so every other `sms_tables` insert (billing SMS, OTPs, `crontab`'s `send_sms()`, etc.) is never picked up by the agent without those writers needing changes. Only `queueSms()` writes `sent_status = 0`.
- **Claiming**: under a per-org MySQL `GET_LOCK`, the agent takes `MAX(processing_id) + 1` and claims a batch of 50 with one atomic `UPDATE ... SET processing_id = :id, date_changed = :now WHERE processing_id = 0 AND sent_status = 0 AND deleted = 0 ORDER BY sms_id LIMIT 50`. The atomic UPDATE is what prevents double-claims; the lock only stops two concurrent runs from picking the same `processing_id` (which would make each read back the other's rows). Never claim with a separate `SELECT` then `UPDATE`.
- **Sending**: each claimed row goes through `send_sms_via_provider()` in `crontab/shared_functions.php` (send-only, no insert), then is updated to `sent_status = 1`, `sms_status` = 1/0, `date_sent` = actual send time. Loops batch by batch until nothing is left.
- **Failures are never re-sent automatically**: rows claimed but still unsent after 10 minutes (`date_changed` = claim time) are marked failed (`sms_status = 0, sent_status = 1`) on the agent's next run for that org; the operator resends by hand. If the org's `send_sms = 0` or no `sms_sender` is configured, the whole queue is marked failed.
- **UI**: `sent_status = 0` shows as a yellow "Queued" badge in the SMS list (`smsDatatable`) and a yellow icon on the dashboard's recent SMS.
- **Not covered**: WhatsApp bulk (`/whatsapp/bulk`) still sends inline. `whatsapp_chats.processing_id`/`sent_status` exist but are unused, and `whatsapp_chats.sent_status` still defaults to `0` — change that default to `1` (and backfill) before building a WhatsApp agent, for the same reason as `sms_tables`.

**Schema changes** (applied directly to the `mikrotik_cloud` dev DB only):
```sql
ALTER TABLE `sms_tables`
  ADD COLUMN `processing_id` INT(11) NOT NULL DEFAULT 0 AFTER `sms_status`,
  ADD COLUMN `sent_status` INT(11) NOT NULL DEFAULT 0 AFTER `processing_id`,
  ADD INDEX `idx_processing_id` (`processing_id`);
ALTER TABLE `sms_tables` ALTER `sent_status` SET DEFAULT 1;
UPDATE `sms_tables` SET `sent_status` = 1 WHERE `processing_id` = 0 AND `sent_status` = 0;

ALTER TABLE `whatsapp_chats`
  ADD COLUMN `processing_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `delivery_status`,
  ADD COLUMN `sent_status` TINYINT(1) NOT NULL DEFAULT 0 AFTER `processing_id`,
  ADD INDEX `idx_processing_id` (`processing_id`);
```

**Cross-system sync needed**: `mikrotik_cloud_manager` must apply these to every existing org DB (and to new org DBs at provisioning). Order matters for `sms_tables`: the backfill `UPDATE` must run with the default change, otherwise the first agent run re-sends that org's entire SMS history. Until applied, bulk SMS from the compose page will fail for those orgs (the `INSERT` sets columns that don't exist).

## Session Progress (last updated 2026-05-16)

WhatsApp module complete and working end-to-end:
- Inbound messages routed to correct org DB; unknown numbers saved to `unknown_wa_chats` in primary DB
- Outbound messages (free-form and template) send successfully
- Real-time polling: active chat messages every 5s, sidebar contacts every 15s
- Client info modal shows full profile including wallet balance
- Variable insert chips in compose area (Name, Phone, Account, Monthly, Wallet, Expiry, Router, Address)
- Template sends only show Meta-approved templates
- Sync result shows only locally matched templates
- Conversation billing fields (`conversation_id`, `billing_category`, `billable`) captured from Meta status webhooks and stored in `whatsapp_chats`
- Delete conversation feature (soft-deletes all messages for a client)

**Possible next steps:**
- Template variable preview in the template editor
- Display raw webhook payloads in the admin UI

## Login OTP Channel Modes

Login OTP has two known-good modes. The system currently runs in **Mode A (default, multi-channel)**, restored on 2026-07-20. Mode B was used temporarily while DigitalOcean was flagging email OTP as spam and WhatsApp OTP was unreliable. Both are documented here so either can be switched to on request without re-deriving the diff.

### Mode A — Default: SMS/Email/WhatsApp, per-org SMS config, per-org `send_sms` enforced (current)

1. `resources/views/login.blade.php` — `send_code` select offers all three options, `EMAILS` is `selected`:
```blade
<option value="SMS">Send SMS</option>
<option selected value="EMAILS">Send Email</option>
<option value="WHATSAPP">Send WhatsApp</option>
```
2. `app/Http/Controllers/Controller.php` — `GlobalSendSMS()` takes no bypass parameter and always enforces the org check:
```php
function GlobalSendSMS($message, $phone_number, $apiKey, $smsSender, $shortcode, $partnerID) {
    if((session()->has("organization") && session("organization")->send_sms == 0)){
        return null;
    }
    ...
}
```
No `getHypbitsSmsOverride()` method exists on `Controller.php`.
3. `app/Http/Controllers/login.php`, admin branch (`processLogin()`, `authority == "admin"`): the `send_sms == 0` check runs and sets `$sms_status = 0`; SMS uses `$this->getSmsSettings()`; `GlobalSendSMS(...)` is called with no trailing bypass argument.
4. `app/Http/Controllers/login.php`, client branch (`processLogin()`, `authority == "client"`): the `send_sms == 0` early-decline block runs and redirects to `/Client-Login` when disabled; SMS uses `$this->getSmsSettings()`; `GlobalSendSMS(...)` is called with no trailing bypass argument.

### Mode B — Hypbits SMS-only override (email/WhatsApp hidden, org `send_sms` bypassed)

**Trigger phrase to switch to this mode:** "force login OTP to SMS-only" or similar. **Trigger phrase to switch back to Mode A:** "revert email and whatsapp on login."

**Why this mode exists:** a fallback for when email OTP is being spam-flagged or WhatsApp OTP is unreliable — routes every org's login OTP through one shared "Hypbits" SMS account instead of each org's own SMS config, bypassing the per-org `send_sms` restriction so it can't block delivery of the fallback channel.

**Changes to apply, file by file:**

1. `resources/views/login.blade.php` — comment out the `EMAILS` and `WHATSAPP` `<option>` tags, move `selected` onto the `SMS` option:
```blade
<option selected value="SMS">Send SMS</option>
{{-- Email OTP temporarily hidden: DigitalOcean is flagging our SMTP sends as spam. Backend flow left intact. --}}
{{-- <option value="EMAILS">Send Email</option> --}}
{{-- WhatsApp OTP temporarily hidden. Backend flow left intact. --}}
{{-- <option value="WHATSAPP">Send WhatsApp</option> --}}
```
2. `app/Http/Controllers/Controller.php` — add a `$bypassOrgSendSmsCheck = false` parameter to `GlobalSendSMS()` and gate the org check on it:
```php
function GlobalSendSMS($message, $phone_number, $apiKey, $smsSender, $shortcode, $partnerID, $bypassOrgSendSmsCheck = false) {
    if (!$bypassOrgSendSmsCheck && (session()->has("organization") && session("organization")->send_sms == 0)) {
        return null;
    }
    ...
}
```
Add a new method directly after `getSmsSettings()`:
```php
function getHypbitsSmsOverride() {
    return [
        'sms_sender'     => env('HYPBITS_SMS_SENDER'),
        'sms_api_key'    => env('HYPBITS_SMS_API_KEY'),
        'sms_partner_id' => env('HYPBITS_SMS_PARTNER_ID'),
        'sms_shortcode'  => env('HYPBITS_SMS_SHORTCODE'),
    ];
}
```
3. `app/Http/Controllers/login.php`, admin branch (`processLogin()`, `authority == "admin"`): comment out the `send_sms == 0` block so `$sms_status` always stays `1`; swap `$this->getSmsSettings()` to `$this->getHypbitsSmsOverride()`; add a trailing `true` bypass argument to the `GlobalSendSMS(...)` call.
4. `app/Http/Controllers/login.php`, client branch (`processLogin()`, `authority == "client"`): comment out the `send_sms == 0` early-decline block; swap `$this->getSmsSettings()` to `$this->getHypbitsSmsOverride()`; add a trailing `true` bypass argument to the `GlobalSendSMS(...)` call.
5. `.env` — requires `HYPBITS_SMS_SENDER`, `HYPBITS_SMS_API_KEY`, `HYPBITS_SMS_PARTNER_ID`, `HYPBITS_SMS_SHORTCODE` to be set.

**Note:** `GlobalSendSMS()`'s own independent `send_sms == 0` check must be bypassed too (not just the one in `login.php`), otherwise orgs with SMS disabled silently get no OTP even though the shared Hypbits account is supposed to ignore that restriction. That's what the `$bypassOrgSendSmsCheck` parameter is for — pass `true` only from the two login OTP call sites, never from other `GlobalSendSMS()` callers.

`resources/views/clients/client-login.blade.php` is unaffected by either mode — its `send_code` selector is already hidden/SMS-only independent of this toggle.

## Git Commit Guidelines

- Commit messages must be plain text describing the change only.
- Do not include "Co-Authored-By", "Generated by Claude", or any mention of AI assistance.
- Do not add trailers or sign-off lines of any kind.
- Keep the message concise: a short subject line, and if needed a brief body explaining what changed and why.

## Hotspot module — golden rules

The hotspot billing module is built on branch `feature/hotspot` in all three repos (baseline tag `pre-hotspot-baseline`). Plan, discovery, impact/safety notes and the GNS3 lab guide live in `docs/hotspot/` (start with `PLAN.md`, then `00-discovery.md`, `00-impact-and-safety.md`, `00-gns3-lab.md`). The do-not-touch list and the baseline regression checklist are in `00-impact-and-safety.md`. Router testing uses the GNS3 lab (virtual MikroTik CHR routers), not live routers.

1. Never run schema changes, seeders or router commands against production. Use local dev and staging only.
2. Never change or drop existing tables or columns; only add.
3. Hotspot is **off by default** for every org. Every hotspot page, route and crontab script checks the org's hotspot flag first and skips orgs without it.
4. Hotspot crontab scripts must never slow down or share loops with existing billing scripts (`check_all_clients.php`, `activate_deactivate_clients.php`, etc.). They are separate files.
5. **Router safety:** day-to-day testing uses GNS3 CHR routers; live routers are touched only for the final pre-pilot check.
   - Router writes from development/staging are allowed only on allow-listed routers (the GNS3 lab routers, and the test account's routers for the final check). Enforce this with a `.env` allowlist (`HOTSPOT_ROUTER_WRITE_ALLOWLIST=<db>:<router_id>,...`) that is checked before every write when `APP_ENV` is not `production`.
   - Hotspot code only creates or edits objects it owns (names prefixed `hypbits-hs-`, or tagged with a `comment=hypbits-hotspot`). It never edits or removes PPPoE secrets, PPPoE profiles, queues, firewall rules or interfaces it did not create.
   - Disabling hotspot never deletes anything on the router without an explicit confirmation.
6. Every router write uses a timeout, up to 3 retries, and a per-router lock (MySQL `GET_LOCK('hs_router_<db>_<router_id>')`) so two scripts never write to the same router at once. Every write is logged to `hotspot_router_activity`.
7. Every money action is idempotent (safe to run twice) and recorded: one M-Pesa receipt = one ticket; one org + billing period = one charge.
8. M-Pesa secrets are never logged, echoed in responses or copied anywhere new. Raw callback payloads are stored without credentials.
9. Reuse what exists: the Chameleon layout and `<x-button>`/`<x-button-link>` components, `routeros_api.php`, `MpesaService.php`, the SMS sending path, `switchDb()`/`change_db()` for tenant resolution.
10. Follow the org DB conventions: `deleted` flag instead of soft-delete timestamps; dates stored as `YmdHis` strings, written with `now()->format('YmdHis')` (Africa/Nairobi).
11. Re-run the Stage 0 baseline regression checklist at the end of every stage. Keep `docs/hotspot/` up to date.
12. Hotspot entry points without a session (captive portal, M-Pesa callback, crontab agents) resolve the org explicitly and fail closed — never rely on `change_db()` falling back to the default `mysql2` DB.
13. A hotspot privilege missing from an admin's `priviledges` JSON is allowed **only for owners** (admins whose "Account and Profile" option has view on and read-only off); everyone else is denied until granted, and an explicit entry always wins. Hotspot routes check the privilege server-side with the `hotspotCan:<option>` middleware (today's `showOption()` treats a missing option as visible, and ISP routes are not privilege-checked). Logic: `App\Classes\HotspotAccess`.

### Hotspot schema

New tables and columns are listed in `docs/hotspot/tables.md` (SQL in `docs/hotspot/sql/`). Org DBs get them from `mikrotik_cloud_manager` (`php artisan hotspot:apply-org-schema` for existing orgs, `imports.sql` for new ones); the manager keeps an identical copy of `org_db.sql` in `database/hotspot/`. Any change to the hotspot org SQL must be made in both copies and in the hotspot block of the manager's `imports.sql`.

### Hotspot mode switch & privileges (Stage 2)

- **Mode** comes from the URL: `/hotspot/*` = hotspot, everything else = ISP billing. `App\Classes\HotspotAccess::menuState()` (called at the top of `components/menu.blade.php`) decides the sidebar and the dashboard selector in the top bar (a dropdown: ISP Billing / Hotspot, ids `billing_mode_isp` / `billing_mode_hotspot`). `<x-menu>` is an anonymous component — `app/View/Components/menu.php` is never loaded (lower-case file name), so don't put logic there.
- **Org flag**: `org_hotspot_settings.hotspot_enabled` in the manager DB, cached 5 minutes (`HotspotAccess::enabledForOrg()`, cache key `hotspot_enabled_<org_id>`). No row = off.
- **Privileges**: options listed in `config/hotspot.php` (`privileges`), stored in the same `priviledges` JSON. Missing option = owners only (golden rule 13). Add/edit admin screens show the hotspot rows through `resources/views/hotspot/partials/admin-privileges.blade.php` when the org has hotspot.
- **Adding a hotspot page**: put the route inside the `Route::prefix("hotspot")->name("hotspot.")` group at the end of `routes/web.php` (group middleware: `checkAccount`, `validated`, `hotspotEnabled`), add `->middleware("hotspotCan:<option>")`, and add a `menu` entry in `config/hotspot.php` with the route name — the sidebar shows it once the route exists. Views go in `resources/views/hotspot/` and use `<x-menu active="hotspot_...">`.
- **403 page**: `resources/views/hotspot/disabled.blade.php` (`reason` = `disabled` or `privilege`).
- **Tests**: `tests/Feature/HotspotModeTest.php` runs each test in its own process because the menu and admin views declare global PHP functions (a page can render only once per process).

### Hotspot billing & M-Pesa test (Stage 3)

Hotspot is turned on per org, and its billing set, in `mikrotik_cloud_manager` (see its CLAUDE.md "Hotspot billing"). This app only reads `org_hotspot_settings.hotspot_enabled`. The hotspot dashboard has "Test M-Pesa credentials" (`POST /hotspot/mpesa/test`, privilege Hotspot Payments, `App\Http\Controllers\Hotspot\Mpesa`), which uses `MpesaService::checkCredentials()` to request a Daraja token with the org's `settings` keys; it reports missing key names and never returns key values or the token.

### Hotspot routers (Stage 4)

- **Pages:** Hotspot → Routers (`/hotspot/routers`, privilege Hotspot Routers, `App\Http\Controllers\Hotspot\Routers`). The list loads from the DB and fetches each router's live status separately (`/hotspot/routers/{id}/status`) so an offline router never blocks it. The router page has detection, the setup wizard (wired port and/or Wi-Fi, "Preview commands" dry run), Adopt / Delete for other hotspot servers, Stop managing, Remove from router, and the activity log (collapsed by default).
- **Reads** go straight to the router with short timeouts through `App\Classes\HotspotRouterService` (SSTP lookup like `Router_Cloud::checkActive()`), using `App\Classes\HotspotRouterApi`, a `routeros_api` subclass whose `read()` waits for the whole reply (the shared client stops early on slow routers, risk R19). Use `HotspotRouterApi` in hotspot code; don't change `routeros_api.php`.
- **Writes** never happen in this app: `App\Classes\HotspotAgent::queue()` checks `HOTSPOT_ROUTER_WRITE_ALLOWLIST` (when `APP_ENV` is not production), stores the task as a `hotspot_router_activity` row (`status=queued`, params in `message`) and calls `HOTSPOT_AGENT_URL?db=&activity=&background=1` (crontab `hotspot_router_agent.php`).
- **Owned objects only:** everything Hypbits creates on a router is named `hypbits-hs*` or commented `hypbits-hotspot`; Remove deletes exactly those. Interfaces in use (WAN, `ether1`, VPN, PPPoE, any static address, bridge ports, bridges with ports) are never offered.
- `.env` keys: `HOTSPOT_AGENT_URL`, `HOTSPOT_PORTAL_HOST` (walled garden), `HOTSPOT_ROUTER_WRITE_ALLOWLIST`, `HOTSPOT_LOGIN_HTTPS` (false while testing, **true on the live server**).
- Confirmations use the app's Bootstrap modals, never `confirm()`/`alert()`.
- Tests: `tests/Feature/HotspotRoutersTest.php` needs the GNS3 lab (router 23 online); only reads reach a router, the agent call is faked.

### Hotspot plans (Stage 5)

Hotspot → Plans (`App\Http\Controllers\Hotspot\Plans`, privilege Hotspot Plans): the ISP's packages in `hotspot_plans`. Helpers in `App\Classes\HotspotPlans` (unit conversions, display text, `forPortal()` = active plans in order). Every save/toggle/delete queues the agent task `sync_profiles` for each managed router (`remote_routers.hotspot_enabled = 1`); the plan is the router user profile `hypbits-hs-<plan_id>` (`rate-limit` = `upload/download`, `shared-users`). A router shows a plan as in sync when its `hotspot_last_sync` is newer than the plan's `date_changed`. Tests: `tests/Feature/HotspotPlansTest.php`.

### Hotspot tickets (Stage 6)

A ticket is a `hotspot_tickets` row plus the router hotspot user `CODE`/`CODE` on the plan profile `hypbits-hs-<plan_id>`. Create with `App\Classes\HotspotTickets::issue()` then `HotspotTickets::provision($router_id, $ids)` (queues the agent task `provision_tickets`); never write router users from the app. Statuses: `pending` (not used yet) → `active` (first login seen) → `used` / `expired`; `failed` = could not be put on the router after 10 tries. Hotspot → Tickets (`App\Http\Controllers\Hotspot\Tickets`, privilege Hotspot Tickets): list with filters and counts, manual (cash) tickets, ticket page (usage readings, resend SMS, end now → agent task `revoke_ticket`). Ticket SMS go through the org's own SMS settings only when `organizations.send_sms = 1` and are logged in `sms_tables` (`sms_type = 1`). Activation, usage, expiry and provisioning retries are done every minute by `crontab/hotspot_tickets.php`. Tests: `tests/Feature/HotspotTicketsTest.php`.

### Hotspot captive portal & branding (Stage 7)

- **Public portal** `/hs/{hotspot_key}` (routes `hs.*`, outside the auth group, CSRF-exempt `hs/*`, rate limiters `hotspot-portal` / `hotspot-code` in `RouteServiceProvider`): `Hotspot\Portal` + `App\Classes\HotspotPortal::resolve()` (key → manager `hotspot_router_keys` → org with hotspot on → `change_db(<org db>)` → managed router; anything missing = 404, never the default DB). Pages: plans + ticket code (`index`, `code`), "You're online" (`online`), router files (`routerFile`), `ping`. Views `resources/views/hotspot/portal/*` are the one place outside Chameleon (plain HTML/CSS/JS, no `<x-button>`), kept small for phones not yet online; load nothing from other hosts (only the portal host is in the walled garden).
- **Router files** `resources/views/hotspot/router-files/*.blade.php` (list in `HotspotPortal::ROUTER_FILES`): tiny pages with RouterOS `$(var)` placeholders that forward to the portal; `login.html` has the offline fallback form. Installed by the agent task `login_files` ("Update login page" on the router page; also at the end of setup). Re-run it on every router when `HOTSPOT_PORTAL_URL`/`APP_URL` changes.
- **Branding**: Hotspot → Branding (`Hotspot\Branding`, privilege Hotspot Branding), `hotspot_login_pages` (default row `router_id` NULL + per-router rows, empty field = default); logos saved as small PNGs in `public/hotspot/logos/` (gitignored) by `HotspotPortal::smallLogo()`.
- `.env`: `HOTSPOT_PORTAL_URL` (default `APP_URL`). Tests: `tests/Feature/HotspotPortalTest.php`.
