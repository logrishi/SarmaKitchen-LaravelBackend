<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

//auth
Route::post('signup', 'AuthAPI\AuthController@register');
Route::post('login', 'AuthAPI\AuthController@login');
Route::post('logout', 'AuthAPI\AuthController@logout');
// Route::post('refresh', 'AuthController@refresh');
// Route::post('me', 'AuthController@me');

//products
Route::resource('products', 'Admin\ProductController');
Route::get('productsByMealType', 'Admin\ProductController@getProductsByMealType');
Route::resource('categories', 'Admin\CategoryController');

// config
Route::get('getSubscriptions', 'Admin\ConfigController@getSubscriptions');
Route::get('getDates', 'Admin\ConfigController@getDates');
Route::get('getLocationInfo', 'Admin\ConfigController@getLocationInfo');
// Route::middleware('isAdmin')->get('getToday', 'Admin\ConfigController@getToday');
Route::get('getToday', 'Admin\ConfigController@getToday');
Route::get('getTime', 'Admin\ConfigController@getTime');


//address
Route::middleware('auth:api')->resource('address', 'AddressController');

//orders
Route::middleware('auth:api')->resource('orders', 'OrderController');
Route::middleware('auth:api')->post('createOrder', 'OrderController@createOrder');
Route::middleware('auth:api')->post('saveRating', 'OrderController@saveRating');
Route::middleware('auth:api')->get('getRating', 'OrderController@getRating');
Route::middleware('auth:api')->resource('cart', 'CartController');
Route::middleware('auth:api')->post('fcm', 'OrderController@fcm');

//admin orders
Route::middleware('isAdmin')->resource('manageOrders', 'Admin\ManageOrdersController');
Route::middleware('isAdmin')->post('updateOrderCourier', 'Admin\ManageOrdersController@updateOrderCourier');
Route::middleware('isAdmin')->post('updateOrderStatus', 'Admin\ManageOrdersController@updateOrderStatus');
Route::middleware('isAdmin')->post('updateSubscriptionItems', 'Admin\ManageOrdersController@updateSubscriptionItems');
Route::middleware('isAdmin')->get('getPastOrders', 'Admin\ManageOrdersController@getPastOrders');
Route::middleware('isAdmin')->get('getCourier', 'Admin\ManageOrdersController@getCourier');

//courier
Route::middleware('isCourier')->get('getOrdersByCourier', 'Admin\ManageOrdersController@getOrdersByCourier');
Route::middleware('isCourier')->post('verifyOtp', 'Admin\ManageOrdersController@verifyOtp');
Route::middleware('isCourier')->get('getDeliveredOrdersByCourier', 'Admin\ManageOrdersController@getDeliveredOrdersByCourier');

////////////////////////////////////////////////////////////////////////
Route::post('productSearch', 'Admin\ProductController@productSearch');

// Route::middleware('jwt.auth')->resource('address', 'AddressController');