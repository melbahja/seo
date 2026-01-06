<?php

return [
	'itemReviewed' => [
		'type' => '\Melbahja\Seo\Schema\Organization',
		'required' => true,
	],
	'ratingValue' => [
		'type' => 'float',
		'required' => true,
	],
	'ratingCount' => [
		'type' => 'int',
		'required' => true,
	],
	'reviewCount' => [
		'type' => 'int',
		'required' => true,
	],
	'bestRating' => 'float',
	'worstRating' => 'float',
];
