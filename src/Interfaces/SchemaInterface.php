<?php
namespace Melbahja\Seo\Interfaces;

/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elabhja
 */
interface SchemaInterface extends SeoInterface, \JsonSerializable
{
	public function __toString(): string;
}
