<?php
$ns = 'Gmf\Sys\Http\Controllers\Passport';

Route::prefix('oauth')->middleware(['throttle'])->namespace($ns)->group(function () {
	Route::post('/token', 'AccessTokenController@issueToken');
});