# Hotspot Billing Module — Development Plan (tailored)

This plan adapts `public/hotspot/Hotspot_Development_Stages.pdf` (v1.0, 16 Oct 2026) to how
`mikrotik_cloud`, `mikrotik_cloud_manager` and `crontab` actually work. Where this file and the PDF
disagree, **this file wins**. The PDF's per-stage acceptance checklists and sign-off lines still
apply unless a stage below replaces them.

- Branch: `feature/hotspot` in `mikrotik_cloud` (created from `claude_code`). Use a branch of the
  same name in `mikrotik_cloud_manager` and `crontab` when work starts there.
- Company name is **Hypbits** (not "Hybits" as written in the PDF).

> **Where we left off (2026-10-17):** Stage 0 done and awaiting sign-off: baseline tags
> `pre-hotspot-baseline` and branch `feature/hotspot` in all three repos; `00-discovery.md`,
> `00-impact-and-safety.md`, `00-gns3-lab.md` written; golden rules added to all three CLAUDE.md
> files; GNS3 lab working (router 23 = CHR-ISP). **Next: Stage 1 (schema).** Read the risks in
> `00-impact-and-safety.md` section 4 first — R1 (hotspot STK payments also reaching `/Transact`)
> must be decided before Stage 8.

## 1. Decisions that change the PDF

| Topic | PDF | This project |
|---|---|---|
| M-Pesa credentials | New encrypted `org_mpesa_credentials` table in the master DB | **Reuse** the ISP's existing `settings` keys (`consumer_key`, `consumer_secret`, `passkey`, `paybill`) read by `app/Classes/MpesaService.php`. No new credentials table, no credentials screen in the manager. |
| Schema changes | Laravel migrations + tenant-migrate command | **Raw SQL** kept in `docs/hotspot/sql/`. Applied by hand to the dev DBs; `mikrotik_cloud_manager` applies it to every existing org DB and adds it to `public/db_imports/imports.sql` so new orgs get it at provisioning. |
| Background work | Laravel queue, supervisor workers, scheduler | **New scripts in the `crontab` project**. Periodic jobs go in the system crontab; on-demand work is started by calling a link, like `send_queued_sms.php`. No Laravel queue, no supervisor. |
| Hypbits' charge to ISPs | Percentage of sales by default; flat / per router / per ticket | Same. Plus manual (cash) tickets are **always charged per ticket** at a rate set in the manager. |
| How the charge is collected | A "Hotspot" line on the CMS invoice | The manager has no invoices. The hotspot charge is **added to the amount deducted from the org's wallet at subscription renewal** (`crontab/organization/activate_organizations.php`), with the breakdown stored in a hotspot statement table. |
| ISP does not pay | Open question | **Existing freeze rules**: if the wallet can't cover subscription + hotspot charge, the org is treated exactly like an unpaid subscription today. |
| Routers | "Over the VPN" | **Cloud routers only** — `remote_routers` (SSTP), handled today by `Router_Cloud.php`. `router_tables` (direct-IP) routers are out of scope. |
| Ticket code fallback | SMS | The ISP's own SMS settings via the existing SMS path; respects the org's `send_sms` flag. |
| Captive portal host | `/hs/{router_key}` | Same, on this app's existing domain. |
| Payment callback | Separate hotspot callback | **Separate**: `/api/hotspot/mpesa/callback/{org_key}/{callback_token}`. The existing PPPoE callbacks in `Transaction.php` / `mpesa_api.php` are not touched. |
| Hotspot customers | Separate `hotspot_customers` table | **Separate**, keyed by phone. Not linked to `client_tables` (possible future work). |
| Permissions | Roles/permissions system | New options in the existing admin `priviledges` JSON (see Stage 2). |
| Router remote access (PDF Stage 10) | Winbox/WebFig through the VPN, reboot, firmware | Cloud routers already have `winbox_port`/`api_port` forwarding and a reboot route. Stage 10 **only adds hotspot-specific views**; existing Winbox access is unchanged. |
| Git (PDF Stage 0) | Initialise local git | Already done — both apps are on GitHub. Stage 0 only tags the baseline. |
| Git deployment (PDF Stage 14) | Final stage | Kept as the final stage. |
| Test environment | Staging + spare test router | Local dev with a **GNS3 lab of virtual MikroTik routers (CHR)** for all router-write testing, plus the **staging copies** of both apps. Live routers (test account only, behind the write allowlist) are used only for a final check before the pilot. See the router safety rules below. |

## 2. Golden rules (to be added to CLAUDE.md in all three repos in Stage 0)

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
13. A hotspot privilege missing from an admin's `priviledges` JSON means **no access**, and hotspot routes check the privilege server-side (today's `showOption()` treats a missing option as visible, and routes are not privilege-checked).

## 3. Where data lives

| Manager DB (`mikrotik_cloud_manager`) | Each org DB |
|---|---|
| `org_hotspot_settings` — org_id, hotspot_enabled, enabled_at, billing_model (`percentage`\|`flat_monthly`\|`per_router`\|`per_ticket`), rate, manual_ticket_rate, minimum_charge, callback_token, effective_from | `hotspot_plans`, `hotspot_tickets`, `hotspot_payments`, `hotspot_customers`, `hotspot_usage_snapshots`, `hotspot_login_pages`, `hotspot_router_activity` |
| `org_hotspot_settings_history` — audit of who changed model/rate and when | `remote_routers` + new columns: `hotspot_enabled`, `hotspot_key` (unique, used in the portal URL), `hotspot_server_name`, `hotspot_interface`, `hotspot_dns_name`, `hotspot_last_sync` |
| `hotspot_billing_defaults` — global default model, rate and manual-ticket rate | Existing `settings` M-Pesa keys (unchanged, reused) |
| `org_hotspot_statements` — org_id, period_start, period_end, gross_sales, stk_ticket_count, manual_ticket_count, active_routers, billing_model, rate, amount_due, charged_at, status | Existing ISP clients, PPPoE billing data (untouched) |

## 4. How money and data flow

1. Customer connects to the hotspot → router redirects to `/hs/{hotspot_key}` on this app → picks a plan, enters a phone number.
2. This app sends an STK push to the **ISP's own Paybill/Till** using the keys in the org's `settings` (via `MpesaService`), with the callback URL `/api/hotspot/mpesa/callback/{org_key}/{callback_token}`.
3. Callback: the org is found from `org_key`, the token is checked against `org_hotspot_settings.callback_token`, the payment is matched by `CheckoutRequestID`, the raw payload is stored, and Safaricom gets an immediate reply. The ticket is issued and the provisioning agent link in `crontab` is called (fire-and-forget, like the SMS agent).
4. The agent creates the hotspot user on the router. The portal, polling a cheap status endpoint, signs the customer in automatically. The ticket code goes out by SMS as a fallback.
5. At org subscription renewal, `activate_organizations.php` computes the hotspot charge for the period that is ending, saves an `org_hotspot_statements` row, and deducts subscription + hotspot charge from the wallet together. If the wallet is short, existing freeze rules apply.

## 5. Progress tracker

| # | Stage | Repos | Est. days | Needs | Status | Started | Done |
|---|---|---|---|---|---|---|---|
| 0 | Study the live system & protect current operations | all 3 | 3 | — | Awaiting sign-off | 2026-10-17 | |
| 1 | Schema (raw SQL, manager rollout) | cloud, manager | 3 | 0 | | | |
| 2 | Mode switch, privileges & shared UI | cloud | 2 | 1 | | | |
| 3 | Manager: enable hotspot & billing model | manager, cloud | 3 | 1 | | | |
| 4 | Cloud routers: hotspot detection & setup | cloud, crontab | 4 | 2 | | | |
| 5 | Hotspot plans | cloud, crontab | 3 | 4 | | | |
| 6 | Tickets & provisioning engine | cloud, crontab | 6 | 5 | | | |
| 7 | Captive portal & custom login page | cloud | 5 | 5 | | | |
| 8 | M-Pesa STK push & automatic sign-in | cloud, crontab | 6 | 3, 6, 7 | | | |
| 9 | Reports | cloud | 4 | 6, 8 | | | |
| 10 | Hotspot router views | cloud | 2 | 4 | | | |
| 11 | Hotspot billing at org renewal | crontab, manager, cloud | 5 | 3, 8 | | | |
| 12 | Testing, load & hardening | all 3 | 5 | all | | | |
| 13 | Deployment, documentation & pilot | all 3 | 2 | 12 | | | |
| 14 | Switch live updates to Git-based deployment | all 3 | 3 | 13 | | | |
| | **Total** | | **56** | | | | |

## 6. Stages

Workflow for every stage is the PDF's: one stage per Claude Code session, start in plan mode,
build, show changed files and SQL, you test on staging, commit and tag `hotspot-stage-NN`,
re-run the baseline checklist, fix within the stage before moving on.

### Stage 0 — Study the live system & protect current operations
**Read-only for application code.** Produce:
- `docs/hotspot/00-discovery.md`: how `switchDb()`/`change_db()` resolve the org DB per request and how crontab scripts loop orgs; how `Organization.php` provisions an org DB from `public/db_imports/imports.sql`; the PPPoE payment flow (`MpesaService`, `Transaction.php`, `mpesa_api.php`, `/Transact`); how `Router_Cloud.php` and `routeros_api.php` connect over SSTP (addressing, timeouts, error handling); the admin `priviledges` JSON format and where the sidebar is rendered; the SMS sending path; org renewal in `crontab/organization/activate_organizations.php` (`getMonthlyPayment()`, `getWaCost()`); server versions and how files are uploaded today.
- `docs/hotspot/00-impact-and-safety.md`: impact map (every existing table, route, middleware, view, crontab script the work reads or changes, and how changes stay backward-compatible); do-not-touch list (at minimum: PPPoE activation/deactivation, `check_all_clients.php`, existing M-Pesa callbacks, `getMonthlyPayment()` logic); a baseline regression checklist of 10–20 flows (client renewal from wallet, auto-deactivate unpaid client, reactivate on payment, PPPoE STK payment, bulk SMS, org renewal and freeze, new org provisioning, …); risks and open questions.
- Golden rules (section 2) added to the CLAUDE.md of all three repos.
- Tag `pre-hotspot-baseline` on the current commit in each repo.
- Build the GNS3 lab and write `docs/hotspot/00-gns3-lab.md` (how to rebuild it):
  - **CHR-VPN** — stand-in for the Hypbits SSTP server; the dev org's `settings` row `sstp_server` points at it, and the dev machine has a route to its SSTP tunnel subnet so the app can reach client routers the same way it does in production.
  - **CHR-ISP** — the ISP's cloud router: SSTP client to CHR-VPN, registered in the dev org DB's `remote_routers` through the normal "add cloud router" flow, with a LAN interface reserved for the hotspot.
  - **Client** — a VM or container with a browser behind CHR-ISP's hotspot interface, to test the captive portal and automatic sign-in.
  - Internet for the routers through a GNS3 NAT node; the dev app reachable from the lab at the host's NAT-side address; ngrok for M-Pesa sandbox callbacks.
  - Snapshot the clean lab so each stage can start from a known router state.
- Set `HOTSPOT_ROUTER_WRITE_ALLOWLIST` to the GNS3 lab router(s) in the dev org DB; identify the test account's org DB and routers on staging for the final check only.

Acceptance: as in the PDF, except "local copy matches live" is checked by comparing files with the live server, and "no production router used" becomes "only allow-listed routers (GNS3 lab, or the test account for the final check) are written to".

### Stage 1 — Schema
- Write `docs/hotspot/sql/org_db.sql` (org DB tables and `remote_routers` columns) and `docs/hotspot/sql/manager_db.sql`, following the PDF's column lists with the conventions in rule 10, and the changes in section 3 (no credentials table; `manual_ticket_rate`, `callback_token` and history table added; statements table replaces monthly summaries).
- Apply both to the local dev DBs.
- Manager side: add the org DB SQL to `public/db_imports/imports.sql` and add a one-off way to apply it to every existing org DB (idempotent: `CREATE TABLE IF NOT EXISTS`, check columns before `ALTER`).
- Add Eloquent models only where the code will use them; no factories or migrations.
- `docs/hotspot/tables.md` lists every new table/column and which DB it lives in.

Acceptance: SQL applies cleanly twice in a row; existing tables untouched; a newly provisioned org gets the hotspot tables; the rollout is safe on an org that already has them.

### Stage 2 — Mode switch, privileges & shared UI
- Hotspot routes under `/hotspot` (route names prefixed `hotspot.`). Mode comes from the URL, not the session.
- Middleware `EnsureHotspotEnabled`: reads `org_hotspot_settings.hotspot_enabled` for the logged-in org from the manager DB (cached 5 minutes) and shows a friendly 403 page when it's off.
- Toggle "ISP Billing | Hotspot" in the top bar, shown only when hotspot is enabled. Hotspot sidebar menu in the existing Chameleon layout.
- New `priviledges` options (e.g. Hotspot Dashboard, Hotspot Plans, Hotspot Tickets, Hotspot Payments, Hotspot Routers, Hotspot Branding, Hotspot Reports), added to the admin create/edit screens and enforced with the existing `showOption()`/`CheckValidatedClients` pattern.
- Hotspot dashboard placeholder: today's sales, active users, tickets sold today, routers online.

Acceptance: as in the PDF, plus an admin without hotspot privileges sees no hotspot menu items.

### Stage 3 — Manager: enable hotspot & billing model
Manager repo:
- "Hotspot billing defaults" settings page: default model (percentage), default rate, default manual-ticket rate. Only system admins can edit.
- "Hotspot" tab on each organization: enable/disable (on enable, copy defaults and generate `callback_token`); billing model + rate + manual-ticket rate + optional minimum charge; changes take effect from the org's **next billing period** (`effective_from` = current `expiry_date`); every change is written to the history table.
- Show whether the org has M-Pesa keys configured (read-only check of its `settings`, values never displayed).

Org app (`mikrotik_cloud`): a "Test M-Pesa credentials" button that requests a Daraja OAuth token with the existing keys and reports success/failure.

Acceptance: as in the PDF, minus the credentials form; no secrets appear in page source, logs or responses.

### Stage 4 — Cloud routers: hotspot detection & setup
- `HotspotRouterService` (org app) built on `routeros_api.php` and the SSTP addressing `Router_Cloud.php` already uses: `detect()` (hotspot servers, profiles, RouterOS version), `health()`, `enable()`, `disable()`, `syncProfiles()`.
- Hotspot Routers page: `remote_routers` with online/offline, RouterOS version, hotspot detected, managed on/off, last sync.
- Enable: choose hotspot server/interface; make sure the walled garden contains this app's domain; set the login page redirect (Stage 7); generate `hotspot_key`; set `hotspot_enabled`.
- Router writes go through a crontab agent `hotspot_router_agent.php?db=<db>&task=...` (fire-and-forget link, same pattern and allowlist as `send_queued_sms.php`), with timeout, 3 retries, the per-router lock, the write allowlist and the `hotspot_router_activity` log. Reads for the page can be direct with short timeouts so an offline router never blocks the page.

Acceptance: as in the PDF, on the GNS3 lab router (then once on the test account's router before the pilot).

### Stage 5 — Hotspot plans
As in the PDF (CRUD, paused vs elapsed explained in the form, validation). On save, the router agent creates/updates a `hypbits-hs-<plan_id>` user profile (rate-limit, shared-users) on every hotspot-enabled router of the org. Deactivating a plan hides it from the portal without touching sold tickets. The plans list shows sync status per router.

### Stage 6 — Tickets & provisioning engine
- `TicketService::issue(plan, router, phone, mac, source, payment?)` in the org app: unique short code, `hotspot_tickets` row, then calls the router agent to run `/ip hotspot user add` (limit-uptime for paused plans, limit-bytes-total if there's a data limit). Idempotent: re-running never creates duplicates.
- Crontab scripts (system crontab, loop only orgs with hotspot enabled):
  - `hotspot_expire.php` — every minute: expire due elapsed tickets (set `expires_at` on first activation), remove the router user, kick the active session; mark paused tickets used when limit-uptime is reached.
  - `hotspot_usage_sync.php` — every 5 minutes, per router: read `/ip hotspot active` and user counters, update tickets, write `hotspot_usage_snapshots`.
  - Retry of tickets whose provisioning failed while a router was offline.
- Tickets Bought page and manual ticket form as in the PDF. Resending the code uses the org's SMS settings.

Acceptance: as in the PDF, on the GNS3 lab router.

### Stage 7 — Captive portal & custom login page
As in the PDF: public `/hs/{hotspot_key}` route outside the auth middleware; generated `login.html` / `alogin.html` / `status.html` with download and, where possible, upload via the router agent; portal shows logo, title, welcome text, active plans, phone input, "I have a ticket code", support phone; Hotspot Branding page with live preview; portal under 150 KB with no heavy frameworks. The portal is the one place outside the Chameleon admin theme.

### Stage 8 — M-Pesa STK push & automatic sign-in
1. Load the org's existing M-Pesa keys through `MpesaService`; cache the OAuth token until it expires.
2. `POST /hs/{hotspot_key}/pay`: validate plan and phone (`2547XXXXXXXX` / `2541XXXXXXXX`), rate-limit per phone and IP, create a pending `hotspot_payments` row, send the STK push with the hotspot callback URL.
3. Separate callback route `/api/hotspot/mpesa/callback/{org_key}/{callback_token}` (CSRF-exempt): resolve the org from the manager DB, verify the token, match `CheckoutRequestID`, be idempotent (same receipt twice = one ticket), store the raw payload, reply to Safaricom immediately, then issue the ticket (Stage 6), update `hotspot_customers`, and send the code by SMS.
4. `GET /hs/status/{payment_id}`: cheap status endpoint. The portal polls every 4 seconds for up to 2 minutes, then shows "Check SMS / enter code". When paid, the portal auto-submits the login to `link-login-only`.
5. Reconciliation: payments with no callback after 2 minutes are checked with the Daraja STK query (crontab script or on the status poll).
6. Hotspot Payments page: list, filters, totals, CSV export.

Acceptance: as in the PDF. The existing PPPoE STK flow still works (baseline checklist).

Before starting: decide risk R1 in `00-impact-and-safety.md` — PPPoE STK payments are credited through the C2B `/Transact` URL, so hotspot STK payments to the same Paybill will reach `mpesaTransactions()` too.

### Stage 9 — Reports
As in the PDF (retention, daily purchases, data usage, sales summary; date range; CSV/PDF export; indexes checked on a seeded org DB with 100k tickets). Manual tickets are always shown separately from paid tickets.

### Stage 10 — Hotspot router views
Only hotspot-specific additions to the router detail page: hotspot active users, DHCP leases on the hotspot interface, actions to disconnect a hotspot user and re-sync profiles and login files. Privilege-checked and logged. Existing Winbox access, reboot and firmware handling stay as they are.

### Stage 11 — Hotspot billing at org renewal
- Crontab: add `getHotspotCost($org, ...)` to `organization/activate_organizations.php`, next to `getWaCost()`. For an org with hotspot enabled it computes, for the subscription period ending at `expiry_date` (start = one month before):
  - `percentage` → paid STK sales × rate %
  - `flat_monthly` → rate
  - `per_router` → hotspot-enabled routers seen online in the period × rate
  - `per_ticket` → paid STK tickets × rate
  - plus manual tickets × `manual_ticket_rate` (all models)
  - then `max(charge, minimum_charge)` if a minimum is set
  using the model and rate effective for that period.
- At renewal, save an `org_hotspot_statements` row (unique on org + period, so a re-run never double-charges), and deduct **subscription + hotspot charge** from the wallet together. If the wallet is short, existing freeze/expiry rules apply unchanged.
- Add the hotspot amount to the renewal/reminder messages (`message_content()` in `shared_functions.php`), e.g. a `[hotspot_cost]` tag.
- Manager: "Hotspot billing" page per org and period (sales, model, rate, amount, status), a month-to-date estimate, and an admin "recalculate" for periods not yet charged.
- Org app: the same statement, read-only.
- Note for Stage 0 to confirm: `getWaCost()` is computed today but appears to be used only in the message text, not in the wallet deduction. Hotspot must actually be deducted; whether WhatsApp should be too is a separate decision.

Acceptance: as in the PDF ("KES 120,000 at 5% = KES 6,000"; rate change mid-period applies next period; hotspot-disabled org not billed; org statement matches the manager; running renewal twice doesn't double-charge), plus manual tickets charged at the manual rate.

### Stage 12 — Testing, load & hardening
As in the PDF, adapted: tests for the mode switch, plan sync, ticket lifecycle (paused and elapsed), STK success/cancel/duplicate/timeout and every billing model; load test the portal, `/hs/status` and the callback with 200 concurrent users on staging; review Apache/PHP-FPM, MySQL indexes and the crontab schedule (no supervisor/queue workers); security review (callback token, rate limits, CSRF exceptions only where needed, privileges, no secrets in logs). Write `docs/hotspot/test-report.md`.

### Stage 13 — Deployment, documentation & pilot
As in the PDF: `docs/hotspot/deployment.md` for today's manual upload (changed files per repo from `git diff pre-hotspot-baseline..feature/hotspot --name-only`, SQL order — manager DB first, then org DBs — `.env` additions, new crontab entries, backup and rollback steps); ISP user guide and manager admin guide; router onboarding checklist (SSTP, hotspot server, walled garden, login files); one-week pilot with one ISP on 1–2 routers.

### Stage 14 — Switch live updates to Git-based deployment
As in the PDF, noting that the GitHub remotes already exist (`hypbits-enterprises/mikrotik_cloud`, `Hilary-2000/mikrotik_cloud_manager`); `crontab` also needs a remote. Reconcile live vs repo first, then deploy keys, release folders with shared `.env`/storage/uploads, `deploy.sh` and `rollback.sh`, and update the deployment docs and CLAUDE.md.

## 7. End-to-end scenarios before go-live
Use the PDF's 13 scenarios with these changes:
- 6: "Router loses its SSTP connection for 10 minutes; tickets bought meanwhile are provisioned when it returns."
- 9: "At the org's renewal, the hotspot charge (percentage model) is computed, recorded as a statement and deducted from the wallet together with the subscription."
- 10: "Admin changes an org to per-router billing; the next period's charge uses the new model."
- Add 14: "Manual ticket sold for cash is charged at the manual-ticket rate at renewal."
- Add 15: "Org whose wallet can't cover subscription + hotspot charge is frozen exactly as today."

## 8. Remaining risks
| Item | Handling |
|---|---|
| Testing on live routers | Avoided for development: GNS3 CHR lab. Final check only on the test account's routers, with the write allowlist + Hypbits-owned object naming (rule 5). |
| Server capacity (portal polling, callbacks) | Status endpoint is cache-only; heavy work happens in crontab agents. Tune before Stage 12. |
| MAC randomisation | Phone number is the customer key; MAC only for the current session. |
| Paybill API access | Each ISP needs Daraja go-live access for its shortcode; "Test M-Pesa credentials" before enabling hotspot. |
| One M-Pesa config for PPPoE and hotspot | Both flows share the ISP's keys; the separate callback URL keeps the flows apart. |
| Cross-system rollout | Each stage that touches the manager or crontab must be deployed to all three systems together. |
