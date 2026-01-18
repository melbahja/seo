<?php

return [
	'url' => 'url',
	'name' => [
		'type' => 'string',
		'required' => true,
	],
	'aggregateRating' => '@AggregateRating',
	'offers' => '@Thing',
	'review' => '@Review',
	'image' => [
		'type' => 'array|url|@ImageObject',
		'item_type' => 'url|@ImageObject',
	],
	'description' => 'string',
	'sku' => 'string',
	'mpn' => 'string',
	'brand' => '@Organization',
	'color' => 'string',
	'size' => 'string',
	'material' => 'string',
	'pattern' => 'string',
	'gtin14' => 'string',
	'itemCondition' => 'string',
	'availability' => 'string',
	'price' => 'float|int',
	'priceCurrency' => 'string',
	'priceValidUntil' => 'iso_date',
	'isVariantOf' => '@ProductGroup',
	'inProductGroupWithID' => 'string',
];
