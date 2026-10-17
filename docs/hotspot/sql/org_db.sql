-- Hotspot module: org (tenant) database schema. Stage 1.
-- Canonical copy. mikrotik_cloud_manager keeps an identical copy in database/hotspot/org_db.sql
-- (used by `php artisan hotspot:apply-org-schema`) and appends it to public/db_imports/imports.sql.
--
-- Rules for this file (the manager splits it on semicolons):
--   * one statement per semicolon, never a semicolon inside a statement, string or comment
--   * tables: CREATE TABLE IF NOT EXISTS only (safe to re-run)
--   * existing tables: one ALTER TABLE ... ADD COLUMN / ADD UNIQUE KEY / ADD KEY per statement,
--     so the rollout command can check information_schema before running each one
--   * no JSON type, no CHECK constraints, no ADD COLUMN IF NOT EXISTS (production may be MySQL 8)
--   * dates are VARCHAR(14) YmdHis, deleted flag instead of soft-delete timestamps

-- Plans the ISP sells on the captive portal (Stage 5)
CREATE TABLE IF NOT EXISTS `hotspot_plans` (
  `plan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `upload_speed` varchar(20) DEFAULT NULL COMMENT 'e.g. 2M',
  `download_speed` varchar(20) DEFAULT NULL COMMENT 'e.g. 5M',
  `rate_limit` varchar(100) DEFAULT NULL COMMENT 'RouterOS rate-limit string rx/tx',
  `validity_type` varchar(10) NOT NULL DEFAULT 'elapsed' COMMENT 'paused | elapsed',
  `duration_minutes` int(10) unsigned NOT NULL DEFAULT 60,
  `data_limit_mb` int(10) unsigned DEFAULT NULL COMMENT 'NULL = unlimited',
  `shared_users` int(10) unsigned NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`plan_id`),
  KEY `idx_active_sort` (`deleted`,`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- One row per ticket (voucher) created on a router (Stage 6)
CREATE TABLE IF NOT EXISTS `hotspot_tickets` (
  `ticket_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mac_address` varchar(17) DEFAULT NULL,
  `plan_id` int(10) unsigned NOT NULL,
  `router_id` int(11) NOT NULL COMMENT 'remote_routers.router_id',
  `payment_id` bigint(20) unsigned DEFAULT NULL COMMENT 'hotspot_payments.payment_id, NULL for manual tickets',
  `source` varchar(10) NOT NULL DEFAULT 'stk' COMMENT 'stk | manual',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'plan price at the time of sale',
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending | active | used | expired | failed',
  `provision_status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending | done | failed (router user created?)',
  `provision_attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `last_error` varchar(255) DEFAULT NULL,
  `provisioned_at` varchar(14) DEFAULT NULL,
  `activated_at` varchar(14) DEFAULT NULL COMMENT 'first login',
  `expires_at` varchar(14) DEFAULT NULL COMMENT 'elapsed plans: set on first login',
  `uptime_used_seconds` int(10) unsigned NOT NULL DEFAULT 0,
  `bytes_in` bigint(20) unsigned NOT NULL DEFAULT 0,
  `bytes_out` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL COMMENT 'admin id for manual tickets',
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ticket_id`),
  UNIQUE KEY `uniq_ticket_code` (`ticket_code`),
  UNIQUE KEY `uniq_payment_id` (`payment_id`),
  KEY `idx_router_status` (`router_id`,`status`),
  KEY `idx_status_expires` (`status`,`expires_at`),
  KEY `idx_provision` (`provision_status`,`router_id`),
  KEY `idx_phone` (`phone`),
  KEY `idx_source_created` (`source`,`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- STK push payments started from the captive portal (Stage 8)
CREATE TABLE IF NOT EXISTS `hotspot_payments` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `plan_id` int(10) unsigned NOT NULL,
  `router_id` int(11) NOT NULL,
  `mac_address` varchar(17) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `checkout_request_id` varchar(100) DEFAULT NULL,
  `merchant_request_id` varchar(100) DEFAULT NULL,
  `mpesa_receipt` varchar(30) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending | paid | failed | cancelled',
  `result_code` varchar(10) DEFAULT NULL,
  `result_desc` varchar(255) DEFAULT NULL,
  `raw_callback` longtext DEFAULT NULL COMMENT 'callback body, no credentials',
  `paid_at` varchar(14) DEFAULT NULL,
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uniq_checkout_request_id` (`checkout_request_id`),
  UNIQUE KEY `uniq_mpesa_receipt` (`mpesa_receipt`),
  KEY `idx_status_created` (`status`,`date_created`),
  KEY `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Hotspot buyers, keyed by phone (not linked to client_tables)
CREATE TABLE IF NOT EXISTS `hotspot_customers` (
  `customer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `first_seen_at` varchar(14) DEFAULT NULL,
  `last_purchase_at` varchar(14) DEFAULT NULL,
  `purchase_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_spent` decimal(12,2) NOT NULL DEFAULT 0.00,
  `date_changed` varchar(14) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `uniq_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Captive portal branding. router_id NULL = org default, a row per router overrides it (Stage 7)
CREATE TABLE IF NOT EXISTS `hotspot_login_pages` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `router_id` int(11) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `primary_color` varchar(7) DEFAULT NULL COMMENT 'hex, e.g. #1e9ff2',
  `secondary_color` varchar(7) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `welcome_text` text DEFAULT NULL,
  `support_phone` varchar(20) DEFAULT NULL,
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `uniq_router_id` (`router_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usage readings taken from the router every few minutes (Stage 6)
CREATE TABLE IF NOT EXISTS `hotspot_usage_snapshots` (
  `snapshot_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `router_id` int(11) NOT NULL,
  `bytes_in` bigint(20) unsigned NOT NULL DEFAULT 0,
  `bytes_out` bigint(20) unsigned NOT NULL DEFAULT 0,
  `uptime_seconds` int(10) unsigned NOT NULL DEFAULT 0,
  `captured_at` varchar(14) NOT NULL,
  PRIMARY KEY (`snapshot_id`),
  KEY `idx_ticket_captured` (`ticket_id`,`captured_at`),
  KEY `idx_router_captured` (`router_id`,`captured_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Log of every router write made by hotspot code (golden rule 6)
CREATE TABLE IF NOT EXISTS `hotspot_router_activity` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `router_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL COMMENT 'e.g. enable, sync_profile, add_user, remove_user',
  `target` varchar(150) DEFAULT NULL COMMENT 'object on the router, e.g. hypbits-hs-3',
  `status` varchar(10) NOT NULL COMMENT 'success | failed',
  `attempts` int(10) unsigned NOT NULL DEFAULT 1,
  `message` text DEFAULT NULL,
  `triggered_by` varchar(50) DEFAULT NULL COMMENT 'admin id or script name',
  `date_created` varchar(14) NOT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `idx_router_created` (`router_id`,`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Existing table remote_routers: hotspot columns (all defaulted or nullable, nothing existing changes)
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_enabled` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_key` varchar(40) DEFAULT NULL COMMENT 'public key in the portal URL /hs/{key}';
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_server_name` varchar(100) DEFAULT NULL;
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_interface` varchar(100) DEFAULT NULL;
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_dns_name` varchar(255) DEFAULT NULL;
ALTER TABLE `remote_routers` ADD COLUMN `hotspot_last_sync` varchar(14) DEFAULT NULL;
ALTER TABLE `remote_routers` ADD UNIQUE KEY `uniq_hotspot_key` (`hotspot_key`);
