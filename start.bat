@echo off
echo Starting Course Mark Management System...
echo.

echo Starting Backend Server (PHP)...
start "Backend Server" cmd /k "cd backend && php -S localhost:8000 simple-index.php"

echo Starting Frontend Server (Vue.js)...
start "Frontend Server" cmd /k "cd frontend && npm run dev"

echo.
echo Application is starting...
echo Backend: http://localhost:8000
echo Frontend: http://localhost:3000
echo.
echo Press any key to exit...
pause > nul 