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
