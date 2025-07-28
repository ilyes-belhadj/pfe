<?php

use Illuminate\Support\Facades\Route;
 

 

//Clear config cache
Route::get('/config-cache', function() {
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
    \Artisan::call('optimize:clear');
    \Artisan::call('route:cache');
    return 'Config cache cleared & cache cleared : optimize, route';
})->name('config.cache');