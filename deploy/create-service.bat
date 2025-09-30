@echo off
echo ========================================
echo Smart C-Lab Windows Service Creation
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
echo Step 1: Creating Windows Service...
echo ========================================

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
echo Step 2: Starting the service...
echo ========================================

C:\nssm\win64\nssm.exe start "SmartCLab"

echo.
echo Step 3: Verifying service status...
echo ========================================

sc query "SmartCLab"

echo.
echo ========================================
echo Windows Service created successfully!
echo ========================================
echo.
echo Service Details:
echo - Name: SmartCLab
echo - Status: Auto-start on boot
echo - URL: http://localhost:8000
echo - Logs: C:\smart-c-lab\storage\logs\service.log
echo.
echo Management Commands:
echo - Start:   sc start SmartCLab
echo - Stop:    sc stop SmartCLab
echo - Restart: sc stop SmartCLab && sc start SmartCLab
echo - Status:  sc query SmartCLab
echo.
echo The application will now start automatically when the computer boots!
echo.
pause
