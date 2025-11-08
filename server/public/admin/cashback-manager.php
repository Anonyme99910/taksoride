<?php
include("../../drop-files/lib/session_init.php");
session_start();
include("../../drop-files/lib/common.php");
include "../../drop-files/config/db.php";

if(isset($_SESSION['expired_session'])){
    header("location: ".SITE_URL."login.php?timeout=1");
    exit;
}

if(!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1)){
    header("location: ".SITE_URL."login.php");
    exit;
}

if($_SESSION['account_type'] != 2 && $_SESSION['account_type'] != 3){
    header("location: ".SITE_URL."access-denied.php");
    exit;
}

$GLOBALS['admin_template']['page_title'] = "<i class='fa fa-gift'></i> Cashback Manager";
$GLOBALS['admin_template']['active_menu'] = "cashback";

$success_message = '';
$error_message = '';

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if(isset($_POST['action']) && $_POST['action'] == 'edit_rule') {
        $rule_id = intval($_POST['rule_id']);
        $rule_name = mysqli_real_escape_string($GLOBALS['DB'], $_POST['rule_name']);
        $rule_type = mysqli_real_escape_string($GLOBALS['DB'], $_POST['rule_type']);
        $cashback_value = floatval($_POST['cashback_value']);
        $min_ride_amount = floatval($_POST['min_ride_amount']);
        $max_cashback = !empty($_POST['max_cashback']) ? floatval($_POST['max_cashback']) : 'NULL';
        $applicable_to = mysqli_real_escape_string($GLOBALS['DB'], $_POST['applicable_to']);
        $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
        $start_date = !empty($_POST['start_date']) ? "'" . mysqli_real_escape_string($GLOBALS['DB'], $_POST['start_date']) . "'" : 'NULL';
        $end_date = !empty($_POST['end_date']) ? "'" . mysqli_real_escape_string($GLOBALS['DB'], $_POST['end_date']) . "'" : 'NULL';
        
        $query = sprintf(
            "UPDATE %stbl_cashback_rules 
            SET rule_name = '%s', rule_type = '%s', cashback_value = %.2f, 
                min_ride_amount = %.2f, max_cashback = %s, applicable_to = '%s', 
                usage_limit = %s, start_date = %s, end_date = %s
            WHERE rule_id = %d",
            DB_TBL_PREFIX,
            $rule_name,
            $rule_type,
            $cashback_value,
            $min_ride_amount,
            $max_cashback,
            $applicable_to,
            $usage_limit,
            $start_date,
            $end_date,
            $rule_id
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Cashback rule updated successfully!";
        } else {
            $error_message = "Error updating rule: " . mysqli_error($GLOBALS['DB']);
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'add_rule') {
        $rule_name = mysqli_real_escape_string($GLOBALS['DB'], $_POST['rule_name']);
        $rule_type = mysqli_real_escape_string($GLOBALS['DB'], $_POST['rule_type']);
        $cashback_value = floatval($_POST['cashback_value']);
        $min_ride_amount = floatval($_POST['min_ride_amount']);
        $max_cashback = !empty($_POST['max_cashback']) ? floatval($_POST['max_cashback']) : 'NULL';
        $applicable_to = mysqli_real_escape_string($GLOBALS['DB'], $_POST['applicable_to']);
        $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
        $start_date = !empty($_POST['start_date']) ? "'" . mysqli_real_escape_string($GLOBALS['DB'], $_POST['start_date']) . "'" : 'NULL';
        $end_date = !empty($_POST['end_date']) ? "'" . mysqli_real_escape_string($GLOBALS['DB'], $_POST['end_date']) . "'" : 'NULL';
        
        $query = sprintf(
            "INSERT INTO %stbl_cashback_rules 
            (rule_name, rule_type, cashback_value, min_ride_amount, max_cashback, applicable_to, usage_limit, start_date, end_date, is_active)
            VALUES ('%s', '%s', %.2f, %.2f, %s, '%s', %s, %s, %s, 1)",
            DB_TBL_PREFIX,
            $rule_name,
            $rule_type,
            $cashback_value,
            $min_ride_amount,
            $max_cashback,
            $applicable_to,
            $usage_limit,
            $start_date,
            $end_date
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Cashback rule added successfully!";
        } else {
            $error_message = "Error adding rule: " . mysqli_error($GLOBALS['DB']);
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'toggle_rule') {
        $rule_id = intval($_POST['rule_id']);
        $is_active = intval($_POST['is_active']);
        
        $query = sprintf(
            "UPDATE %stbl_cashback_rules SET is_active = %d WHERE rule_id = %d",
            DB_TBL_PREFIX,
            $is_active,
            $rule_id
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Rule status updated successfully!";
        } else {
            $error_message = "Error updating status: " . mysqli_error($GLOBALS['DB']);
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'manual_cashback') {
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $description = mysqli_real_escape_string($GLOBALS['DB'], $_POST['description']);
        $expiry_days = intval($_POST['expiry_days']);
        
        $expiry_date = date('Y-m-d H:i:s', strtotime("+{$expiry_days} days"));
        
        // Insert cashback transaction
        $query = sprintf(
            "INSERT INTO %stbl_cashback_transactions 
            (user_id, cashback_amount, status, expiry_date, credited_at, description)
            VALUES (%d, %.2f, 'credited', '%s', NOW(), '%s')",
            DB_TBL_PREFIX,
            $user_id,
            $amount,
            $expiry_date,
            $description
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            // Update user balance
            $update_query = sprintf(
                "UPDATE %stbl_users 
                SET cashback_balance = cashback_balance + %.2f,
                    total_cashback_earned = total_cashback_earned + %.2f
                WHERE user_id = %d",
                DB_TBL_PREFIX,
                $amount,
                $amount,
                $user_id
            );
            mysqli_query($GLOBALS['DB'], $update_query);
            
            $success_message = "Manual cashback credited successfully!";
        } else {
            $error_message = "Error crediting cashback: " . mysqli_error($GLOBALS['DB']);
        }
    }
}

// Get all cashback rules
$rules = [];
$query = "SELECT * FROM " . DB_TBL_PREFIX . "tbl_cashback_rules ORDER BY is_active DESC, cashback_value DESC";
if($result = mysqli_query($GLOBALS['DB'], $query)) {
    while($row = mysqli_fetch_assoc($result)) {
        $rules[] = $row;
    }
    mysqli_free_result($result);
}

// Get cashback statistics
$stats = [];
$query = sprintf(
    "SELECT 
        COUNT(DISTINCT user_id) as total_users_with_cashback,
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'credited' THEN cashback_amount ELSE 0 END) as total_cashback_given,
        SUM(CASE WHEN status = 'pending' THEN cashback_amount ELSE 0 END) as pending_cashback,
        AVG(cashback_amount) as avg_cashback_amount
    FROM %stbl_cashback_transactions",
    DB_TBL_PREFIX
);

if($result = mysqli_query($GLOBALS['DB'], $query)) {
    $stats = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
}

// Get recent cashback transactions
$recent_transactions = [];
$query = sprintf(
    "SELECT ct.*, CONCAT(u.firstname, ' ', u.lastname) as user_name, u.phone
    FROM %stbl_cashback_transactions ct
    JOIN %stbl_users u ON ct.user_id = u.user_id
    ORDER BY ct.created_at DESC
    LIMIT 50",
    DB_TBL_PREFIX,
    DB_TBL_PREFIX
);

if($result = mysqli_query($GLOBALS['DB'], $query)) {
    while($row = mysqli_fetch_assoc($result)) {
        $recent_transactions[] = $row;
    }
    mysqli_free_result($result);
}

// Get top cashback users
$top_users = [];
$query = sprintf(
    "SELECT u.user_id, CONCAT(u.firstname, ' ', u.lastname) as user_name, u.phone,
           u.cashback_balance, u.total_cashback_earned, u.total_cashback_used
    FROM %stbl_users u
    WHERE u.total_cashback_earned > 0
    ORDER BY u.total_cashback_earned DESC
    LIMIT 10",
    DB_TBL_PREFIX
);

if($result = mysqli_query($GLOBALS['DB'], $query)) {
    while($row = mysqli_fetch_assoc($result)) {
        $top_users[] = $row;
    }
    mysqli_free_result($result);
}

ob_start();
?>

<style>
.rule-card {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s;
}
.rule-card:hover {
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.rule-card.inactive {
    opacity: 0.6;
    background-color: #f5f5f5;
}
.rule-badge {
    font-size: 24px;
    font-weight: bold;
    color: #f39c12;
}
.transaction-row {
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
}
.transaction-row:hover {
    background-color: #f9f9f9;
}
.status-credited { color: #00a65a; }
.status-pending { color: #f39c12; }
.status-expired { color: #dd4b39; }
.status-cancelled { color: #999; }
</style>

<div class="row">
    <div class="col-md-12">
        
        <?php if($success_message): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check"></i> <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if($error_message): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Users with Cashback</span>
                        <span class="info-box-number"><?php echo $stats['total_users_with_cashback'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-gift"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cashback Given</span>
                        <span class="info-box-number">$<?php echo number_format($stats['total_cashback_given'] ?? 0, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Cashback</span>
                        <span class="info-box-number">$<?php echo number_format($stats['pending_cashback'] ?? 0, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Avg Cashback</span>
                        <span class="info-box-number">$<?php echo number_format($stats['avg_cashback_amount'] ?? 0, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#rules" data-toggle="tab"><i class="fa fa-list"></i> Cashback Rules</a></li>
                <li><a href="#transactions" data-toggle="tab"><i class="fa fa-history"></i> Recent Transactions</a></li>
                <li><a href="#topusers" data-toggle="tab"><i class="fa fa-trophy"></i> Top Users</a></li>
                <li><a href="#manual" data-toggle="tab"><i class="fa fa-plus-circle"></i> Manual Cashback</a></li>
            </ul>
            
            <div class="tab-content">
                
                <!-- Cashback Rules Tab -->
                <div class="tab-pane active" id="rules">
                    <div class="box-header">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addRuleModal">
                            <i class="fa fa-plus"></i> Add New Rule
                        </button>
                    </div>
                    
                    <div class="row" style="margin-top: 15px;">
                        <?php foreach($rules as $rule): ?>
                        <div class="col-md-6">
                            <div class="rule-card <?php echo $rule['is_active'] ? '' : 'inactive'; ?>">
                                <div class="row">
                                    <div class="col-xs-8">
                                        <h4 style="margin: 0 0 10px 0;">
                                            <?php echo htmlspecialchars($rule['rule_name']); ?>
                                            <?php if(!$rule['is_active']): ?>
                                            <span class="label label-default">Inactive</span>
                                            <?php endif; ?>
                                        </h4>
                                    </div>
                                    <div class="col-xs-4 text-right">
                                        <div class="rule-badge">
                                            <?php if($rule['rule_type'] == 'percentage'): ?>
                                                <?php echo $rule['cashback_value']; ?>%
                                            <?php else: ?>
                                                $<?php echo number_format($rule['cashback_value'], 2); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="margin: 10px 0;">
                                    <span class="label label-info"><?php echo ucfirst($rule['rule_type']); ?></span>
                                    <span class="label label-primary"><?php echo ucfirst(str_replace('_', ' ', $rule['applicable_to'])); ?></span>
                                    <?php if($rule['min_ride_amount'] > 0): ?>
                                    <span class="label label-warning">Min: $<?php echo number_format($rule['min_ride_amount'], 2); ?></span>
                                    <?php endif; ?>
                                    <?php if($rule['max_cashback']): ?>
                                    <span class="label label-danger">Max: $<?php echo number_format($rule['max_cashback'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($rule['start_date'] || $rule['end_date']): ?>
                                <div style="margin: 10px 0; font-size: 12px; color: #666;">
                                    <?php if($rule['start_date']): ?>
                                    <i class="fa fa-calendar"></i> From: <?php echo date('M d, Y', strtotime($rule['start_date'])); ?>
                                    <?php endif; ?>
                                    <?php if($rule['end_date']): ?>
                                    <i class="fa fa-calendar"></i> To: <?php echo date('M d, Y', strtotime($rule['end_date'])); ?>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($rule['usage_limit']): ?>
                                <div style="margin: 5px 0; font-size: 12px; color: #666;">
                                    <i class="fa fa-repeat"></i> Usage limit: <?php echo $rule['usage_limit']; ?> per user
                                </div>
                                <?php endif; ?>
                                
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e0e0e0;">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editRule(<?php echo htmlspecialchars(json_encode($rule)); ?>)">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_rule">
                                        <input type="hidden" name="rule_id" value="<?php echo $rule['rule_id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $rule['is_active'] ? 0 : 1; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $rule['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                            <i class="fa fa-<?php echo $rule['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            <?php echo $rule['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Recent Transactions Tab -->
                <div class="tab-pane" id="transactions">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Ride Amount</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_transactions as $trans): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', strtotime($trans['created_at'])); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($trans['user_name']); ?><br>
                                        <small class="text-muted"><?php echo $trans['phone']; ?></small>
                                    </td>
                                    <td><strong>$<?php echo number_format($trans['cashback_amount'], 2); ?></strong></td>
                                    <td><?php echo $trans['ride_amount'] ? '$' . number_format($trans['ride_amount'], 2) : '-'; ?></td>
                                    <td><span class="status-<?php echo $trans['status']; ?>"><?php echo ucfirst($trans['status']); ?></span></td>
                                    <td><?php echo $trans['expiry_date'] ? date('M d, Y', strtotime($trans['expiry_date'])) : '-'; ?></td>
                                    <td><small><?php echo htmlspecialchars($trans['description']); ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Top Users Tab -->
                <div class="tab-pane" id="topusers">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Phone</th>
                                    <th>Current Balance</th>
                                    <th>Total Earned</th>
                                    <th>Total Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach($top_users as $user): ?>
                                <tr>
                                    <td><?php echo $rank++; ?></td>
                                    <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                    <td><?php echo $user['phone']; ?></td>
                                    <td><strong class="text-success">$<?php echo number_format($user['cashback_balance'], 2); ?></strong></td>
                                    <td>$<?php echo number_format($user['total_cashback_earned'], 2); ?></td>
                                    <td>$<?php echo number_format($user['total_cashback_used'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Manual Cashback Tab -->
                <div class="tab-pane" id="manual">
                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Use this to manually credit cashback to users for special promotions, compensations, or rewards.
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="manual_cashback">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User ID *</label>
                                        <input type="number" name="user_id" class="form-control" required>
                                        <small class="text-muted">Enter the user ID to credit cashback</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount ($) *</label>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Description *</label>
                                <input type="text" name="description" class="form-control" required placeholder="e.g., Compensation for delayed ride">
                            </div>
                            
                            <div class="form-group">
                                <label>Expiry (Days) *</label>
                                <input type="number" name="expiry_days" class="form-control" value="30" min="1" required>
                                <small class="text-muted">Number of days before cashback expires</small>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-gift"></i> Credit Cashback
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<!-- Add Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_rule">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add New Cashback Rule</h4>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rule Name *</label>
                                <input type="text" name="rule_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rule Type *</label>
                                <select name="rule_type" class="form-control" required>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cashback Value *</label>
                                <input type="number" name="cashback_value" class="form-control" step="0.01" min="0" required>
                                <small class="text-muted">% or $ depending on type</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min Ride Amount ($)</label>
                                <input type="number" name="min_ride_amount" class="form-control" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Cashback ($)</label>
                                <input type="number" name="max_cashback" class="form-control" step="0.01" min="0">
                                <small class="text-muted">Leave empty for no limit</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Applicable To *</label>
                                <select name="applicable_to" class="form-control" required>
                                    <option value="all">All Users</option>
                                    <option value="new_users">New Users Only</option>
                                    <option value="regular_users">Regular Users</option>
                                    <option value="vip_users">VIP Users</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Usage Limit per User</label>
                                <input type="number" name="usage_limit" class="form-control" min="1">
                                <small class="text-muted">Leave empty for unlimited</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="datetime-local" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Add Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rule Modal -->
<div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_rule">
                <input type="hidden" name="rule_id" id="edit_rule_id">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Cashback Rule</h4>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rule Name *</label>
                                <input type="text" name="rule_name" id="edit_rule_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rule Type *</label>
                                <select name="rule_type" id="edit_rule_type" class="form-control" required>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cashback Value *</label>
                                <input type="number" name="cashback_value" id="edit_cashback_value" class="form-control" step="0.01" min="0" required>
                                <small class="text-muted">% or $ depending on type</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min Ride Amount ($)</label>
                                <input type="number" name="min_ride_amount" id="edit_min_ride_amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Cashback ($)</label>
                                <input type="number" name="max_cashback" id="edit_max_cashback" class="form-control" step="0.01" min="0">
                                <small class="text-muted">Leave empty for no limit</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Applicable To *</label>
                                <select name="applicable_to" id="edit_applicable_to" class="form-control" required>
                                    <option value="all">All Users</option>
                                    <option value="new_users">New Users Only</option>
                                    <option value="regular_users">Regular Users</option>
                                    <option value="vip_users">VIP Users</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Usage Limit per User</label>
                                <input type="number" name="usage_limit" id="edit_usage_limit" class="form-control" min="1">
                                <small class="text-muted">Leave empty for unlimited</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="datetime-local" name="start_date" id="edit_start_date" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="datetime-local" name="end_date" id="edit_end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRule(rule) {
    // Populate form fields
    $('#edit_rule_id').val(rule.rule_id);
    $('#edit_rule_name').val(rule.rule_name);
    $('#edit_rule_type').val(rule.rule_type);
    $('#edit_cashback_value').val(rule.cashback_value);
    $('#edit_min_ride_amount').val(rule.min_ride_amount);
    $('#edit_max_cashback').val(rule.max_cashback || '');
    $('#edit_applicable_to').val(rule.applicable_to);
    $('#edit_usage_limit').val(rule.usage_limit || '');
    
    // Handle dates
    if(rule.start_date) {
        var startDate = new Date(rule.start_date);
        $('#edit_start_date').val(startDate.toISOString().slice(0, 16));
    } else {
        $('#edit_start_date').val('');
    }
    
    if(rule.end_date) {
        var endDate = new Date(rule.end_date);
        $('#edit_end_date').val(endDate.toISOString().slice(0, 16));
    } else {
        $('#edit_end_date').val('');
    }
    
    // Show modal
    $('#editRuleModal').modal('show');
}
</script>

<?php
$pageContent = ob_get_clean();
$GLOBALS['admin_template']['page_content'] = $pageContent;
include "../../drop-files/templates/admin/admin-interface.php";
?>
