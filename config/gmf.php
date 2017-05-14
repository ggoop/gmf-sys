<?php

return [
	'publishes' => env('GMF_PUBLISHES', 'gmf'),
	'auth_redirect' => env('GMF_AUTH_REDIRECT', '/'),

	'oauth_client_id' => env('GMF_OAUTH_CLIENT_ID', ''),
	'oauth_client_name' => env('APP_TITLE', ''),
	'oauth_client_secret' => env('GMF_OAUTH_CLIENT_SECRET', ''),
	'oauth_client_user' => env('GMF_OAUTH_CLIENT_USER', ''),

	'admin_user_id' => env('GMF_ADMIN_USER_ID', ''),
	'admin_user_email' => env('GMF_ADMIN_USER_EMAIL', ''),
	'admin_user_pwd' => env('GMF_ADMIN_USER_PWD', ''),

	'ent_session_name' => env('GMF_ENT_SESSION_NAME', 'GMFENTSESSIONNAME'),
];
