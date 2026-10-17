# Stage 0 — Discovery

How the existing system works in the places the hotspot module will sit next to. Read-only
study done on 2026-10-17 against the `pre-hotspot-baseline` tags:

| Repo | Baseline commit |
|---|---|
| `mikrotik_cloud` | `c74d786` Split regions into regions and subregions |
| `mikrotik_cloud_manager` | `a7d6fc8` Document pending org DB schema changes from mikrotik_cloud |
| `crontab` | `94d0ce7` Reply early from send_queued_sms.php when called in background mode |

Paths without a repo prefix are in `mikrotik_cloud`. `manager:` = `mikrotik_cloud_manager`,
`crontab:` = `/opt/lampp/htdocs/crontab`.

## 1. Environment

| | Dev (this machine) |
|---|---|
| Laravel | 8.83.0 (`composer.json`: php `^7.3\|^8.0`) |
| PHP | 8.1.2 CLI (system) used by `php artisan`; 8.2.12 in LAMPP (`/opt/lampp/bin/php`) serves `crontab` |
| DB | MariaDB 10.4.32 (LAMPP) |
| Timezone | `Africa/Nairobi` (`config/app.php`, and `date_default_timezone_set` in crontab scripts) |
| Cron | Nothing scheduled locally (`crontab -l` is empty). Production runs the crontab scripts as web requests to LAMPP/Apache (`crontab:CLAUDE.md`); every script includes `crontab:allowed_ip.php`, whose IP check is on in production and commented out in the repo. |
| Deploy | Manual file upload to the live server (no git deploy yet — PDF Stage 14). |

## 2. Tenant (org DB) resolution

**Two connections** (`config/database.php:66`): `mysql` = manager DB `mikrotik_cloud_manager`;
`mysql2` = an org DB, defaulting to `DB_DATABASE_CLIENT` (`mikrotik_cloud` in dev).

**Per request (web):** login puts the org on the session — `login::processLogin()`
(`app/Http/Controllers/login.php:17`) sets `database_name`, `organization_id`, `organization`
(the whole `organizations` row), `Userid`, `auth` (`admin`|`client`) and `priviledges`
(`login.php:43-55`, client branch `:249-256`). Controllers then call
`(new login())->change_db()` (`login.php:567`), which reads `session('database_name')`,
`Config::set('database.connections.mysql2.database', …)`, `DB::purge` + `reconnect`.
Variants: `Clients::change_db()` (`Clients.php:558`), `WhatsApp::switchDb()`
(`WhatsApp.php:1097`, purge only).

- `change_db()` **returns** `redirect("/Login")` when there is no session DB, but callers ignore
  the return value — the request continues on the **default** `mysql2` DB. Hotspot code must
  never rely on that fallback; it must resolve the org explicitly and fail closed.
- `change_db($database_name)` with an explicit name is how session-less entry points switch DB
  (e.g. the M-Pesa C2B handler, §4). The hotspot portal and callback will do the same.

**Session-less org lookup patterns already in use:**
- C2B M-Pesa: `organizations.BusinessShortCode` → `organization_database` (`Transaction.php:710`).
- WhatsApp webhook: phone / `wa_message_id` searched across all org DBs (`WhatsApp.php`).

**Crontab:** each script connects to the manager DB (`crontab:db_connect.php`,
`db_credential.php`), selects from `organizations` and opens each `organization_database` in turn.
Scripts that loop orgs: `check_all_clients.php`, `activate_deactivate_clients.php`,
`freeze_clients.php`, `check_client_status.php`, `remindpayment.php`, `import_config_router.php`,
`organization/*.php`; `send_queued_sms.php` takes one org via `?db=`.

## 3. Org provisioning (manager)

`manager:Organization::process_new()` (`Organization.php:381`) → `import_database()`
(`Organization.php:2518`) with `public/db_imports/imports.sql` and `$dbname = <account number>`.
`import_database()`:
- runs **`DROP DATABASE IF EXISTS`** then `CREATE DATABASE` (destructive by design — it is only
  called for a new account number, after a duplicate check);
- splits the file with `explode(";", …)` and runs each piece — so SQL added to `imports.sql`
  **must not contain `;` inside strings, comments, triggers or procedures**.

`imports.sql` has 25 `CREATE TABLE`s plus seed `INSERT`s into `settings` (including
`sstp_server`, line 180). Note: `imports.sql` already lags the dev DB for some changes
(e.g. the `sms_tables.processing_id/sent_status` columns are documented as pending in
`manager:CLAUDE.md`).

`organizations` columns (manager DB): `organization_id, organization_name, organization_address,
organization_main_contact, organization_database, organization_email, organization_status,
organization_logo, BusinessShortCode, date_joined, account_no, package_name, wallet, freeze_date,
freeze_status, freeze_until, discount_type, discount_amount, lenience, payment_description,
free_trial_period, monthly_payment, expiry_date, payment_status, send_sms, preferred_channel`.
There is no public org key — Stage 8's `{org_key}` needs one (e.g. `account_no`, or a new random
key in `org_hotspot_settings`).

## 4. PPPoE M-Pesa payment flow

**Credentials:** org `settings` keywords `consumer_key`, `consumer_secret`, `passkey`, `paybill`,
read in `MpesaService::__construct()` (`app/Classes/MpesaService.php:17`). The constructor
calls `change_db()` itself, i.e. it **only works with a session** (no way to pass a DB name).

**STK push:** `POST /Payment/stkpush` (`routes/web.php:304`, `clientValidate`) →
`Transaction::initiate_stk()` (`Transaction.php:1100`) → `MpesaService::stkPush()` with
`AccountReference = <client account>`; on error it retries with Daraja `v2`.
- `CallBackURL` is hard-coded to `https://mydomain.com/path` (`MpesaService.php:30`): **STK
  callbacks are not used at all.**
- No OAuth token caching — a token is requested on every push. Daraja URL is hard-coded to
  production (`api.safaricom.co.ke`).

**Confirmation = C2B:** since the STK callback goes nowhere, PPPoE STK payments can only be
credited through the C2B confirmation, i.e. Safaricom posts Paybill payments — STK ones included —
to the C2B confirmation URL (inferred from the code; confirm on one live STK payment) registered by `mpesa_api::register_url()` (`routes/web.php:437`), which is
`POST /Transact` → `Transaction::mpesaTransactions()` (`Transaction.php:705`, no middleware):
1. org from `organizations.BusinessShortCode`, then `change_db(<org db>)`;
2. client from `client_tables.client_account = BillRefNumber`;
3. **known account:** add `TransAmount` to `wallet_amount`, send "payment received" (or "below
   minimum") via `dispatchAutomationMessage()`, referral commission;
4. **unknown account:** send the "payment to wrong account" message to the payer;
5. insert into `transaction_tables` (`Transaction.php:958`).
Activation after payment is not done here — `crontab:check_all_clients.php` renews from the wallet.

Observed: no duplicate-`TransID` check in the handler; `BillRefNumber` is interpolated directly
into SQL (`Transaction.php:739`).

## 5. Cloud routers (SSTP)

`settings.sstp_server` (org DB) = JSON `{username, password, ip_address, port}` — API login of the
Hypbits SSTP server. Read by `Router_Cloud::getSSTPAddress()` (`Router_Cloud.php:1134`); seeded
from `imports.sql:180` at provisioning; **no screen in any system edits it**.

- **Add router** — `save_cloud_router()` (`Router_Cloud.php:836`): random 9-char SSTP username,
  18-char password; adds `/ppp/secret` (`service=sstp`, `profile=SSTP_PROFILE`) on the SSTP
  server; inserts `remote_routers` (`router_name, sstp_username, sstp_password, router_location,
  router_coordinates, winbox_port, api_port, date_changed, last_seen, activated, deleted`).
- **Router config script** — shown on the profile page (`resources/views/router/infor.blade.php:578-620`):
  SSTP client `SYSTEM_SSTP_TWO` to `ip_address`, routes to `192.168.252-254.0/24` via
  `192.168.254.1`, input-accept on the SSTP interface, API on `api_port`, a `SYSTEM_FULL` group and
  an API user = SSTP username/password. Also written to `public/scripts/add_mikrotik_cloud_<id>.rsc`.
- **Reach a router** — `checkActive()` (`Router_Cloud.php:1104`): log in to the SSTP server API,
  read `/ppp/active`, take the `address` of the entry whose `name` = `sstp_username` and
  `service` = `sstp`. Then connect **directly** to that tunnel IP on `api_port` with the router's
  SSTP credentials (`connect_router()` `:922`, `view_router_details()` `:990`, `reboot()` `:1047`).
  So the app server must have a route into `192.168.254.0/24` (production: to confirm).
- **API client** — `app/Classes/routeros_api.php`: `attempts=3`, `delay=1s`, `timeout=3s`
  (`:28-32`). An offline router costs ~12 s per `connect()`, and pages call `checkActive()` + a
  router connect in sequence. `crontab:routeros_api.php` is a different copy (`delay=3s`).

## 6. Privileges & sidebar

- Stored in the **manager DB** `admin_tables.priviledges` as JSON:
  `[{"option":"My Clients","view":true,"readonly":false,"expiry":"indefinate_expiry","expiry_date":""}, …]`.
  Options today: My Clients, Transactions, Expenses, My Routers, SMS, Account and Profile,
  Quick Register, Clients Issues (default set in `resources/views/addadmin.blade.php:125`).
- Sidebar: `resources/views/components/menu.blade.php` (`<x-menu active="…">`, used by 44 views).
  Its local `showOption()` (`menu.blade.php:194`) returns CSS classes (`""`, `d-none`,
  `disabled`), honours per-option expiry and `session('inactive_menu')`.
- **`showOption()` returns "visible" when an option is missing from the JSON** (menu, and
  `CheckValidatedClients::showOption()` `:42`, `admin.php:14`, `Clients.php:3496`). New hotspot
  options would therefore be shown to every existing admin unless the hotspot check defaults to
  deny.
- Privileges are **UI-only**: routes are not privilege-checked server-side.

**Middleware** (`app/Http/Kernel.php:68-70`):
- `checkAccount` — switches DB, forces password reset, loads the org, sets
  `block_edits`/`inactive_menu` when the org is near expiry and the wallet can't cover it.
- `validated` (`CheckValidatedClients`) — admin only (403 otherwise), loads unvalidated clients.
- `clientValidate` — client portal session guard.
- About 150 routes in `routes/web.php` have no middleware (including `/Transact`, `/activate/…`,
  `/changeWallet`, `/new_cloud_router`, `/Routers/Delete/{id}`, `/BillingSms/*`); some are meant
  to be public (callbacks, activation links), others rely on controller-level checks.

## 7. SMS path

- Settings: `Controller::getSmsSettings()` (`Controller.php:630`) → sender, api key, partner id,
  shortcode from org `settings`.
- Send now: `Controller::GlobalSendSMS()` (`Controller.php:492`) — returns `null` when
  `session('organization')->send_sms == 0`, else dispatches to the provider (HostPinnacle,
  Afrokatt, Celcom, TalkSasa, BlessedTexts). **With no session the `send_sms` check is skipped**,
  so session-less callers (hotspot callback) must check `organizations.send_sms` themselves.
- Log: callers insert an `sms_tables` row themselves.
- Bulk: `Sms::queueSms()` (`Sms.php:543`) + `Sms::startSmsAgent()` (`:559`) →
  `crontab:send_queued_sms.php?db=<db>&background=1` (fire-and-forget; see CLAUDE.md "Bulk SMS
  Sending Agent"). This is the pattern for the hotspot router agent.

## 8. Org renewal & freeze (crontab)

`crontab:organization/activate_organizations.php`, for every org with `payment_status = 1`
(`:39`):
- if `now > expiry_date` (`:51`): `total_cost = getMonthlyPayment()` (`:114`, active clients in
  the last 3 months × `per_head_cost` 20, in batches of 50, minus free clients) and
  `wa_cost = getWaCost()` (`:150`);
- if `wallet >= total_cost && wallet > 0 && total_cost > 0`: `wallet -= total_cost`,
  `expiry_date = now + 1 month`, `organization_status = 1`, "account extended" message;
- else: `organization_status = 0` and an "account deactivated" message (first time only).
- not expired and status 0 → set back to 1.

Confirmed for PLAN.md Stage 11:
- **`wa_cost` is not deducted** — it is only passed to `message_content()` for the message text.
- The new period starts at the **renewal run time** (`now + 1 month`), not at the old
  `expiry_date`, so periods have gaps when an org renews late. Hotspot statements should run from
  the previous statement's `period_end` to the renewal time, so no sales fall in a gap.
- `total_cost > 0` is required to renew: an org with no billable PPPoE clients is deactivated
  instead of renewed. A hotspot-only org would hit this — Stage 11 must handle it.
- Freeze of individual clients is separate (`crontab:freeze_clients.php`); org freeze columns on
  `organizations` (`freeze_*`) are not used by this script.

## 9. Local lab

The GNS3 lab used for all router testing is documented in `00-gns3-lab.md`.
