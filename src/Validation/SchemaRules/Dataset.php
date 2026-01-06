<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'description' => [
		'type' => 'string',
		'required' => true,
	],
	'creator' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	'license' => 'string',
	'url' => 'url',
	'identifier' => 'string',
	'keywords' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'variableMeasured' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'temporalCoverage' => 'string',
	'spatialCoverage' => '\Melbahja\Seo\Schema\Place|string',
	'version' => 'string',
];
