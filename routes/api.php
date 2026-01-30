<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

/*
|--------------------------------------------------------------------------
| Auth Routes (Public)
|--------------------------------------------------------------------------
*/
$router->group(['prefix' => 'auth', 'namespace' => 'Api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
$router->group(['middleware' => 'auth', 'namespace' => 'Api'], function () use ($router) {
    // Auth
    $router->post('auth/logout', 'AuthController@logout');
    $router->get('auth/profile', 'AuthController@profile');
    
    // Services (Read Only - Customer dapat akses)
    $router->get('services', 'ServiceController@index');
    $router->get('services/{id}', 'ServiceController@show');
    
    // Barbers (Read Only - Customer dapat akses)
    $router->get('barbers', 'BarberController@index');
    $router->get('barbers/{id}', 'BarberController@show');
    
    // Bookings (Customer dapat akses semua)
    $router->get('bookings', 'BookingController@index');
    $router->post('bookings', 'BookingController@store');
    $router->get('bookings/available-slots', 'BookingController@getAvailableSlots');
    $router->get('bookings/{id}', 'BookingController@show');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes (Require Authentication + Admin Role)
|--------------------------------------------------------------------------
*/
$router->group(['middleware' => ['auth', 'admin'], 'namespace' => 'Api'], function () use ($router) {
    // Services Management (Admin Only)
    $router->post('services', 'ServiceController@store');
    $router->put('services/{id}', 'ServiceController@update');
    $router->delete('services/{id}', 'ServiceController@destroy');
    
    // Barbers Management (Admin Only)
    $router->post('barbers', 'BarberController@store');
    $router->put('barbers/{id}', 'BarberController@update');
    $router->patch('barbers/{id}/status', 'BarberController@updateStatus');
    $router->delete('barbers/{id}', 'BarberController@destroy');
    
    // Booking Management (Admin Only)
    $router->patch('bookings/{id}/status', 'BookingController@updateStatus');
});
