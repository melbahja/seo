<?php

//
// location, startDate, name are required
//
return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'startDate' => [
		'type' => 'iso_date',
		'required' => true,
	],
	'location' => [
		'type' => '\Melbahja\Seo\Schema\Place|\Melbahja\Seo\Schema\Intangible\VirtualLocation',
		'required' => true,
	],
	'endDate' => 'iso_date',
	'eventStatus' => 'string',
	'eventAttendanceMode' => 'string',
	'description' => 'string',
	'offers' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\Offer|array',
		'item_type' => '\Melbahja\Seo\Schema\Intangible\Offer',
	],
	'performer' => [
		'type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization|array',
		'item_type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	],
	'organizer' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	'image' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
];
