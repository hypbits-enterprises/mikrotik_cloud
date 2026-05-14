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

## Session Progress (last updated 2026-05-14)

WhatsApp module is complete and confirmed working end-to-end:
- Inbound messages from Meta are received, routed to the correct org DB, and saved
- Outbound messages (free-form and template) send successfully
- Client info modal in chat header shows formatted client details
- SMS/WhatsApp channel tabs in compose view working
- adminsms sms.js crash fixed (hidden nav stubs); SMS graph rendering fixed
- App timezone set to `Africa/Nairobi` — stored times now match EAT

**Possible next steps:**
- Display raw webhook payloads in the admin UI (currently only in `laravel.log`)
- WhatsApp chats: real-time updates via polling or WebSocket instead of full-page reload
- Template variable preview in the template editor

## Git Commit Guidelines

- Commit messages must be plain text describing the change only.
- Do not include "Co-Authored-By", "Generated by Claude", or any mention of AI assistance.
- Do not add trailers or sign-off lines of any kind.
- Keep the message concise: a short subject line, and if needed a brief body explaining what changed and why.
