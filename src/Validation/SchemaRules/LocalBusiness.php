<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => [
		'type' => '@PostalAddress',
		'required' => true,
	],
	'telephone' => 'string',
	'priceRange' => 'string',
	'openingHoursSpecification' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
		'rules' => [
			'dayOfWeek' => [
				'type'      => 'string|array',
				'item_type' => 'string',
			],
			'opens' => 'string',
			'closes' => 'string',
		],
	],
	'geo' => [
		'type' => '@Thing',
		'rules' => [
			'latitude' => 'float|int',
			'longitude' => 'float|int',
		],
	],
	'url' => 'url',
	'sameAs' => [
		'type'      => 'array|url',
		'item_type' => 'url',
	],
	'image' => 'url|@ImageObject',
];
