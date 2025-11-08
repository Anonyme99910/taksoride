# TaksoRide - Ride Hailing Platform

![TaksoRide](https://img.shields.io/badge/Version-2.2.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![License](https://img.shields.io/badge/License-Open%20Source-green)

**TaksoRide** is a comprehensive ride-hailing platform rebranded from DropTaxi, featuring real-time driver tracking, automated dispatch, multiple payment gateways, and a powerful admin panel.

## 🚀 Features

### Core Functionality
- ✅ **Real-time Driver Tracking** - Live location updates using PubNub
- ✅ **Automated Dispatch System** - Smart driver allocation algorithm
- ✅ **Multi-language Support** - Internationalization ready
- ✅ **Booking Management** - Schedule rides, instant bookings
- ✅ **Wallet System** - Digital wallet for riders and drivers
- ✅ **Referral Program** - Built-in referral system for growth
- ✅ **Rating & Reviews** - Two-way rating system
- ✅ **Reward Points** - Loyalty program for riders

### Payment Gateways
- Stripe
- PayPal
- Paystack
- Flutterwave
- Paytm
- PayTR
- Pesapal
- PhonePe
- Midtrans
- Payku
- Paymob
- VoguePay

### Admin Features
- 📊 Comprehensive Dashboard
- 👥 User & Driver Management
- 🚗 Vehicle & Route Management
- 💰 Transaction Monitoring
- 📈 Reports & Analytics
- 🎫 Coupon Management
- 🏢 Franchise System
- 🔑 **Service Activation Panel** (NEW!)

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Apache** with mod_rewrite enabled
- **Redis** (optional, for caching)
- **XAMPP** (recommended for Windows)

## 🛠️ Installation

### Quick Setup (5 minutes)

1. **Clone or extract** the project to XAMPP htdocs:
   ```
   C:\xampp\htdocs\hamma\
   ```

2. **Start XAMPP** services:
   - Apache
   - MySQL

3. **Run the database setup script**:
   ```bash
   # Double-click this file:
   C:\xampp\htdocs\hamma\setup_database.bat
   ```

4. **Access the application**:
   - Frontend: http://localhost/hamma/server/public/
   - Admin: http://localhost/hamma/server/public/login.php

5. **Login with default credentials**:
   ```
   Email: admin@taksoride.com
   Password: admin123
   ```

### Detailed Installation

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) for complete setup instructions.

## 🔑 Service Configuration

After installation, configure essential services:

1. Login as admin
2. Navigate to **Admin → Service Activation**
3. Configure the following:

### Required Services

#### Google Maps API
```json
{
  "api_key": "YOUR_API_KEY",
  "enabled": true
}
```
Get from: https://console.cloud.google.com/

#### Firebase (Push Notifications)
```json
{
  "server_key": "YOUR_SERVER_KEY",
  "sender_id": "YOUR_SENDER_ID",
  "project_id": "YOUR_PROJECT_ID"
}
```
Get from: https://console.firebase.google.com/

#### PubNub (Real-time Tracking)
```json
{
  "publish_key": "YOUR_KEY",
  "subscribe_key": "YOUR_KEY",
  "secret_key": "YOUR_KEY"
}
```
Get from: https://www.pubnub.com/

## 📁 Project Structure

```
hamma/
├── server/
│   ├── public/              # Web-accessible files
│   │   ├── admin/          # Admin panel
│   │   ├── assets/         # CSS, JS, images
│   │   ├── ajax_2_2_0.php  # Rider API endpoints
│   │   ├── ajaxdriver_2_2_0.php  # Driver API endpoints
│   │   ├── ajaxsd.php      # Dispatcher endpoints
│   │   ├── cron.php        # Background jobs
│   │   └── index.php       # Entry point
│   └── drop-files/         # Backend logic
│       ├── config/         # Configuration files
│       ├── lib/            # Core libraries
│       ├── templates/      # View templates
│       ├── lang/           # Language files
│       └── install/        # Database schema
├── setup_database.bat      # Quick setup script
├── INSTALLATION_GUIDE.md   # Detailed setup guide
└── README.md              # This file
```

## 🔐 Security

### Important Security Steps

1. **Change default admin password** immediately after first login
2. **Set MySQL root password** and update in `config/db.php`
3. **Secure API keys** - Never commit to version control
4. **Enable HTTPS** in production
5. **Review** the security audit report in the documentation

### Known Security Issues (Fixed)

- ✅ Removed plaintext password storage
- ✅ Removed commercial license checks
- ✅ Added input sanitization helpers
- ⚠️ SQL injection vulnerabilities still exist (use prepared statements)
- ⚠️ Implement CSRF protection for production

## 🚦 Background Services

### Cron Job (Auto-dispatch)

The cron job handles:
- Automatic driver allocation
- Booking auto-cancellation
- Notification scheduling
- Driver timeout management

**Start the cron:**
```bash
cd C:\xampp\htdocs\hamma\server\public
C:\xampp\php\php.exe cron.php
```

**Control the cron:**
- Edit `crondata.txt`:
  - `1` = Running
  - `0` or `off` = Stop
  - `2` or `restart` = Restart

## 📱 Mobile Apps

This is the backend server. Mobile apps (if available) should connect to:
- **API Base URL:** `http://localhost/hamma/server/public/`
- **Rider Endpoints:** `ajax_2_2_0.php`
- **Driver Endpoints:** `ajaxdriver_2_2_0.php`

## 🧪 Testing

### Test Admin Login
```
URL: http://localhost/hamma/server/public/login.php
Email: admin@taksoride.com
Password: admin123
```

### Test Database Connection
```
URL: http://localhost/hamma/server/public/
Should load without errors
```

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database name is `hamma`
- Check credentials in `drop-files/config/db.php`

### 404 Page Not Found
- Verify Apache is running
- Check `.htaccess` exists in `public/` folder
- Enable `mod_rewrite` in Apache

### Cannot Login
- Verify database import was successful
- Check `cab_tbl_users` table exists
- Clear browser cache

### Redis Errors
- Redis is optional for basic functionality
- Comment out Redis code if not needed
- Or install Redis for Windows

## 📊 Database Schema

- **Database Name:** `hamma`
- **Table Prefix:** `cab_`
- **Key Tables:**
  - `cab_tbl_users` - Riders and admins
  - `cab_tbl_drivers` - Driver accounts
  - `cab_tbl_bookings` - Ride bookings
  - `cab_tbl_routes` - Service areas/cities
  - `cab_tbl_service_config` - API keys (NEW!)

## 🔄 Updates & Maintenance

### Backup Database
```bash
C:\xampp\mysql\bin\mysqldump -u root hamma > backup.sql
```

### Update Configuration
- Edit files in `drop-files/config/`
- Update constants in `drop-files/lib/common.php`

## 📝 Changelog

### Version 2.2.0 - TaksoRide Rebrand
- ✅ Removed commercial licensing system
- ✅ Rebranded from DropTaxi to TaksoRide
- ✅ Added Service Activation admin page
- ✅ Created centralized API key management
- ✅ Improved security with helper functions
- ✅ Updated default admin credentials
- ✅ Added installation automation scripts

### Original Version (DropTaxi)
- Complete ride-hailing platform
- Multi-payment gateway support
- Real-time tracking
- Admin panel

## 🤝 Contributing

This is an open-source rebranded version. Contributions welcome!

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

Open Source - Commercial license removed

Original software: DropTaxi (Commercial)
Rebranded to: TaksoRide (Open Source)

## 🆘 Support

For issues:
1. Check `INSTALLATION_GUIDE.md`
2. Review error logs in `public/cron.log`
3. Check PHP error logs in XAMPP
4. Review the security audit report

## 🎯 Roadmap

- [ ] Implement prepared statements for SQL queries
- [ ] Add CSRF protection
- [ ] Create API documentation
- [ ] Add unit tests
- [ ] Implement rate limiting
- [ ] Add two-factor authentication
- [ ] Create Docker setup
- [ ] Add CI/CD pipeline

## 👥 Credits

- Original Platform: DropTaxi
- Rebranded By: TaksoRide Team
- PHP Framework: Custom
- UI Framework: AdminLTE

---

**Made with ❤️ for the ride-hailing community**

For questions or support, check the documentation or create an issue.
