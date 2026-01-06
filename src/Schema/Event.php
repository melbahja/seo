<?php
namespace Melbahja\Seo\Schema;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Event
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Event extends Thing
{
	public function __construct(array $props = [])
	{
		parent::__construct('Event', $props);
	}
}
