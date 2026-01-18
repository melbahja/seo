<?php

return [
	'hasPart' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
		'required' => true,
	],
	'about' => '@Thing',
	'educationalAlignment' => [
		'type' => 'array|@Thing',
		'item_type' => '@Thing',
	],
];
