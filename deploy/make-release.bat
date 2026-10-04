@echo off
REM ==========================================================================
REM  Run this on YOUR computer (the one with internet and Composer).
REM  It builds a clean copy of e-CERTIFY, ready to copy to the barangay PC
REM  with a USB drive. The barangay PC does not need internet or Composer.
REM ==========================================================================
setlocal
cd /d "%~dp0.."
set SRC=%CD%
set OUT=%SRC%\..\e-certify-release

echo.
echo [1/3] Installing production packages...
call composer install --no-dev --optimize-autoloader
if errorlevel 1 (echo Composer failed. & pause & exit /b 1)

echo.
echo [2/3] Copying files to "%OUT%" ...
REM Left out on purpose: .env (passwords), uploaded logos/signature, logs, .git, node_modules
robocopy "%SRC%" "%OUT%" /MIR /NFL /NDL /NJH /NJS /XD "%SRC%\.git" "%SRC%\.github" "%SRC%\node_modules" "%SRC%\tests" "%SRC%\storage\app\public" "%SRC%\public\storage" /XF .env *.patch *.zip *.log *.sqlite
if errorlevel 8 (echo Copy failed. & pause & exit /b 1)

REM Empty the temporary folders so the barangay PC starts clean
for %%D in (views sessions cache\data) do (
    if exist "%OUT%\storage\framework\%%D" rd /s /q "%OUT%\storage\framework\%%D"
    mkdir "%OUT%\storage\framework\%%D"
)
if not exist "%OUT%\storage\app\public" mkdir "%OUT%\storage\app\public"

REM Make sure the .bat files in the package use Windows line endings
powershell -NoProfile -Command "Get-ChildItem '%OUT%\deploy\*.bat' | ForEach-Object { (Get-Content $_.FullName) | Set-Content -Encoding ASCII $_.FullName }"
if not exist "%OUT%\storage\logs" mkdir "%OUT%\storage\logs"

echo.
echo [3/3] Done.
echo Copy this folder to the barangay PC:  %OUT%
echo (Put it at C:\xampp\htdocs\e-certify and follow DEPLOYMENT.md)
echo.
echo NOTE: run "composer install" again on your own PC if you want dev tools back.
pause
