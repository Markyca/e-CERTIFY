@echo off
REM ==========================================================================
REM  Run this ON THE BARANGAY PC after the files are in C:\xampp\htdocs\e-certify
REM  and the MySQL database + user have been created (see DEPLOYMENT.md).
REM  Right-click this file and choose "Run as administrator".
REM  Safe to run again after every update.
REM ==========================================================================
setlocal
set PHP=C:\xampp\php\php.exe
cd /d "%~dp0.."

if not exist "%PHP%" (
    echo Could not find PHP at %PHP%
    echo Edit the first lines of this file if XAMPP is installed somewhere else.
    pause & exit /b 1
)

if not exist ".env" (
    copy "deploy\env.production.example" ".env" >nul
    echo Created .env from the production template.
    echo.
    echo  ** Open .env now and set DB_PASSWORD, save it, then run this file again. **
    notepad ".env"
    pause & exit /b 0
)

REM Create the app key only once. Changing it later would log everyone out.
findstr /b /c:"APP_KEY=base64:" ".env" >nul
if errorlevel 1 "%PHP%" artisan key:generate --force

echo.
echo Creating / updating database tables...
"%PHP%" artisan migrate --force
if errorlevel 1 (
    echo.
    echo Migration failed. Check that MySQL is running and the DB_* lines in .env are right.
    pause & exit /b 1
)

echo.
echo Linking the uploads folder (logos, signature)...
if not exist "public\storage" "%PHP%" artisan storage:link

REM Clear any old cached settings
"%PHP%" artisan config:clear >nul
"%PHP%" artisan view:clear >nul

echo.
echo All set. Open  http://ecertify.local  in a browser.
pause
