<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
		'required' => true,
	],
	'recipeIngredient' => [
		'type' => 'array|string',
		'item_type' => 'string',
		'required' => true,
	],
	'recipeInstructions' => [
		'type' => 'array|string|@ItemList',
		'item_type' => 'string|@ItemList',
		'required' => true,
	],
	'author' => '@Person|@Organization',
	'datePublished' => 'iso_date',
	'description' => 'string',
	'recipeYield' => 'string|int',
	'aggregateRating' => '@AggregateRating',
	'video'         => '@VideoObject',
];
