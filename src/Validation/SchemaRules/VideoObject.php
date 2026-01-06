<?php

// TODO: add BroadcastEvent, Clip
return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'thumbnailUrl' => [
		'type' => 'string|array',
		'item_type' => 'string',
		'required' => true,
	],
	'uploadDate' => [
		'type' => 'iso_date',
		'required' => true,
	],
	'contentUrl' => 'url',
	'description' => 'string',
	'duration' => 'string',
	'embedUrl' => 'url',
	'expires' => 'iso_date',
	'hasPart' => [
		'type' => '\Melbahja\Seo\Schema\CreativeWork\Clip|array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Clip',
	],
	'publication' => '\Melbahja\Seo\Schema\CreativeWork\BroadcastEvent',
];
