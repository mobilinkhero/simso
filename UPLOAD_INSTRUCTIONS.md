# 🔧 UPLOAD REQUIRED - get_config.php

## ❌ **Problem:**
The Android app is getting a **404 error** when trying to fetch configuration because the file doesn't exist on the remote server.

**Error:** `404 https://api.cnic.pro/app/api/get_config.php`

---

## ✅ **Solution:**

You need to **upload `get_config.php` to your remote server** at:
```
https://api.cnic.pro/app/api/get_config.php
```

---

## 📤 **File to Upload:**

**Location on remote server:**
```
/app/api/get_config.php
```

**File content:** (Already created at `c:\wamp64\www\simsosiminfobackend\api\get_config.php`)

---

## 🚀 **Upload Steps:**

### **Option 1: FTP/SFTP Upload**
1. Open your FTP client (FileZilla, WinSCP, etc.)
2. Connect to `api.cnic.pro`
3. Navigate to `/app/api/` directory
4. Upload the file: `get_config.php`
5. Set permissions to `644` (readable)

### **Option 2: cPanel File Manager**
1. Login to your cPanel
2. Go to File Manager
3. Navigate to `/app/api/` directory
4. Click "Upload"
5. Upload `get_config.php` from:
   ```
   c:\wamp64\www\simsosiminfobackend\api\get_config.php
   ```

### **Option 3: Copy from Local**
Since your local backend already has the file, just copy it to the remote location!

---

## 🧪 **Test After Upload:**

1. **Open in browser:**
   ```
   https://api.cnic.pro/app/api/get_config.php
   ```

2. **Should return JSON:**
   ```json
   {
     "success": true,
     "config": {
       "home_title": "SIMSO",
       "home_subtitle": "SIM INFORMATION CHECKER",
       ...
     }
   }
   ```

3. **Run Android app again** - Should work!

---

## 📝 **File Location:**

**Local (Already Created):**
```
c:\wamp64\www\simsosiminfobackend\api\get_config.php
```

**Remote (Needs Upload):**
```
https://api.cnic.pro/app/api/get_config.php
```

---

## ⚠️ **Important:**

Make sure the remote server has:
- ✅ PHP installed
- ✅ MySQL/Database connection configured
- ✅ Same settings table structure
- ✅ The `whitelisted_countries` and popup settings in database

---

**Upload the file to fix the 404 error!** 🚀
