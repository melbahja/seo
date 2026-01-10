<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'url' => 'url',
	'image' => 'string|@ImageObject',
	'sameAs' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'description' => 'string',
	'jobTitle' => 'string',
];

