<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'answerCount' => [
		'type' => 'int',
		'required' => true,
	],
	'acceptedAnswer' => [
		'type' => '\Melbahja\Seo\Schema\CreativeWork\Answer|array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Answer',
	],
	'suggestedAnswer' => [
		'type' => '\Melbahja\Seo\Schema\CreativeWork\Answer|array',
		'item_type' => '\Melbahja\Seo\Schema\CreativeWork\Answer',
	],
	'text' => 'string',
	'datePublished' => 'iso_date',
	'author' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
];
