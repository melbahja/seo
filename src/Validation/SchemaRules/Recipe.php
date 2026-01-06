<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'image' => [
		'type' => 'string|array',
		'item_type' => 'string',
		'required' => true,
	],
	'author' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	'datePublished' => 'iso_date',
	'description' => 'string',
	'prepTime' => 'string',
	'cookTime' => 'string',
	'totalTime' => 'string',
	'recipeYield' => 'string',
	'recipeCategory' => 'string',
	'recipeCuisine' => 'string',
	'recipeIngredient' => [
		'type' => 'array',
		'item_type' => 'string',
	],
	'recipeInstructions' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\HowToStep|\Melbahja\Seo\Schema\CreativeWork\HowToSection',
	],
	'aggregateRating' => '\Melbahja\Seo\Schema\Intangible\AggregateRating',
	'video' => '\Melbahja\Seo\Schema\CreativeWork\VideoObject',
];
