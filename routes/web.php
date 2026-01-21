<?php

use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register');

// Dashboard routes
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Asset request routes
Route::post('/asset-requests', 'AssetRequestController@create')->name('asset.requests.create');
Route::post('/asset-requests/{id}/approve', 'AssetRequestController@approve')->name('asset.requests.approve');
Route::post('/asset-requests/{id}/reject', 'AssetRequestController@reject')->name('asset.requests.reject');
Route::post('/asset-requests/{id}/issue', 'AssetRequestController@issue')->name('asset.requests.issue');
Route::post('/asset-requests/{id}/accept', 'AssetRequestController@accept')->name('asset.requests.accept');

// Asset routes
Route::get('/assets', 'AssetController@index')->name('assets.list');
Route::post('/assets', 'AssetController@create')->name('assets.create');
Route::put('/assets/{id}', 'AssetController@edit')->name('assets.edit');
Route::delete('/assets/{id}', 'AssetController@delete')->name('assets.delete');

// Report routes
Route::get('/reports', 'ReportController@index')->name('reports.index');
Route::get('/reports/{id}', 'ReportController@show')->name('reports.show');
