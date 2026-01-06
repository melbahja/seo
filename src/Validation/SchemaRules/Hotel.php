<?php

//TODO: add LocationFeatureSpecification
return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
		'required' => true,
	],
	'amenityFeature' => [
		'type' => 'array',
		'item_type' => '\Melbahja\Seo\Schema\Intangible\LocationFeatureSpecification',
	],
	'checkinTime' => 'string',
	'checkoutTime' => 'string',
];