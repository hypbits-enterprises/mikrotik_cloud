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
