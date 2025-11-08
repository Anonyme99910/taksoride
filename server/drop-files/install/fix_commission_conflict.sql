-- ============================================
-- Fix Commission System Conflict
-- Migrate from per-driver commission to subscription-based commission
-- ============================================

-- 1. Add migration flag to track which system is being used
ALTER TABLE `cab_tbl_drivers` 
ADD COLUMN `use_subscription_commission` tinyint(1) DEFAULT 1 COMMENT '1 = Use subscription commission, 0 = Use legacy driver_commision field';

-- 2. Update existing drivers to use subscription system by default
UPDATE `cab_tbl_drivers` SET `use_subscription_commission` = 1;

-- 3. Create a view that shows the effective commission rate for each driver
CREATE OR REPLACE VIEW vw_driver_effective_commission AS
SELECT 
    d.driver_id,
    CONCAT(d.firstname, ' ', d.lastname) AS driver_name,
    d.driver_commision AS legacy_commission,
    COALESCE(sp.commission_rate, 20.00) AS subscription_commission,
    d.use_subscription_commission,
    CASE 
        WHEN d.use_subscription_commission = 1 THEN COALESCE(sp.commission_rate, 20.00)
        ELSE d.driver_commision
    END AS effective_commission_rate,
    ds.subscription_id,
    sp.plan_name,
    ds.status AS subscription_status,
    ds.end_date AS subscription_expires
FROM cab_tbl_drivers d
LEFT JOIN cab_tbl_driver_subscriptions ds ON d.current_subscription_id = ds.subscription_id AND ds.status = 'active'
LEFT JOIN cab_tbl_subscription_plans sp ON ds.plan_id = sp.plan_id;

-- 4. Create stored procedure to get driver's effective commission
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_get_driver_commission$$

CREATE PROCEDURE sp_get_driver_commission(
    IN p_driver_id INT,
    OUT p_commission_rate DECIMAL(5,2),
    OUT p_source VARCHAR(20)
)
BEGIN
    DECLARE v_use_subscription TINYINT(1);
    DECLARE v_legacy_commission DECIMAL(4,1);
    DECLARE v_subscription_commission DECIMAL(5,2);
    DECLARE v_subscription_status VARCHAR(20);
    
    -- Get driver settings
    SELECT 
        use_subscription_commission,
        driver_commision
    INTO v_use_subscription, v_legacy_commission
    FROM cab_tbl_drivers
    WHERE driver_id = p_driver_id;
    
    -- If using subscription system
    IF v_use_subscription = 1 THEN
        -- Get subscription commission
        SELECT sp.commission_rate, ds.status
        INTO v_subscription_commission, v_subscription_status
        FROM cab_tbl_driver_subscriptions ds
        JOIN cab_tbl_subscription_plans sp ON ds.plan_id = sp.plan_id
        WHERE ds.driver_id = p_driver_id 
        AND ds.status = 'active'
        AND ds.end_date >= NOW()
        ORDER BY ds.end_date DESC
        LIMIT 1;
        
        -- If has active subscription, use it
        IF v_subscription_commission IS NOT NULL THEN
            SET p_commission_rate = v_subscription_commission;
            SET p_source = 'subscription';
        ELSE
            -- No active subscription, use default 20%
            SET p_commission_rate = 20.00;
            SET p_source = 'default';
        END IF;
    ELSE
        -- Use legacy per-driver commission
        SET p_commission_rate = v_legacy_commission;
        SET p_source = 'legacy';
    END IF;
END$$

DELIMITER ;

-- 5. Update the commission calculation stored procedure
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_calculate_commission$$

CREATE PROCEDURE sp_calculate_commission(
    IN p_booking_id INT,
    IN p_driver_id INT,
    IN p_ride_amount DECIMAL(10,2)
)
BEGIN
    DECLARE v_subscription_id INT;
    DECLARE v_commission_rate DECIMAL(5,2);
    DECLARE v_commission_source VARCHAR(20);
    DECLARE v_commission_amount DECIMAL(10,2);
    DECLARE v_driver_earnings DECIMAL(10,2);
    DECLARE v_company_earnings DECIMAL(10,2);
    
    -- Get effective commission rate
    CALL sp_get_driver_commission(p_driver_id, v_commission_rate, v_commission_source);
    
    -- Get subscription ID if using subscription
    IF v_commission_source = 'subscription' THEN
        SELECT subscription_id INTO v_subscription_id
        FROM cab_tbl_driver_subscriptions
        WHERE driver_id = p_driver_id 
        AND status = 'active'
        AND end_date >= NOW()
        ORDER BY end_date DESC
        LIMIT 1;
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
    
    -- Update subscription stats if applicable
    IF v_subscription_id IS NOT NULL THEN
        UPDATE cab_tbl_driver_subscriptions
        SET rides_completed = rides_completed + 1,
            total_earnings = total_earnings + v_driver_earnings,
            commission_paid = commission_paid + v_commission_amount
        WHERE subscription_id = v_subscription_id;
    END IF;
    
    -- Update booking with commission info
    UPDATE cab_tbl_bookings
    SET driver_commision = v_commission_rate
    WHERE id = p_booking_id;
    
    SELECT v_commission_rate AS commission_rate, 
           v_commission_amount AS commission_amount,
           v_driver_earnings AS driver_earnings,
           v_company_earnings AS company_earnings,
           v_commission_source AS commission_source;
END$$

DELIMITER ;

-- 6. Success message
SELECT 'Commission conflict resolved! Subscription system is now primary.' AS message;
