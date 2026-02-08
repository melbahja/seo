<?php
namespace Melbahja\Seo;

use Melbahja\Seo\Utils\Utils;
use Melbahja\Seo\Interfaces\SeoInterface;
use Melbahja\Seo\Interfaces\SchemaInterface;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class MetaTags implements SeoInterface
{
	/**
	 * Page title
	 * @var string
	 */
	public ?string $title = null;

	/**
	 * Meta tags
	 * @var array
	 */
	protected array $tags = [];

	/**
	 * Schema object to be included
	 * @var SchemaInterface
	 */
	protected ?SchemaInterface $schema = null;

	/**
	 * Initialize new meta tags builder
	 *
	 * @param array $meta Meta tags array. Supports:
	 *                    - Simple: ['description' => '...', 'keywords' => '...']
	 *                    - Nested arrays for multi-arg methods: ['verification' => ['google' => 'abc123']]
	 *                    - Link tags: ['link' => [['rel' => '...', 'href' => '...']]]
	 * @param array $og      Open Graph tags: ['type' => 'article', 'locale' => 'en_US']
	 * @param array $twitter X Card tags: ['card' => 'summary', 'creator' => '@user']
	 * @param SchemaInterface|null $schema Schema object to include as JSON-LD
	 */
	public function __construct(array $meta = [], array $og = [], array $twitter = [], ?SchemaInterface $schema = null)
	{
		foreach ($meta as $k => $v)
		{
			if (is_array($v)) {

				foreach ($v as $kk => $vv)
				{
					if (method_exists($this, $k)) {
						$this->$k($kk, $vv);
						continue;
					}

					$this->push($k, $vv);
				}

				continue;
			}

			if (method_exists($this, $k)) {
				$this->$k($v);
				continue;
			}

			$this->push('meta', ['name' => $k, 'content' => $v]);
		}

		foreach ($og as $k => $v)
		{
			$this->og($k, $v);
		}

		foreach ($twitter as $k => $v)
		{
			$this->twitter($k, $v);
		}

		if ($schema !== null) {
			$this->schema = $schema;
		}
	}


	/**
	 * Set page and meta title
	 *
	 * @param  string $title
	 * @return self
	 */
	public function title(string $title): self
	{
		$this->title = $title;
		return $this->meta('title', $title)->og('title', $title)->twitter('title', $title);
	}

	/**
	 * Set page description.
	 * @param  string $desc
	 * @return self
	 */
	public function description(string $desc): self
	{
		return $this->meta('description', $desc)->og('description', $desc)->twitter('description', $desc);
	}

	/**
	 * Set a mobile link (Http header "Vary: User-Agent" is required)
	 *
	 * @param  string $url
	 * @return self
	 */
	public function mobile(string $url): self
	{
		return $this->push('link', [
			'href' => $url,
			'rel'   => 'alternate',
			'media' => 'only screen and (max-width: 640px)',
		]);
	}

	/**
	 * Set robots meta tags
	 *
	 * @param string|array $options index,follow OR ['index', 'follow', 'max-snippet' => -1]
	 * @param string $botName robots|googlebot|bingbot|etc
	 * @return self
	 */
	public function robots(string|array $options, string $botName = 'robots'): self
	{
		if (is_array($options)) {

			$parts = [];
			foreach ($options as $k => $v)
			{
				$parts[] = is_int($k) ? $v : "{$k}:{$v}";
			}
			$options = implode(', ', $parts);
		}

		return $this->meta($botName, $options);
	}

	/**
	 * Set RSS or Atom feed link
	 *
	 * @param string $url feed URL
	 * @param string $type application/rss+xml|application/atom+xml
	 * @param string|null $title feed title
	 * @return self
	 */
	public function feed(string $url, string $type = 'application/rss+xml', ?string $title = null): self
	{
		return $this->push('link', [
			'rel' => 'alternate',
			'title' => $title,
			'type' => $type,
			'href' => $url,
		]);
	}

	/**
	* Set search engine verification meta tag
	*
	* @param string $engine google|bing|yandex|pinterest|etc
	* @param string $code verification code
	* @return self
	*/
	public function verification(string $engine, string $code): self
	{
		return $this->meta("{$engine}-site-verification", $code);
	}

	/**
	 * Set AMP link
	 *
	 * @param  string $url
	 * @return self
	 */
	public function amp(string $url): self
	{
		return $this->push('link', ['rel' => 'amphtml', 'href' => $url]);
	}

	/**
	 * Set canonical url
	 *
	 * @param  string $url
	 * @return self
	 */
	public function canonical(string $url): self
	{
		return $this->push('link', ['rel' => 'canonical', 'href' => $url]);
	}


	/**
	 * Set social media url.
	 *
	 * @param  string $url
	 * @return self
	 */
	public function url(string $url): self
	{
		return $this->og('url', $url)->twitter('url', $url);
	}

	/**
	 * Set alternate language url.
	 *
	 * @param  string $lang for eg: en
	 * @param  string $url  alternate language page url.
	 * @return self
	 */
	public function hreflang(string $lang, string $url): self
	{
		return $this->push('link', ['rel' => 'alternate', 'href' => $url, 'hreflang' => $lang]);
	}

	/**
	* Set multiple alternate language URLs at once
	*
	* @param array $langUrls Associative array of lang => url pairs (e.g., ['en' => 'url', 'fr' => 'url'])
	* @param string|null $defaultUrl Optional x-default URL for language fallback
	* @return self
	*/
	public function hreflangs(array $langUrls, ?string $defaultUrl = null): self
	{
		if ($defaultUrl !== null) {
			$langUrls['x-default'] = $defaultUrl;
		}

		foreach ($langUrls as $lang => $url)
		{
			$this->hreflang($lang, $url);
		}

		return $this;
	}

	/**
	 * Set a meta tag
	 *
	 * @param string $name
	 * @param string $value
	 * @return self
	 */
	public function meta(string $name, string $value): self
	{
		return $this->push('meta', ['name' => $name,'content' => $value]);
	}

	/**
	 * Append new tag
	 *
	 * @param string $name
	 * @param array  $attrs
	 * @return self
	 */
	public function push(string $name, array $attrs): self
	{
		foreach ($attrs as $k => $v)
		{
			$attrs[$k] = $v;
		}

		$this->tags[] = [$name, $attrs];
		return $this;
	}

	/**
	 * Set a open graph tag
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return self
	 */
	public function og(string $name, string $value): self
	{
		$this->tags[] = ['meta', ['property' => "og:{$name}", 'content' => $value]];
		return $this;
	}


	/**
	 * Set a twitter tag
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return self
	 */
	public function twitter(string $name, string $value): self
	{
		$this->tags[] = ['meta', ['property' => "twitter:{$name}", 'content' => $value]];
		return $this;
	}

	/**
	 * Set short link tag
	 *
	 * @param  string $url
	 * @return self
	 */
	public function shortlink(string $url): self
	{
		return $this->push('link', ['rel' => 'shortlink', 'href' => $url]);
	}

	/**
	 * Set image meta
	 *
	 * @param  string $url
	 * @param  string $card Twitter card
	 * @return self
	 */
	public function image(string $url, string $card = 'summary_large_image'): self
	{
		return $this->og('image', $url)->twitter('card', $card)->twitter('image', $url);
	}

	/**
	* Set article metadata
	*
	* @param string $published Article published time
	* @param string|null $modified Article modified time
	* @param string|null $author Article author
	* @return self
	*/
	public function articleMeta(string $published, ?string $modified = null, ?string $author = null): self
	{
		$this->tags[] = ['meta', ['property' => 'article:published_time', 'content' => $published]];

		if ($modified) {
			$this->tags[] = ['meta', ['property' => 'article:modified_time', 'content' => $modified]];
		}

		if ($author) {
			$this->tags[] = ['meta', ['property' => 'article:author', 'content' => $author]];
		}

		return $this;
	}

	/**
	 * Set pagination links
	 *
	 * @param string|null $prev previous page URL
	 * @param string|null $next next page URL
	 * @param string|null $first first page URL (optional)
	 * @param string|null $last last page URL (optional)
	 * @return self
	 */
	public function pagination(?string $prev = null, ?string $next = null, ?string $first = null, ?string $last = null): self
	{
		if ($prev) {
			$this->push('link', ['rel' => 'prev', 'href' => $prev]);
		}

		if ($next) {
			$this->push('link', ['rel' => 'next', 'href' => $next]);
		}

		if ($first) {
			$this->push('link', ['rel' => 'first', 'href' => $first]);
		}

		if ($last) {
			$this->push('link', ['rel' => 'last', 'href' => $last]);
		}

		return $this;
	}

	/**
	 * Add Schema objects to be rendered with metatags
	 *
	 * @param  SchemaInterface $schema Any Schema object
	 * @return self
	 */
	public function schema(SchemaInterface $schema): self
	{
		$this->schema = $schema;
		return $this;
	}

	/**
	 * Build meta tags
	 *
	 * @param  array  $tags
	 * @return string
	 */
	public function build(array $tags): string
	{
		// Sort tags for nice readability
		usort($tags, function($a, $b)
		{
			$getType = function($tag)
			{
				if (isset($tag[1]['property'])) {

					if (str_starts_with($tag[1]['property'], 'og:')) {
						return 3;
					}

					if (str_starts_with($tag[1]['property'], 'twitter:')) {
						return 4;
					}
				}

				if ($tag[0] === 'meta') {
					return 1;
				}

				if ($tag[0] === 'link') {
					return 2;
				}

				return 5;
			};

			return $getType($a) <=> $getType($b);
		});

		$out = '';
		foreach ($tags as $tag)
		{
			$out .= PHP_EOL . "<{$tag[0]} ";

			foreach ($tag[1] as $a => $v)
			{
				// empty values will be skipped.
				if (!$v) {
					continue;
				}

				// attrs values are escaped to avoid XSS attacks, but attrs names MUST be trusted!
				// if you trust your users to set arbitary meta attr names that a STUPID idea, but
				// anyway I did a small replace to avid common XSS chars that may hack you!
				$a = str_replace(['"', "'", '<', '>', ' ', "\t", "\n", "\r"], '', $a);

				// Set attr=value
				$out .= $a .'="'. Utils::escape($v) .'" ';
			}

			$out .= "/>";
		}

		if ($this->schema !== null) {
			$out .= (string) $this->schema;
		}

		return $out;
	}


	/**
	 * Object to string
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		$title = '';
		if ($this->title !== null) {
			$title = Utils::escape($this->title);
			$title = "<title>{$title}</title>";
		}

		return $title . $this->build($this->tags);
	}
}
