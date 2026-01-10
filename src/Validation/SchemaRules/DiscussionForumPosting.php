<?php

return [
	'author' => [
		'type' => '@Person|@Organization',
		'required' => true,
	],
	'datePublished' => [
		'type' => 'string',
		'required' => true,
	],
	'text' => 'string',
	'image' => 'url|@ImageObject',
	'video' => '@VideoObject',
	'headline' => 'string',
	'comment' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
	],
	'interactionStatistic' => '@Thing',
	'url' => 'url',
	'dateModified' => 'string',
	'creativeWorkStatus' => 'string',
	'isPartOf' => 'url|@Thing',
	'sharedContent' => '@Thing',
];
