@echo off
CHCP 65001
:update
choice /t 30 /d y /n >nul
php autoUpdate.php power/updateTitle
goto update