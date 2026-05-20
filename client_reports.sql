ALTER TABLE `sms_tables`
    ADD COLUMN IF NOT EXISTS `channel` VARCHAR(20) NOT NULL DEFAULT 'sms'
      COMMENT 'sms | whatsapp | email' AFTER `sms_type`,
    ADD COLUMN IF NOT EXISTS `message_category` VARCHAR(30) NULL DEFAULT NULL
      COMMENT 'service | utility | authentication | marketing' AFTER `channel`;

  -- Backfill existing rows
  UPDATE `sms_tables` SET `channel` = 'sms' WHERE `channel` = '' OR `channel` IS NULL;

  -- ─── 2. Create whatsapp_chats ─────────────────────────────────────────────────
  CREATE TABLE IF NOT EXISTS `whatsapp_chats` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `message_id`       INT UNSIGNED     NOT NULL COMMENT 'FK → sms_tables.sms_id',
    `direction`        VARCHAR(10)      NOT NULL DEFAULT 'outbound' COMMENT 'inbound | outbound',
    `wa_message_id`    VARCHAR(100)     NULL     COMMENT 'Meta message ID',
    `message_category` VARCHAR(30)      NULL,
    `template_name`    VARCHAR(100)     NULL,
    `template_variables` JSON           NULL,
    `delivery_status`  VARCHAR(20)      NOT NULL DEFAULT 'sent'
      COMMENT 'sent | delivered | read | failed | received',
    `window_open`      TINYINT(1)       NOT NULL DEFAULT 0,
    `received_at`      VARCHAR(20)      NULL     COMMENT 'YmdHis — only for inbound',
    INDEX `idx_message_id` (`message_id`),
    INDEX `idx_wa_message_id` (`wa_message_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- ─── 3. Create whatsapp_templates ────────────────────────────────────────────
  CREATE TABLE IF NOT EXISTS `whatsapp_templates` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`          VARCHAR(255)  NOT NULL COMMENT 'Display name',
    `template_name` VARCHAR(255)  NOT NULL COMMENT 'Meta registered name (snake_case)',
    `category`      VARCHAR(30)   NOT NULL DEFAULT 'utility'
      COMMENT 'service | utility | authentication | marketing',
    `body_text`     TEXT          NOT NULL COMMENT 'Template body with {{1}} {{2}} placeholders',
    `variables`     JSON          NULL     COMMENT 'Ordered list of variable names',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `date_changed`  VARCHAR(20)   NULL,
    `deleted`       CHAR(1)       NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- ─── 4. Add preferred_channel to settings ────────────────────────────────────
  -- Only insert if it doesn't already exist
  INSERT INTO `settings` (`keyword`, `value`, `status`)
  SELECT 'preferred_channel', 'sms', '1'
  WHERE NOT EXISTS (
    SELECT 1 FROM `settings` WHERE `keyword` = 'preferred_channel'
  );

ALTER TABLE `whatsapp_templates`
    ADD COLUMN IF NOT EXISTS `meta_status` VARCHAR(20) NOT NULL DEFAULT 'not_submitted' AFTER `is_active`;

ALTER TABLE `whatsapp_templates`
    ADD COLUMN IF NOT EXISTS `meta_template_id` VARCHAR(100) NULL AFTER `meta_status`,
    ADD COLUMN IF NOT EXISTS `language` VARCHAR(10) NOT NULL DEFAULT 'en' AFTER `meta_template_id`;

UPDATE sms_tables SET channel = 'email' WHERE recipient_phone LIKE '%@%';
ALTER TABLE `whatsapp_chats`
    ADD COLUMN `conversation_id`  VARCHAR(100) NULL AFTER `wa_message_id`,
    ADD COLUMN `billing_category` VARCHAR(20)  NULL AFTER `conversation_id`,
    ADD COLUMN `billable`         TINYINT(1)   NULL AFTER `billing_category`,
    ADD INDEX  `idx_conversation_id` (`conversation_id`);

-- FOR MIKROTIK_CLOUD_MANAGER
CREATE TABLE `unknown_wa_chats` (
      `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      `phone`           VARCHAR(30)     NOT NULL,
      `wa_message_id`   VARCHAR(100)    DEFAULT NULL,
      `direction`       ENUM('inbound','outbound') NOT NULL DEFAULT 'inbound',
      `message`         TEXT            NOT NULL,
      `delivery_status` VARCHAR(20)     NOT NULL DEFAULT 'received',
      `date_sent`       VARCHAR(14)     NOT NULL,
      `deleted`         TINYINT(1)      NOT NULL DEFAULT 0,
      `created_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_phone`         (`phone`),
      KEY `idx_wa_message_id` (`wa_message_id`),
      KEY `idx_deleted`       (`deleted`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    -- ============================================================
  -- 1. Add preferred_channel column to organizations
  -- ============================================================
  ALTER TABLE `organizations`
    ADD COLUMN `preferred_channel` VARCHAR(20) NOT NULL DEFAULT 'sms'
    AFTER `send_sms`;

  -- ============================================================
  -- 2. Universal WhatsApp automation templates table
  -- ============================================================
  CREATE TABLE `whatsapp_automation_templates` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `internal_name`    VARCHAR(100)     NOT NULL,
    `name`             VARCHAR(255)     NOT NULL,
    `template_name`    VARCHAR(255)     NOT NULL,
    `category`         VARCHAR(30)      NOT NULL DEFAULT 'utility',
    `body_text`        TEXT             NOT NULL,
    `variables`        LONGTEXT         DEFAULT NULL,
    `is_active`        TINYINT(1)       NOT NULL DEFAULT 1,
    `meta_status`      VARCHAR(20)      NOT NULL DEFAULT 'not_submitted',
    `meta_template_id` VARCHAR(100)     DEFAULT NULL,
    `language`         VARCHAR(10)      NOT NULL DEFAULT 'en',
    `date_changed`     VARCHAR(20)      DEFAULT NULL,
    `deleted`          CHAR(1)          NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_internal_name` (`internal_name`),
    KEY `idx_deleted` (`deleted`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  -- ============================================================
  -- 3. Insert the 13 automation templates
  -- ============================================================
  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('new_client_welcome','New Client 
  Welcome','new_client_welcome','utility','Welcome {{1}}. Your account has been successfully registered. Monthly payment: {{2}}. Account number: {{3}}. Expires: 
  {{4}}.','["client_f_name","monthly_fees","acc_no","exp_date"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('payment_received','Payment 
  Received','payment_received','utility','Hello {{1}}, we have received your payment of {{2}} on {{3}} at {{4}}. Your new wallet balance is 
  {{5}}.','["client_f_name","trans_amnt","today","now","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('payment_wrong_account','Payment Wrong 
  Account','payment_wrong_account','utility','Payment of {{1}} received on {{2}} at {{3}} could not be matched to an account. Please contact support with your M-Pesa transaction ID for 
  assistance.','["trans_amnt","today","now"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('payment_below_minimum','Payment Below 
  Minimum','payment_below_minimum','utility','Hello {{1}}, we received your payment of {{2}}. The minimum payment required is {{3}}. Your current wallet balance is {{4}}. Please top up to activate your 
  account.','["client_f_name","trans_amnt","min_amnt","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('referral_commission','Referral Commission
   Earned','referral_commission','utility','Hello {{1}}, you have earned a referral commission of {{2}} from {{3}} on {{4}} at {{5}}. Your new wallet balance is
  {{6}}.','["client_f_name","refferer_trans_amount","refferer_name","today","now","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_renewed','Account 
  Renewed','account_renewed','utility','Dear {{1}}, your account {{2}} has been renewed and is active until {{3}}. Your new wallet balance is 
  {{4}}.','["client_f_name","acc_no","exp_date","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_extended','Account 
  Extended','account_extended','utility','Dear {{1}}, your account {{2}} has been extended and is active until {{3}}. Your new wallet balance is 
  {{4}}.','["client_f_name","acc_no","exp_date","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_deactivated','Account 
  Deactivated','account_deactivated','utility','Dear {{1}}, your account {{2}} has been deactivated due to insufficient balance. Your wallet balance is {{3}}. Please make a payment to reactivate your 
  service.','["client_f_name","acc_no","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_frozen','Account 
  Frozen','account_frozen','utility','Dear {{1}}, your account {{2}} has been frozen for {{3}} until {{4}}. Your wallet balance as of {{5}} at {{6}} is 
  {{7}}.','["client_f_name","acc_no","days_frozen","unfreeze_date","today","now","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_freeze_scheduled','Account Freeze
   Scheduled','account_freeze_scheduled','utility','Dear {{1}}, your account {{2}} is scheduled to be frozen on {{3}} for {{4}} until {{5}}. Your current wallet balance is 
  {{6}}.','["client_f_name","acc_no","frozen_date","days_frozen","unfreeze_date","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('account_unfrozen','Account 
  Unfrozen','account_unfrozen','utility','Welcome back {{1}}, your account {{2}} has been reactivated after the freeze period. Your current wallet balance is 
  {{3}}.','["client_f_name","acc_no","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('payment_reminder_day_before','Payment 
  Reminder - Day Before Expiry','payment_reminder_day_before','utility','Dear {{1}}, your account expires tomorrow. Please make a payment to account {{2}} to avoid service interruption. Current wallet balance: 
  {{3}}.','["client_f_name","acc_no","client_wallet"]','not_submitted','en',NULL);

  INSERT INTO `whatsapp_automation_templates` (`internal_name`,`name`,`template_name`,`category`,`body_text`,`variables`,`meta_status`,`language`,`date_changed`) VALUES ('payment_reminder_day_after','Payment 
  Reminder - Day After Expiry','payment_reminder_day_after','utility','Dear {{1}}, your account expired yesterday. Please make a payment to account {{2}} to restore your service. Current wallet balance: 
  {{3}}.','["client_f_name","acc_no","client_wallet"]','not_submitted','en',NULL);

  -- 1. Create the billing rates table
  CREATE TABLE IF NOT EXISTS `whatsapp_billing_rates` (
      `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `category`    VARCHAR(20)    NOT NULL UNIQUE,
      `rate`        DECIMAL(10,4)  NOT NULL DEFAULT 0.0000,
      `currency`    VARCHAR(5)     NOT NULL DEFAULT 'KES',
      `description` VARCHAR(255)   NULL,
      `updated_at`  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
  
  -- 2. Seed default Meta rates (Sub-Saharan Africa, KES at ~130/USD)
  INSERT IGNORE INTO `whatsapp_billing_rates` (`category`, `rate`, `currency`, `description`) VALUES
  ('service',        1.2900, 'KES', 'Customer-initiated conversations (first 1,000/month free per WABA)'),
  ('utility',        1.2900, 'KES', 'Transactional messages (account updates, renewals, billing)'),
  ('authentication', 1.4700, 'KES', 'OTP and verification messages'),
  ('marketing',      2.7700, 'KES', 'Promotional and marketing messages');