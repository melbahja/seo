<?php
namespace Melbahja\Seo\Schema;

use Melbahja\Seo\Schema\Intangible;

/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/MonetaryAmount
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class MonetaryAmount extends Intangible
{
	protected array|string $type = "MonetaryAmount";
}
