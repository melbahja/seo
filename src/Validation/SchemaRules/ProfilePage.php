<?php

return [
	'mainEntity' => [
		'type' => '@Person|@Organization',
		'required' => true,
	],
	'dateCreated' => 'iso_date',
	'dateModified' => 'iso_date',
];
