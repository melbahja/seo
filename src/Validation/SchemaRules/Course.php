<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'url' => [
		'type' => 'url',
		'required' => true,
	],
	'description' => 'string',
	'provider' => [
		'type' => 'array|@Organization|@Person',
		'item_type' => '@Organization|@Person',
	],
	'courseMode' => [
		'type' => 'array|string',
		'item_type' => 'string',
	],
	'coursePrerequisites' => 'string',
	'educationalLevel' => 'string',
	'teaches' => [
		'type' => 'array|string',
		'item_type' => 'string',
	],
	'timeRequired' => 'string',
];
