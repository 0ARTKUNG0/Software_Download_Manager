@echo off
echo ========================================
echo     Database Migration & Seeding
echo ========================================
echo.
echo Make sure you have:
echo 1. XAMPP MySQL running
echo 2. Created database 'software_download_manager'
echo 3. Backend .env file configured
echo.
echo Press any key to continue...
pause > nul

cd /d "%~dp0\backend"

echo.
echo [1/4] Testing database connection...
php -r "try { new PDO('mysql:host=127.0.0.1;dbname=software_download_manager', 'root', ''); echo 'Database connection: OK'; } catch(Exception \$e) { echo 'Database connection: FAILED - ' . \$e->getMessage(); exit(1); }"
if errorlevel 1 (
    echo.
    echo ERROR: Cannot connect to database!
    echo Please check:
    echo - XAMPP MySQL is running
    echo - Database 'software_download_manager' exists
    echo - .env database settings are correct
    pause
    exit /b 1
)

echo.
echo [2/4] Running database migrations...
call php artisan migrate
if errorlevel 1 (
    echo ERROR: Migration failed!
    pause
    exit /b 1
)

echo.
echo [3/4] Seeding database with software data...
call php artisan db:seed
if errorlevel 1 (
    echo WARNING: Seeding failed or already completed
)

echo.
echo [4/4] Creating storage symlink...
call php artisan storage:link

echo.
echo ========================================
echo       Database Setup Complete!
echo ========================================
echo.
echo You can now:
echo 1. Start backend: php artisan serve
echo 2. Start frontend: npm start
echo.
echo Backend API: http://localhost:8000/api
echo Frontend:    http://localhost:3000
echo.
pause
