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

### Test Database

Tests run against a real MySQL database (not SQLite in-memory) — see `phpunit.xml`. Ensure the test database is available and `DB_DATABASE` is set before running tests.
