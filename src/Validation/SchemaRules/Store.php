<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'address' => [
		'type' => '\Melbahja\Seo\Schema\Intangible\PostalAddress',
		'required' => true,
	],
];
