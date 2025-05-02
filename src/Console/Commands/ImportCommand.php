<?php

namespace KhanhDuy\DanhmuchanhchinhvnMaps\Console\Commands;

use KhanhDuy\DanhmuchanhchinhvnMaps\Helpers\DownloadFile;
use KhanhDuy\DanhmuchanhchinhvnMaps\Imports\GSOImports;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gso:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data for GSO command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import...');

        if (file_exists(storage_path("excel/gso.xls"))) {
            $tmpFile = storage_path("excel/gso.xls");
        } else {
            $tmpFile = app(DownloadFile::class)->download() ?: realpath(__DIR__ . "/../../../database/excel/gso.xls");
        }

        if (empty($tmpFile) || !file_exists($tmpFile)) {
            $this->error('File not found.');
            return;
        }

        $this->info('File downloaded successfully.');

        $this->info('Importing data...');
        try {
            Excel::import(new GSOImports(), $tmpFile);
            $this->info('Data imported successfully.');
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return;
        }

        $this->info('Cleaning up temporary files...');
        if (File::exists($tmpFile)) {
            File::delete($tmpFile);
            $this->info('Deleted temp file: ' . $tmpFile);
        }
        if (File::exists(storage_path("excel/gso.xls"))) {
            File::delete(storage_path("excel/gso.xls"));
            $this->info('Deleted stored file: ' . storage_path("excel/gso.xls"));
        }

        $this->info('Import completed successfully.');
    }
}
