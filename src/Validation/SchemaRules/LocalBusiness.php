<?php

//
// name and address are required
//

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
		'required' => true,
	],
	'url' => 'url',
	'telephone' => 'string',
	'priceRange' => 'string',
	'openingHoursSpecification' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\Intangible\OpeningHoursSpecification',
	],
	'aggregateRating' => '\Melbahja\Seo\Schema\Intangible\AggregateRating',
	'review' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Review',
	],
	'geo' => '\Melbahja\Seo\Schema\Intangible\GeoCoordinates',
	'servesCuisine' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
];
