<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['success' => 'ok'], Response::HTTP_OK);
});
