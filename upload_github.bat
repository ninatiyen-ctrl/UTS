@echo off
echo ======================================
echo     AUTO UPLOAD FLUTTER TO GITHUB
echo ======================================
echo.

REM ====== SETUP REPO ======
git init

REM Set remote origin baru (hapus kalau sudah ada)
git remote remove origin 2>nul
git remote add origin https://github.com/ninatiyen-ctrl/UTS.git

echo.
echo ====== PULL LATEST FROM GITHUB ======
git pull origin main --allow-unrelated-histories --no-edit

REM Jika ada konflik di README.md → pakai versi lokal
if exist README.md (
    git add README.md
)

echo.
echo ====== ADD ALL FILES ======
git add .

REM Commit otomatis dengan timestamp
for /f "tokens=1-5 delims=/:. " %%a in ("%date% %time%") do (
    set dt=%%a-%%b-%%c_%%d-%%e
)
git commit -m "Auto commit on %dt%"

echo.
echo ====== PUSH TO GITHUB ======
git branch -M main
git push -u origin main

echo.
echo ========== DONE ==========
echo File Flutter berhasil di-upload ke GitHub!
pause
