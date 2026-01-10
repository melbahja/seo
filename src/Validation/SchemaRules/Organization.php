<?php

return [
	'name' => 'string',
	'url' => 'url',
	'logo' => 'string',
	'address' => '@PostalAddress',
	'contactPoint' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
	],
	'sameAs' => [
		'type' => 'string|array',
		'item_type' => 'string',
	],
	'description' => 'string',
	'email' => 'email',
	'telephone' => 'string',
	'foundingDate' => 'iso_date',
	'numberOfEmployees' => '@Thing',
];
