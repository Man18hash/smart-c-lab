# Quick Start Guide: Timer API Integration

## ✅ What's Been Set Up

Your Laravel app is now ready to communicate with remote laptops on your local network! Here's what was configured:

### 1. **API Endpoint Created**
- **URL:** `http://192.168.100.6:8000/api/timer/current`
- **Method:** GET
- **Authentication:** API Key via `X-Api-Key` header
- **Key:** `supersecretlong`

### 2. **Files Created/Modified**
- ✅ `routes/api.php` - API route definition
- ✅ `app/Http/Controllers/Api/TimerApiController.php` - Timer API logic
- ✅ `app/Http/Middleware/ApiKeyMiddleware.php` - API security (already existed)
- ✅ `app/Models/Borrowing.php` - Added `duration_hours` to fillable
- ✅ `.env` - Added `TIMER_API_KEY=supersecretlong`
- ✅ `bootstrap/app.php` - Middleware aliases (already configured)
- ✅ `config/services.php` - Timer API config (already configured)

## 🚀 How To Use

### Step 1: Start Laravel Server

Your Laravel server is currently running on:
```
http://192.168.100.6:8000
```

To restart it manually:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 2: Test the API

**From this computer (Windows PowerShell):**
```powershell
$response = Invoke-WebRequest -Uri "http://192.168.100.6:8000/api/timer/current" -Headers @{"X-Api-Key"="supersecretlong"} -UseBasicParsing
$response.Content
```

**Expected Response (No Active Borrowing):**
```json
{
  "command": "stop",
  "duration_seconds": 0,
  "issued_at": null,
  "message": "No active borrowing found"
}
```

### Step 3: Approve a Borrowing

1. Go to: `http://localhost:8000/admin/borrower`
2. Login as admin
3. Approve a pending request
4. The borrowing will be set to `checked_out` status

### Step 4: API Will Return Active Borrowing

Once approved, the API returns:
```json
{
  "command": "start",
  "duration_seconds": 3600,
  "total_duration_seconds": 7200,
  "issued_at": "2025-10-13T10:30:00+00:00",
  "borrowing_id": 1,
  "student_name": "John Doe",
  "laptop_name": "LAPTOP-001",
  "ip_address": "192.168.1.100",
  "approved_at": "2025-10-13T10:30:00+00:00",
  "due_at": "2025-10-13T12:30:00+00:00"
}
```

### Step 5: Run Python Timer App on Remote Laptop

Your Python app (`timer_gui.py`) is already configured correctly:
```python
BASE_URL = "http://192.168.100.6:8000"
API_KEY = "supersecretlong"
POLL_SEC = 5
```

Just run it on the other laptop:
```bash
python timer_gui.py
```

## 🔄 How It Works

1. **Python app polls every 5 seconds** → Requests `/api/timer/current`
2. **Laravel checks for active borrowings** → Finds most recent `checked_out` borrowing
3. **Returns timer data** → Including remaining time in seconds
4. **Python detects new borrowing** → Compares `issued_at` timestamp
5. **Timer auto-starts** → Sets countdown to remaining duration

## 🎯 Testing the Full Flow

### Test Scenario:
1. **Admin:** Approve a 2-hour borrowing request
2. **Python App:** Auto-detects the approval
3. **Timer:** Starts countdown from 2 hours (7200 seconds)
4. **Display:** Shows student name, laptop name, remaining time

### Expected Behavior:
- ✅ Timer starts automatically when borrowing is approved
- ✅ Timer shows remaining time (counts down)
- ✅ Timer only starts once per borrowing (tracks `issued_at`)
- ✅ Multiple laptops can run the same timer (all show latest borrowing)

## 🔧 Configuration Options

### Change API Key (Production)
1. Edit `.env`:
   ```env
   TIMER_API_KEY=your-very-secure-random-key-here
   ```
2. Update Python app `API_KEY` to match
3. Restart Laravel: `php artisan config:clear`

### Change Poll Interval
In `timer_gui.py`:
```python
POLL_SEC = 10  # Poll every 10 seconds instead of 5
```

### Change Server IP/Port
If your IP changes:
1. Find new IP: `ipconfig` (look for IPv4)
2. Update Python `BASE_URL`
3. Restart Laravel server on new IP

## 🐛 Troubleshooting

### "Cannot connect" from Python
- ✅ Check Laravel server is running
- ✅ Verify IP address: `ipconfig`
- ✅ Check Windows Firewall allows port 8000
- ✅ Try `http://localhost:8000/api/timer/current` on Laravel computer

### "401 Unauthorized"
- ✅ Check API keys match in `.env` and Python
- ✅ Restart Laravel: `php artisan config:clear`

### "No active borrowing found"
- ✅ Approve a borrowing in admin panel
- ✅ Check borrowing status is `checked_out`
- ✅ Verify `approved_at` and `due_at` are set

### Timer doesn't auto-start
- ✅ Check Python console for errors
- ✅ Verify `issued_at` changes when new borrowing is approved
- ✅ Try manually clicking "Fetch from Laravel" in Python app

## 📊 API Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `command` | string | `"start"` or `"stop"` |
| `duration_seconds` | integer | Remaining time from NOW to due_at |
| `total_duration_seconds` | integer | Total approved duration |
| `issued_at` | string | ISO timestamp - used to detect new borrowings |
| `borrowing_id` | integer | Database ID |
| `student_name` | string | Student's full name |
| `laptop_name` | string | Laptop device name |
| `ip_address` | string\|null | Assigned IP (if any) |
| `approved_at` | string | When admin approved |
| `due_at` | string | When borrowing expires |

## 🎉 You're All Set!

Your timer system is ready to use! 

1. Start the Laravel server (already running)
2. Run the Python app on remote laptop
3. Approve borrowings in the admin panel
4. Watch the timer auto-start!

For more detailed information, see `TIMER_API_README.md`.

