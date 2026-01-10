<?php

return [
	'headline' => 'string',
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
	],
	'datePublished' => 'iso_date',
	'dateModified' => 'iso_date',
	'author' => [
		'type' => 'array|@Person|@Organization',
		'item_type' => '@Person|@Organization',
	],
];
