<?php

use Illuminate\Support\Facades\Schedule;
use Modules\HRM\Services\SalaryService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::call(function () {
    app(SalaryService::class)->generateAll();
})->everyMinute();