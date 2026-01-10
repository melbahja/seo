<?php

// TODO: required_if
return [
	'author' => [
		'type' => '@Person|@Organization',
		'required' => true,
	],
	'reviewRating' => [
		'type' => '\Melbahja\Seo\Schema\Thing',
		'required' => true,
	],
	'itemReviewed' => '\Melbahja\Seo\Schema\Thing',
	'datePublished' => 'iso_date',
	'reviewBody' => 'string',
];
