@echo off
echo ========================================
echo Smart C-Lab Service Management
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
echo Current service status:
sc query "SmartCLab" 2>nul
if %errorLevel% == 0 (
    echo Service exists
) else (
    echo Service does not exist. Creating it now...
    
    if not exist "C:\php\php.exe" (
        echo ERROR: PHP not found at C:\php\php.exe
        echo Please install PHP first
        pause
        exit /b 1
    )
    
    if not exist "C:\nssm\win64\nssm.exe" (
        echo ERROR: NSSM not found at C:\nssm\win64\nssm.exe
        echo Please install NSSM first
        pause
        exit /b 1
    )
    
    if not exist "C:\smart-c-lab" (
        echo ERROR: Application not found at C:\smart-c-lab
        echo Please run the deployment script first
        pause
        exit /b 1
    )
    
    echo Creating Windows Service...
    C:\nssm\win64\nssm.exe install "SmartCLab" "C:\php\php.exe" "-S 0.0.0.0:8000 -t C:\smart-c-lab\public"
    
    echo Configuring service...
    C:\nssm\win64\nssm.exe set "SmartCLab" DisplayName "Smart C-Lab Web Application"
    C:\nssm\win64\nssm.exe set "SmartCLab" Description "Smart C-Lab Laptop Management System"
    C:\nssm\win64\nssm.exe set "SmartCLab" Start SERVICE_AUTO_START
    C:\nssm\win64\nssm.exe set "SmartCLab" AppDirectory "C:\smart-c-lab"
    C:\nssm\win64\nssm.exe set "SmartCLab" AppStdout "C:\smart-c-lab\storage\logs\service.log"
    C:\nssm\win64\nssm.exe set "SmartCLab" AppStderr "C:\smart-c-lab\storage\logs\service-error.log"
    
    echo Service created successfully!
)

echo.
echo Starting Smart C-Lab service...
sc start "SmartCLab"

echo.
echo Waiting for service to start...
timeout /t 5 /nobreak >nul

echo.
echo Service status:
sc query "SmartCLab"

echo.
echo Checking if application is accessible...
timeout /t 3 /nobreak >nul
curl -s http://localhost:8000 >nul 2>&1
if %errorLevel% == 0 (
    echo ✓ Application is accessible at http://localhost:8000
) else (
    echo ✗ Application is not accessible at http://localhost:8000
    echo Check the logs at C:\smart-c-lab\storage\logs\service.log
)

echo.
echo ========================================
echo Service Management Complete
echo ========================================
echo.
echo Application URL: http://localhost:8000
echo Service Logs: C:\smart-c-lab\storage\logs\service.log
echo.
echo To stop the service: sc stop SmartCLab
echo To restart the service: sc stop SmartCLab && sc start SmartCLab
echo.
pause
