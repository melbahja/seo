<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
		'required' => true,
	],
	'servesCuisine' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'menu' => 'string',
	'acceptsReservations' => 'bool',
];
