<?php
namespace Melbahja\Seo\Sitemap;

use Melbahja\Seo\{
	Exceptions\SitemapException,
	Interfaces\SitemapSetupableInterface
};

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class NewsBuilder extends LinksBuilder implements SitemapSetupableInterface
{

	/**
	 * Default publication
	 * @var array
	 */
	private $publication = ['name' => null, 'lang' => null];

	/**
	 * Initialize NewsBuilder
	 *
	 * @param string $options
	 * @return array
	 */
	public function preSetup(array $options): array
	{
		$options['news'] = true;
		return $options;
	}

	/**
	 * Set dafault publication
	 *
	 * @param string $name
	 * @param string $lang
	 * @return SitemapBuilderInterface
	 */
	public function setPublication(string $name, string $lang): SitemapBuilderInterface
	{
		$this->publication = ['name' => $name, 'lang' => $lang];
		return $this;
	}

	/**
	 * Get publication
	 *
	 * @return array
	 */
	public function getPublication(): array
	{
		return $this->publication;
	}


	/**
	 * Add news elem to the current url.
	 *
	 * @param  array  $options
	 * @return self
	 */
	public function news(array $options): self
	{
		$options['name']     = $options['name'] ?? $this->publication['name'];
		$options['language'] = $options['language'] ?? $this->publication['lang'];

		if (isset($options['name'], $options['language'], $options['publication_date'], $options['title']) === false) {
			throw new SitemapException("News map require: name, language, publication_date and title");
		}

		$this->url['news'] = $options;

		return $this;
	}

}
