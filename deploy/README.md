# Smart C-Lab Deployment Guide

This guide will help you deploy the Smart C-Lab application on a Windows machine with automatic startup on boot.

## Prerequisites

- Windows 10/11
- Administrator access
- Internet connection for downloading dependencies

## Quick Deployment

### Option 1: Automated Setup (Recommended)

1. **Copy the entire project** to the target machine
2. **Right-click** on `deploy/setup.bat` and select **"Run as administrator"**
3. **Copy your project files** to `C:\smart-c-lab`
4. **Right-click** on `deploy/install-app.bat` and select **"Run as administrator"**
5. **Right-click** on `deploy/create-service.bat` and select **"Run as administrator"**

The application will now start automatically on boot!

### Option 2: Manual Setup

Follow the detailed steps below if you prefer manual installation.

## Detailed Installation Steps

### Step 1: Install PHP

1. Download PHP 8.2+ from [php.net](https://windows.php.net/download/)
2. Extract to `C:\php`
3. Add `C:\php` to your system PATH

### Step 2: Install Composer

1. Download Composer from [getcomposer.org](https://getcomposer.org/download/)
2. Install to `C:\composer`
3. Add `C:\composer` to your system PATH

### Step 3: Install NSSM (Non-Sucking Service Manager)

1. Download NSSM from [nssm.cc](https://nssm.cc/download)
2. Extract to `C:\nssm`

### Step 4: Deploy Application

1. Copy project files to `C:\smart-c-lab`
2. Run `composer install --no-dev --optimize-autoloader`
3. Copy `.env.example` to `.env`
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `php artisan storage:link`
7. Run `php artisan config:cache`
8. Run `php artisan route:cache`
9. Run `php artisan view:cache`

### Step 5: Create Windows Service

```cmd
C:\nssm\win64\nssm.exe install "SmartCLab" "C:\php\php.exe" "-S 0.0.0.0:8000 -t C:\smart-c-lab\public"
C:\nssm\win64\nssm.exe set "SmartCLab" DisplayName "Smart C-Lab Web Application"
C:\nssm\win64\nssm.exe set "SmartCLab" Description "Smart C-Lab Laptop Management System"
C:\nssm\win64\nssm.exe set "SmartCLab" Start SERVICE_AUTO_START
C:\nssm\win64\nssm.exe set "SmartCLab" AppDirectory "C:\smart-c-lab"
C:\nssm\win64\nssm.exe start "SmartCLab"
```

## Service Management

### Start Service
```cmd
sc start SmartCLab
```

### Stop Service
```cmd
sc stop SmartCLab
```

### Restart Service
```cmd
sc stop SmartCLab && sc start SmartCLab
```

### Check Service Status
```cmd
sc query SmartCLab
```

### Remove Service
```cmd
sc delete SmartCLab
```

## Application Access

- **Local Access**: http://localhost:8000
- **Network Access**: http://[IP_ADDRESS]:8000

## Default Login Credentials

- **Admin**: admin@smartclab.com / password
- **Student**: Use registration form to create accounts

## Logs

- **Service Logs**: `C:\smart-c-lab\storage\logs\service.log`
- **Error Logs**: `C:\smart-c-lab\storage\logs\service-error.log`
- **Application Logs**: `C:\smart-c-lab\storage\logs\laravel.log`

## Updating the Application

1. **Right-click** on `deploy/update-app.bat` and select **"Run as administrator"**

Or manually:
1. Stop the service: `sc stop SmartCLab`
2. Update files in `C:\smart-c-lab`
3. Run `composer install --no-dev --optimize-autoloader`
4. Run `php artisan migrate --force`
5. Run `php artisan config:cache`
6. Start the service: `sc start SmartCLab`

## Troubleshooting

### Service Won't Start
1. Check logs in `C:\smart-c-lab\storage\logs\`
2. Verify PHP is in PATH: `php --version`
3. Check service status: `sc query SmartCLab`

### Application Not Accessible
1. Check if service is running: `sc query SmartCLab`
2. Verify port 8000 is not blocked by firewall
3. Check if another application is using port 8000

### Database Issues
1. Check if SQLite file exists: `C:\smart-c-lab\database\database.sqlite`
2. Run migrations: `php artisan migrate --force`
3. Check permissions on database file

### Permission Issues
1. Run scripts as Administrator
2. Check folder permissions on `C:\smart-c-lab`
3. Ensure storage folders are writable

## Firewall Configuration

To allow network access, add a firewall rule:

```cmd
netsh advfirewall firewall add rule name="Smart C-Lab" dir=in action=allow protocol=TCP localport=8000
```

## Uninstallation

1. Stop and remove service: `sc stop SmartCLab && sc delete SmartCLab`
2. Delete application folder: `rmdir /s C:\smart-c-lab`
3. Remove PHP and Composer if not needed elsewhere
4. Remove firewall rule: `netsh advfirewall firewall delete rule name="Smart C-Lab"`

## Support

For issues or questions:
1. Check the logs first
2. Verify all prerequisites are installed
3. Ensure scripts are run as Administrator
4. Check Windows Event Viewer for service errors
