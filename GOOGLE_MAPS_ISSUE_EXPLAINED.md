# Google Maps "Can't Load" Issue - Explained

## What You're Seeing

### In Map Tracker Page
```
❌ "This page can't load Google Maps correctly"
```

### In Test Button
```
❌ "API key is invalid or restricted"
```

## Why This Happens

### The Issue
Google Maps shows this error for **THREE different reasons**:

1. **Billing Not Enabled** (Most Common)
2. **API Restrictions** (Domain/IP restrictions)
3. **Wrong APIs Enabled**

### Important: The Key MIGHT Be Working!
The error message is **misleading**. Your key might be valid but:
- Billing is not set up
- Wrong APIs are enabled
- Domain restrictions are blocking it

---

## How to Fix - Step by Step

### Step 1: Check Your Google Cloud Console

1. Go to: https://console.cloud.google.com/
2. Select your project
3. Go to **APIs & Services** → **Dashboard**

### Step 2: Enable Required APIs

You need **ALL** of these APIs enabled:

✅ **Maps JavaScript API** - For displaying maps
✅ **Geocoding API** - For address to coordinates
✅ **Directions API** - For route planning
✅ **Places API** - For place search
✅ **Distance Matrix API** - For distance calculation

**How to Enable:**
1. Click **"+ ENABLE APIS AND SERVICES"**
2. Search for each API above
3. Click **ENABLE**

### Step 3: Set Up Billing

**This is REQUIRED even for free tier!**

1. Go to **Billing** in left menu
2. Click **"Link a billing account"**
3. Add a credit card (Google gives $200 free credit/month)
4. Confirm billing is active

**Don't worry:** You get $200 free credit every month. Most small apps never exceed this.

### Step 4: Remove Restrictions (For Testing)

1. Go to **APIs & Services** → **Credentials**
2. Click on your API key
3. Under **"API restrictions"**:
   - Select **"Don't restrict key"** (for testing)
4. Under **"Application restrictions"**:
   - Select **"None"** (for testing)
5. Click **SAVE**

**Important:** Add restrictions back after testing for security!

---

## Debug Your API Key

### Method 1: Use Our Debug Tool

```
http://localhost/hamma/server/public/admin/debug-api.php?key=YOUR_API_KEY_HERE
```

This will show you:
- Exact error message from Google
- HTTP response code
- Full API response
- What's wrong with your key

### Method 2: Test in Browser

Open this URL in your browser (replace YOUR_KEY):
```
https://maps.googleapis.com/maps/api/geocode/json?address=New+York&key=YOUR_KEY
```

**What you should see:**

✅ **If working:**
```json
{
  "status": "OK",
  "results": [...]
}
```

❌ **If billing not enabled:**
```json
{
  "status": "REQUEST_DENIED",
  "error_message": "You must enable Billing on the Google Cloud Project..."
}
```

❌ **If API not enabled:**
```json
{
  "status": "REQUEST_DENIED",
  "error_message": "This API project is not authorized to use this API..."
}
```

---

## Common Error Messages Explained

### "You must enable Billing"
**Problem:** No credit card linked
**Solution:** Add billing account in Google Cloud Console

### "This API project is not authorized"
**Problem:** Geocoding API not enabled
**Solution:** Enable Geocoding API

### "API key not valid"
**Problem:** Key is wrong or deleted
**Solution:** Create new API key

### "This IP, site or mobile application is not authorized"
**Problem:** Domain/IP restrictions
**Solution:** Remove restrictions or add your domain

---

## Testing Checklist

### ✅ Before Testing
- [ ] Billing account linked
- [ ] Credit card added
- [ ] Maps JavaScript API enabled
- [ ] Geocoding API enabled
- [ ] Directions API enabled
- [ ] Places API enabled
- [ ] API restrictions set to "None" (for testing)
- [ ] Application restrictions set to "None" (for testing)

### ✅ After Setup
1. Wait 2-3 minutes for changes to propagate
2. Test with debug tool
3. Test with test button
4. Test in map tracker page

---

## Why Test Button Shows Different Result

### The Map Uses:
- Maps JavaScript API
- Loaded in browser
- Shows visual map

### The Test Button Uses:
- Geocoding API
- Server-side cURL request
- Tests API programmatically

**Both need to work!** If one fails, check which API is not enabled.

---

## Production Setup (After Testing)

### 1. Add Domain Restrictions
```
Application restrictions:
- HTTP referrers (web sites)
- Add: localhost/* (for development)
- Add: yourdomain.com/* (for production)
```

### 2. Add API Restrictions
```
API restrictions:
- Restrict key
- Select only the APIs you use:
  ✓ Maps JavaScript API
  ✓ Geocoding API
  ✓ Directions API
  ✓ Places API
```

### 3. Monitor Usage
```
Go to: APIs & Services → Dashboard
Check: Requests per day
Ensure: Under free tier limit (28,500 map loads/month)
```

---

## Cost Breakdown

### Free Tier (Every Month)
- $200 credit = FREE
- 28,500 map loads
- 40,000 geocoding requests
- 40,000 directions requests

### Typical Usage (Small App)
- 100 users/day
- 10 rides/day
- ~3,000 API calls/month
- **Cost: $0** (well within free tier)

---

## Quick Fix Commands

### Test Your Key Right Now

**In browser:**
```
https://maps.googleapis.com/maps/api/geocode/json?address=London&key=YOUR_KEY
```

**In our debug tool:**
```
http://localhost/hamma/server/public/admin/debug-api.php?key=YOUR_KEY
```

**Expected result if working:**
```json
{
  "status": "OK",
  "results": [{
    "formatted_address": "London, UK",
    ...
  }]
}
```

---

## Still Not Working?

### Check These:

1. **Wait Time**
   - Changes take 2-5 minutes to apply
   - Clear browser cache
   - Try incognito mode

2. **Key Format**
   - Should look like: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`
   - 39 characters long
   - Starts with `AIza`

3. **Project Status**
   - Project not suspended
   - Billing account active
   - No payment issues

4. **API Quotas**
   - Not exceeded daily limit
   - Check quota page in console

---

## Summary

### Your Issue
✅ API key exists
❌ But billing not enabled OR wrong APIs enabled

### Solution
1. Enable billing (add credit card)
2. Enable all required APIs
3. Remove restrictions (for testing)
4. Wait 2-3 minutes
5. Test again

### After Fix
- Test button will show: ✓ "Google Maps API is working!"
- Map will load correctly
- No more error messages

---

## Need Help?

### Use Debug Tool
```
http://localhost/hamma/server/public/admin/debug-api.php?key=YOUR_KEY
```

This will tell you EXACTLY what's wrong!

---

**The test button is correct - it's showing the real API status. Fix the billing/API issues and both will work!**
