<?php
namespace Melbahja\Seo;

/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright 2019-present Mohamed Elabhja
 */
class Helper
{

	public static $encoding = 'UTF-8';

	public static function escape(string $text): string
	{
		return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, static::$encoding);
	}
}

