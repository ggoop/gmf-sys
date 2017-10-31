<?php
Route::middleware(['web'])
	->prefix('sys/lns')
	->namespace('Gmf\Sys\Http\Controllers')
	->group(function () {
		Route::get('regist', ['uses' => 'LnsController@getWebRegist']);
		Route::post('regist', ['uses' => 'LnsController@storeWebRegist']);
	});