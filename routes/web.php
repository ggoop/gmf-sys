<?php
$ns = 'Gmf\Sys\Http\Controllers';

Route::prefix('oauth')->middleware(['throttle'])->namespace($ns)->group(function () {
  Route::post('/token', 'Passport\AccessTokenController@issueToken');
});

Route::prefix('oauth')->middleware(['web'])->namespace($ns)->group(function () {
  Route::get('/authorize', 'OAuth\AuthorizeController@getAuthorize');
});