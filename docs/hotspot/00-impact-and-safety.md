# Stage 0 — Impact map, do-not-touch list, baseline checklist, risks

Companion to `00-discovery.md` (which has the file:line detail). Re-run section 3 at the end of
every stage.

## 1. Impact map

What each stage reads (R), adds (A) or changes (C) in existing code/data, and how it stays
backward-compatible. Anything not listed is not touched.

| Existing thing | Stages | Use | Backward-compatibility rule |
|---|---|---|---|
| Org DB `remote_routers` | 1, 4, 6, 10 | A columns (`hotspot_enabled`, `hotspot_key`, …) | Nullable / defaulted columns only; existing `Router_Cloud.php` queries use explicit columns or `SELECT *` and ignore extras. |
| Org DB `settings` (M-Pesa keys, SMS keys, `sstp_server`) | 4, 6, 8 | R | Read only. No new keywords required. |
| Manager `organizations` | 2, 3, 8, 11 | R | Read only (`send_sms`, `expiry_date`, `wallet`, `organization_database`). Hotspot settings live in new `org_hotspot_settings`. |
| Manager `admin_tables.priviledges` | 2 | A options | New hotspot options appended to the JSON by the admin create/edit screens. Missing option = **hidden** for hotspot items (opposite of today's default — see risk R6). Existing options untouched. |
| `public/db_imports/imports.sql` (manager) | 1 | A | Append `CREATE TABLE IF NOT EXISTS` / guarded `ALTER`s. No `;` inside statements (split by `explode(";")`). |
| `routes/web.php` | 2, 5–10 | A | New `/hotspot/*` (auth + `checkAccount` + `validated` + hotspot middleware), public `/hs/*`, `/api/hotspot/mpesa/callback/*`. No existing route edited. |
| `app/Http/Kernel.php` | 2 | A | Register `EnsureHotspotEnabled` alias only. |
| `app/Http/Middleware/VerifyCsrfToken.php` | 8 | A | Add the hotspot callback path only. |
| `resources/views/components/menu.blade.php` | 2 | A | Hotspot block + "ISP Billing \| Hotspot" toggle, rendered only when hotspot is enabled **and** the admin has a hotspot privilege. Existing items unchanged. |
| `resources/views/addadmin.blade.php` + admin edit view, `admin.php` | 2 | A | Extra privilege rows; JSON shape unchanged. |
| `app/Classes/MpesaService.php` | 8 | C (additive) | Add optional constructor/method parameters (org DB name, callback URL). Default behaviour — session DB, `mydomain.com` callback — stays identical for `initiate_stk()`. |
| `app/Classes/routeros_api.php` | 4 | R | Used as-is; hotspot code sets `attempts`/`timeout` on its own instance. |
| `Router_Cloud.php` (`checkActive`, `getSSTPAddress`) | 4, 10 | R / reuse | Call or copy the lookup; do not change its behaviour. Router detail view gets a hotspot section (Stage 10) as an include. |
| `Controller.php` SMS helpers | 6, 8 | R | Call `getSmsSettings()` / `GlobalSendSMS()`; hotspot checks `organizations.send_sms` itself (no session). |
| `Transaction::mpesaTransactions()` (`/Transact`) | 8 | **Open question R1** | Not changed unless R1 forces a guard; if so it is a minimal early-return for hotspot references, reviewed separately. |
| `crontab:organization/activate_organizations.php` | 11 | C | Add `getHotspotCost()` and the statement insert; deduction becomes `total_cost + hotspot_cost` only for hotspot-enabled orgs. `getMonthlyPayment()` untouched. |
| `crontab:shared_functions.php` `message_content()` | 11 | A | New `[hotspot_cost]` tag. |
| `crontab` new scripts | 4, 6, 8 | A | `hotspot_router_agent.php`, `hotspot_expire.php`, `hotspot_usage_sync.php`, reconciliation — separate files, never included by existing scripts. |
| `crontab:allowed_ip.php` | 4+ | R | New scripts include it like the others. |

## 2. Do-not-touch list

Changes to any of these need an explicit, separate decision — not as a side effect of a stage.

- PPPoE client activation/deactivation: `crontab:activate_deactivate_clients.php`,
  `freeze_clients.php`, `check_client_status.php`, and the `/activate/…`, `/deactivate/…` routes.
- `crontab:check_all_clients.php` (wallet renewal of clients) and `remindpayment.php`.
- Existing M-Pesa: `Transaction::mpesaTransactions()` (`/Transact`), `verify_client_transaction()`
  (`/Validate`), `initiate_stk()`/`stkpush()`, `mpesa_api::register_url()`, `billsms_manager`.
- `getMonthlyPayment()` and `getWaCost()` logic in `activate_organizations.php`.
- `Router_Cloud.php` / `Router.php` existing behaviour, the generated router config script, PPPoE
  secrets/profiles/queues/firewall on routers.
- `routeros_api.php` (both copies).
- `send_queued_sms.php` and the `sms_tables` queue markers.
- `login.php` auth/OTP flow, `checkAccount` / `validated` / `clientValidate` middleware.
- `Organization::import_database()` (manager) — only `imports.sql` content is appended to.
- Existing tables/columns: never altered or dropped (golden rule 2).

## 3. Baseline regression checklist

Run against **dev DBs only** (`mikrotik_cloud_manager` + `mikrotik_cloud`, org LADYBIRD SYSTEMS)
and the GNS3 lab router 23 (CHR-ISP).

**Before running:** the dev org has `send_sms = 1` and 10 clients with real-looking phone
numbers. Either point the org's SMS settings at a test sender/number, or set `send_sms = 0` for
the run and treat "message queued/logged in `sms_tables`" as the pass condition.

| # | Flow | How to check | Pass |
|---|---|---|---|
| B1 | Admin login + dashboard | `/Login` → `/Dashboard` | Loads, counts shown |
| B2 | Client list & profile | `/Clients`, open a client | Loads; region row present |
| B3 | Cloud router profile (SSTP path) | `/Routers` → router 23 | RouterOS details load (SSTP lookup + direct API to `192.168.254.x`) |
| B4 | Create PPPoE client on router 23 | `/Clients/NewPPPoE` | `/ppp secret` appears on CHR-ISP |
| B5 | Deactivate / activate a client | Client profile toggle | Secret disabled/enabled on CHR-ISP |
| B6 | Auto-deactivate unpaid client | Set a client's `next_expiration_date` in the past, wallet 0; run `php crontab/check_all_clients.php` then `activate_deactivate_clients.php` | Client deactivated on CHR-ISP |
| B7 | Renew from wallet | Same client, wallet ≥ monthly; run `check_all_clients.php` | Expiry extended, wallet reduced, re-enabled |
| B8 | C2B payment to known account | `POST /Transact` with a sample C2B JSON (`BusinessShortCode` = org's, `BillRefNumber` = client account) | Wallet credited, `transaction_tables` row, message logged |
| B9 | C2B payment to unknown account | Same with a wrong `BillRefNumber` | Unassigned transaction + "wrong account" message logged |
| B10 | PPPoE STK push (client portal) | `/Payment/stkpush` | Daraja accepted response (needs real/sandbox keys — mark N/A if none) |
| B11 | Single SMS | `/sms/compose` one number | `sms_tables` row, `sms_status` per provider |
| B12 | Bulk SMS via agent | `/sms/compose` → Filter Clients | Rows queued (`sent_status = 0`) then sent by `send_queued_sms.php` |
| B13 | Org renewal | Set org `expiry_date` in the past, wallet ≥ cost; run `php crontab/organization/activate_organizations.php` | Wallet reduced by `getMonthlyPayment()`, expiry +1 month |
| B14 | Org renewal, short wallet | Same with wallet 0 | `organization_status = 0`, deactivation message |
| B15 | Org near expiry UI block | Expiry within 5 days, low wallet | `block_edits` / `inactive_menu` behaviour unchanged |
| B16 | New org provisioning (manager) | Create a throwaway org | New DB created from `imports.sql`; drop it afterwards |
| B17 | Admin privileges | Admin with "My Routers" off | Menu item hidden |
| B18 | WhatsApp inbox | `/whatsapp/chats` | Loads |
| B19 | Crontab syntax | `for f in /opt/lampp/htdocs/crontab/*.php /opt/lampp/htdocs/crontab/organization/*.php; do /opt/lampp/bin/php -l $f; done` | No syntax errors |

Restore any dates/wallets changed for B6/B7/B13–B15 afterwards.

## 4. Risks & open questions

| # | Item | Impact on hotspot | Handling |
|---|---|---|---|
| R1 | **Hotspot STK payments will also arrive at `/Transact`.** PPPoE relies on C2B confirmation for STK payments (STK callback is `mydomain.com`). A hotspot STK push to the same Paybill will hit `mpesaTransactions()`, find no client for its `AccountReference`, store an unassigned transaction and SMS the customer "payment to wrong account". | High — customer confusion, polluted transactions | Decide in Stage 8 (open question). Options: (a) hotspot `AccountReference` with a fixed prefix (e.g. `HS…`) plus a minimal early-return in `mpesaTransactions()` for that prefix (touches do-not-touch code — needs sign-off); (b) leave `/Transact` alone and accept the unassigned row but suppress the SMS (still a change there). Confirm on one sandbox/live STK payment whether C2B fires for STK on these Paybills. |
| R2 | `MpesaService` only works with a session (constructor calls `change_db()`), hard-codes the callback URL and production URL, no token cache. | Medium | Stage 8 adds optional params (DB name, callback URL) without changing defaults; hotspot caches the token. |
| R3 | No duplicate-`TransID` check in `/Transact`; `BillRefNumber` interpolated into SQL. | Existing issue (not hotspot) | Recorded. Hotspot callback is idempotent on `CheckoutRequestID` and receipt and uses bound parameters. Fix of `/Transact` is a separate decision. |
| R4 | `routeros_api` defaults: 3 attempts × (3 s + 1 s) ≈ 12 s per offline router, twice per page. | Medium — slow pages | Hotspot reads use `attempts=1`, `timeout=2`; writes go through the crontab agent. |
| R5 | Org renewal: periods start at renewal time (gaps when late); `wa_cost` not deducted; `total_cost > 0` required, so an org with no billable PPPoE clients is deactivated. | High for Stage 11 | Statement period = previous `period_end` → renewal time. Hotspot-only orgs: renew if `total_cost + hotspot_cost > 0`. WhatsApp deduction stays a separate decision. |
| R6 | `showOption()` treats a missing privilege option as visible; privileges are not enforced on routes. | High for Stage 2 | Hotspot menu items and routes default to **deny** when the option is missing; hotspot routes check the privilege server-side in middleware. |
| R7 | **Production SSTP server API credentials are in plain text** in `manager:public/db_imports/imports.sql:180` and git history; every new org gets them in `settings.sstp_server`. | Security (existing) | Recommend rotating that password on the live VPN server; longer term keep `sstp_server` out of `imports.sql` and set it at provisioning from manager config. |
| R8 | **`public/scripts/*.rsc` contain router SSTP/API passwords**, are tracked in git, and are served publicly from `public/`. Regenerated whenever a router profile is opened. | Security (existing) | Do not commit regenerated files. Recommend moving generation out of `public/` and untracking the folder (separate decision). |
| R9 | Commented-out M-Pesa consumer key/secret/passkey in `app/Classes/MpesaService.php:18-28`, in git history. | Security (existing) | Recommend removing and rotating if they are live. |
| R10 | ~150 routes in `routes/web.php` have no middleware (e.g. `/changeWallet`, `/new_cloud_router`, `/Routers/Delete/{id}`, `/BillingSms/*`). | Security (existing) | Out of hotspot scope; every hotspot route gets explicit middleware. Worth a separate review. |
| R11 | `change_db()` silently falls back to the default `mysql2` DB without a session. | Medium | Hotspot entry points resolve the org explicitly and abort if not found. |
| R12 | Nothing can edit `sstp_server` except the DB. | Low | Possible manager screen later. In dev it was edited directly (now `192.168.86.21`, see `00-gns3-lab.md`). |
| R13 | How does the production app server reach `192.168.254.x` tunnel IPs (same box as the VPN, SSTP client, or a route)? | Needed for Stage 4 agent placement | Confirm on the live server before Stage 4. |
| R14 | `imports.sql` lags the dev schema (e.g. pending `sms_tables` columns). | Medium for Stage 1 | Stage 1 rollout must check columns before `ALTER`, and new orgs must get pending changes too. |
| R15 | `import_database()` drops the target DB first. | Low (only for new accounts) | Never call it for existing orgs; rollout of hotspot SQL to existing orgs is a separate idempotent script. |
| R16 | Org public key for portal/callback URLs does not exist (`organizations` has none). | Low | Stage 1/3: random key in `org_hotspot_settings` (not the guessable `account_no`). |
| R17 | **`.env` is tracked in git** (last committed in `9b01010`), so DB passwords, SMS/M-Pesa/WhatsApp keys and any new hotspot settings go into history when it's committed. | Security (existing) | Hotspot work never commits `.env`; new keys are documented in `docs/hotspot/` and `.env.example` instead. Recommend `git rm --cached .env` + `.gitignore` and rotating what was committed (separate decision). |
| R18 | **`crontab/routeros_api.php` hangs on "add" replies.** `read()` only stops when the last word read is `!done`; an add replies `!done` then `=ret=*id`, so the script waits forever (found 2026-10-17: the agent hung holding a router lock). | Existing; affects any crontab script that adds router objects | The hotspot agent uses its own subclass `hs_routeros_api` with a fixed `read()`. The shared file is not changed (do-not-touch). Worth fixing there separately. |
| R19 | **`app/Classes/routeros_api.php` stops reading too early on slow routers** (`$LENGTH === 0` ends the read): on a 600 MHz RB951 replies spill into the next command, so pages can show another command's data. | Existing; may explain odd router data on slow routers | Hotspot code uses `App\Classes\HotspotRouterApi` (fixed `read()`); existing router pages are unchanged. Worth fixing in the shared client separately. |
| R20 | **Router-profile script firewall move fails on RouterOS v7** when the first input rule is dynamic: `move … destination=0` does nothing, so "Allow SYSTEM remote access" stays below "drop all not coming from LAN" and the app can't reach the router through the VPN. Fix on the router: `/ip firewall filter move [find where comment="Do not delete: Allow SYSTEM remote access"] destination=[find where comment="defconf: drop all not coming from LAN"]`. | Existing; blocks cloud-router onboarding on such routers | Not changed in this work (router-profile code). Fix the generated script to move the rule above the first non-dynamic rule / the drop rule. |
| R21 | Tunnel addresses are handed out per VPN session: after the VPN server restarts, routers can swap `192.168.254.x` addresses. | Low | All lookups (app and agent) read `/ppp/active` every time; never store a router's tunnel IP. |
