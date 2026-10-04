@echo off
REM ==========================================================================
REM  Daily backup of the e-CERTIFY database and uploaded images.
REM  1) Edit the 4 lines below.  2) Test it by double-clicking.
REM  3) Schedule it (see DEPLOYMENT.md, "Backups").
REM  Keep this file on the barangay PC only: it contains the database password.
REM ==========================================================================
setlocal
set DB_NAME=e_certify
set DB_USER=ecertify
set DB_PASS=CHANGE_THIS_PASSWORD
REM Best: a USB / external drive, e.g. E:\ecertify-backups
set DEST=C:\ecertify-backups

set MYSQLDUMP=C:\xampp\mysql\bin\mysqldump.exe
set APPDIR=%~dp0..

if not exist "%DEST%" mkdir "%DEST%"
for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmm"') do set STAMP=%%i

"%MYSQLDUMP%" -u%DB_USER% -p%DB_PASS% --single-transaction %DB_NAME% > "%DEST%\ecertify_%STAMP%.sql"
if errorlevel 1 (
    echo BACKUP FAILED. Check the settings at the top of this file.
    del "%DEST%\ecertify_%STAMP%.sql" 2>nul
    pause & exit /b 1
)

REM Uploaded logos / QR / signature
robocopy "%APPDIR%\storage\app\public" "%DEST%\uploads" /MIR /NFL /NDL /NJH /NJS >nul

REM Keep the last 60 days of database backups
forfiles /p "%DEST%" /m ecertify_*.sql /d -60 /c "cmd /c del @path" 2>nul

echo Backup saved: %DEST%\ecertify_%STAMP%.sql
exit /b 0
