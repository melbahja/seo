<?php

// TODO: add QuantitativeValue
return [
	'name' => 'string',
	'url' => 'url',
	'logo' => 'string',
	'address' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
	'contactPoint' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\ContactPoint|array',
		'item_type' => '\Melbahja\Seo\Schema\Intangible\ContactPoint',
	],
	'sameAs' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'description' => 'string',
	'email' => 'email',
	'telephone' => 'string',
	'foundingDate' => 'iso_date',
	'numberOfEmployees' => '\Melbahja\Seo\Schema\Intangible\QuantitativeValue',
];
