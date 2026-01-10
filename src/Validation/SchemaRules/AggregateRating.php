<?php

return [
	'itemReviewed' => [
		'type' => '@Thing',
		'required' => true,
	],
	'ratingValue' => [
		'type' => 'string|int|float',
		'required' => true,
	],
	'ratingCount' => 'int',
	'reviewCount' => 'int',
	'bestRating' => 'string|int|float',
	'worstRating' => 'string|int|float',
];
