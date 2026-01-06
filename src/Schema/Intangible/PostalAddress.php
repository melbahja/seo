<?php
namespace Melbahja\Seo\Schema\Intangible;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/PostalAddress
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class PostalAddress extends ContactPoint
{
	protected array|string $type = "PostalAddress";
}
