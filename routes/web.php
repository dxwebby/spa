<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

////////////////////////////////////////////////////////////////////
// Overwrite register
Auth::routes(['register' => false]);
////////////////////////////////////////////////////////////////////
//Auth::routes(['verify' => true, 'register' => false]);
// Auth::routes();
Route::get('/', 'PagesController@index');
Route::get('/home', 'HomeController@index')->name('home');

// Expenses Charts & Bills for each Payments (Range 2 months)
Route::get('/chart/expenses', 'ExpensesController@chart');
Route::get('/chart/bills', 'BillsController@billsChart');

// Charts full
Route::get('/chart/full/expenses', 'ExpensesController@expensesChartFull');
Route::get('/chart/full/bills', 'BillsController@billsChartFull');

////////////////////////////////////////////////////////////////////

// Unlisted
Route::get('/unlisted', 'UnlistedController@index');
Route::get('/unlisted/{id}', 'HomeController@redirectPost');
Route::post('/unlisted/{id}', 'UnlistedController@billId');
Route::get('/unlisted/history/{id}', 'UnlistedController@billHistory');
Route::patch('/unlisted/action', 'UnlistedController@update');
Route::delete('/unlisted/delete/{id}', 'UnlistedController@destroy');

// Unlisted Expenses
Route::get('/unlistedexpenses', 'UnlistedController@unlistedExpenses');
Route::put('/unlisted/action', 'UnlistedController@update');
Route::delete('/unlistedexpenses/delete/{id}', 'UnlistedController@deleteExpenses');

////////////////////////////////////////////////////////////////////
// BillsController
Route::get('/bills', 'BillsController@index');

// Add New Bill
Route::post('/bills/new', 'BillsController@store');
Route::get('/bills/new', 'HomeController@redirectPost');

// Update Bill
Route::patch('/bills/update/{id}', 'BillsController@update');

// Delete Bill
Route::delete('/bills/delete/{id}', 'BillsController@destroy');

// Mark Bill as Paid
Route::patch('/bills/paid/{id}', 'BillsController@markaspaid');

// Unmark Bill
Route::patch('/bills/unpaid/{id}', 'BillsController@unmarkaspaid');

////////////////////////////////////////////////////////////////////
// Get Payment Data
Route::get('/payments/data/{id}', 'PaymentsController@payment');

// Add Payment
Route::get('/newpayment', 'HomeController@redirectPost');
Route::post('/newpayment', 'PaymentsController@store');

// Delete Payment
Route::post('/payment/delete/{id}', 'PaymentsController@destroy');
////////////////////////////////////////////////////////////////////

// Get expenses
Route::get('/allexpenses', 'ExpensesController@index');
// Add Expenses
Route::get('/expenses', 'ExpensesController@redirectPost');
Route::post('/expenses', 'ExpensesController@store');

// Edit Expenses (editexpenses.vue) retrieving payments
Route::get('/payments/{id}', 'HomeController@payments');

// Update Expenses Payment (myexpense.vue)
Route::post('/expenses/payment/{id}', 'ExpensesController@updatePayment');

// Delete Expenses
Route::post('/expenses/delete/{id}', 'ExpensesController@destroy');

// Update Expenses 
Route::get('/update/expenses/{id}', 'HomeController@redirectPost');
Route::patch('/update/expenses/{id}', 'ExpensesController@update');

// Delete Expenses 
Route::get('/delete/expenses/{id}', 'HomeController@redirectPost');
Route::post('/delete/expenses/{id}', 'ExpensesController@destroy');

// Create pdf
Route::get('/dynamic_pdf', 'PDFController@index');
Route::get('/dynamic_pdf/pdf/{option}', 'PDFController@pdf');
Route::get('/dynamic_pdf/expenses/pdf/{option}', 'PDFController@pdfExpenses');

////////////////////////////////////////////////////////////////////

// Expenses Main Router
Route::get('/batches', 'ExpensesController@batches');
Route::get('/batches/{id}', 'ExpensesController@batchById');

////////////////////////////////////////////////////////////////////

// Route::get('/test', 'api\ExpensesController@test');
Route::get('/test', 'HomeController@test');


// Logout for prevent accessing /logout route
Route::get('logout', 'Auth\LoginController@logout', function () {
    return abort(404);
});

////////////////////////////////////////////////////////////////////