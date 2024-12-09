@echo off
ECHO Wait in progress...
TITLE Google Two Factor
start http://localhost:4466
php -S localhost:4466 src/index.php
PAUSE