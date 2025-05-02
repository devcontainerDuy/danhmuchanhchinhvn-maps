<?php

namespace KhanhDuy\DanhmuchanhchinhvnMaps\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gso:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the application and sets up necessary configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting installation...');
        
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call("config:clear");
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Artisan::call('gso:import');

        $this->info('Installation completed successfully.');
    }
}
