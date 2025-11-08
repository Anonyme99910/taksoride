@echo off
echo ========================================
echo TaksoRide Database Setup
echo ========================================
echo.

REM Check if MySQL is running
echo Checking if MySQL is running...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running
) else (
    echo [ERROR] MySQL is not running!
    echo Please start MySQL from XAMPP Control Panel
    pause
    exit /b 1
)

echo.
echo Creating database 'hamma'...
echo.

REM Create database
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS hamma CHARACTER SET utf8 COLLATE utf8_unicode_ci;"

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database 'hamma' created successfully
) else (
    echo [ERROR] Failed to create database
    pause
    exit /b 1
)

echo.
echo Importing database schema...
echo This may take a few minutes...
echo.

REM Import schema
C:\xampp\mysql\bin\mysql.exe -u root hamma < "C:\xampp\htdocs\hamma\server\drop-files\install\database_setup.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo [SUCCESS] Database setup completed!
    echo ========================================
    echo.
    echo Database Name: hamma
    echo Admin Email: admin@taksoride.com
    echo Admin Password: admin123
    echo.
    echo Next steps:
    echo 1. Open http://localhost/hamma/server/public/
    echo 2. Login with admin credentials
    echo 3. Go to Service Activation to configure API keys
    echo.
) else (
    echo [ERROR] Failed to import database schema
    echo Please check the error messages above
)

echo.
pause
