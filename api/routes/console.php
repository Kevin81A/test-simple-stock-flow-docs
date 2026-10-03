<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Simple Stock Flow API');
})->purpose('Display an inspiring quote');
