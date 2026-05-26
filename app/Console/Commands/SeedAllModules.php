<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

class SeedAllModules extends Command
{
    protected $signature = 'module:seed-all';
    protected $description = 'Run seeders for all modules';

    public function handle()
    {
        foreach (Module::all() as $module) {
            $name = $module->getName();
            $this->info("Seeding module: $name");
            $this->call('module:seed', ['module' => $name]);
        }
    }
}
