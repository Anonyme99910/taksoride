-- ============================================
-- TaksoRide Subscription System
-- Commission + Monthly Subscriptions + Cashback
-- ============================================

-- 1. Subscription Plans Table
CREATE TABLE IF NOT EXISTS `cab_tbl_subscription_plans` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(100) NOT NULL,
  `plan_description` text,
  `plan_type` enum('monthly','quarterly','yearly') DEFAULT 'monthly',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'USD',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Commission % per ride (0-100)',
  `features` text COMMENT 'JSON array of features',
  `max_rides` int(11) DEFAULT NULL COMMENT 'NULL = unlimited',
  `priority_level` int(11) DEFAULT 0 COMMENT 'Higher = more priority for ride allocation',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`plan_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Driver subscription plans';

-- 2. Driver Subscriptions Table
CREATE TABLE IF NOT EXISTS `cab_tbl_driver_subscriptions` (
  `subscription_id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(10) unsigned NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','expired','cancelled','pending') DEFAULT 'pending',
  `payment_status` enum('paid','pending','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `auto_renew` tinyint(1) DEFAULT 1,
  `rides_completed` int(11) DEFAULT 0 COMMENT 'Rides completed in this subscription period',
  `total_earnings` decimal(10,2) DEFAULT 0.00 COMMENT 'Total earnings in this period',
  `commission_paid` decimal(10,2) DEFAULT 0.00 COMMENT 'Total commission paid in this period',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscription_id`),
  KEY `driver_id` (`driver_id`),
  KEY `plan_id` (`plan_id`),
  KEY `status` (`status`),
  KEY `end_date` (`end_date`),
  CONSTRAINT `fk_driver_subscription_driver` FOREIGN KEY (`driver_id`) REFERENCES `cab_tbl_drivers` (`driver_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_driver_subscription_plan` FOREIGN KEY (`plan_id`) REFERENCES `cab_tbl_subscription_plans` (`plan_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Driver active subscriptions';

-- 3. Cashback Rules Table
CREATE TABLE IF NOT EXISTS `cab_tbl_cashback_rules` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(100) NOT NULL,
  `rule_type` enum('percentage','fixed','tiered') DEFAULT 'percentage',
  `cashback_value` decimal(10,2) NOT NULL COMMENT 'Percentage or fixed amount',
  `min_ride_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Minimum ride amount to qualify',
  `max_cashback` decimal(10,2) DEFAULT NULL COMMENT 'Maximum cashback per ride',
  `applicable_to` enum('all','new_users','regular_users','vip_users') DEFAULT 'all',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `usage_limit` int(11) DEFAULT NULL COMMENT 'Max uses per user (NULL = unlimited)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rule_id`),
  KEY `is_active` (`is_active`),
  KEY `rule_type` (`rule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Cashback rules for riders';

-- 4. User Cashback Transactions Table
CREATE TABLE IF NOT EXISTS `cab_tbl_cashback_transactions` (
  `cashback_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `booking_id` int(10) unsigned DEFAULT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `cashback_amount` decimal(10,2) NOT NULL,
  `ride_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','credited','expired','cancelled') DEFAULT 'pending',
  `expiry_date` datetime DEFAULT NULL,
  `credited_at` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cashback_id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  KEY `rule_id` (`rule_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_cashback_user` FOREIGN KEY (`user_id`) REFERENCES `cab_tbl_users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cashback_booking` FOREIGN KEY (`booking_id`) REFERENCES `cab_tbl_bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cashback_rule` FOREIGN KEY (`rule_id`) REFERENCES `cab_tbl_cashback_rules` (`rule_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User cashback history';

-- 5. Commission Transactions Table
CREATE TABLE IF NOT EXISTS `cab_tbl_commission_transactions` (
  `commission_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned NOT NULL,
  `driver_id` int(10) unsigned NOT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `ride_amount` decimal(10,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL COMMENT 'Commission % applied',
  `commission_amount` decimal(10,2) NOT NULL,
  `driver_earnings` decimal(10,2) NOT NULL COMMENT 'Amount driver receives',
  `company_earnings` decimal(10,2) NOT NULL COMMENT 'Amount company receives',
  `payment_status` enum('pending','paid','hold') DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commission_id`),
  KEY `booking_id` (`booking_id`),
  KEY `driver_id` (`driver_id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `payment_status` (`payment_status`),
  CONSTRAINT `fk_commission_booking` FOREIGN KEY (`booking_id`) REFERENCES `cab_tbl_bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_commission_driver` FOREIGN KEY (`driver_id`) REFERENCES `cab_tbl_drivers` (`driver_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_commission_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `cab_tbl_driver_subscriptions` (`subscription_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Commission tracking per ride';

-- 6. Add subscription fields to drivers table
ALTER TABLE `cab_tbl_drivers` 
ADD COLUMN `current_subscription_id` int(11) DEFAULT NULL COMMENT 'Active subscription',
ADD COLUMN `subscription_status` enum('none','active','expired','trial') DEFAULT 'none',
ADD COLUMN `subscription_expires_at` datetime DEFAULT NULL,
ADD KEY `subscription_status` (`subscription_status`),
ADD KEY `current_subscription_id` (`current_subscription_id`);

-- 7. Add cashback balance to users table
ALTER TABLE `cab_tbl_users` 
ADD COLUMN `cashback_balance` decimal(10,2) DEFAULT 0.00 COMMENT 'Available cashback balance',
ADD COLUMN `total_cashback_earned` decimal(10,2) DEFAULT 0.00 COMMENT 'Lifetime cashback earned',
ADD COLUMN `total_cashback_used` decimal(10,2) DEFAULT 0.00 COMMENT 'Lifetime cashback used';

-- ============================================
-- Insert Default Subscription Plans
-- ============================================

INSERT INTO `cab_tbl_subscription_plans` 
(`plan_name`, `plan_description`, `plan_type`, `price`, `commission_rate`, `features`, `max_rides`, `priority_level`, `is_active`) 
VALUES
-- Free Plan (Commission Only)
('Commission Only', 'Pay commission per ride. No monthly fee. Perfect for occasional drivers.', 'monthly', 0.00, 20.00, 
'["No monthly fee", "20% commission per ride", "Standard support", "Basic features"]', 
NULL, 1, 1),

-- Basic Plan
('Basic Plan', 'Low monthly fee with reduced commission. Great for part-time drivers.', 'monthly', 49.99, 10.00,
'["$49.99/month", "10% commission per ride", "Priority support", "Unlimited rides", "Performance analytics"]',
NULL, 2, 1),

-- Professional Plan
('Professional Plan', 'Best value for full-time drivers. Lowest commission rate.', 'monthly', 99.99, 5.00,
'["$99.99/month", "Only 5% commission", "Premium support 24/7", "Unlimited rides", "Advanced analytics", "Priority ride allocation", "Marketing tools"]',
NULL, 3, 1),

-- Premium Plan
('Premium Plan', 'Zero commission! Fixed monthly fee. Maximum earnings for high-volume drivers.', 'monthly', 199.99, 0.00,
'["$199.99/month", "0% commission - Keep 100% of earnings!", "VIP support", "Unlimited rides", "Full analytics suite", "Highest priority", "Dedicated account manager", "Marketing & promotion"]',
NULL, 4, 1);

-- ============================================
-- Insert Default Cashback Rules
-- ============================================

INSERT INTO `cab_tbl_cashback_rules` 
(`rule_name`, `rule_type`, `cashback_value`, `min_ride_amount`, `max_cashback`, `applicable_to`, `is_active`, `usage_limit`) 
VALUES
-- Welcome Cashback
('Welcome Bonus', 'fixed', 10.00, 0.00, 10.00, 'new_users', 1, 1),

-- Regular Cashback
('Standard Cashback', 'percentage', 5.00, 10.00, 20.00, 'all', 1, NULL),

-- VIP Cashback
('VIP Cashback', 'percentage', 10.00, 20.00, 50.00, 'vip_users', 1, NULL),

-- Weekend Bonus
('Weekend Bonus', 'percentage', 15.00, 15.00, 30.00, 'all', 1, NULL);

-- ============================================
-- Indexes for Performance
-- ============================================

CREATE INDEX idx_driver_subscription_active ON cab_tbl_driver_subscriptions(driver_id, status, end_date);
CREATE INDEX idx_cashback_user_status ON cab_tbl_cashback_transactions(user_id, status);
CREATE INDEX idx_commission_driver_date ON cab_tbl_commission_transactions(driver_id, created_at);

-- ============================================
-- Views for Easy Reporting
-- ============================================

-- Active Driver Subscriptions View
CREATE OR REPLACE VIEW vw_active_driver_subscriptions AS
SELECT 
    ds.subscription_id,
    ds.driver_id,
    CONCAT(d.firstname, ' ', d.lastname) AS driver_name,
    d.phone AS driver_phone,
    sp.plan_name,
    sp.price AS plan_price,
    sp.commission_rate,
    ds.start_date,
    ds.end_date,
    ds.status,
    ds.rides_completed,
    ds.total_earnings,
    ds.commission_paid,
    DATEDIFF(ds.end_date, NOW()) AS days_remaining
FROM cab_tbl_driver_subscriptions ds
JOIN cab_tbl_drivers d ON ds.driver_id = d.driver_id
JOIN cab_tbl_subscription_plans sp ON ds.plan_id = sp.plan_id
WHERE ds.status = 'active';

-- User Cashback Summary View
CREATE OR REPLACE VIEW vw_user_cashback_summary AS
SELECT 
    u.user_id,
    CONCAT(u.firstname, ' ', u.lastname) AS user_name,
    u.phone,
    u.cashback_balance,
    u.total_cashback_earned,
    u.total_cashback_used,
    COUNT(ct.cashback_id) AS total_cashback_transactions,
    SUM(CASE WHEN ct.status = 'pending' THEN ct.cashback_amount ELSE 0 END) AS pending_cashback
FROM cab_tbl_users u
LEFT JOIN cab_tbl_cashback_transactions ct ON u.user_id = ct.user_id
GROUP BY u.user_id;

-- Commission Summary View
CREATE OR REPLACE VIEW vw_commission_summary AS
SELECT 
    DATE(ct.created_at) AS transaction_date,
    COUNT(ct.commission_id) AS total_rides,
    SUM(ct.ride_amount) AS total_ride_amount,
    SUM(ct.commission_amount) AS total_commission,
    SUM(ct.driver_earnings) AS total_driver_earnings,
    SUM(ct.company_earnings) AS total_company_earnings,
    AVG(ct.commission_rate) AS avg_commission_rate
FROM cab_tbl_commission_transactions ct
GROUP BY DATE(ct.created_at);

-- ============================================
-- Stored Procedures
-- ============================================

DELIMITER $$

-- Procedure to activate driver subscription
CREATE PROCEDURE sp_activate_driver_subscription(
    IN p_driver_id INT,
    IN p_plan_id INT,
    IN p_payment_reference VARCHAR(255)
)
BEGIN
    DECLARE v_plan_price DECIMAL(10,2);
    DECLARE v_plan_type VARCHAR(20);
    DECLARE v_end_date DATETIME;
    
    -- Get plan details
    SELECT price, plan_type INTO v_plan_price, v_plan_type
    FROM cab_tbl_subscription_plans
    WHERE plan_id = p_plan_id AND is_active = 1;
    
    -- Calculate end date
    SET v_end_date = CASE v_plan_type
        WHEN 'monthly' THEN DATE_ADD(NOW(), INTERVAL 1 MONTH)
        WHEN 'quarterly' THEN DATE_ADD(NOW(), INTERVAL 3 MONTH)
        WHEN 'yearly' THEN DATE_ADD(NOW(), INTERVAL 1 YEAR)
        ELSE DATE_ADD(NOW(), INTERVAL 1 MONTH)
    END;
    
    -- Deactivate any existing active subscriptions
    UPDATE cab_tbl_driver_subscriptions
    SET status = 'cancelled'
    WHERE driver_id = p_driver_id AND status = 'active';
    
    -- Create new subscription
    INSERT INTO cab_tbl_driver_subscriptions
    (driver_id, plan_id, start_date, end_date, status, payment_status, payment_reference, amount_paid)
    VALUES
    (p_driver_id, p_plan_id, NOW(), v_end_date, 'active', 'paid', p_payment_reference, v_plan_price);
    
    -- Update driver table
    UPDATE cab_tbl_drivers
    SET current_subscription_id = LAST_INSERT_ID(),
        subscription_status = 'active',
        subscription_expires_at = v_end_date
    WHERE driver_id = p_driver_id;
    
    SELECT LAST_INSERT_ID() AS subscription_id;
END$$

-- Procedure to calculate and apply cashback
CREATE PROCEDURE sp_apply_cashback(
    IN p_user_id INT,
    IN p_booking_id INT,
    IN p_ride_amount DECIMAL(10,2)
)
BEGIN
    DECLARE v_rule_id INT;
    DECLARE v_cashback_value DECIMAL(10,2);
    DECLARE v_rule_type VARCHAR(20);
    DECLARE v_max_cashback DECIMAL(10,2);
    DECLARE v_cashback_amount DECIMAL(10,2);
    DECLARE v_expiry_date DATETIME;
    
    -- Find applicable cashback rule
    SELECT rule_id, cashback_value, rule_type, max_cashback
    INTO v_rule_id, v_cashback_value, v_rule_type, v_max_cashback
    FROM cab_tbl_cashback_rules
    WHERE is_active = 1
    AND p_ride_amount >= min_ride_amount
    AND (start_date IS NULL OR start_date <= NOW())
    AND (end_date IS NULL OR end_date >= NOW())
    ORDER BY cashback_value DESC
    LIMIT 1;
    
    IF v_rule_id IS NOT NULL THEN
        -- Calculate cashback
        IF v_rule_type = 'percentage' THEN
            SET v_cashback_amount = (p_ride_amount * v_cashback_value / 100);
        ELSE
            SET v_cashback_amount = v_cashback_value;
        END IF;
        
        -- Apply max cashback limit
        IF v_max_cashback IS NOT NULL AND v_cashback_amount > v_max_cashback THEN
            SET v_cashback_amount = v_max_cashback;
        END IF;
        
        -- Set expiry (30 days from now)
        SET v_expiry_date = DATE_ADD(NOW(), INTERVAL 30 DAY);
        
        -- Insert cashback transaction
        INSERT INTO cab_tbl_cashback_transactions
        (user_id, booking_id, rule_id, cashback_amount, ride_amount, status, expiry_date, credited_at, description)
        VALUES
        (p_user_id, p_booking_id, v_rule_id, v_cashback_amount, p_ride_amount, 'credited', v_expiry_date, NOW(), 
         CONCAT('Cashback for ride #', p_booking_id));
        
        -- Update user cashback balance
        UPDATE cab_tbl_users
        SET cashback_balance = cashback_balance + v_cashback_amount,
            total_cashback_earned = total_cashback_earned + v_cashback_amount
        WHERE user_id = p_user_id;
        
        SELECT v_cashback_amount AS cashback_earned;
    ELSE
        SELECT 0 AS cashback_earned;
    END IF;
END$$

-- Procedure to calculate commission
CREATE PROCEDURE sp_calculate_commission(
    IN p_booking_id INT,
    IN p_driver_id INT,
    IN p_ride_amount DECIMAL(10,2)
)
BEGIN
    DECLARE v_subscription_id INT;
    DECLARE v_commission_rate DECIMAL(5,2);
    DECLARE v_commission_amount DECIMAL(10,2);
    DECLARE v_driver_earnings DECIMAL(10,2);
    DECLARE v_company_earnings DECIMAL(10,2);
    
    -- Get driver's active subscription and commission rate
    SELECT ds.subscription_id, sp.commission_rate
    INTO v_subscription_id, v_commission_rate
    FROM cab_tbl_driver_subscriptions ds
    JOIN cab_tbl_subscription_plans sp ON ds.plan_id = sp.plan_id
    WHERE ds.driver_id = p_driver_id 
    AND ds.status = 'active'
    AND ds.end_date >= NOW()
    LIMIT 1;
    
    -- If no active subscription, use default commission (20%)
    IF v_commission_rate IS NULL THEN
        SET v_commission_rate = 20.00;
    END IF;
    
    -- Calculate amounts
    SET v_commission_amount = (p_ride_amount * v_commission_rate / 100);
    SET v_driver_earnings = p_ride_amount - v_commission_amount;
    SET v_company_earnings = v_commission_amount;
    
    -- Insert commission transaction
    INSERT INTO cab_tbl_commission_transactions
    (booking_id, driver_id, subscription_id, ride_amount, commission_rate, commission_amount, driver_earnings, company_earnings, payment_status)
    VALUES
    (p_booking_id, p_driver_id, v_subscription_id, p_ride_amount, v_commission_rate, v_commission_amount, v_driver_earnings, v_company_earnings, 'pending');
    
    -- Update subscription stats
    IF v_subscription_id IS NOT NULL THEN
        UPDATE cab_tbl_driver_subscriptions
        SET rides_completed = rides_completed + 1,
            total_earnings = total_earnings + v_driver_earnings,
            commission_paid = commission_paid + v_commission_amount
        WHERE subscription_id = v_subscription_id;
    END IF;
    
    SELECT v_commission_rate AS commission_rate, 
           v_commission_amount AS commission_amount,
           v_driver_earnings AS driver_earnings,
           v_company_earnings AS company_earnings;
END$$

DELIMITER ;

-- ============================================
-- Success Message
-- ============================================
SELECT 'Subscription system installed successfully!' AS message;
