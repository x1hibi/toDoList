<?php

use Illuminate\Support\Facades\Route;

//SAP page 
Route::get('/', function () {
    return view('list');
});
