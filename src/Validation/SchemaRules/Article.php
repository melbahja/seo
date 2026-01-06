<?php

return [
	'headline' => 'string',
	'image' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'datePublished' => 'iso_date',
	'dateModified' => 'iso_date',
	'author' => [
		'type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization|array',
		'item_type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	],
	'publisher' => '\Melbahja\Seo\Schema\Organization',
];
