<?php

return [
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
	'itemReviewed' => '\Melbahja\Seo\Schema\Thing',
	'bestRating' => 'float',
	'worstRating' => 'float',
];
