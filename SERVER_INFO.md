# 🖥️ Server Information - Quick Reference

## 🔐 Server Access
```
SSH: ssh root@45.93.139.14
Domain: test.taksoride.com
```

## 💾 Database Credentials
```
Database Name: taksoride
Database User: taksoride
Database Password: Tabarka2016@@GG00
Host: localhost
```

## 📁 File Locations
```
Web Root: /var/www/html/test.taksoride.com
Public Directory: /var/www/html/test.taksoride.com/server/public
Config File: /var/www/html/test.taksoride.com/server/drop-files/config/db_config.php
Apache Config: /etc/apache2/sites-available/test.taksoride.com.conf
```

## 🌐 URLs
```
Website: https://test.taksoride.com
Admin Panel: https://test.taksoride.com/server/public/admin/
Rider API: https://test.taksoride.com/server/public/ajax_2_2_0.php
Driver API: https://test.taksoride.com/server/public/ajaxdriver_2_2_0.php
Admin API: https://test.taksoride.com/server/public/ajaxsd.php
```

## 👤 Default Admin Login
```
Username: admin
Password: admin123
⚠️ CHANGE THIS IMMEDIATELY AFTER FIRST LOGIN!
```

## 🚀 Quick Deployment Commands

### First Time Deployment
```bash
# 1. Connect to server
ssh root@45.93.139.14

# 2. Upload and run deployment script
cd /root
wget https://raw.githubusercontent.com/Anonyme99910/taksoride/main/deploy.sh
chmod +x deploy.sh
./deploy.sh --init
```

### Update Deployment
```bash
ssh root@45.93.139.14
cd /var/www/html/test.taksoride.com
git pull origin main
systemctl restart apache2
```

## 📊 Useful Commands

### Check Status
```bash
# Apache status
systemctl status apache2

# MySQL status
systemctl status mysql

# Check disk space
df -h

# Check memory
free -m
```

### View Logs
```bash
# Apache error log
tail -f /var/log/apache2/test.taksoride.com-error.log

# Apache access log
tail -f /var/log/apache2/test.taksoride.com-access.log

# MySQL error log
tail -f /var/log/mysql/error.log
```

### Database Operations
```bash
# Connect to database
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride

# Backup database
mysqldump -u taksoride -p'Tabarka2016@@GG00' taksoride > backup_$(date +%Y%m%d).sql

# Import database
mysql -u taksoride -p'Tabarka2016@@GG00' taksoride < backup.sql
```

### File Permissions
```bash
# Fix ownership
chown -R www-data:www-data /var/www/html/test.taksoride.com

# Fix permissions
find /var/www/html/test.taksoride.com -type d -exec chmod 755 {} \;
find /var/www/html/test.taksoride.com -type f -exec chmod 644 {} \;

# Make uploads writable
chmod -R 777 /var/www/html/test.taksoride.com/server/public/img/uploads
```

## 🔒 Security Checklist
- [ ] Change default admin password
- [ ] Configure firewall (UFW)
- [ ] Enable fail2ban
- [ ] Set up SSL certificate
- [ ] Regular backups configured
- [ ] Keep system updated

## 📱 Mobile App Configuration
Update these in your mobile apps:
```
API Base URL: https://test.taksoride.com/server/public/
```

## ⚠️ Important Notes
- **DO NOT touch gt-academy.com or any other domains on this server**
- Always backup before making changes
- Test changes in staging before production
- Keep database credentials secure
- Monitor logs regularly

## 🆘 Emergency Contacts
- Server IP: 45.93.139.14
- Domain: test.taksoride.com
- GitHub: https://github.com/Anonyme99910/taksoride

---

**Last Updated:** $(date)
