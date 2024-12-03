@echo off
REM Navigate to the Laravel project directory
cd /d C:\inetpub\wwwroot\Dispenser\dispenser

REM Run the Artisan backup command
php artisan db:backup

REM Log the completion time (optional)
echo Backup completed at %date% %time% >> C:\Scripts\backup_log.txt
