<?php
$ns = 'Gmf\Sys\Http\Controllers';

Route::prefix('api/sys/auth')->middleware(['web'])->namespace($ns)->group(function () {
	Route::post('/token', 'AuthController@issueToken');
	Route::post('/login', 'AuthController@issueLogin');
	Route::any('/logout', 'AuthController@issueLogout');
});
Route::prefix('api/sys/auth')->middleware(['web', 'auth'])->namespace($ns)->group(function () {
	Route::post('/entry-ent/{id}', 'AuthController@entryEnt');
});
Route::prefix('api/sys')->middleware(['api'])->namespace($ns)->group(function () {

	Route::get('uid', 'DataController@issueUid');
	Route::resource('datas', 'DataController', ['only' => ['index', 'show']]);
	Route::resource('components', 'ComponentController', ['only' => ['index', 'show']]);
});

Route::prefix('api/sys')->middleware(['api', 'auth:api'])->namespace($ns)->group(function () {

	Route::post('/lns/request', 'LnsController@issueRequest');
	Route::post('/lns/answer', 'LnsController@issueAnswer');
	Route::post('/lns/regist', 'LnsController@storeRegist');

	Route::get('/entities/pager', 'EntityController@pager');
	Route::get('/enums/all', 'EntityController@getAllEnums');
	Route::get('/enums/{enum}', 'EntityController@getEnum');
	Route::resource('entities', 'EntityController', ['only' => ['index', 'show']]);

	Route::get('/queries/{query}/cases', 'QueryController@getCases');
	Route::post('/queries/query/{query}', 'QueryController@query');
	Route::resource('queries', 'QueryController', ['only' => ['index', 'show']]);

	Route::resource('query-cases', 'QueryCaseController', ['only' => ['show', 'store', 'destroy']]);

	Route::get('/menus/all', 'MenuController@all');
	Route::get('/menus/path/{id}', 'MenuController@getPath');
	Route::resource('menus', 'MenuController', ['only' => ['index', 'show']]);

	Route::resource('languages', 'LanguageController', ['only' => ['index', 'show']]);

	Route::resource('users', 'UserController', ['only' => ['index', 'show']]);

	Route::post('/profiles/batch', 'ProfileController@batchStore');
	Route::resource('profiles', 'ProfileController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('files', 'FileController', ['only' => ['store', 'show']]);

	Route::get('/ents/my', 'EntController@getMyEnts');
	Route::any('/ents/seed/{id}', 'EntController@seedDatas');
	Route::resource('ents', 'EntController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('dtis', 'DtiController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
	Route::resource('dti-categories', 'DtiCategoryController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
	Route::resource('dti-params', 'DtiParamController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

});

Route::prefix('api/sys/authority')->middleware(['api', 'auth:api'])->namespace($ns)->group(function () {
	Route::post('/roles/batch', 'Authority\RoleController@batchStore');
	Route::resource('roles', 'Authority\RoleController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::post('/permits/batch', 'Authority\PermitController@batchStore');
	Route::resource('permits', 'Authority\PermitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	Route::resource('role-entities', 'Authority\RoleEntityController', ['only' => ['index', 'store', 'destroy']]);

	Route::resource('role-permits', 'Authority\RolePermitController', ['only' => ['index', 'store', 'destroy']]);

	Route::resource('role-menus', 'Authority\RoleMenuController', ['only' => ['index', 'store', 'destroy']]);

	Route::resource('role-users', 'Authority\RoleUserController', ['only' => ['index', 'store', 'destroy']]);

});