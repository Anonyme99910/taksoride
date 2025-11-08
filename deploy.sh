#!/bin/bash

# TaksoRide Deployment Script
# Server: 45.93.139.14
# Domain: test.taksoride.com
# Database: taksoride

echo "🚀 TaksoRide Deployment Script"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
SERVER_IP="45.93.139.14"
DOMAIN="test.taksoride.com"
DB_NAME="taksoride"
DB_USER="taksoride"
DB_PASS="Tabarka2016@@GG00"
WEB_ROOT="/var/www/html"
REPO_URL="https://github.com/Anonyme99910/taksoride.git"

# Function to print colored output
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root (use sudo)"
    exit 1
fi

print_info "Starting deployment process..."
echo ""

# Step 1: Navigate to web root
print_info "Step 1: Navigating to web root..."
cd $WEB_ROOT || exit 1
print_success "In web root: $WEB_ROOT"
echo ""

# Step 2: Clone or update repository
if [ -d "$DOMAIN" ]; then
    print_info "Step 2: Updating existing repository..."
    cd $DOMAIN
    git pull origin main
    print_success "Repository updated"
else
    print_info "Step 2: Cloning repository..."
    git clone $REPO_URL $DOMAIN
    cd $DOMAIN
    print_success "Repository cloned"
fi
echo ""

# Step 3: Set permissions
print_info "Step 3: Setting permissions..."
chown -R www-data:www-data .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Create upload directories if they don't exist
mkdir -p server/public/img/uploads
mkdir -p server/public/img/drivers
mkdir -p server/public/img/users
mkdir -p server/public/img/cars

# Set writable permissions for upload directories
chmod -R 777 server/public/img/uploads
chmod -R 777 server/public/img/drivers
chmod -R 777 server/public/img/users
chmod -R 777 server/public/img/cars

print_success "Permissions set"
echo ""

# Step 4: Configure database connection
print_info "Step 4: Configuring database connection..."
cat > server/drop-files/config/db_config.php << EOF
<?php
define('DB_HOST', 'localhost');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASS');
define('DB_NAME', '$DB_NAME');
define('DB_TBL_PREFIX', 'cab_');
?>
EOF

chmod 600 server/drop-files/config/db_config.php
print_success "Database configuration created"
echo ""

# Step 5: Import database (only if --init flag is passed)
if [ "$1" == "--init" ]; then
    print_info "Step 5: Importing database..."
    
    # Check if database exists
    DB_EXISTS=$(mysql -u $DB_USER -p$DB_PASS -e "SHOW DATABASES LIKE '$DB_NAME';" | grep "$DB_NAME")
    
    if [ -z "$DB_EXISTS" ]; then
        print_info "Creating database..."
        mysql -u $DB_USER -p$DB_PASS -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        print_success "Database created"
    fi
    
    # Import SQL files
    cd server/drop-files/install
    
    if [ -f "database_setup.sql" ]; then
        mysql -u $DB_USER -p$DB_PASS $DB_NAME < database_setup.sql
        print_success "Main database imported"
    fi
    
    if [ -f "create_settings_table.sql" ]; then
        mysql -u $DB_USER -p$DB_PASS $DB_NAME < create_settings_table.sql
        print_success "Settings table created"
    fi
    
    if [ -f "subscription_system.sql" ]; then
        mysql -u $DB_USER -p$DB_PASS $DB_NAME < subscription_system.sql
        print_success "Subscription system imported"
    fi
    
    if [ -f "fix_commission_conflict.sql" ]; then
        mysql -u $DB_USER -p$DB_PASS $DB_NAME < fix_commission_conflict.sql
        print_success "Commission system configured"
    fi
    
    cd ../../..
    
    # Create default admin user
    print_info "Creating default admin user..."
    mysql -u $DB_USER -p$DB_PASS $DB_NAME -e "INSERT IGNORE INTO cab_tbl_admin_logins (admin_username, admin_password, account_type) VALUES ('admin', MD5('admin123'), 3);"
    print_success "Admin user created (username: admin, password: admin123)"
    echo ""
else
    print_info "Step 5: Skipping database import (use --init flag to import)"
    echo ""
fi

# Step 6: Configure site URL
print_info "Step 6: Configuring site URL..."
sed -i "s|define('SITE_URL', '.*')|define('SITE_URL', 'https://$DOMAIN/server/public/')|g" server/drop-files/lib/common.php
print_success "Site URL configured"
echo ""

# Step 7: Configure Apache Virtual Host
print_info "Step 7: Configuring Apache Virtual Host..."
cat > /etc/apache2/sites-available/$DOMAIN.conf << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $WEB_ROOT/$DOMAIN/server/public
    
    <Directory $WEB_ROOT/$DOMAIN/server/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/$DOMAIN-error.log
    CustomLog \${APACHE_LOG_DIR}/$DOMAIN-access.log combined
</VirtualHost>
EOF

# Enable site and required modules
a2enmod rewrite
a2ensite $DOMAIN.conf
print_success "Virtual host configured"
echo ""

# Step 8: Install SSL Certificate
print_info "Step 8: Installing SSL certificate..."
if command -v certbot &> /dev/null; then
    certbot --apache -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
    print_success "SSL certificate installed"
else
    print_error "Certbot not found. Install it with: apt-get install certbot python3-certbot-apache"
    print_info "You can install SSL later by running: certbot --apache -d $DOMAIN"
fi
echo ""

# Step 9: Restart Apache
print_info "Step 9: Restarting Apache..."
systemctl restart apache2
print_success "Apache restarted"
echo ""

# Step 10: Final checks
print_info "Step 10: Running final checks..."

# Check if Apache is running
if systemctl is-active --quiet apache2; then
    print_success "Apache is running"
else
    print_error "Apache is not running"
fi

# Check if MySQL is running
if systemctl is-active --quiet mysql; then
    print_success "MySQL is running"
else
    print_error "MySQL is not running"
fi

# Check database connection
if mysql -u $DB_USER -p$DB_PASS -e "USE $DB_NAME;" 2>/dev/null; then
    print_success "Database connection successful"
else
    print_error "Database connection failed"
fi

echo ""
echo "================================"
print_success "Deployment completed!"
echo "================================"
echo ""
echo "🌐 Your site is now available at:"
echo "   https://$DOMAIN/server/public/admin/"
echo ""
echo "🔐 Default Admin Login:"
echo "   Username: admin"
echo "   Password: admin123"
echo "   ⚠️  CHANGE THIS PASSWORD IMMEDIATELY!"
echo ""
echo "📝 Next Steps:"
echo "   1. Visit admin panel and change password"
echo "   2. Configure API keys in Settings"
echo "   3. Test subscription and cashback features"
echo "   4. Set up cron jobs for scheduled tasks"
echo ""
echo "📊 View logs:"
echo "   Apache Error: tail -f /var/log/apache2/$DOMAIN-error.log"
echo "   Apache Access: tail -f /var/log/apache2/$DOMAIN-access.log"
echo ""
print_info "For updates, run: $0"
print_info "For fresh install, run: $0 --init"
echo ""
