<?php
$ns = 'Gmf\Sys\Http\Controllers';
Route::middleware(['web', 'auth'])->prefix('sys/lns')->namespace($ns)->group(function () {
	Route::get('regist', 'LnsController@getWebRegist');
	Route::post('regist', 'LnsController@storeWebRegist');
});
Route::middleware(['web'])->prefix('sys')->namespace($ns)->group(function () {
	Route::get('image/{id}', 'ImageController@show');
});