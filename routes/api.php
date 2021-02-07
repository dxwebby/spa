<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Custom protection by passing user's api_token 
// Get all expenses

Route::get('/expenses/{id}/{api_token}', 'api\ExpensesController@expenses');        
// Route::get('/expenses/all/{id}', 'api\ExpensesController@allexpenses');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});


// Delete selected expenses by id
//Route::post('/expenses/{id}/delete_expenses', 'api\ExpensesController@destroy');    