<?php

// one of: creator, creditText, copyrightNotice, or license
// TODO: here tooo we need required_if
return [
	'contentUrl' => [
		'type' => 'string',
		'required' => true,
	],
	'license' => 'url',
	'acquireLicensePage' => 'url',
	'creator' => '\Melbahja\Seo\Schema\Person|\Melbahja\Seo\Schema\Organization',
	'creditText' => 'string',
	'copyrightNotice' => 'string',
	'name' => 'string',
];
