<?php

return [
	'text' => [
		'type' => 'string',
		'required' => true,
	],
	'datePublished' => 'iso_date',
	'author' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	'upvoteCount' => 'int',
];
