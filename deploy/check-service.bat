@echo off
echo ========================================
echo Smart C-Lab Service Status Check
echo ========================================
echo.

echo Checking if SmartCLab service exists...
sc query "SmartCLab" 2>nul
if %errorLevel% == 0 (
    echo.
    echo Service exists. Current status:
    sc query "SmartCLab"
    echo.
    echo Service configuration:
    sc qc "SmartCLab"
) else (
    echo.
    echo ERROR: SmartCLab service does not exist!
    echo.
    echo To fix this, run:
    echo 1. deploy\fix-deployment.bat (as Administrator)
    echo 2. Or manually create the service
)

echo.
echo Checking if application files exist...
if exist "C:\smart-c-lab" (
    echo ✓ Application directory exists: C:\smart-c-lab
) else (
    echo ✗ Application directory missing: C:\smart-c-lab
)

if exist "C:\smart-c-lab\artisan" (
    echo ✓ Laravel artisan file exists
) else (
    echo ✗ Laravel artisan file missing
)

if exist "C:\smart-c-lab\.env" (
    echo ✓ Environment file exists
) else (
    echo ✗ Environment file missing
)

echo.
echo Checking if PHP is accessible...
if exist "C:\php\php.exe" (
    echo ✓ PHP found at C:\php\php.exe
    C:\php\php.exe --version
) else (
    echo ✗ PHP not found at C:\php\php.exe
)

echo.
echo Checking if Composer is accessible...
if exist "C:\composer\composer.exe" (
    echo ✓ Composer found at C:\composer\composer.exe
    C:\composer\composer.exe --version
) else (
    echo ✗ Composer not found at C:\composer\composer.exe
)

echo.
echo Checking if NSSM is accessible...
if exist "C:\nssm\win64\nssm.exe" (
    echo ✓ NSSM found at C:\nssm\win64\nssm.exe
) else (
    echo ✗ NSSM not found at C:\nssm\win64\nssm.exe
)

echo.
echo Checking if port 8000 is in use...
netstat -an | findstr ":8000" >nul
if %errorLevel% == 0 (
    echo ✓ Port 8000 is in use (service might be running)
    netstat -an | findstr ":8000"
) else (
    echo ✗ Port 8000 is not in use (service not running)
)

echo.
echo ========================================
echo Service Check Complete
echo ========================================
echo.
echo If the service is not running, try:
echo 1. Run deploy\fix-deployment.bat as Administrator
echo 2. Check the logs at C:\smart-c-lab\storage\logs\service.log
echo 3. Manually start the service: sc start SmartCLab
echo.
pause
