<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeLocalCommand extends Command
{
    protected $signature = 'serve:local';
    protected $description = 'Start the Laravel development server on localhost:8000';

    public function handle(): void
    {
        $this->info('Starting Laravel development server on localhost:8000...');
        passthru('php artisan serve --host=localhost --port=8000');
    }
}
