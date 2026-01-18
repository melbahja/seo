<?php

return [
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'offers' => [
		'type' => '@Thing|array',
		'item_type' => '@Thing',
		'required' => true,
	],
	'aggregateRating' => '@AggregateRating',
	'review' => [
		'type' => '@Review|array',
		'item_type' => '@Review',
	],
	'applicationCategory' => 'string',
	'operatingSystem' => 'string',
];
