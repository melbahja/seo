<?php

// TODO: add Country
return [
	'title' => [
		'type' => 'string',
		'required' => true,
	],
	'description' => [
		'type' => 'string',
		'required' => true,
	],
	'datePosted' => [
		'type' => 'iso_date',
		'required' => true,
	],
	'hiringOrganization' => [
		'type' => '\Melbahja\Seo\Schema\Organization',
		'required' => true,
	],
	'jobLocation' => [
		'type' => '\Melbahja\Seo\Schema\Place|array',
		'item_type' => '\Melbahja\Seo\Schema\Place',
		'required' => true,
	],
	'employmentType' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'validThrough' => 'iso_date',
	'baseSalary' => '\Melbahja\Seo\Schema\Intangible\MonetaryAmount',
	'applicantLocationRequirements' => [
		'type' => '\Melbahja\Seo\Schema\Place\Country|array',
		'item_type' => '\Melbahja\Seo\Schema\Place\Country',
	],
	'jobLocationType' => 'string',
	'directApply' => 'bool',
];
