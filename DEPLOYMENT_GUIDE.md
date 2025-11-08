# 🚀 Server Deployment Guide

## Server Information
- **Server IP:** 45.93.139.14
- **Domain:** test.taksoride.com
- **Database Name:** taksoride
- **Database Password:** Tabarka2016@@GG00
- **Access:** SSH root access

---

## 📋 Pre-Deployment Checklist

### 1. Server Requirements
- [x] PHP 7.4 or higher
- [x] MySQL/MariaDB
- [x] Apache/Nginx web server
- [x] SSL certificate for HTTPS
- [x] Git installed
- [x] Composer (for PHP dependencies)

### 2. Database Setup
- [x] Database created: `taksoride`
- [x] Database user configured
- [x] Password set: `Tabarka2016@@GG00`

---

## 🔧 Deployment Steps

### Step 1: Connect to Server
```bash
ssh root@45.93.139.14
```

### Step 2: Navigate to Web Root
```bash
cd /var/www/html
# or
cd /home/username/public_html
```

### Step 3: Clone Repository
```bash
git clone https://github.com/Anonyme99910/taksoride.git test.taksoride.com
cd test.taksoride.com
```

### Step 4: Set Permissions
```bash
# Set ownership
chown -R www-data:www-data /var/www/html/test.taksoride.com
# or for cPanel
chown -R username:username /home/username/public_html/test.taksoride.com

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make specific directories writable
chmod -R 777 server/public/img/uploads
chmod -R 777 server/public/img/drivers
chmod -R 777 server/public/img/users
```

### Step 5: Configure Database Connection
```bash
cd server/drop-files/config
nano db_config.php
```

**Update with:**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'taksoride');
define('DB_PASS', 'Tabarka2016@@GG00');
define('DB_NAME', 'taksoride');
define('DB_TBL_PREFIX', 'cab_');
?>
```

### Step 6: Import Database
```bash
# Navigate to install directory
cd /var/www/html/test.taksoride.com/server/drop-files/install

# Import main database
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < database_setup.sql

# Import settings table
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < create_settings_table.sql

# Import subscription system
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < subscription_system.sql

# Import commission fix
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < fix_commission_conflict.sql
```

### Step 7: Configure Site URL
```bash
cd /var/www/html/test.taksoride.com/server/drop-files/lib
nano common.php
```

**Update:**
```php
define('SITE_URL', 'https://test.taksoride.com/server/public/');
define('WEBSITE_NAME', 'TaksoRide');
```

### Step 8: Configure Apache Virtual Host
```bash
nano /etc/apache2/sites-available/test.taksoride.com.conf
```

**Add:**
```apache
<VirtualHost *:80>
    ServerName test.taksoride.com
    ServerAlias www.test.taksoride.com
    DocumentRoot /var/www/html/test.taksoride.com/server/public
    
    <Directory /var/www/html/test.taksoride.com/server/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/test.taksoride.com-error.log
    CustomLog ${APACHE_LOG_DIR}/test.taksoride.com-access.log combined
</VirtualHost>
```

**Enable site:**
```bash
a2ensite test.taksoride.com.conf
systemctl reload apache2
```

### Step 9: Install SSL Certificate
```bash
# Install Certbot
apt-get update
apt-get install certbot python3-certbot-apache

# Get SSL certificate
certbot --apache -d test.taksoride.com -d www.test.taksoride.com
```

### Step 10: Configure DNS
**Point domain to server:**
- A Record: `test.taksoride.com` → `45.93.139.14`
- A Record: `www.test.taksoride.com` → `45.93.139.14`

---

## 🔐 Security Configuration

### 1. Secure Database Config
```bash
chmod 600 server/drop-files/config/db_config.php
```

### 2. Update .htaccess
```bash
cd /var/www/html/test.taksoride.com/server/public
nano .htaccess
```

**Ensure it has:**
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 3. Disable Directory Listing
```apache
Options -Indexes
```

---

## 📱 Post-Deployment Configuration

### 1. Create Admin Account
```bash
# Access MySQL
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride

# Create admin user
INSERT INTO cab_tbl_admin_logins (admin_username, admin_password, account_type) 
VALUES ('admin', MD5('admin123'), 3);
```

### 2. Configure API Keys
Visit: `https://test.taksoride.com/server/public/admin/settings.php`

**Set:**
- Google Maps API Key
- Firebase Server Key
- PubNub Keys
- Payment Gateway Keys (Stripe, PayPal, etc.)

### 3. Test System
- **Admin Panel:** https://test.taksoride.com/server/public/admin/
- **Login:** admin / admin123
- **Test:** Create subscription plan, cashback rule

---

## 🧪 Testing Checklist

### Admin Panel
- [ ] Login works
- [ ] Dashboard loads
- [ ] Subscription plans visible
- [ ] Cashback manager accessible
- [ ] Can edit plans/rules

### API Endpoints
- [ ] `ajax_2_2_0.php` (rider API)
- [ ] `ajaxdriver_2_2_0.php` (driver API)
- [ ] `ajaxsd.php` (admin API)

### Database
- [ ] All tables created
- [ ] Stored procedures working
- [ ] Views accessible
- [ ] Foreign keys intact

### Features
- [ ] Subscription system working
- [ ] Cashback calculation correct
- [ ] Commission calculation accurate
- [ ] Wallet transfers working

---

## 🔄 Update Deployment

### Pull Latest Changes
```bash
cd /var/www/html/test.taksoride.com
git pull origin main
```

### Update Database
```bash
# If schema changes
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < new_migration.sql
```

### Clear Cache
```bash
# Clear PHP opcache
systemctl restart apache2

# Clear application cache if any
rm -rf cache/*
```

---

## 🐛 Troubleshooting

### Issue: Database Connection Failed
```bash
# Check MySQL is running
systemctl status mysql

# Test connection
mysql -u taksoride -p'Tabarka2016@@GG00' -h localhost taksoride
```

### Issue: Permission Denied
```bash
# Fix ownership
chown -R www-data:www-data /var/www/html/test.taksoride.com

# Fix permissions
chmod -R 755 /var/www/html/test.taksoride.com
```

### Issue: 500 Internal Server Error
```bash
# Check Apache error log
tail -f /var/log/apache2/test.taksoride.com-error.log

# Check PHP errors
tail -f /var/log/apache2/error.log
```

### Issue: SSL Not Working
```bash
# Renew certificate
certbot renew

# Force HTTPS
# Add to .htaccess (already included above)
```

---

## 📊 Monitoring

### Check Logs
```bash
# Apache access log
tail -f /var/log/apache2/test.taksoride.com-access.log

# Apache error log
tail -f /var/log/apache2/test.taksoride.com-error.log

# MySQL slow query log
tail -f /var/log/mysql/slow-query.log
```

### Monitor Performance
```bash
# Check disk space
df -h

# Check memory
free -m

# Check CPU
top

# Check MySQL processes
mysqladmin -u taksoride -p'Tabarka2016@@GG00' processlist
```

---

## 🔒 Backup Strategy

### Database Backup
```bash
# Create backup script
nano /root/backup_taksoride.sh
```

**Script:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u taksoride -p'Tabarka2016@@GG00' taksoride > $BACKUP_DIR/taksoride_$DATE.sql

# Compress
gzip $BACKUP_DIR/taksoride_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "taksoride_*.sql.gz" -mtime +7 -delete

echo "Backup completed: taksoride_$DATE.sql.gz"
```

**Make executable:**
```bash
chmod +x /root/backup_taksoride.sh
```

**Schedule daily backup:**
```bash
crontab -e
# Add line:
0 2 * * * /root/backup_taksoride.sh
```

### Files Backup
```bash
# Backup uploads
tar -czf /root/backups/uploads_$(date +%Y%m%d).tar.gz /var/www/html/test.taksoride.com/server/public/img/uploads
```

---

## 🚀 Quick Deployment Script

Create this script for easy deployment:

```bash
nano /root/deploy_taksoride.sh
```

**Content:**
```bash
#!/bin/bash

echo "🚀 Starting TaksoRide Deployment..."

# Navigate to web root
cd /var/www/html

# Clone or pull repository
if [ -d "test.taksoride.com" ]; then
    echo "📥 Pulling latest changes..."
    cd test.taksoride.com
    git pull origin main
else
    echo "📦 Cloning repository..."
    git clone https://github.com/Anonyme99910/taksoride.git test.taksoride.com
    cd test.taksoride.com
fi

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 777 server/public/img/uploads
chmod -R 777 server/public/img/drivers
chmod -R 777 server/public/img/users

# Import database (only on first run)
if [ "$1" == "init" ]; then
    echo "💾 Importing database..."
    cd server/drop-files/install
    mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < database_setup.sql
    mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < create_settings_table.sql
    mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < subscription_system.sql
    mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < fix_commission_conflict.sql
    cd ../../..
fi

# Restart Apache
echo "🔄 Restarting Apache..."
systemctl restart apache2

echo "✅ Deployment completed!"
echo "🌐 Visit: https://test.taksoride.com/server/public/admin/"
```

**Make executable:**
```bash
chmod +x /root/deploy_taksoride.sh
```

**Run deployment:**
```bash
# First time (with database import)
/root/deploy_taksoride.sh init

# Updates only
/root/deploy_taksoride.sh
```

---

## 📝 Important Notes

⚠️ **Security:**
- Change default admin password immediately
- Keep database credentials secure
- Enable firewall (UFW)
- Regular security updates

🔧 **Configuration:**
- Update all API keys in admin settings
- Configure email settings for notifications
- Set up cron jobs for scheduled tasks

📱 **Mobile Apps:**
- Update API base URL in mobile apps
- Test all endpoints before release
- Configure push notifications

---

## ✅ Deployment Complete!

Your TaksoRide system should now be live at:
- **Website:** https://test.taksoride.com
- **Admin Panel:** https://test.taksoride.com/server/public/admin/
- **API Base:** https://test.taksoride.com/server/public/

**Default Admin Login:**
- Username: admin
- Password: admin123 (CHANGE THIS!)

---

**Need help? Check logs or contact support!** 🚀
