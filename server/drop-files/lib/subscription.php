<?php
/**
 * TaksoRide Subscription System Library
 * Handles driver subscriptions, commission calculation, and cashback
 */

class SubscriptionManager {
    
    private $db;
    
    public function __construct($db_connection) {
        $this->db = $db_connection;
    }
    
    /**
     * Get all active subscription plans
     */
    public function getActivePlans() {
        $plans = [];
        $query = "SELECT * FROM " . DB_TBL_PREFIX . "tbl_subscription_plans WHERE is_active = 1 ORDER BY price ASC";
        
        if($result = mysqli_query($this->db, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                $row['features'] = json_decode($row['features'], true);
                $plans[] = $row;
            }
            mysqli_free_result($result);
        }
        
        return $plans;
    }
    
    /**
     * Get driver's current active subscription
     */
    public function getDriverSubscription($driver_id) {
        $query = sprintf(
            "SELECT ds.*, sp.plan_name, sp.commission_rate, sp.features 
            FROM %1\$stbl_driver_subscriptions ds
            JOIN %1\$stbl_subscription_plans sp ON ds.plan_id = sp.plan_id
            WHERE ds.driver_id = %2\$d AND ds.status = 'active' AND ds.end_date >= NOW()
            ORDER BY ds.end_date DESC LIMIT 1",
            DB_TBL_PREFIX,
            (int)$driver_id
        );
        
        if($result = mysqli_query($this->db, $query)) {
            if(mysqli_num_rows($result)) {
                $subscription = mysqli_fetch_assoc($result);
                $subscription['features'] = json_decode($subscription['features'], true);
                mysqli_free_result($result);
                return $subscription;
            }
            mysqli_free_result($result);
        }
        
        return null;
    }
    
    /**
     * Subscribe driver to a plan
     */
    public function subscribeDriver($driver_id, $plan_id, $payment_reference = null) {
        // Call stored procedure
        $query = sprintf(
            "CALL sp_activate_driver_subscription(%d, %d, %s)",
            (int)$driver_id,
            (int)$plan_id,
            $payment_reference ? "'" . mysqli_real_escape_string($this->db, $payment_reference) . "'" : "NULL"
        );
        
        if($result = mysqli_query($this->db, $query)) {
            $data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            return [
                'success' => true,
                'subscription_id' => $data['subscription_id'],
                'message' => 'Subscription activated successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to activate subscription: ' . mysqli_error($this->db)
        ];
    }
    
    /**
     * Cancel driver subscription
     */
    public function cancelSubscription($subscription_id, $driver_id) {
        $query = sprintf(
            "UPDATE %stbl_driver_subscriptions 
            SET status = 'cancelled', auto_renew = 0 
            WHERE subscription_id = %d AND driver_id = %d",
            DB_TBL_PREFIX,
            (int)$subscription_id,
            (int)$driver_id
        );
        
        if(mysqli_query($this->db, $query)) {
            // Update driver table
            $query2 = sprintf(
                "UPDATE %stbl_drivers 
                SET subscription_status = 'none', current_subscription_id = NULL 
                WHERE driver_id = %d",
                DB_TBL_PREFIX,
                (int)$driver_id
            );
            mysqli_query($this->db, $query2);
            
            return ['success' => true, 'message' => 'Subscription cancelled'];
        }
        
        return ['success' => false, 'message' => 'Failed to cancel subscription'];
    }
    
    /**
     * Calculate commission for a ride
     * Uses stored procedure to handle both subscription and legacy commission
     */
    public function calculateCommission($driver_id, $ride_amount) {
        // Call stored procedure to get effective commission rate
        $query = sprintf(
            "CALL sp_get_driver_commission(%d, @commission_rate, @commission_source)",
            (int)$driver_id
        );
        
        mysqli_query($this->db, $query);
        
        // Get the output variables
        $result = mysqli_query($this->db, "SELECT @commission_rate AS commission_rate, @commission_source AS commission_source");
        $data = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        
        $commission_rate = $data['commission_rate'] ?? 20.00;
        $commission_source = $data['commission_source'] ?? 'default';
        
        // Get subscription ID if using subscription
        $subscription_id = null;
        if($commission_source == 'subscription') {
            $subscription = $this->getDriverSubscription($driver_id);
            if($subscription) {
                $subscription_id = $subscription['subscription_id'];
            }
        }
        
        $commission_amount = ($ride_amount * $commission_rate) / 100;
        $driver_earnings = $ride_amount - $commission_amount;
        $company_earnings = $commission_amount;
        
        return [
            'subscription_id' => $subscription_id,
            'commission_rate' => $commission_rate,
            'commission_amount' => $commission_amount,
            'driver_earnings' => $driver_earnings,
            'company_earnings' => $company_earnings,
            'commission_source' => $commission_source
        ];
    }
    
    /**
     * Record commission transaction
     */
    public function recordCommission($booking_id, $driver_id, $ride_amount) {
        $commission = $this->calculateCommission($driver_id, $ride_amount);
        
        $query = sprintf(
            "INSERT INTO %stbl_commission_transactions 
            (booking_id, driver_id, subscription_id, ride_amount, commission_rate, commission_amount, driver_earnings, company_earnings, payment_status)
            VALUES (%d, %d, %s, %.2f, %.2f, %.2f, %.2f, %.2f, 'pending')",
            DB_TBL_PREFIX,
            (int)$booking_id,
            (int)$driver_id,
            $commission['subscription_id'] ? (int)$commission['subscription_id'] : 'NULL',
            $ride_amount,
            $commission['commission_rate'],
            $commission['commission_amount'],
            $commission['driver_earnings'],
            $commission['company_earnings']
        );
        
        if(mysqli_query($this->db, $query)) {
            // Update subscription stats if applicable
            if($commission['subscription_id']) {
                $update_query = sprintf(
                    "UPDATE %stbl_driver_subscriptions 
                    SET rides_completed = rides_completed + 1,
                        total_earnings = total_earnings + %.2f,
                        commission_paid = commission_paid + %.2f
                    WHERE subscription_id = %d",
                    DB_TBL_PREFIX,
                    $commission['driver_earnings'],
                    $commission['commission_amount'],
                    (int)$commission['subscription_id']
                );
                mysqli_query($this->db, $update_query);
            }
            
            return array_merge(['success' => true], $commission);
        }
        
        return ['success' => false, 'message' => 'Failed to record commission'];
    }
    
    /**
     * Check and expire subscriptions
     */
    public function checkExpiredSubscriptions() {
        $query = sprintf(
            "UPDATE %stbl_driver_subscriptions 
            SET status = 'expired' 
            WHERE status = 'active' AND end_date < NOW()",
            DB_TBL_PREFIX
        );
        
        mysqli_query($this->db, $query);
        
        // Update driver table
        $query2 = sprintf(
            "UPDATE %stbl_drivers d
            LEFT JOIN %stbl_driver_subscriptions ds ON d.current_subscription_id = ds.subscription_id
            SET d.subscription_status = 'expired'
            WHERE ds.status = 'expired' OR ds.end_date < NOW()",
            DB_TBL_PREFIX,
            DB_TBL_PREFIX
        );
        
        mysqli_query($this->db, $query2);
    }
}

class CashbackManager {
    
    private $db;
    
    public function __construct($db_connection) {
        $this->db = $db_connection;
    }
    
    /**
     * Get active cashback rules
     */
    public function getActiveRules() {
        $rules = [];
        $query = sprintf(
            "SELECT * FROM %stbl_cashback_rules 
            WHERE is_active = 1 
            AND (start_date IS NULL OR start_date <= NOW())
            AND (end_date IS NULL OR end_date >= NOW())
            ORDER BY cashback_value DESC",
            DB_TBL_PREFIX
        );
        
        if($result = mysqli_query($this->db, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                $rules[] = $row;
            }
            mysqli_free_result($result);
        }
        
        return $rules;
    }
    
    /**
     * Calculate cashback for a ride
     */
    public function calculateCashback($user_id, $ride_amount) {
        // Find applicable rule
        $query = sprintf(
            "SELECT * FROM %stbl_cashback_rules 
            WHERE is_active = 1 
            AND %.2f >= min_ride_amount
            AND (start_date IS NULL OR start_date <= NOW())
            AND (end_date IS NULL OR end_date >= NOW())
            ORDER BY cashback_value DESC LIMIT 1",
            DB_TBL_PREFIX,
            $ride_amount
        );
        
        if($result = mysqli_query($this->db, $query)) {
            if(mysqli_num_rows($result)) {
                $rule = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
                
                // Calculate cashback amount
                if($rule['rule_type'] == 'percentage') {
                    $cashback_amount = ($ride_amount * $rule['cashback_value']) / 100;
                } else {
                    $cashback_amount = $rule['cashback_value'];
                }
                
                // Apply max cashback limit
                if($rule['max_cashback'] && $cashback_amount > $rule['max_cashback']) {
                    $cashback_amount = $rule['max_cashback'];
                }
                
                return [
                    'rule_id' => $rule['rule_id'],
                    'rule_name' => $rule['rule_name'],
                    'cashback_amount' => $cashback_amount
                ];
            }
            mysqli_free_result($result);
        }
        
        return null;
    }
    
    /**
     * Apply cashback to user - Directly transfers to wallet
     */
    public function applyCashback($user_id, $booking_id, $ride_amount) {
        $cashback = $this->calculateCashback($user_id, $ride_amount);
        
        if(!$cashback) {
            return ['success' => false, 'message' => 'No cashback applicable'];
        }
        
        $expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Insert cashback transaction
        $query = sprintf(
            "INSERT INTO %stbl_cashback_transactions 
            (user_id, booking_id, rule_id, cashback_amount, ride_amount, status, expiry_date, credited_at, description)
            VALUES (%d, %d, %d, %.2f, %.2f, 'credited', '%s', NOW(), 'Cashback for ride #%d - Credited to wallet')",
            DB_TBL_PREFIX,
            (int)$user_id,
            (int)$booking_id,
            (int)$cashback['rule_id'],
            $cashback['cashback_amount'],
            $ride_amount,
            $expiry_date,
            (int)$booking_id
        );
        
        if(mysqli_query($this->db, $query)) {
            // Transfer cashback directly to rider's wallet (wallet_amount)
            $update_query = sprintf(
                "UPDATE %stbl_users 
                SET wallet_amount = wallet_amount + %.2f,
                    cashback_balance = cashback_balance + %.2f,
                    total_cashback_earned = total_cashback_earned + %.2f
                WHERE user_id = %d",
                DB_TBL_PREFIX,
                $cashback['cashback_amount'], // Add to wallet
                $cashback['cashback_amount'], // Track in cashback_balance
                $cashback['cashback_amount'], // Track total earned
                (int)$user_id
            );
            
            mysqli_query($this->db, $update_query);
            
            return [
                'success' => true,
                'cashback_amount' => $cashback['cashback_amount'],
                'rule_name' => $cashback['rule_name'],
                'transferred_to_wallet' => true
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to apply cashback'];
    }
    
    /**
     * Get user cashback balance
     */
    public function getUserCashbackBalance($user_id) {
        $query = sprintf(
            "SELECT cashback_balance, total_cashback_earned, total_cashback_used 
            FROM %stbl_users WHERE user_id = %d",
            DB_TBL_PREFIX,
            (int)$user_id
        );
        
        if($result = mysqli_query($this->db, $query)) {
            if(mysqli_num_rows($result)) {
                $data = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
                return $data;
            }
            mysqli_free_result($result);
        }
        
        return ['cashback_balance' => 0, 'total_cashback_earned' => 0, 'total_cashback_used' => 0];
    }
    
    /**
     * Use cashback for payment
     */
    public function useCashback($user_id, $amount) {
        $balance = $this->getUserCashbackBalance($user_id);
        
        if($balance['cashback_balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient cashback balance'];
        }
        
        $query = sprintf(
            "UPDATE %stbl_users 
            SET cashback_balance = cashback_balance - %.2f,
                total_cashback_used = total_cashback_used + %.2f
            WHERE user_id = %d",
            DB_TBL_PREFIX,
            $amount,
            $amount,
            (int)$user_id
        );
        
        if(mysqli_query($this->db, $query)) {
            return [
                'success' => true,
                'amount_used' => $amount,
                'remaining_balance' => $balance['cashback_balance'] - $amount
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to use cashback'];
    }
    
    /**
     * Get user cashback history
     */
    public function getUserCashbackHistory($user_id, $limit = 50) {
        $history = [];
        $query = sprintf(
            "SELECT ct.*, cr.rule_name 
            FROM %stbl_cashback_transactions ct
            LEFT JOIN %stbl_cashback_rules cr ON ct.rule_id = cr.rule_id
            WHERE ct.user_id = %d 
            ORDER BY ct.created_at DESC LIMIT %d",
            DB_TBL_PREFIX,
            DB_TBL_PREFIX,
            (int)$user_id,
            (int)$limit
        );
        
        if($result = mysqli_query($this->db, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                $history[] = $row;
            }
            mysqli_free_result($result);
        }
        
        return $history;
    }
}

// Initialize global instances
if(isset($GLOBALS['DB'])) {
    $GLOBALS['SubscriptionManager'] = new SubscriptionManager($GLOBALS['DB']);
    $GLOBALS['CashbackManager'] = new CashbackManager($GLOBALS['DB']);
}
