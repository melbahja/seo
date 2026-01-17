<?php
namespace Melbahja\Seo\Utils;

use Traversable,
	IteratorAggregate;


/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class Utils
{

	public static $encoding = 'UTF-8';

	/**
	 * Escape attr chars.
	 *
	 * @param  string $text
	 * @return string
	 */
	public static function escape(string $text): string
	{
		return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, static::$encoding);
	}

	/**
	 * Escape url for sitemaps.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function escapeUrl(string $url): string
	{
		$url = parse_url($url);
		$url['path'] = $url['path'] ?? '';
		$url['query'] = $url['query'] ?? '';

		if ($url['path'] !== '') {
			$url['path'] = implode('/', array_map('rawurlencode', explode('/', $url['path'])));
		}

		if ($url['query'] !== '') {
			$url['query'] = "?{$url['query']}";
		}

		return str_replace(
			['&', "'", '"', '>', '<'],
			['&amp;', '&apos;', '&quot;', '&gt;', '&lt;'],
			$url['scheme'] . "://{$url['host']}{$url['path']}{$url['query']}"
		);
	}

	/**
	 * Encode url for sitemaps, other qoute/senstive chars are already escaped by xml writer!
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function encodeSitemapUrl(string $url): string
	{
		$url = parse_url($url);
		$url['path']  = $url['path'] ?? '';
		$url['query'] = $url['query'] ?? '';

		if ($url['path'] !== '') {
			$url['path'] = implode('/', array_map('rawurlencode', explode('/', $url['path'])));
		}

		if ($url['query'] !== '') {
			$url['query'] = "?{$url['query']}";
		}

		return "{$url['scheme']}://{$url['host']}{$url['path']}{$url['query']}";
	}

	/**
	 * Wrap callable func into a Traversable generator.
	 *
	 * @param  callable $func  must have yields
	 * @return Traversable
	 */
	static function generator(callable $func): Traversable
	{
		return new class($func) implements IteratorAggregate
		{
			public function __construct(
				private readonly mixed $callable
			){}

			public function getIterator(): Traversable
			{
				return ($this->callable)();
			}
		};
	}
}

