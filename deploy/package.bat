@echo off
echo ========================================
echo Smart C-Lab Deployment Package Creator
echo ========================================
echo.

echo Creating deployment package...

REM Create deployment directory
if not exist "deployment-package" mkdir "deployment-package"
if not exist "deployment-package\deploy" mkdir "deployment-package\deploy"

echo.
echo Step 1: Copying deployment scripts...
echo ========================================

copy "deploy\setup.bat" "deployment-package\deploy\"
copy "deploy\install-app.bat" "deployment-package\deploy\"
copy "deploy\create-service.bat" "deployment-package\deploy\"
copy "deploy\start-app.bat" "deployment-package\deploy\"
copy "deploy\stop-service.bat" "deployment-package\deploy\"
copy "deploy\update-app.bat" "deployment-package\deploy\"
copy "deploy\README.md" "deployment-package\deploy\"

echo.
echo Step 2: Copying application files...
echo ========================================

REM Copy essential application files
xcopy "app" "deployment-package\app\" /E /I /Y
xcopy "bootstrap" "deployment-package\bootstrap\" /E /I /Y
xcopy "config" "deployment-package\config\" /E /I /Y
xcopy "database" "deployment-package\database\" /E /I /Y
xcopy "public" "deployment-package\public\" /E /I /Y
xcopy "resources" "deployment-package\resources\" /E /I /Y
xcopy "routes" "deployment-package\routes\" /E /I /Y
xcopy "storage" "deployment-package\storage\" /E /I /Y

REM Copy essential files
copy "artisan" "deployment-package\"
copy "composer.json" "deployment-package\"
copy "composer.lock" "deployment-package\"
copy "package.json" "deployment-package\"
copy "package-lock.json" "deployment-package\"
copy "phpunit.xml" "deployment-package\"
copy "vite.config.js" "deployment-package\"
copy ".env.example" "deployment-package\"

echo.
echo Step 3: Creating deployment instructions...
echo ========================================

echo # Smart C-Lab Deployment Package > "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo. >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo This package contains everything needed to deploy Smart C-Lab on Windows. >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo. >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo QUICK START: >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo 1. Copy this entire folder to the target Windows machine >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo 2. Right-click deploy\setup.bat and select "Run as administrator" >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo 3. Right-click deploy\install-app.bat and select "Run as administrator" >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo 4. Right-click deploy\create-service.bat and select "Run as administrator" >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo. >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo The application will be available at http://localhost:8000 >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo. >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"
echo For detailed instructions, see deploy\README.md >> "deployment-package\DEPLOYMENT_INSTRUCTIONS.txt"

echo.
echo Step 4: Creating ZIP package...
echo ========================================

powershell -Command "& {Compress-Archive -Path 'deployment-package\*' -DestinationPath 'smart-c-lab-deployment.zip' -Force}"

echo.
echo ========================================
echo Deployment package created successfully!
echo ========================================
echo.
echo Package location: smart-c-lab-deployment.zip
echo.
echo To deploy on another machine:
echo 1. Copy smart-c-lab-deployment.zip to the target machine
echo 2. Extract the ZIP file
echo 3. Follow the instructions in DEPLOYMENT_INSTRUCTIONS.txt
echo.
echo The application will start automatically on boot!
echo.
pause
