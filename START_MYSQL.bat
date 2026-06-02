@echo off
echo ============================================
echo    Starting MySQL Service...
echo ============================================

REM Try to start MySQL service
net start MySQL80

if %errorlevel% neq 0 (
    echo.
    echo MySQL service not found or already running.
    echo Trying alternative service names...
    net start MySQL
    if %errorlevel% neq 0 (
        echo.
        echo ERROR: MySQL service not found!
        echo.
        echo Please ensure MySQL is installed:
        echo - XAMPP: Start from XAMPP Control Panel
        echo - WAMP: Start from WAMP System Tray
        echo - Manual MySQL: Install from mysql.com
        echo.
        pause
        exit /b 1
    )
)

echo.
echo ============================================
echo    MySQL Started Successfully!
echo ============================================
echo.
echo Next steps:
echo 1. Open: http://localhost/thesis/setup.php
echo 2. Login with: admin / admin123
echo.
pause
