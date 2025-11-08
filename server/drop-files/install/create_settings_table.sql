-- Create settings table for TaksoRide
-- This table stores application settings like API keys

CREATE TABLE IF NOT EXISTS `cab_tbl_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(255) NOT NULL,
  `option_value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application settings';

-- Insert default settings
INSERT INTO `cab_tbl_settings` (`option_name`, `option_value`) VALUES
('google-maps-api-key', ''),
('google-push-server-key', ''),
('firebase-web-api-key', ''),
('firebase-rtdb-url', ''),
('firebase-storage-bucket', ''),
('pubnub-publish-key', ''),
('pubnub-subscribe-key', ''),
('pubnub-secret-key', ''),
('smtp-host', ''),
('smtp-port', '587'),
('smtp-username', ''),
('smtp-password', ''),
('sms-otp-service', 'firebase')
ON DUPLICATE KEY UPDATE option_name=option_name;
