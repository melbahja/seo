<?php

return [
	'claimReviewed' => [
		'type' => 'string',
		'required' => true,
	],
	'reviewRating' => [
		'type' => '@Rating',
		'required' => true,
	],
	'url' => [
		'type' => 'url',
		'required' => true,
	],
	'author' => '@Organization|@Person',
	'itemReviewed' => '@Thing',
];
