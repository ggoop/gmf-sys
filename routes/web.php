<?php
$ns = 'Gmf\Sys\Http\Controllers';
Route::middleware(['web', 'auth'])->prefix('sys/lns')->namespace($ns)->group(function () {
	Route::get('regist', 'LnsController@getWebRegist');
	Route::post('regist', 'LnsController@storeWebRegist');
});
