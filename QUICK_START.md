# TaksoRide - Quick Start Guide

## 🚀 Access Your Application

### Method 1: Short URL (Recommended)
```
http://localhost/hamma
```
✅ Automatically redirects to the application

### Method 2: Full URL
```
http://localhost/hamma/server/public/
```

---

## 🔑 Login Credentials

### Admin Account
```
URL: http://localhost/hamma/server/public/login.php
Email: admin@taksoride.com
Password: admin123
Account Type: Admin
```

---

## ⚙️ Configure Services (Important!)

After logging in, go to **Service Activation** page:

### 1. Google Maps API (Required)
```json
{
  "api_key": "YOUR_GOOGLE_MAPS_API_KEY",
  "enabled": true
}
```
- Get key from: https://console.cloud.google.com/
- Click **Test Connection** to verify
- ✅ Should show: "Google Maps API is working correctly!"

### 2. Firebase (For Push Notifications)
```json
{
  "server_key": "YOUR_FCM_SERVER_KEY",
  "sender_id": "YOUR_SENDER_ID",
  "project_id": "YOUR_PROJECT_ID",
  "credentials_json": ""
}
```
- Get from: https://console.firebase.google.com/
- Click **Test Connection** to verify

### 3. PubNub (For Real-time Tracking)
```json
{
  "publish_key": "YOUR_PUBLISH_KEY",
  "subscribe_key": "YOUR_SUBSCRIBE_KEY",
  "secret_key": "YOUR_SECRET_KEY"
}
```
- Get from: https://www.pubnub.com/
- Click **Test Connection** to verify

---

## 🧪 Testing Services

For each service:
1. ✏️ Enter your API credentials in JSON format
2. 🔵 Click **"Test Connection"** button
3. ⏳ Wait for test result (1-3 seconds)
4. ✅ Green = Working | ❌ Red = Error
5. 💾 Click **"Save Configuration"** when done

---

## 📱 Test Results Examples

### ✅ Success
```
✓ Google Maps API is working correctly!
✓ Firebase credentials are valid!
✓ Redis connection successful!
```

### ❌ Error
```
✗ API key is invalid or restricted
✗ Cannot connect to Redis server
✗ Invalid Firebase server key
```

---

## 🎯 Next Steps

1. ✅ Change admin password
2. ✅ Configure essential services (Google Maps, Firebase, PubNub)
3. ✅ Add routes/cities for your service area
4. ✅ Create ride types (Economy, Premium, etc.)
5. ✅ Set up tariffs and pricing
6. ✅ Add test drivers and riders
7. ✅ Test complete booking flow

---

## 🔧 Troubleshooting

### Session Warnings?
✅ **FIXED!** - Should not appear anymore

### Can't access localhost/hamma?
✅ **FIXED!** - Now redirects automatically

### Service test not working?
- Check internet connection
- Verify API key is correct
- Check browser console for errors
- Try testing with empty credentials to see validation

---

## 📚 Documentation

- **Full Installation Guide:** `INSTALLATION_GUIDE.md`
- **Fixes Applied:** `FIXES_APPLIED.md`
- **Main README:** `README.md`

---

## 🆘 Quick Help

**Database Issues:**
```bash
# Re-run database setup
C:\xampp\htdocs\hamma\setup_database.bat
```

**Apache Not Running:**
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

**Clear Browser Cache:**
- Chrome: Ctrl + Shift + Delete
- Firefox: Ctrl + Shift + Delete
- Edge: Ctrl + Shift + Delete

---

## 🎉 You're All Set!

Your TaksoRide platform is ready to use with:
- ✅ No session warnings
- ✅ Clean URL access
- ✅ Service testing functionality
- ✅ Professional admin panel

**Enjoy building your ride-hailing service! 🚗💨**
