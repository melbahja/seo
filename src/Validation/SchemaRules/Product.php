<?php

// name, image, offers are required for merchant listings
// For product snippets: review or aggregateRating or offers is required
// TODO: add required_if to handle this case.
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
	'description' => 'string',
	'brand' => '\Melbahja\Seo\Schema\Intangible\Brand|\Melbahja\Seo\Schema\Organization',
	'offers' => '\Melbahja\Seo\Schema\Intangible\Offer|\Melbahja\Seo\Schema\Intangible\AggregateOffer',
	'aggregateRating' => '\Melbahja\Seo\Schema\Intangible\AggregateRating',
	'review' => [
		'type' => '\Melbahja\Seo\Schema\CreativeWork\Review|array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Review',
	],
	'sku' => 'string',
	'mpn' => 'string',
	'gtin' => 'string',
];
