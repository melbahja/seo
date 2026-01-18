<?php

return [
	'description' => [
		'type' => 'string',
		'required' => true,
	],
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'alternateName' => 'string',
	'creator' => [
		'type' => '@Person|@Organization|array',
		'item_type' => '@Person|@Organization',
	],
	'citation' => 'string|@CreativeWork',
	'funder' => [
		'type' => '@Person|@Organization|array',
		'item_type' => '@Person|@Organization',
	],
	'hasPart' => [
		'type' => 'array|@Dataset',
		'item_type' => '@Dataset',
	],
	'isPartOf' => 'url|@Dataset',
	'identifier' => 'url|string|@Thing',
	'isAccessibleForFree' => 'bool',
	'keywords' => 'string',
	'license' => 'url|@CreativeWork',
	'measurementTechnique' => 'string|url',
	'sameAs' => 'url',
	'spatialCoverage' => 'string|@Place',
	'temporalCoverage' => 'string',
	'variableMeasured' => 'string|@Thing',
	'version' => 'string|int|float',
	'url' => 'url',
	'distribution' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
	],
];
