<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'productGroupID' => 'string',
	'variesBy' => [
		'type' => 'array|string',
		'item_type' => 'string',
	],
	'hasVariant' => [
		'type' => 'array|@Product',
		'item_type' => '@Product',
	],
	'aggregateRating' => '@AggregateRating',
	'brand' => '@Organization',
	'description' => 'string',
	'review' => '@Review',
	'url' => 'url',
];
