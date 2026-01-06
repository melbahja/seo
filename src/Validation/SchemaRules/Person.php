<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'url' => 'url',
	'image' => 'string',
	'sameAs' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'description' => 'string',
	'jobTitle' => 'string',
];
