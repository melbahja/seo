<?php

return [
	'mainEntity' => [
		'type' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
		'required' => true,
	],
	'dateCreated' => 'iso_date',
	'dateModified' => 'iso_date',
];
