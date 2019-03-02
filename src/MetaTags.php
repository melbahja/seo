<?php
namespace Melbahja\Seo;

use Melbahja\Seo\Interfaces\MetaTagsInterface;

/**
 * @package Melbahja\Seo
 * @since v1.0
 * @see https://git.io/phpseo 
 * @license MIT
 * @copyright 2019 Mohamed Elabhja 
 */
class MetaTags implements MetaTagsInterface
{

	/**
	 * Generated tags
	 * @var array
	 */
	protected $tags = [], $og = [], $tw = []

	/**
	 * Is meta available in fakebook and twitter
	 * @var array
	 */
	, $supported =  ['title', 'description']

	/**
	 * Is canonical set
	 * @var bool
	 */
	, $canonical = false;

	/**
	 * Initiablize new meta tags builder
	 *
	 * @param array $tags
	 */
	public function __construct(array $tags = [])
	{
		foreach ($tags as $n => $v)
		{
			$this->meta($n, $v);
		}
	}

	/**
	 * Set a mobile link (Http header "Vary: User-Agent" is required)
	 *
	 * @param  string $url
	 * @return MetaTagsInterface
	 */
	public function mobile(string $url): MetaTagsInterface
	{
		return $this->push(
			'link', ['rel' => 'alternate', 'media' => 'only screen and (max-width: 640px)', 'href' => $url]
		);
	}

	/**
	 * Set AMP link
	 *
	 * @param  string $url
	 * @return MetaTagsInterface
	 */
	public function amp(string $url): MetaTagsInterface
	{
		return $this->push('link', ['rel' => 'amphtml', 'href' => $url]);
	}

	/**
	 * Set canonical url
	 *
	 * @param  string $url
	 * @return MetaTagsInterface
	 */
	public function canonical(string $url): MetaTagsInterface
	{
		$this->canonical = true;
		return $this->push('link', ['rel' => 'canonical', 'href' => $url]);
	}

	/**
	 * Set a url
	 *
	 * @param  string $url
	 * @return MetaTagsInterface
	 */
	public function url(string $url): MetaTagsInterface
	{
		if ($this->canonical === false) {

			$this->push('link', ['rel' => 'canonical', 'href' => $url]);
		}

		return $this->facebook('url', $url)->twitter('url', $url);
	}

	/**
	 * Set a meta tag
	 *
	 * @param string $name
	 * @param string $value
	 * @return MetaTagsInterface
	 */
	public function meta(string $name, string $value): MetaTagsInterface
	{
		if (in_array($name, $this->supported)) {

			$this->facebook($name, $value)->twitter($name, $value);
		}

		return $this->push('meta', ['name' => $name, 'content' => $value]);
	}

	/**
	 * Append new tag
	 *
	 * @param string $name
	 * @param array  $attrs
	 * @return MetaTagsInterface
	 */
	public function push(string $name, array $attrs): MetaTagsInterface
	{
		$this->tags[] = [$name, $attrs];

		return $this;
	}

	/**
	 * Set a open graph tag
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return MetaTagsInterface
	 */
	public function facebook(string $name, string $value): MetaTagsInterface
	{
		$this->og[] = ['meta', ['property' => "og:{$name}", 'content' => $value]];
		return $this;
	}

	/**
	 * Set a twitter tag
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return MetaTagsInterface
	 */
	public function twitter(string $name, string $value): MetaTagsInterface
	{
		$this->tw[] = ['meta', ['property' => "twitter:{$name}", 'content' => $value]];
		return $this;
	}

	/**
	 * Set short link tag
	 * 
	 * @param  string $url
	 * @return MetaTagsInterface
	 */
	public function shortlink(string $url): MetaTagsInterface
	{
		return $this->push('link', ['rel' => 'shortlink', 'href' => $url]);
	}

	/**
	 * Set image meta
	 *
	 * @param  string $url
	 * @param  string $card Twitter card
	 * @return MetaTagsInterface
	 */
	public function image(string $url, string $card = 'summary_large_image'): MetaTagsInterface
	{
		return $this->facebook('image', $url)->twitter('card', $card)->twitter('image', $url);
	}

	/**
	 * Build meta tags
	 *
	 * @param  array  $tags
	 * @return string
	 */
	public function build(array $tags): string
	{
		$out = '';

		foreach ($tags as $tag)
		{
			$out .= "\n<{$tag[0]} ";

			foreach ($tag[1] as $a => $v)
			{
				$out .= $a .'="'. $v .'" ';
			}

			$out .= "/>";
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
		return $this->build($this->tags) . $this->build($this->tw) . $this->build($this->og);
	}
}
