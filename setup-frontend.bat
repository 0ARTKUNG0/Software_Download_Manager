@echo off
echo ========================================
echo    Software Download Manager Frontend
echo            Setup Script
echo ========================================
echo.

cd /d "%~dp0"

echo [1/6] Checking Node.js installation...
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js is not installed!
    echo Please install Node.js from https://nodejs.org/
    echo Recommended version: 18.x or higher
    pause
    exit /b 1
) else (
    echo ✓ Node.js is installed
    node --version
    npm --version
)

echo.
echo [2/6] Checking if frontend directory exists...
if not exist "frontend" (
    echo ERROR: Frontend directory not found!
    echo Make sure you're running this from the project root
    pause
    exit /b 1
)

echo.
echo [3/6] Installing frontend dependencies...
cd frontend
if exist "package-lock.json" (
    echo ✓ Found package-lock.json, using npm ci for faster install
    call npm ci
) else (
    echo Installing npm packages...
    call npm install
)

if errorlevel 1 (
    echo ERROR: Failed to install dependencies!
    echo Trying alternative install method...
    call npm install --legacy-peer-deps
    if errorlevel 1 (
        echo ERROR: All install methods failed!
        pause
        exit /b 1
    )
)

echo.
echo [4/6] Verifying React Scripts installation...
if not exist "node_modules\.bin\react-scripts.cmd" (
    echo Installing React Scripts...
    call npm install react-scripts@5.0.1
    if errorlevel 1 (
        echo WARNING: React Scripts install failed, but continuing...
    )
) else (
    echo ✓ React Scripts found
)

echo.
echo [5/6] Checking required dependencies...
if not exist "node_modules\axios" (
    echo Installing missing axios...
    call npm install axios
)
if not exist "node_modules\react-router-dom" (
    echo Installing missing react-router-dom...
    call npm install react-router-dom
)
if not exist "node_modules\tailwindcss" (
    echo Installing missing tailwindcss...
    call npm install tailwindcss
)

echo.
echo [6/6] Frontend setup completed!
echo.
echo ========================================
echo           Ready to Start!
echo ========================================
echo.
echo FRONTEND READY!
echo.
echo To start the development server:
echo   cd frontend
echo   npm start
echo.
echo URLs:
echo   Frontend: http://localhost:3000
echo   Backend:  http://localhost:8000
echo.
echo IMPORTANT:
echo 1. Make sure backend is running first
echo 2. Database must be migrated and seeded
echo 3. Check browser console for any errors
echo.
echo Enjoy your Software Download Manager!
echo.
pause