<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'url' => [
		'type' => 'url',
		'required' => true,
	],
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
	],
	'telephone' => 'string',
	'priceRange' => 'string',
	'address' => '@PostalAddress',
	'aggregateRating' => '@AggregateRating',
];
