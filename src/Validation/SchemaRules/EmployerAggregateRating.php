<?php

return [
	'itemReviewed' => [
		'type' => '@Organization',
		'required' => true,
	],
	'ratingValue' => [
		'type' => 'string|int|float',
		'required' => true,
	],
	'ratingCount' => [
		'type' => 'int',
		'required' => true,
	],
	'reviewCount' => 'int',
	'bestRating' => 'int',
	'worstRating' => 'int',
];
