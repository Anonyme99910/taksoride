# TaksoRide Installation Guide

## Prerequisites
- XAMPP installed and running
- Apache and MySQL services started

## Installation Steps

### 1. Database Setup

#### Option A: Using phpMyAdmin (Recommended)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "New" in the left sidebar to create a new database
3. Enter database name: `hamma`
4. Select collation: `utf8_unicode_ci`
5. Click "Create"
6. Select the `hamma` database from the left sidebar
7. Click on "Import" tab
8. Click "Choose File" and select: `C:\xampp\htdocs\hamma\server\drop-files\install\database_setup.sql`
9. Click "Go" at the bottom to import
10. Wait for the success message

#### Option B: Using Command Line
```bash
# Open Command Prompt and navigate to MySQL bin directory
cd C:\xampp\mysql\bin

# Create database and import schema
mysql -u root -p
CREATE DATABASE hamma CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE hamma;
source C:/xampp/htdocs/hamma/server/drop-files/install/database_setup.sql;
exit;
```

### 2. Access the Application

1. **Frontend URL:** `http://localhost/hamma/server/public/`
2. **Admin Login:** `http://localhost/hamma/server/public/login.php`

### 3. Default Admin Credentials

```
Email: admin@taksoride.com
Password: admin123
Account Type: Admin
```

### 4. Configure Services (Important!)

After logging in as admin:

1. Go to: **Admin Panel → Service Activation**
2. Configure the following essential services:

#### Google Maps API (Required for core functionality)
```json
{
  "api_key": "YOUR_GOOGLE_MAPS_API_KEY",
  "enabled": true
}
```
- Get your key from: https://console.cloud.google.com/
- Enable: Maps JavaScript API, Geocoding API, Directions API, Places API

#### Firebase Cloud Messaging (Required for push notifications)
```json
{
  "server_key": "YOUR_FCM_SERVER_KEY",
  "sender_id": "YOUR_SENDER_ID",
  "project_id": "YOUR_PROJECT_ID",
  "credentials_json": "{...}"
}
```
- Get from: https://console.firebase.google.com/

#### PubNub (Required for real-time tracking)
```json
{
  "publish_key": "YOUR_PUBLISH_KEY",
  "subscribe_key": "YOUR_SUBSCRIBE_KEY",
  "secret_key": "YOUR_SECRET_KEY"
}
```
- Get from: https://www.pubnub.com/

#### SMTP Email (Required for notifications)
```json
{
  "host": "smtp.gmail.com",
  "port": "587",
  "username": "your-email@gmail.com",
  "password": "your-app-password",
  "from_email": "noreply@taksoride.com",
  "from_name": "TaksoRide"
}
```

### 5. Start the Cron Job (Background Tasks)

The cron job handles automatic driver allocation and booking management.

#### Windows (Using Task Scheduler)
1. Open Task Scheduler
2. Create Basic Task
3. Name: "TaksoRide Cron"
4. Trigger: At startup
5. Action: Start a program
6. Program: `C:\xampp\php\php.exe`
7. Arguments: `C:\xampp\htdocs\hamma\server\public\cron.php`

#### Manual Start (For Testing)
```bash
cd C:\xampp\htdocs\hamma\server\public
C:\xampp\php\php.exe cron.php
```

### 6. Verify Installation

1. **Check Database Connection:**
   - Visit: `http://localhost/hamma/server/public/`
   - You should see the homepage without errors

2. **Test Admin Login:**
   - Go to: `http://localhost/hamma/server/public/login.php`
   - Login with admin credentials
   - You should see the admin dashboard

3. **Check Service Activation:**
   - Navigate to: Service Activation page
   - Verify all services are listed

## Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP Control Panel
- Check database name is `hamma` in phpMyAdmin
- Verify credentials in: `server/drop-files/config/db.php`

### Page Not Found (404)
- Check Apache is running
- Verify .htaccess file exists in `server/public/`
- Enable mod_rewrite in Apache config

### Cannot Login
- Verify database was imported successfully
- Check `cab_tbl_users` table has the admin user
- Clear browser cache and cookies

### Redis Connection Error
- Redis is optional for basic functionality
- Install Redis for Windows if needed
- Or disable Redis-dependent features

## Security Recommendations

1. **Change Default Password:**
   - Login as admin
   - Go to Profile Settings
   - Change password immediately

2. **Update Database Password:**
   - Set a password for MySQL root user
   - Update in `server/drop-files/config/db.php`

3. **Secure API Keys:**
   - Never commit API keys to version control
   - Use environment variables in production

4. **Enable HTTPS:**
   - Get SSL certificate
   - Update SITE_URL in `server/drop-files/lib/common.php`
   - Set `session.cookie_secure` to 1

## Next Steps

1. Configure payment gateways (Stripe, PayPal, etc.)
2. Set up routes/cities for your service area
3. Add ride types (Economy, Premium, etc.)
4. Configure tariffs and pricing
5. Test the complete booking flow
6. Set up mobile apps (if available)

## Support

For issues or questions:
- Check the admin dashboard for system status
- Review error logs in: `server/public/cron.log`
- Check PHP error logs in XAMPP

## License

TaksoRide - Open Source Version
Commercial license removed and rebranded from DropTaxi
