@echo off
echo ========================================
echo Smart C-Lab Deployment Fix
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
echo Step 1: Checking PHP installation...
echo ========================================

if exist "C:\php\php.exe" (
    echo PHP found at C:\php\php.exe
    set PHP_PATH=C:\php\php.exe
) else (
    echo PHP not found at C:\php\php.exe
    echo Please install PHP first
    pause
    exit /b 1
)

echo.
echo Step 2: Checking Composer installation...
echo ========================================

if exist "C:\composer\composer.exe" (
    echo Composer found at C:\composer\composer.exe
    set COMPOSER_PATH=C:\composer\composer.exe
) else (
    echo Composer not found at C:\composer\composer.exe
    echo Please install Composer first
    pause
    exit /b 1
)

echo.
echo Step 3: Checking NSSM installation...
echo ========================================

if exist "C:\nssm\win64\nssm.exe" (
    echo NSSM found at C:\nssm\win64\nssm.exe
    set NSSM_PATH=C:\nssm\win64\nssm.exe
) else (
    echo NSSM not found at C:\nssm\win64\nssm.exe
    echo Please install NSSM first
    pause
    exit /b 1
)

echo.
echo Step 4: Navigating to application directory...
echo ========================================

if not exist "C:\smart-c-lab" (
    echo Application directory not found at C:\smart-c-lab
    echo Please run the clone-and-deploy.bat script first
    pause
    exit /b 1
)

cd /d "C:\smart-c-lab"
echo Current directory: %CD%

echo.
echo Step 5: Installing application dependencies...
echo ========================================

"%COMPOSER_PATH%" install --no-dev --optimize-autoloader

echo.
echo Step 6: Setting up environment file...
echo ========================================

if not exist ".env" (
    copy ".env.example" ".env"
    echo Environment file created
) else (
    echo Environment file already exists
)

echo.
echo Step 7: Generating application key...
echo ========================================

"%PHP_PATH%" artisan key:generate

echo.
echo Step 8: Creating storage directories...
echo ========================================

if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo.
echo Step 9: Setting permissions...
echo ========================================

icacls "storage" /grant "Everyone:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T

echo.
echo Step 10: Running database migrations...
echo ========================================

"%PHP_PATH%" artisan migrate --force

echo.
echo Step 11: Seeding database...
echo ========================================

"%PHP_PATH%" artisan db:seed --force

echo.
echo Step 12: Creating storage link...
echo ========================================

"%PHP_PATH%" artisan storage:link

echo.
echo Step 13: Optimizing application...
echo ========================================

"%PHP_PATH%" artisan config:cache
"%PHP_PATH%" artisan route:cache
"%PHP_PATH%" artisan view:cache

echo.
echo Step 14: Removing existing service (if any)...
echo ========================================

sc query "SmartCLab" >nul 2>&1
if %errorLevel% == 0 (
    echo Stopping existing service...
    sc stop "SmartCLab"
    timeout /t 3 /nobreak >nul
    echo Removing existing service...
    sc delete "SmartCLab"
    timeout /t 3 /nobreak >nul
)

echo.
echo Step 15: Creating Windows Service...
echo ========================================

"%NSSM_PATH%" install "SmartCLab" "%PHP_PATH%" "-S 0.0.0.0:8000 -t C:\smart-c-lab\public"

echo.
echo Step 16: Configuring service settings...
echo ========================================

"%NSSM_PATH%" set "SmartCLab" DisplayName "Smart C-Lab Web Application"
"%NSSM_PATH%" set "SmartCLab" Description "Smart C-Lab Laptop Management System"
"%NSSM_PATH%" set "SmartCLab" Start SERVICE_AUTO_START
"%NSSM_PATH%" set "SmartCLab" AppDirectory "C:\smart-c-lab"
"%NSSM_PATH%" set "SmartCLab" AppStdout "C:\smart-c-lab\storage\logs\service.log"
"%NSSM_PATH%" set "SmartCLab" AppStderr "C:\smart-c-lab\storage\logs\service-error.log"

echo.
echo Step 17: Starting the service...
echo ========================================

"%NSSM_PATH%" start "SmartCLab"

echo.
echo Step 18: Verifying service status...
echo ========================================

timeout /t 5 /nobreak >nul
sc query "SmartCLab"

echo.
echo ========================================
echo Deployment fix completed!
echo ========================================
echo.
echo Application Details:
echo - URL: http://localhost:8000
echo - Service: SmartCLab (Auto-start on boot)
echo - Logs: C:\smart-c-lab\storage\logs\service.log
echo.
echo Default Login:
echo - Admin: admin@smartclab.com / password
echo.
echo The application should now start automatically when the computer boots!
echo.
pause
