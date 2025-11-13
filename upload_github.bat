@echo off
echo ================================
echo   AUTO UPLOAD TO GITHUB
echo ================================
echo.

REM Inisialisasi git jika belum
git init

REM Tambahkan remote origin (diabaikan jika sudah ada)
git remote remove origin
git remote add origin YOUR_REPO_URL

REM Add semua file
git add .

REM Commit dengan tanggal otomatis
set dt=%date% %time%
git commit -m "Auto commit on %dt%"

REM Push ke GitHub
git branch -M main
git push -u origin main

echo.
echo ==== SELESAI UPLOAD KE GITHUB ====
pause
