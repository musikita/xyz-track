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

Route::middleware(['guest:cms'])->group(function () {
    // CMS Auth
    Route::get('/login', 'Auth\LoginController@cmsLoginForm')->name('cms.login');
    Route::post('/login', 'Auth\LoginController@cmsLogin')->name('cms.login.login');
});

Route::middleware(['auth:cms'])->group(function () {
    Route::get('/', 'Cms\DashboardController@index')->name('cms.index');
    Route::get('/logout', 'Auth\LoginController@cmsLogout')->name('cms.logout');
});
