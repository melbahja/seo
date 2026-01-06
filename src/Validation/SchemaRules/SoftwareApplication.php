<?php

//
// TODO: required_if
//
return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'offers' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\Offer|\Melbahja\Seo\Schema\Intangible\AggregateOffer',
		'required' => true,
	],
	'aggregateRating' => '\Melbahja\Seo\Schema\Intangible\AggregateRating',
	'review' => [
		'type' => '\Melbahja\Seo\Schema\CreativeWork\Review|array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Review',
	],
	'applicationCategory' => 'string',
	'operatingSystem' => 'string',
];
