<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
  return view('welcome');
});

Auth::routes();

// Route::post('register', 'Auth\RegisterController@register');
// Route::post('/register', 'API\RegistrationController@register');
Route::get('/home', 'HomeController@index')->name('home');

Route::resource('products', 'Admin\ProductController');
Route::resource('categories', 'Admin\CategoryController');

// Route::get('product', 'RazorpayController@razorpayProduct');
// Route::post('paysuccess', 'RazorpayController@paysuccess');
// Route::post('razor-thank-you', 'RazorpayController@thankYou');