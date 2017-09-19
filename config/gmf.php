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
	'oauth_client_id' => env('GMF_OAUTH_CLIENT_ID', ''),
	'oauth_client_name' => env('APP_TITLE', ''),
	'oauth_client_secret' => env('GMF_OAUTH_CLIENT_SECRET', ''),
	'oauth_client_user' => env('GMF_OAUTH_CLIENT_USER', ''),

	'admin_user_id' => env('GMF_ADMIN_USER_ID', ''),
	'admin_user_email' => env('GMF_ADMIN_USER_EMAIL', ''),
	'admin_user_pwd' => env('GMF_ADMIN_USER_PWD', ''),
];
