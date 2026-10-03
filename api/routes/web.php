<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('', 404, ['Content-Length' => '0']);
});
