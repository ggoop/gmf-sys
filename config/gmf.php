<?php

return [
	'publishes' => env('GMF_PUBLISHES', 'gmf'),
	'auth_redirect' => env('GMF_AUTH_REDIRECT', '/'),

	'oauth_client_id' => env('GMF_OAUTH_CLIENT_ID', ''),
	'oauth_client_name' => env('APP_TITLE', ''),
	'oauth_client_secret' => env('GMF_OAUTH_CLIENT_SECRET', ''),
];
