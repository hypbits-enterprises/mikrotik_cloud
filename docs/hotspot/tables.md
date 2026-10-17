# Hotspot tables

Every table and column the hotspot module adds, and which database it lives in. SQL:
`docs/hotspot/sql/org_db.sql` (org DBs) and `docs/hotspot/sql/manager_db.sql` (manager DB).
Conventions: dates are `VARCHAR(14)` `YmdHis` (Africa/Nairobi), `deleted` flag instead of soft
deletes, money `DECIMAL(12,2)`, rates `DECIMAL(10,4)`, enum-like values as `VARCHAR` (allowed values
in the column `COMMENT`).

## How the schema is applied

| Where | How |
|---|---|
| Manager DB `mikrotik_cloud_manager` | Run `docs/hotspot/sql/manager_db.sql` once per environment (safe to re-run). |
| Existing org DBs | In `mikrotik_cloud_manager`: `php artisan hotspot:apply-org-schema [--org=<id>] [--dry-run]`. Checks `information_schema` before each table, column and index, so it can run any number of times. Reads `database/hotspot/org_db.sql` (identical copy of the org SQL). |
| New org DBs | Appended to `mikrotik_cloud_manager/public/db_imports/imports.sql`, so `Organization::import_database()` creates them at provisioning. |

When the org SQL changes: edit `docs/hotspot/sql/org_db.sql`, copy it to the manager's
`database/hotspot/org_db.sql`, update the hotspot block at the end of `imports.sql`, then run the
command. Keep one statement per semicolon and no semicolons inside statements or comments
(`import_database()` splits the file on `;`).

Applied to dev on 2026-10-17: `mikrotik_cloud_manager` and org DB `mikrotik_cloud`.

## Org DB (each ISP)

| Table / column | Purpose | Written by |
|---|---|---|
| `hotspot_plans` | Plans sold on the portal: `price` (KES), `duration_minutes`, `validity_type` (`paused` = counts connected time, `elapsed` = clock runs from first login), `upload_speed`/`download_speed` (`2M`, `512k`, NULL = unlimited), `rate_limit` (RouterOS `rx/tx` from the router's side = `upload/download`, NULL = unlimited), `data_limit_mb` (NULL = unlimited), `shared_users` (devices per ticket), `is_active` (shown on the portal), `sort_order`, `deleted`. Each plan = router user profile `hypbits-hs-<plan_id>` | Stage 5 |
| `hotspot_tickets` | One row per ticket on a router. `ticket_code` unique (a paid ticket's code = its payment's `account_code`); `payment_id` unique (one payment = one ticket, NULL for manual); `source` `stk`\|`paybill`\|`manual`; `amount` = price at sale; `status` `pending`\|`active`\|`used`\|`expired`\|`failed`; `provision_status`/`provision_attempts`/`last_error` for creating the router user while the router is offline; usage counters | Stage 6 (manual), Stage 8 (STK) |
| `hotspot_payments` | Purchases started on the portal. `account_code` (unique, added Stage 8) = STK AccountReference / Paybill account number = the ticket code; `method` `stk`\|`paybill`\|`assigned`\|`claimed`; `public_token` = secret the portal polls with; `last_query_at` = last Daraja STK query. `checkout_request_id` and `mpesa_receipt` unique (idempotent callbacks); `status` `pending`\|`paid`\|`failed`\|`cancelled`\|`expired`; `raw_callback` without credentials | Stage 8 |
| `hotspot_transactions` | Hotspot earnings: one row per M-Pesa transaction for hotspot (never in `transaction_tables`). `trans_id` unique (`STK-<CheckoutRequestID>` until the receipt arrives with the C2B confirmation); `source` `stk`\|`billing_c2b` (shared Paybill, via `/Transact`)\|`hotspot_c2b`\|`assigned`; `status` `matched`\|`unmatched`\|`review`\|`rejected`; `phone` as received (Safaricom hashes some C2B numbers, SHA-256) | Stage 8 |
| `hotspot_customers` | Buyers keyed by `phone` (unique): first seen, last purchase, purchase count, total spent. Not linked to `client_tables` | Stage 8 |
| `hotspot_login_pages` | Portal branding. `router_id` NULL = org default, one row per router overrides it field by field (empty = default). `logo_path` = small PNG under `public/hotspot/logos/` (no row/logo: the org's profile logo, shrunk). `redirect_url` (added Stage 7) = page after login instead of the "You're online" page | Stage 7 |
| `hotspot_usage_snapshots` | Usage readings per active ticket every 5 minutes (`bytes_in` = customer upload, `bytes_out` = download, `uptime_seconds` = connected time) | Stage 6 (`crontab/hotspot_tickets.php`) |
| `hotspot_router_activity` | One row per router task, also the agent's task queue. `action`: `setup`, `adopt`, `remove`, `delete_server`, `sync_profiles`, `provision_tickets`, `revoke_ticket`, `login_files` (agent), `tickets` (minute job, only when it changed something on the router) or `stop` (app only). `status`: `queued` → `running` → `success`\|`failed`. `message` is JSON `{"params": {...task parameters...}, "log": [{time, step, result, detail}, ...]}`; `params.dry_run` = only list the changes. `triggered_by` = `admin:<id>` | Stage 4 on |
| `remote_routers.hotspot_enabled` | Router managed by the hotspot module (default 0) | Stage 4 |
| `remote_routers.hotspot_key` | Public key in the portal URL `/hs/{hotspot_key}`, unique | Stage 4 |
| `remote_routers.hotspot_server_name`, `hotspot_interface`, `hotspot_dns_name` | Hotspot server chosen on the router | Stage 4 |
| `remote_routers.hotspot_last_sync` | Last successful profile/login-file sync | Stage 4–7 |

## Manager DB (`mikrotik_cloud_manager`)

| Table | Purpose | Written by |
|---|---|---|
| `org_hotspot_settings` | Current settings per org: `hotspot_enabled` (default 0), `billing_model` (`percentage`\|`flat_monthly`\|`per_router`\|`per_ticket`), `rate`, `manual_ticket_rate`, `minimum_charge`, `effective_from`, plus `org_key` (random public key for callback URLs) and `callback_token` (secret checked on each M-Pesa callback) | Stage 3 |
| `org_hotspot_settings_history` | Every change to an org's settings with `effective_from`. The row with the greatest `effective_from` on or before a period's start (ties: highest `id`) is what bills that period. `hotspot_enabled` here is informational | Stage 3 (written), Stage 11 (read) |
| `hotspot_billing_defaults` | Global defaults copied to an org when hotspot is first enabled. One row, seeded with `percentage` and rates 0 until set in Stage 3 | Stage 3 |
| `org_hotspot_statements` | Hotspot charge per org per billing period (sales, ticket counts, routers, model, rates, `amount_due`, `status` `estimate`\|`charged`\|`unpaid`\|`waived`). Unique on (`organization_id`, `period_start`) so renewal can't charge a period twice | Stage 11 |
| `hotspot_router_keys` | Global index `hotspot_key` → (`organization_id`, `router_id`) so the session-less portal finds the org in one lookup | Stage 4 |

No Eloquent models yet: each stage adds the models it uses.
