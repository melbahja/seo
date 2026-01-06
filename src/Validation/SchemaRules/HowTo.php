<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'step' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\HowToStep|\Melbahja\Seo\Schema\CreativeWork\HowToSection',
		'required' => true,
	],
	'image' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'totalTime' => 'string',
	'description' => 'string',
];
