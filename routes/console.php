<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('Stay focused and ship great software.');
})->purpose('Display an inspiring quote');
