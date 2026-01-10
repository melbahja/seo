<?php

return [
	'title' => 'string',
	'datePosted' => 'iso_date',
	'description' => 'string',
	'hiringOrganization' => '@Organization',
	'jobLocation' => '@Place',
	'baseSalary' => '@Thing',
	'directApply' => 'bool',
	'identifier' => '@Thing',
	'jobLocationType' => 'string',
	'validThrough' => 'iso_date',
];
