<?php

return [
	'containsPlace' => [
		'type' => '@Thing',
		'required' => true,
	],
	'identifier' => [
		'type' => 'string',
		'required' => true,
	],
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
		'required' => true,
	],
	'latitude' => [
		'type' => 'float',
		'required' => true,
	],
	'longitude' => [
		'type' => 'float',
		'required' => true,
	],
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => '@PostalAddress',
	'aggregateRating' => '@AggregateRating',
	'brand' => '@Organization',
	'checkinTime' => 'string',
	'checkoutTime' => 'string',
	'description' => 'string',
	'knowsLanguage' => [
		'type' => 'array|string',
		'item_type' => 'string',
	],
	'review' => [
		'type' => 'array|@Review',
		'item_type' => '@Review',
	],
];
