-- Hotspot module: manager (mikrotik_cloud_manager) database schema. Stage 1.
-- Apply once per environment. Safe to re-run: CREATE TABLE IF NOT EXISTS, and the seed row is
-- only inserted when the table is empty.
-- Same file rules as org_db.sql: one statement per semicolon, no JSON type, no CHECK constraints,
-- dates as VARCHAR(14) YmdHis.

-- Current hotspot settings per organization (Stage 3 writes it)
CREATE TABLE IF NOT EXISTS `org_hotspot_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `hotspot_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `enabled_at` varchar(14) DEFAULT NULL,
  `billing_model` varchar(20) NOT NULL DEFAULT 'percentage' COMMENT 'percentage | flat_monthly | per_router | per_ticket',
  `rate` decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'percent for percentage, KES otherwise',
  `manual_ticket_rate` decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'KES per manual (cash) ticket, all models',
  `minimum_charge` decimal(12,2) DEFAULT NULL COMMENT 'NULL = no minimum',
  `org_key` varchar(32) DEFAULT NULL COMMENT 'random public key used in callback URLs',
  `callback_token` varchar(64) DEFAULT NULL COMMENT 'secret checked on every M-Pesa callback',
  `effective_from` varchar(14) DEFAULT NULL COMMENT 'when the current model and rate start applying',
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_organization_id` (`organization_id`),
  UNIQUE KEY `uniq_org_key` (`org_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Every change to an org's hotspot settings. The row with the latest effective_from on or
-- before a period's start is the model and rate used to bill that period (Stage 11)
CREATE TABLE IF NOT EXISTS `org_hotspot_settings_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `hotspot_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `billing_model` varchar(20) NOT NULL,
  `rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `manual_ticket_rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `minimum_charge` decimal(12,2) DEFAULT NULL,
  `effective_from` varchar(14) NOT NULL,
  `changed_by` int(11) DEFAULT NULL COMMENT 'admin_tables.admin_id',
  `changed_at` varchar(14) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_org_effective` (`organization_id`,`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Global defaults copied to an org when hotspot is first enabled (Stage 3). Single row.
CREATE TABLE IF NOT EXISTS `hotspot_billing_defaults` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `billing_model` varchar(20) NOT NULL DEFAULT 'percentage',
  `rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `manual_ticket_rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `minimum_charge` decimal(12,2) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hotspot_billing_defaults` (`billing_model`, `rate`, `manual_ticket_rate`, `minimum_charge`, `updated_by`, `updated_at`)
SELECT 'percentage', 0.0000, 0.0000, NULL, NULL, DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `hotspot_billing_defaults`);

-- Hotspot charge per org per billing period, written at org renewal (Stage 11).
-- UNIQUE (organization_id, period_start) makes a re-run of the renewal script safe.
CREATE TABLE IF NOT EXISTS `org_hotspot_statements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `period_start` varchar(14) NOT NULL,
  `period_end` varchar(14) NOT NULL,
  `gross_sales` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'paid STK sales in the period',
  `stk_ticket_count` int(10) unsigned NOT NULL DEFAULT 0,
  `manual_ticket_count` int(10) unsigned NOT NULL DEFAULT 0,
  `active_routers` int(10) unsigned NOT NULL DEFAULT 0,
  `billing_model` varchar(20) NOT NULL,
  `rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `manual_ticket_rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `minimum_charge` decimal(12,2) DEFAULT NULL,
  `amount_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(10) NOT NULL DEFAULT 'estimate' COMMENT 'estimate | charged | unpaid | waived',
  `charged_at` varchar(14) DEFAULT NULL,
  `date_created` varchar(14) DEFAULT NULL,
  `date_changed` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_org_period` (`organization_id`,`period_start`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Global index of hotspot router keys, so the session-less captive portal /hs/{hotspot_key}
-- finds the org and router in one lookup (written in Stage 4 with remote_routers.hotspot_key)
CREATE TABLE IF NOT EXISTS `hotspot_router_keys` (
  `hotspot_key` varchar(40) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `router_id` int(11) NOT NULL COMMENT 'remote_routers.router_id in that org DB',
  `date_created` varchar(14) DEFAULT NULL,
  PRIMARY KEY (`hotspot_key`),
  UNIQUE KEY `uniq_org_router` (`organization_id`,`router_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
