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

$GLOBALS['admin_template']['page_title'] = "<i class='fa fa-credit-card'></i> Subscription Plans";
$GLOBALS['admin_template']['active_menu'] = "subscriptions";

$success_message = '';
$error_message = '';

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if(isset($_POST['action']) && $_POST['action'] == 'edit_plan') {
        $plan_id = intval($_POST['plan_id']);
        $plan_name = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_name']);
        $plan_description = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_description']);
        $plan_type = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_type']);
        $price = floatval($_POST['price']);
        $commission_rate = floatval($_POST['commission_rate']);
        $priority_level = intval($_POST['priority_level']);
        $max_rides = !empty($_POST['max_rides']) ? intval($_POST['max_rides']) : 'NULL';
        
        // Build features array
        $features = [];
        if(!empty($_POST['features'])) {
            $features = array_filter(array_map('trim', explode("\n", $_POST['features'])));
        }
        $features_json = json_encode($features);
        
        $query = sprintf(
            "UPDATE %stbl_subscription_plans 
            SET plan_name = '%s', plan_description = '%s', plan_type = '%s', 
                price = %.2f, commission_rate = %.2f, features = '%s', 
                max_rides = %s, priority_level = %d
            WHERE plan_id = %d",
            DB_TBL_PREFIX,
            $plan_name,
            $plan_description,
            $plan_type,
            $price,
            $commission_rate,
            $features_json,
            $max_rides,
            $priority_level,
            $plan_id
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Subscription plan updated successfully!";
        } else {
            $error_message = "Error updating plan: " . mysqli_error($GLOBALS['DB']);
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'add_plan') {
        $plan_name = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_name']);
        $plan_description = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_description']);
        $plan_type = mysqli_real_escape_string($GLOBALS['DB'], $_POST['plan_type']);
        $price = floatval($_POST['price']);
        $commission_rate = floatval($_POST['commission_rate']);
        $priority_level = intval($_POST['priority_level']);
        $max_rides = !empty($_POST['max_rides']) ? intval($_POST['max_rides']) : 'NULL';
        
        // Build features array
        $features = [];
        if(!empty($_POST['features'])) {
            $features = array_filter(array_map('trim', explode("\n", $_POST['features'])));
        }
        $features_json = json_encode($features);
        
        $query = sprintf(
            "INSERT INTO %stbl_subscription_plans 
            (plan_name, plan_description, plan_type, price, commission_rate, features, max_rides, priority_level)
            VALUES ('%s', '%s', '%s', %.2f, %.2f, '%s', %s, %d)",
            DB_TBL_PREFIX,
            $plan_name,
            $plan_description,
            $plan_type,
            $price,
            $commission_rate,
            $features_json,
            $max_rides,
            $priority_level
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Subscription plan added successfully!";
        } else {
            $error_message = "Error adding plan: " . mysqli_error($GLOBALS['DB']);
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'toggle_status') {
        $plan_id = intval($_POST['plan_id']);
        $is_active = intval($_POST['is_active']);
        
        $query = sprintf(
            "UPDATE %stbl_subscription_plans SET is_active = %d WHERE plan_id = %d",
            DB_TBL_PREFIX,
            $is_active,
            $plan_id
        );
        
        if(mysqli_query($GLOBALS['DB'], $query)) {
            $success_message = "Plan status updated successfully!";
        } else {
            $error_message = "Error updating status: " . mysqli_error($GLOBALS['DB']);
        }
    }
}

// Get all plans
$plans = [];
$query = "SELECT * FROM " . DB_TBL_PREFIX . "tbl_subscription_plans ORDER BY price ASC";
if($result = mysqli_query($GLOBALS['DB'], $query)) {
    while($row = mysqli_fetch_assoc($result)) {
        $row['features'] = json_decode($row['features'], true);
        $plans[] = $row;
    }
    mysqli_free_result($result);
}

// Get statistics
$stats = [];
$query = sprintf(
    "SELECT 
        COUNT(DISTINCT ds.driver_id) as total_subscribed_drivers,
        COUNT(CASE WHEN ds.status = 'active' THEN 1 END) as active_subscriptions,
        SUM(CASE WHEN ds.status = 'active' THEN ds.amount_paid ELSE 0 END) as total_revenue,
        AVG(sp.commission_rate) as avg_commission_rate
    FROM %stbl_driver_subscriptions ds
    JOIN %stbl_subscription_plans sp ON ds.plan_id = sp.plan_id",
    DB_TBL_PREFIX,
    DB_TBL_PREFIX
);

if($result = mysqli_query($GLOBALS['DB'], $query)) {
    $stats = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
}

ob_start();
?>

<style>
.plan-card {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s;
}
.plan-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}
.plan-card.inactive {
    opacity: 0.6;
    background-color: #f5f5f5;
}
.plan-header {
    border-bottom: 2px solid #00a65a;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.plan-price {
    font-size: 32px;
    font-weight: bold;
    color: #00a65a;
}
.plan-commission {
    font-size: 24px;
    color: #f39c12;
}
.feature-list {
    list-style: none;
    padding: 0;
}
.feature-list li {
    padding: 5px 0;
    padding-left: 25px;
    position: relative;
}
.feature-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #00a65a;
    font-weight: bold;
}
.stats-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
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
                        <span class="info-box-text">Subscribed Drivers</span>
                        <span class="info-box-number"><?php echo $stats['total_subscribed_drivers'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Subscriptions</span>
                        <span class="info-box-number"><?php echo $stats['active_subscriptions'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-dollar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Revenue</span>
                        <span class="info-box-number">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Avg Commission</span>
                        <span class="info-box-number"><?php echo number_format($stats['avg_commission_rate'] ?? 0, 1); ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add New Plan Button -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Subscription Plans</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPlanModal">
                        <i class="fa fa-plus"></i> Add New Plan
                    </button>
                </div>
            </div>
            <div class="box-body">
                
                <div class="row">
                    <?php foreach($plans as $plan): ?>
                    <div class="col-md-6">
                        <div class="plan-card <?php echo $plan['is_active'] ? '' : 'inactive'; ?>">
                            <div class="plan-header">
                                <h3 style="margin: 0;">
                                    <?php echo htmlspecialchars($plan['plan_name']); ?>
                                    <?php if(!$plan['is_active']): ?>
                                    <span class="label label-default">Inactive</span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="plan-price">
                                        $<?php echo number_format($plan['price'], 2); ?>
                                        <small style="font-size: 14px; color: #666;">/<?php echo $plan['plan_type']; ?></small>
                                    </div>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <div class="plan-commission">
                                        <?php echo $plan['commission_rate']; ?>%
                                        <small style="font-size: 14px; color: #666; display: block;">Commission</small>
                                    </div>
                                </div>
                            </div>
                            
                            <p style="margin: 15px 0; color: #666;">
                                <?php echo htmlspecialchars($plan['plan_description']); ?>
                            </p>
                            
                            <ul class="feature-list">
                                <?php if($plan['features']): ?>
                                    <?php foreach($plan['features'] as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                            
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                <span class="label label-info">Priority: <?php echo $plan['priority_level']; ?></span>
                                <?php if($plan['max_rides']): ?>
                                <span class="label label-warning">Max Rides: <?php echo $plan['max_rides']; ?></span>
                                <?php else: ?>
                                <span class="label label-success">Unlimited Rides</span>
                                <?php endif; ?>
                                
                                <div class="pull-right">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editPlan(<?php echo htmlspecialchars(json_encode($plan)); ?>)">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $plan['is_active'] ? 0 : 1; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo $plan['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                            <i class="fa fa-<?php echo $plan['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            <?php echo $plan['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add_plan">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add New Subscription Plan</h4>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Name *</label>
                                <input type="text" name="plan_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Type *</label>
                                <select name="plan_type" class="form-control" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="plan_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Price ($) *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Commission Rate (%) *</label>
                                <input type="number" name="commission_rate" class="form-control" step="0.01" min="0" max="100" required>
                                <small class="text-muted">0 = No commission, 100 = Full commission</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority Level *</label>
                                <input type="number" name="priority_level" class="form-control" min="0" value="1" required>
                                <small class="text-muted">Higher = More priority</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Max Rides per Period</label>
                        <input type="number" name="max_rides" class="form-control" min="1">
                        <small class="text-muted">Leave empty for unlimited rides</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Features (one per line)</label>
                        <textarea name="features" class="form-control" rows="5" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_plan">
                <input type="hidden" name="plan_id" id="edit_plan_id">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Subscription Plan</h4>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Name *</label>
                                <input type="text" name="plan_name" id="edit_plan_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plan Type *</label>
                                <select name="plan_type" id="edit_plan_type" class="form-control" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="plan_description" id="edit_plan_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Price ($) *</label>
                                <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Commission Rate (%) *</label>
                                <input type="number" name="commission_rate" id="edit_commission_rate" class="form-control" step="0.01" min="0" max="100" required>
                                <small class="text-muted">0 = No commission, 100 = Full commission</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority Level *</label>
                                <input type="number" name="priority_level" id="edit_priority_level" class="form-control" min="0" required>
                                <small class="text-muted">Higher = More priority</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Max Rides per Period</label>
                        <input type="number" name="max_rides" id="edit_max_rides" class="form-control" min="1">
                        <small class="text-muted">Leave empty for unlimited rides</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Features (one per line)</label>
                        <textarea name="features" id="edit_features" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPlan(plan) {
    // Populate form fields
    $('#edit_plan_id').val(plan.plan_id);
    $('#edit_plan_name').val(plan.plan_name);
    $('#edit_plan_description').val(plan.plan_description);
    $('#edit_plan_type').val(plan.plan_type);
    $('#edit_price').val(plan.price);
    $('#edit_commission_rate').val(plan.commission_rate);
    $('#edit_priority_level').val(plan.priority_level);
    $('#edit_max_rides').val(plan.max_rides || '');
    
    // Handle features array
    if(plan.features && Array.isArray(plan.features)) {
        $('#edit_features').val(plan.features.join('\n'));
    } else {
        $('#edit_features').val('');
    }
    
    // Show modal
    $('#editPlanModal').modal('show');
}
</script>

<?php
$pageContent = ob_get_clean();
$GLOBALS['admin_template']['page_content'] = $pageContent;
include "../../drop-files/templates/admin/admin-interface.php";
?>
