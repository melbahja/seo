<?php

return [
	'author' => [
		'type' => '@Person|@Organization|array',
		'item_type' => '@Person|@Organization',
		'required' => true,
	],
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'url' => [
		'type' => 'url',
		'required' => true,
	],
	'workExample' => [
		'type' => 'array|@Book',
		'item_type' => '@Book',
	],
	'sameAs' => 'url',
	'bookFormat' => 'string',
	'inLanguage' => 'string',
	'isbn' => 'string',
	'datePublished' => 'iso_date',
	'identifier' => 'string',
];
