<?php

return [
	'publishes' => env('GMF_PUBLISHES', 'gmf'),
	//授权
	'auth' => [
		'proxy' => env('GMF_AUTH_PROXY'),
		'redirect' => env('GMF_AUTH_REDIRECT', '/'),
	],
	//用户信息
	'user' => [
		//用户模型
		'model' => env('GMF_USER_MODEL', Gmf\Sys\Models\User::class),
		//用户实体编码
		'entity' => env('GMF_USER_ENTITY', 'gmf.sys.user'),
	],
	//应用信息
	'client' => [
		'id' => env('GMF_OAUTH_CLIENT_ID', ''),
		'name' => env('APP_NAME', ''),
		'secret' => env('GMF_OAUTH_CLIENT_SECRET', ''),
		'user' => env('GMF_OAUTH_CLIENT_USER', ''),
	],
	//管理员信息
	'admin' => [
		'id' => env('GMF_ADMIN_USER_ID', ''),
		'email' => env('GMF_ADMIN_USER_EMAIL', ''),
		'pwd' => env('GMF_ADMIN_USER_PWD', ''),
	],
	//企业信息
	'ent' => [
		'session' => env('GMF_ENT_SESSION', 'GMFENTSESSIONNAME'),
		'id' => env('GMF_ENT_ID'),
		'name' => env('GMF_ENT_NAME'),
	],
];
