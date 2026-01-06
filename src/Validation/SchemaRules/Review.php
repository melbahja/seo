<?php

// TODO: required_if

return [
	'author' => [
		'type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
		'required' => true,
	],
	'reviewRating' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\Rating',
		'required' => true,
	],
	'itemReviewed' => '\Melbahja\Seo\Schema\Thing',
	'datePublished' => 'iso_date',
	'reviewBody' => 'string',
];
