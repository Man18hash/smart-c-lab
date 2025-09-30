@echo off
echo ========================================
echo Smart C-Lab Clone and Deploy
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
echo Step 1: Installing Git (if not already installed)...
echo ========================================

where git >nul 2>&1
if %errorLevel% == 0 (
    echo Git is already installed
) else (
    echo Downloading and installing Git...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/Git-2.42.0.2-64-bit.exe' -OutFile 'git-installer.exe'}"
    
    echo Installing Git...
    git-installer.exe /SILENT
    
    echo Cleaning up...
    del git-installer.exe
    
    echo Git installed successfully!
    echo Please restart this script after Git installation completes.
    pause
    exit /b 0
)

echo.
echo Step 2: Installing PHP (if not already installed)...
echo ========================================

where php >nul 2>&1
if %errorLevel% == 0 (
    echo PHP is already installed
) else (
    echo Downloading and installing PHP...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/php-8.2.12-Win32-vs16-x64.zip' -OutFile 'php.zip'}"
    
    echo Extracting PHP...
    powershell -Command "& {Expand-Archive -Path 'php.zip' -DestinationPath 'C:\' -Force}"
    
    echo Cleaning up...
    del php.zip
    
    echo PHP installed successfully!
)

echo.
echo Step 3: Installing Composer (if not already installed)...
echo ========================================

where composer >nul 2>&1
if %errorLevel% == 0 (
    echo Composer is already installed
) else (
    echo Downloading and installing Composer...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile 'composer-setup.php'}"
    
    echo Installing Composer...
    C:\php\php.exe composer-setup.php --install-dir=C:\composer --filename=composer
    
    echo Cleaning up...
    del composer-setup.php
    
    echo Composer installed successfully!
)

echo.
echo Step 4: Installing NSSM (if not already installed)...
echo ========================================

if not exist "C:\nssm" (
    echo Downloading and installing NSSM...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://nssm.cc/release/nssm-2.24.zip' -OutFile 'nssm.zip'}"
    
    echo Extracting NSSM...
    powershell -Command "& {Expand-Archive -Path 'nssm.zip' -DestinationPath 'C:\' -Force}"
    
    echo Cleaning up...
    del nssm.zip
    
    echo NSSM installed successfully!
) else (
    echo NSSM is already installed
)

echo.
echo Step 5: Setting up environment variables...
echo ========================================

REM Add PHP and Composer to PATH
setx PATH "%PATH%;C:\php;C:\composer" /M

echo.
echo Step 6: Cloning the repository...
echo ========================================

if exist "C:\smart-c-lab" (
    echo Application directory already exists. Updating...
    cd /d "C:\smart-c-lab"
    git pull origin main
) else (
    echo Cloning repository...
    git clone https://github.com/YOUR_USERNAME/smart-c-lab.git C:\smart-c-lab
)

echo.
echo Step 7: Installing application dependencies...
echo ========================================

cd /d "C:\smart-c-lab"
C:\composer\composer install --no-dev --optimize-autoloader

echo.
echo Step 8: Setting up environment file...
echo ========================================

if not exist ".env" (
    copy ".env.example" ".env"
    echo Environment file created
) else (
    echo Environment file already exists
)

echo.
echo Step 9: Generating application key...
echo ========================================

C:\php\php.exe artisan key:generate

echo.
echo Step 10: Creating storage directories...
echo ========================================

if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo.
echo Step 11: Setting permissions...
echo ========================================

REM Set permissions for storage and bootstrap/cache
icacls "storage" /grant "Everyone:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T

echo.
echo Step 12: Running database migrations...
echo ========================================

C:\php\php.exe artisan migrate --force

echo.
echo Step 13: Seeding database...
echo ========================================

C:\php\php.exe artisan db:seed --force

echo.
echo Step 14: Creating storage link...
echo ========================================

C:\php\php.exe artisan storage:link

echo.
echo Step 15: Optimizing application...
echo ========================================

C:\php\php.exe artisan config:cache
C:\php\php.exe artisan route:cache
C:\php\php.exe artisan view:cache

echo.
echo Step 16: Creating Windows Service...
echo ========================================

REM Remove existing service if it exists
sc query "SmartCLab" >nul 2>&1
if %errorLevel% == 0 (
    echo Stopping existing service...
    sc stop "SmartCLab"
    timeout /t 3 /nobreak >nul
    echo Removing existing service...
    sc delete "SmartCLab"
    timeout /t 3 /nobreak >nul
)

REM Create the service
C:\nssm\win64\nssm.exe install "SmartCLab" "C:\php\php.exe" "-S 0.0.0.0:8000 -t C:\smart-c-lab\public"

REM Configure service settings
C:\nssm\win64\nssm.exe set "SmartCLab" DisplayName "Smart C-Lab Web Application"
C:\nssm\win64\nssm.exe set "SmartCLab" Description "Smart C-Lab Laptop Management System"
C:\nssm\win64\nssm.exe set "SmartCLab" Start SERVICE_AUTO_START
C:\nssm\win64\nssm.exe set "SmartCLab" AppDirectory "C:\smart-c-lab"
C:\nssm\win64\nssm.exe set "SmartCLab" AppStdout "C:\smart-c-lab\storage\logs\service.log"
C:\nssm\win64\nssm.exe set "SmartCLab" AppStderr "C:\smart-c-lab\storage\logs\service-error.log"

echo.
echo Step 17: Starting the service...
echo ========================================

C:\nssm\win64\nssm.exe start "SmartCLab"

echo.
echo Step 18: Verifying service status...
echo ========================================

timeout /t 5 /nobreak >nul
sc query "SmartCLab"

echo.
echo ========================================
echo Deployment completed successfully!
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
echo The application will start automatically when the computer boots!
echo.
pause
