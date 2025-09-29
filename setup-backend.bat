@echo off
echo ========================================
echo   Software Download Manager Backend
echo            Setup Script
echo ========================================
echo.
echo REQUIREMENTS:
echo 1. XAMPP (Apache + MySQL)
echo 2. Composer installed globally
echo 3. PHP 8.0+ installed
echo.
echo Press any key to continue...
pause > nul

cd /d "%~dp0\backend"

echo [1/7] Checking PHP installation...
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH!
    echo Please install PHP or add it to your PATH
    pause
    exit /b 1
) else (
    echo ✓ PHP is installed
    php --version | findstr /C:"PHP"
)

echo.
echo [2/7] Checking Composer installation...
composer --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Composer is not installed!
    echo Please install Composer from https://getcomposer.org/
    pause
    exit /b 1
) else (
    echo ✓ Composer is installed
)

echo.
echo [3/7] Creating required directories...
if not exist "bootstrap\cache" mkdir bootstrap\cache
if not exist "storage\framework" mkdir storage\framework
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\framework\cache\data" mkdir storage\framework\cache\data
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\logs" mkdir storage\logs
if not exist "storage\app" mkdir storage\app
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\app\public\downloads" mkdir storage\app\public\downloads
if not exist "storage\app\temp" mkdir storage\app\temp
echo ✓ Directories created

echo.
echo [4/7] Installing Composer dependencies...
if not exist "vendor" (
    call composer install
    if errorlevel 1 (
        echo ERROR: Composer install failed!
        pause
        exit /b 1
    )
) else (
    echo ✓ Dependencies already installed
    call composer update
)

echo.
echo [5/7] Setting up environment file...
if not exist ".env" (
    if exist ".env.example" (
        copy .env.example .env
        echo ✓ Environment file created from .env.example
    ) else (
        echo WARNING: No .env.example found
    )
) else (
    echo ✓ Environment file already exists
)

echo.
echo [6/7] Generating application key...
call php artisan key:generate
if errorlevel 1 (
    echo ERROR: Failed to generate application key!
    pause
    exit /b 1
)

echo.
echo [7/7] Generating JWT secret...
call php artisan jwt:secret --force
if errorlevel 1 (
    echo ERROR: Failed to generate JWT secret!
    echo Manual JWT secret has been set in .env
)

echo.
echo ========================================
echo        Backend Setup Complete!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Configure database in backend\.env:
echo    - DB_DATABASE=software_download_manager
echo    - DB_USERNAME=root
echo    - DB_PASSWORD=(your MySQL password)
echo.
echo 2. Create database in phpMyAdmin:
echo    CREATE DATABASE software_download_manager;
echo.
echo 3. Run migrations:
echo    Double-click 'run-migrations.bat'
echo.
echo 4. Start server:
echo    php artisan serve
echo.
pause
