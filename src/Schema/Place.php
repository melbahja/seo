<?php
namespace Melbahja\Seo\Schema;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Place
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Place extends Thing
{
	public function __construct(array $props = [])
	{
		parent::__construct('Place', $props);
	}
}
