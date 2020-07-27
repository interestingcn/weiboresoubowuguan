@echo off
CHCP 65001
:check
php autoUpdate.php power/checkMax
goto check
