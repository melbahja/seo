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
	'dateCreated' => 'iso_date',
	'director' => [
		'type' => 'array|@Person',
		'item_type' => '@Person',
	],
	'review' => '@Review',
	'aggregateRating' => '@AggregateRating',
];
