<?php

return [
	'publishes' => env('GMF_PUBLISHES', 'gmf'),
	'ent_session_name' => env('GMF_ENT_SESSION_NAME', 'GMFENTSESSIONNAME'),
	'user' => [
		//用户模型
		'model' => env('GMF_USER_MODEL', Gmf\Ac\Models\User::class),
		//用户实体编码
		'entity' => env('GMF_USER_ENTITY', 'gmf.ac.user'),
	],

	'client' => [
		'id' => env('GMF_OAUTH_CLIENT_ID', ''),
		'name' => env('APP_TITLE', ''),
		'secret' => env('GMF_OAUTH_CLIENT_SECRET', ''),
		'user' => env('GMF_OAUTH_CLIENT_USER', ''),
	],

	'admin' => [
		'id' => env('GMF_ADMIN_USER_ID', ''),
		'email' => env('GMF_ADMIN_USER_EMAIL', ''),
		'pwd' => env('GMF_ADMIN_USER_PWD', ''),
	],

	'ent' => [
		'id' => env('GMF_ENT_ID'),
		'name' => env('GMF_ENT_NAME'),
	],
];
