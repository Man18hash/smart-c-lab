@echo off
echo ========================================
echo Smart C-Lab Application Startup
echo ========================================
echo.

cd /d "C:\smart-c-lab"

echo Starting Smart C-Lab application...
echo URL: http://localhost:8000
echo.
echo Press Ctrl+C to stop the application
echo.

C:\php\php.exe artisan serve --host=0.0.0.0 --port=8000
