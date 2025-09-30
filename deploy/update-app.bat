@echo off
echo ========================================
echo Smart C-Lab Application Update
echo ========================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo Running as Administrator - Good!
) else (
    echo ERROR: This script must be run as Administrator
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo.
echo Step 1: Stopping the service...
echo ========================================

sc stop "SmartCLab"
timeout /t 3 /nobreak >nul

echo.
echo Step 2: Updating application files...
echo ========================================

cd /d "C:\smart-c-lab"

echo Updating Composer dependencies...
C:\composer\composer install --no-dev --optimize-autoloader

echo.
echo Step 3: Running database migrations...
echo ========================================

C:\php\php.exe artisan migrate --force

echo.
echo Step 4: Clearing and rebuilding caches...
echo ========================================

C:\php\php.exe artisan config:clear
C:\php\php.exe artisan route:clear
C:\php\php.exe artisan view:clear

C:\php\php.exe artisan config:cache
C:\php\php.exe artisan route:cache
C:\php\php.exe artisan view:cache

echo.
echo Step 5: Starting the service...
echo ========================================

sc start "SmartCLab"

echo.
echo Step 6: Verifying service status...
echo ========================================

timeout /t 5 /nobreak >nul
sc query "SmartCLab"

echo.
echo ========================================
echo Application update completed!
echo ========================================
echo.
echo The application has been updated and restarted.
echo URL: http://localhost:8000
echo.
pause
