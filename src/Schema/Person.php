<?php
namespace Melbahja\Seo\Schema;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Person
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Person extends Thing
{
	public function __construct(array $props = [])
	{
		parent::__construct('Person', $props);
	}
}
