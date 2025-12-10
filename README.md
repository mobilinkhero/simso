# 🎯 SIMSO SIM INFO - PHP Backend

Complete PHP backend with MySQL database and admin panel for managing SIM information.

## 📂 **Project Structure:**

```
simsosiminfobackend/
├── config/
│   └── database.php          # Database configuration
├── database/
│   └── schema.sql            # Database schema & sample data
├── api/
│   ├── check.php             # Main API endpoint for Android app
│   └── admin/                # Admin API endpoints
│       ├── stats.php         # Dashboard statistics
│       ├── get_sims.php      # Get all SIM records
│       ├── get_logs.php      # Get API logs
│       ├── get_settings.php  # Get settings
│       ├── save_sim.php      # Add/Update SIM
│       ├── delete_sim.php    # Delete SIM
│       └── save_settings.php # Update settings
└── admin/
    ├── index.html            # Admin panel UI
    └── admin.js              # Admin panel logic
```

---

## 🚀 **Setup Instructions:**

### **1. Requirements:**
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP (recommended for local development)

### **2. Database Setup:**

1. Open phpMyAdmin or MySQL command line
2. Import the database schema:
   ```sql
   source database/schema.sql
   ```
   Or manually execute the SQL file in phpMyAdmin

3. Update database credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'simso_db');
   ```

### **3. Web Server Setup:**

**For XAMPP:**
1. Copy the `simsosiminfobackend` folder to `C:/xampp/htdocs/`
2. Start Apache and MySQL from XAMPP Control Panel
3. Access admin panel: `http://localhost/simsosiminfobackend/admin/`

**For Production:**
1. Upload files to your web server
2. Update database credentials
3. Ensure proper permissions (755 for directories, 644 for files)

---

## 🎯 **API Endpoints:**

### **1. Check SIM Information**
**Endpoint:** `POST /api/check.php`

**Request:**
```json
{
  "phone_number": "+923001234567"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "SIM information found",
  "data": {
    "Phone Number": "+923001234567",
    "Operator Name": "Jazz",
    "Status": "Active",
    "Data Plan": "50GB Monthly",
    "Roaming": "Off",
    "Network Type": "4G LTE",
    "Country": "Pakistan",
    "Region": "Punjab"
  }
}
```

**Response (Not Found):**
```json
{
  "success": false,
  "message": "No information found for this number",
  "data": {
    "Phone Number": "+923001234567",
    "Status": "Not Found"
  }
}
```

---

## 🎨 **Admin Panel Features:**

Access: `http://localhost/simsosiminfobackend/admin/`

### **Dashboard:**
- 📊 Total SIM Records
- 📈 API Requests Today
- ✅ Active SIMs Count
- 📉 Success Rate

### **SIM Data Management:**
- ➕ Add new SIM records
- ✏️ Edit existing records
- 🗑️ Delete records
- 📋 View all SIM data in table

### **API Logs:**
- 📝 View all API requests
- 🕐 Timestamps
- 📍 IP addresses
- ✅ Response status
- ⚡ Response times

### **Settings:**
- 🔧 Enable/Disable API
- ⚙️ Configure defaults
- 🛠️ Maintenance mode
- 📊 Rate limiting

---

## 📱 **Android App Integration:**

Update your Android app to use this backend:

**1. Add Retrofit dependency** (if not already added):
```gradle
implementation 'com.squareup.retrofit2:retrofit:2.9.0'
implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
```

**2. API Base URL:**
```java
public static final String BASE_URL = "http://your-domain.com/api/";
// For local testing: "http://10.0.2.2/simsosiminfobackend/api/"
```

**3. API Call Example:**
```java
// Request
JSONObject request = new JSONObject();
request.put("phone_number", phoneNumber);

// Make POST request to check.php
// Parse JSON response
```

---

## 🗄️ **Database Schema:**

### **sim_data table:**
- `id` - Auto increment primary key
- `phone_number` - Unique phone number
- `operator_name` - Operator/carrier name
- `status` - Active/Inactive/Suspended
- `data_plan` - Data plan details
- `roaming` - On/Off
- `registration_date` - Date registered
- `network_type` - 3G/4G/5G
- `country` - Country name
- `region` - Region/state
- `custom_fields` - JSON for extra fields
- `created_at` - Record creation timestamp
- `updated_at` - Last update timestamp

### **api_logs table:**
- `id` - Auto increment primary key
- `phone_number` - Searched number
- `ip_address` - Client IP
- `user_agent` - Client user agent
- `response_status` - success/not_found
- `response_time_ms` - Response time
- `created_at` - Request timestamp

### **settings table:**
- `id` - Auto increment primary key
- `setting_key` - Setting name
- `setting_value` - Setting value
- `description` - Setting description
- `updated_at` - Last update timestamp

---

## 🔒 **Security Notes:**

1. **Production Deployment:**
   - Change database credentials
   - Add authentication to admin panel
   - Enable HTTPS
   - Implement rate limiting
   - Sanitize all inputs

2. **Recommended .htaccess:**
```apache
# Prevent directory listing
Options -Indexes

# Protect config files
<Files "database.php">
    Order Allow,Deny
    Deny from all
</Files>
```

---

## 📊 **Sample Data:**

The database includes 4 sample SIM records for testing:
- Jazz (+923001234567)
- Telenor (+923111234567)
- Zong (+923211234567)
- Ufone (+923331234567)

---

## 🎯 **Next Steps:**

1. ✅ Import database schema
2. ✅ Configure database connection
3. ✅ Access admin panel
4. ✅ Add your SIM data
5. ✅ Test API endpoint
6. ✅ Integrate with Android app

---

## 🆘 **Troubleshooting:**

**Database connection error:**
- Check credentials in `config/database.php`
- Ensure MySQL is running
- Verify database exists

**API returns 404:**
- Check file paths
- Verify .htaccess configuration
- Enable mod_rewrite in Apache

**CORS errors:**
- Headers are already set in API files
- Check browser console for details

---

## 📞 **Support:**

For issues or questions:
1. Check database connection
2. Verify file permissions
3. Check PHP error logs
4. Test API with Postman

---

**Backend is ready! 🚀**
