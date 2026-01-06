<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'itemListElement' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\HowToStep|\Melbahja\Seo\Schema\CreativeWork\HowToSection',
		'required' => true,
	],
];
