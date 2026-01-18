<?php
namespace Melbahja\Seo\Indexing;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
enum URLIndexingType: string
{
	case UPDATE = 'update';
	case DELETE = 'delete';
}
