<?php

return [
	'name' => 'string',
	'address' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
	'geo' => '\Melbahja\Seo\Schema\Intangible\GeoCoordinates',
	'telephone' => 'string',
	'image' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
];
