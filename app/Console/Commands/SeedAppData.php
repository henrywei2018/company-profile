<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataSeedingService;

class SeedAppData extends Command
{
    protected $signature = 'app:seed-data';
    protected $description = 'Seed all sample data for the application';

    public function handle(DataSeedingService $seeder)
    {
        $result = $seeder->seedAll();

        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}
