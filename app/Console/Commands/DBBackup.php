<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;
use Storage;

class DBBackup extends Command
{
    protected $process;

    protected $signature = 'db:backup';

    protected $description = 'Export database as sql file.';

    public function __construct()
    {
        parent::__construct();

        // Storage::


        $this->process = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('DBBackups/' . Date('Ymd-His') . '.sql')
        ));
    }

      public function handle()
    {
        try {
            Storage::disk('backup')->makeDirectory('DBBackups');
            $this->process->mustRun();
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}
