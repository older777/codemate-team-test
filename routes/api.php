<?php

use App\Http\Controllers\AccountOperation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::controller(AccountOperation::class)->group(function (Router $route) {
    $route->post('/deposit', 'deposit')->name('deposit');
    $route->post('/withdraw', 'withdraw')->name('withdraw');
    $route->post('/transfer', 'transfer')->name('transfer');
    $route->get('/balance/{user}', 'getBalance')->name('balance');
});
