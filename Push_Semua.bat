@echo off
color 0A
title Push ke GitHub - MAS Mifhda
echo ==================================================
echo   TOMBOL AJAIB - PUSH KODE MAS MIFHDA KE GITHUB
echo ==================================================
echo.
set /p pesan="Masukkan pesan commit (atau tekan Enter untuk pesan otomatis): "
if "%pesan%"=="" set pesan=Auto sync kode dari laptop - %date% %time%

echo.
echo [1/3] Menambahkan file ke Git (git add .)...
git add .

echo.
echo [2/3] Menyimpan perubahan (git commit)...
git commit -m "%pesan%"

echo.
echo [3/3] Mengirim ke GitHub (git push)...
git push origin main

echo.
echo ==================================================
echo   SELESAI! KODE SUDAH DI PUSH KE GITHUB.
echo ==================================================
pause
