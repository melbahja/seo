<?php
namespace Melbahja\Seo\Sitemap;

/**
 * Sitemap URL item for Traversable generators.
 *
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class SitemapUrl
{
	public function __construct(
		public readonly string $url,
		public string|int|null $lastmod = null,
		public ?string $changefreq = null,
		public float|int|null $priority = null,
		public array $images = [],
		public array $videos = [],

		// attrs for news
		public ?array $news = null,

		// hreflangs [ [href, lang] ]
		public array $alternates = []
	) {}

	/**
	 * Add an image to this URL
	 *
	 * @param string $imageUrl Image location
	 * @param array $options Optional: caption, geo_location, title, license
	 * @return self
	 */
	public function image(string $imageUrl, array $options = []): self
	{
		$options['loc'] = $imageUrl;
		$this->images[] = $options;
		return $this;
	}

	/**
	 * Add a video to this URL
	 *
	 * @param string $title Video title
	 * @param array $options Required: thumbnail_loc, description. Optional: content_loc, player_loc, etc.
	 * @return self
	 */
	public function video(string $title, array $options): self
	{
		$options['title'] = $title;
		$this->videos[] = $options;
		return $this;
	}

	/**
	 * Set alternative url.
	 *
	 * @param  string $url
	 * @param  string $lang   ISO 639-1 or ISO 3166-1 alpha-2
	 */
	public function alternate(string $url, string $lang): self
	{
		$this->alternates[] = ['href' => $url, 'lang' => $lang];
		return $this;
	}
}
