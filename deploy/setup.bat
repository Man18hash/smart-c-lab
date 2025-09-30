@echo off
echo ========================================
echo Smart C-Lab Deployment Setup
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
echo Step 1: Installing PHP...
echo ========================================

REM Download and install PHP
if not exist "C:\php" (
    echo Downloading PHP...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/php-8.2.12-Win32-vs16-x64.zip' -OutFile 'php.zip'}"
    
    echo Extracting PHP...
    powershell -Command "& {Expand-Archive -Path 'php.zip' -DestinationPath 'C:\' -Force}"
    
    echo Cleaning up...
    del php.zip
    
    echo PHP installed successfully!
) else (
    echo PHP already installed at C:\php
)

echo.
echo Step 2: Installing Composer...
echo ========================================

if not exist "C:\composer" (
    echo Downloading Composer...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile 'composer-setup.php'}"
    
    echo Installing Composer...
    C:\php\php.exe composer-setup.php --install-dir=C:\composer --filename=composer
    
    echo Cleaning up...
    del composer-setup.php
    
    echo Composer installed successfully!
) else (
    echo Composer already installed at C:\composer
)

echo.
echo Step 3: Setting up environment...
echo ========================================

REM Add PHP and Composer to PATH
setx PATH "%PATH%;C:\php;C:\composer" /M

echo.
echo Step 4: Creating application directory...
echo ========================================

if not exist "C:\smart-c-lab" (
    mkdir "C:\smart-c-lab"
    echo Application directory created at C:\smart-c-lab
) else (
    echo Application directory already exists
)

echo.
echo Step 5: Installing NSSM (Non-Sucking Service Manager)...
echo ========================================

if not exist "C:\nssm" (
    echo Downloading NSSM...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://nssm.cc/release/nssm-2.24.zip' -OutFile 'nssm.zip'}"
    
    echo Extracting NSSM...
    powershell -Command "& {Expand-Archive -Path 'nssm.zip' -DestinationPath 'C:\' -Force}"
    
    echo Cleaning up...
    del nssm.zip
    
    echo NSSM installed successfully!
) else (
    echo NSSM already installed
)

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Copy your Laravel project files to C:\smart-c-lab
echo 2. Run deploy\install-app.bat to install the application
echo 3. Run deploy\create-service.bat to create the Windows service
echo.
pause
