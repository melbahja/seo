<?php

return [
	'dataFeedElement' => [
		'type' => 'array|@DataFeedItem',
		'item_type' => '@DataFeedItem',
		'rules'     => [
			'dateModified' => 'iso_date',
			'item' => '@Thing',
		],
	],
	'dateModified' => 'iso_date',
	'name' => 'string',
];
