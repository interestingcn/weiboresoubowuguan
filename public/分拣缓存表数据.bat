@echo off
CHCP 65001
:update
php autoUpdate.php power/updateTitle
goto update
