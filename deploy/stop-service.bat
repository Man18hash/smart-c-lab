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
sc query "SmartCLab"

echo.
echo Stopping Smart C-Lab service...
sc stop "SmartCLab"

echo.
echo Service stopped. Press any key to start it again, or close this window.
pause

echo.
echo Starting Smart C-Lab service...
sc start "SmartCLab"

echo.
echo Service started. Current status:
sc query "SmartCLab"

echo.
pause
