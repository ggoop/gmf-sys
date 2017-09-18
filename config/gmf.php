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

];
