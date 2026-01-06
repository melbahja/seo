<?php
namespace Melbahja\Seo\Schema;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Organization
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Organization extends Thing
{
	public function __construct(array $props = [])
	{
		parent::__construct('Organization', $props);
	}
}
