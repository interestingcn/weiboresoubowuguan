@echo off
CHCP 65001
:Monitor
php autoUpdate.php firewall/Monitor
goto Monitor
