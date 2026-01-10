<?php

return [
	'location' => [
		'type' => '@Place|@Thing',
		'required' => true,
	],
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'startDate' => [
		'type' => 'string',
		'required' => true,
	],
	'description' => 'string',
	'endDate' => 'string',
	'eventStatus' => 'string',
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
	],
	'offers' => '@Thing',
	'organizer' => '@Organization|@Person',
	'performer' => '@Person|@Organization',
	'previousStartDate' => 'string',
];