<?php
namespace Melbahja\Seo\Schema;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Product
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Product extends Thing
{
	public function __construct(array $props = [])
	{
		parent::__construct('Product', $props);
	}
}
