@echo off
echo ========================================
echo Smart C-Lab Application Installation
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
echo Step 1: Installing Composer dependencies...
echo ========================================

cd /d "C:\smart-c-lab"
C:\composer\composer install --no-dev --optimize-autoloader

echo.
echo Step 2: Setting up environment file...
echo ========================================

if not exist ".env" (
    copy ".env.example" ".env"
    echo Environment file created
) else (
    echo Environment file already exists
)

echo.
echo Step 3: Generating application key...
echo ========================================

C:\php\php.exe artisan key:generate

echo.
echo Step 4: Creating storage directories...
echo ========================================

if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo.
echo Step 5: Setting permissions...
echo ========================================

REM Set permissions for storage and bootstrap/cache
icacls "storage" /grant "Everyone:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T

echo.
echo Step 6: Running database migrations...
echo ========================================

C:\php\php.exe artisan migrate --force

echo.
echo Step 7: Seeding database...
echo ========================================

C:\php\php.exe artisan db:seed --force

echo.
echo Step 8: Creating storage link...
echo ========================================

C:\php\php.exe artisan storage:link

echo.
echo Step 9: Optimizing application...
echo ========================================

C:\php\php.exe artisan config:cache
C:\php\php.exe artisan route:cache
C:\php\php.exe artisan view:cache

echo.
echo ========================================
echo Application installation completed!
echo ========================================
echo.
echo The application is now ready to run.
echo Next: Run deploy\create-service.bat to create the Windows service
echo.
pause
