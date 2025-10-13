# Timer API Integration Guide

This guide explains how to use the Timer API to communicate between your Laravel app and remote laptops on the local network.

## 🎯 How It Works

1. **Admin approves a borrowing** → Laravel sets `status='checked_out'`, `approved_at`, and `due_at`
2. **Python app polls the API** every 5 seconds (configurable)
3. **API returns timer data** with the remaining time
4. **Python app auto-starts timer** when a new borrowing is approved

## 📡 API Endpoint

```
GET /api/timer/current
```

**Headers:**
```
X-Api-Key: your-secret-api-key
```

**Response (Active Borrowing):**
```json
{
  "command": "start",
  "duration_seconds": 3600,
  "total_duration_seconds": 7200,
  "issued_at": "2025-10-13T10:30:00+00:00",
  "borrowing_id": 123,
  "student_name": "John Doe",
  "laptop_name": "LAPTOP-001",
  "ip_address": "192.168.1.100",
  "approved_at": "2025-10-13T10:30:00+00:00",
  "due_at": "2025-10-13T12:30:00+00:00"
}
```

**Response (No Active Borrowing):**
```json
{
  "command": "stop",
  "duration_seconds": 0,
  "issued_at": null,
  "message": "No active borrowing found"
}
```

## 🔧 Setup Instructions

### Step 1: Configure Laravel

1. **Add API Key to `.env`:**
   ```env
   TIMER_API_KEY=supersecretlong
   ```
   
   ⚠️ **Important:** Use a strong, random key in production!

2. **Make sure your Laravel server is accessible on the local network:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

3. **Find your Laravel server's IP address:**
   - Windows: `ipconfig` (look for IPv4 Address)
   - Linux/Mac: `ifconfig` or `ip addr`

### Step 2: Configure Python App

Update your `timer_gui.py` settings:

```python
BASE_URL = "http://192.168.100.6:8000"  # Your Laravel server IP
API_KEY  = "supersecretlong"             # Must match .env TIMER_API_KEY
POLL_SEC = 5                             # Poll every 5 seconds
```

### Step 3: Test the Connection

1. **Run the test script on the same computer as Laravel:**
   ```bash
   python test_api.py
   ```

2. **If successful, run the Python timer app on the remote laptop:**
   ```bash
   python timer_gui.py
   ```

## 🧪 Testing Flow

1. **Start Laravel server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Login as admin and approve a borrowing request**
   - Go to `http://localhost:8000/admin/borrower`
   - Approve a pending request with a specific duration

3. **The Python app should automatically:**
   - Detect the new borrowing (via `issued_at` timestamp)
   - Start the timer with the remaining duration
   - Display student and laptop information

## 🔒 Security Notes

1. **API Key:** The API is protected by a simple API key. Make sure to use a strong, random key.

2. **Network Security:** This API is designed for local network use. If you need internet access:
   - Use HTTPS (SSL/TLS)
   - Implement additional authentication
   - Consider rate limiting

3. **Firewall:** Make sure Windows Firewall allows connections on port 8000.

## 🐛 Troubleshooting

### Python app shows "Cannot connect"
- Check if Laravel server is running
- Verify the IP address in `BASE_URL`
- Check firewall settings
- Try accessing `http://192.168.100.6:8000` in a browser

### API returns 401 Unauthorized
- Check if `TIMER_API_KEY` in `.env` matches `API_KEY` in Python
- Make sure you added the key to `.env` and restarted Laravel

### API returns "No active borrowing found"
- Approve a borrowing request in the admin panel
- Check if the borrowing status is `checked_out`
- Verify `approved_at` and `due_at` are set

### Timer doesn't auto-start
- Check if `issued_at` is changing (Python app tracks this)
- Increase `POLL_SEC` if the network is slow
- Check Python console for errors

## 📝 API Response Fields Explained

| Field | Description |
|-------|-------------|
| `command` | `"start"` or `"stop"` - tells the timer what to do |
| `duration_seconds` | Remaining time from NOW to due_at (in seconds) |
| `total_duration_seconds` | Total approved duration (from approved_at to due_at) |
| `issued_at` | Unique timestamp - Python uses this to detect new borrowings |
| `borrowing_id` | Database ID of the borrowing |
| `student_name` | Full name of the student |
| `laptop_name` | Device name of the borrowed laptop |
| `ip_address` | Assigned IP address (if any) |
| `approved_at` | When the admin approved the request |
| `due_at` | When the borrowing expires |

## 🔄 How the Python App Detects New Borrowings

The Python app tracks the `issued_at` (which is the `approved_at` timestamp):

```python
if issued_at and issued_at != self.last_issued_at:
    self.last_issued_at = issued_at
    self.start(duration_seconds)  # Auto-start timer
```

This ensures the timer only auto-starts when a **new** borrowing is approved, not every time it polls.

## ⚙️ Advanced Configuration

### Change Poll Interval
In `timer_gui.py`, adjust:
```python
POLL_SEC = 10  # Poll every 10 seconds instead of 5
```

### Multiple Timer Laptops
Each laptop can run the same Python app - they'll all show the same most recent borrowing. To assign specific laptops to specific borrowings, you could:
- Add a `laptop_id` query parameter to the API
- Modify the controller to filter by laptop
- Update Python to send its laptop ID

### Auto-Return Expired Borrowings
You can set up a cron job to automatically return expired borrowings:

**Windows Task Scheduler:**
```bash
php artisan route:list | grep auto-return
```

**Call the endpoint:**
```
POST /admin/borrower/auto-return-expired
```

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode in `.env`: `APP_DEBUG=true`
3. Test the API endpoint directly in a browser or Postman

