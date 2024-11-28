<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Route::get("/get-updates", function () {
//     $updates = Telegram::getUpdates();
    
//     return $updates;
//   });


require __DIR__.'/auth.php';
