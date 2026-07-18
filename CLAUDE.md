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

Controllers do not extend a resource base class — `Controller.php` (the base) provides shared utility methods like `getPPPSecrets($router_id)` and `convertBits($bits, $type)`.

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

## TEMPORARY: Login OTP forced to SMS-only (delete this section once reverted)

**Trigger phrase:** when the user says "revert email and whatsapp on login," undo every change listed below, then delete this entire section from CLAUDE.md.

**Why this exists:** Email OTP is being flagged as spam by DigitalOcean and WhatsApp OTP is not yet reliable, so login OTP was temporarily forced to SMS-only, routed through one shared "Hypbits" SMS account instead of each org's own SMS config, bypassing the per-org `send_sms` restriction. This is intended to be reverted once WhatsApp and email are sorted out.

**Changes to undo, file by file:**

1. `resources/views/login.blade.php` — the `send_code` select: uncomment the `EMAILS` and `WHATSAPP` `<option>` tags, and restore `selected` on the `EMAILS` option (remove `selected` from `SMS`).
2. `app/Http/Controllers/Controller.php` — delete the `getHypbitsSmsOverride()` method (added directly after `getSmsSettings()`). Also revert `GlobalSendSMS()`: remove the `$bypassOrgSendSmsCheck = false` parameter and restore the unconditional `if((session()->has("organization") && session("organization")->send_sms == 0)){ return null; }` check.
3. `app/Http/Controllers/login.php`, admin login branch (`processLogin()`, `authority == "admin"`):
   - Uncomment the `if($organization_details[0]->send_sms == 0){ ... $sms_status = 0; }` block.
   - Change `$this->getHypbitsSmsOverride()` back to `$this->getSmsSettings()`.
   - Remove the trailing `true` bypass argument from the `GlobalSendSMS(...)` call.
4. `app/Http/Controllers/login.php`, client login branch (`processLogin()`, `authority == "client"`):
   - Uncomment the `if($organization_data->send_sms == 0){ ... return redirect("/Client-Login"); }` early-decline block.
   - Change `$this->getHypbitsSmsOverride()` back to `$this->getSmsSettings()`.
   - Remove the trailing `true` bypass argument from the `GlobalSendSMS(...)` call.
5. `.env` — the `HYPBITS_SMS_*` keys can be left in place (unused once the code above is reverted) or removed; not required for the revert to work.

**Note:** `GlobalSendSMS()` had its own independent `session("organization")->send_sms == 0` check (separate from the one in `login.php`) that was missed in the initial temporary change — it silently blocked login OTP for orgs with SMS disabled even though the shared Hypbits account was supposed to bypass that restriction. Fixed by adding the `$bypassOrgSendSmsCheck` parameter, passed as `true` only from the two login OTP call sites.

Note: `resources/views/clients/client-login.blade.php` was not changed (its `send_code` selector was already hidden/SMS-only before this temporary change) — nothing to revert there.

## Git Commit Guidelines

- Commit messages must be plain text describing the change only.
- Do not include "Co-Authored-By", "Generated by Claude", or any mention of AI assistance.
- Do not add trailers or sign-off lines of any kind.
- Keep the message concise: a short subject line, and if needed a brief body explaining what changed and why.
