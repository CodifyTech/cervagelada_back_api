<?php

use App\Console\Commands\AuditCheckAlertsCommand;
use App\Domains\Pedido\Commands\ExpireUnpaidOrders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run operational alert checks every 5 minutes
Schedule::command(AuditCheckAlertsCommand::class)->everyFiveMinutes();

// Expire unpaid orders every 10 minutes (30-min timeout)
Schedule::command(ExpireUnpaidOrders::class)->everyTenMinutes();
