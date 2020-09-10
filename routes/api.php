<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//routes for login and register of users
Route::post('/register', 'UserController@registerUserData');
Route::get('/login','UserController@loginUser');

//routes to check user and get userdata
Route::get('/authUserAndGetData', 'UserController@checkUserData');

//routes to save modificacions
Route::post('/saveModificationsToUserLists', 'ToDoListController@saveChanges');
Route::post('/saveModificationsToUserTasks', 'TaskController@saveChanges');

//ROUTES for version 1.1
Route::put('/update', 'UserController@update');
Route::delete('/delete', 'UserController@delete');


