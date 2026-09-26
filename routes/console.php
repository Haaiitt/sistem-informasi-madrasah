<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\PublishScheduledPosts;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\TransitionAdmissionWaves;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command(PublishScheduledPosts::class)
    ->everyMinute();

Schedule::command(TransitionAdmissionWaves::class)
    ->everyMinute();
