<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Set the filename and path
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Retrieve MySQL credentials from .env
        $dbHost = env('DB_HOST');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbName = env('DB_DATABASE');

        // Run mysqldump command
        // $command = "mysqldump -h $dbHost -u $dbUser -p$dbPass $dbName > $path";
        $command = "\"C:\\xampp\\mysql\\bin\\mysqldump\" -h $dbHost -u $dbUser -p$dbPass $dbName > \"$path\"";

        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar == 0) {
            $this->info("Database backup successful! File saved at: {$path}");
        } else {
            $this->error("Database backup failed!");
        }
    }
}
