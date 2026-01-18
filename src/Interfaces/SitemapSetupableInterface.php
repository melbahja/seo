<?php
namespace Melbahja\Seo\Interfaces;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
interface SitemapSetupableInterface extends SitemapInterface
{

	/**
	 * Pre setup hook for the sitemaps to allow
	 * extended classes to modify options before init.
	 *
	 * @param  array $options
	 * @return array           same or modified options to be used by the Sitemap.
	 */
	public function preSetup(array $options): array;
}
