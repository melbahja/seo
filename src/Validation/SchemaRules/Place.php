<?php

return [
	'name' => 'string',
	'address' => [
		'type' => '@Thing',
		'rules' => [
			'streetAddress' => 'string',
			'addressLocality' => 'string',
			'addressRegion' => 'string',
			'postalCode' => 'string',
			'addressCountry' => 'string',
		],
	],
	'geo' => [
		'type' => '@Thing',
		'rules' => [
			'latitude' => 'float|int',
			'longitude' => 'float|int',
		],
	],
];
