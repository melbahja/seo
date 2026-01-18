<?php
namespace Melbahja\Seo\Interfaces;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
interface SitemapBuilderInterface extends SitemapInterface, \Stringable
{

	/**
	 * News namespace
	 * @var string
	 */
	public const NEWS_NS = 'https://www.google.com/schemas/sitemap-news/0.9';

	/**
	 * Images namespace
	 */
	public const IMAGE_NS = 'http://www.google.com/schemas/sitemap-image/1.1';

	/**
	 * Videos namespace
	 */
	public const VIDEO_NS = 'http://www.google.com/schemas/sitemap-video/1.1';


	/**
	 * XHTML links namespace for href
	 */
	public const XHTML_NS = 'http://www.w3.org/1999/xhtml';


	/**
	 * Render the sitemap into uriPath
	 *
	 * @param  string|null $uriPath URI path to render the sitemap into, or null will return the xml
	 * @return bool|string boolean when uri oath passed or a generated xml as string
	 */
	public function render(?string $uriPath = null): bool|string;
}
