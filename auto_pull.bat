@echo off
title Auto Pull GitHub - MAS Mifhda
color 0A

echo ==========================================
echo    SYSTEM AUTO PULL GITHUB AKTIF (MAS MIFHDA)
echo ==========================================
echo.

:loop
echo [%date% %time%] Sedang mengecek update terbaru dari GitHub...
git pull origin main
echo.
echo Menunggu 60 detik sebelum mengecek lagi...
timeout /t 60 >nobreak
goto loop
