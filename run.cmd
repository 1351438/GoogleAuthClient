@echo off
ECHO Wait in progress...
TITLE Google Two Factor
start http://localhost:4466
%cd%/php/php.exe -S localhost:4466 src/index.php
PAUSE