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
		//应用名称
		'name' => env('GMF_OAUTH_CLIENT_NAME', env('APP_NAME', '')),
		//应用秘钥,默认为应用KEY
		'secret' => env('GMF_OAUTH_CLIENT_SECRET', env('APP_KEY', '')),
		//应用默认用户，默认为系统管理员
		'user' => env('GMF_OAUTH_CLIENT_USER', env('GMF_ADMIN_USER_ACCOUNT', '')),
	],
	//管理员信息
	'admin' => [
		//管理员账号
		'account' => env('GMF_ADMIN_USER_ACCOUNT', ''),
		//管理员密码
		'pwd' => env('GMF_ADMIN_USER_PWD', ''),
	],
	//企业信息
	'ent' => [
		//企业缓存KEY
		'session' => env('GMF_ENT_SESSION', 'GMFENTSESSIONNAME'),
		//企业模型
		'model' => env('GMF_ENT_MODEL', Gmf\Sys\Models\Ent\Ent::class),
		//企业编码，
		'id' => env('GMF_ENT_ID', ''),
		//企业名称，默认为应用名称
		'name' => env('GMF_ENT_NAME', env('APP_NAME', '')),
		//企业管理员,默认为系统管理员
		'user' => env('GMF_ENT_USER', env('GMF_ADMIN_USER_ACCOUNT', '')),
	],
];
