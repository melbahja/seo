<?php
namespace Melbahja\Seo;

use Stringable;
use Melbahja\Seo\Interfaces\SeoInterface;

/**
 * @package Melbahja\Seo
 * @since v3.0
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elabhja
 *
 */
class Robots implements SeoInterface, Stringable
{
	/**
	 * All robot.txt rules
	 * @var array
	 */
	protected array $rules = [];

	/**
	 * Add a comment line.
	 *
	 * @param  string $text Comment text
	 * @return Robots
	 */
	public function addComment(string $text): Robots
	{
		$this->rules[] = ['type' => 'comment', 'text' => $text];
		return $this;
	}

	/**
	 * Add a sitemap URL.
	 *
	 * @param  string $url Sitemap URL
	 * @return Robots
	 */
	public function addSitemap(string $url): Robots
	{
		$this->rules[] = ['type' => 'sitemap', 'url' => $url];
		return $this;
	}

	/**
	 * Add rules for a bot by user agent name.
	 *
	 * @param  string $userAgent Bot user agent name
	 * @param  array  $disallow Array of paths to disallow
	 * @param  array  $allow Array of paths to allow
	 * @param  int|null $crawlDelay Crawl delay in seconds
	 * @return Robots
	 */
	public function addRule(
		string $userAgent = '*',
		array $disallow = [],
		array $allow = [],
		?int $crawlDelay = null
	): Robots
	{
		$this->rules[] = [
			'type' => 'rule',
			'userAgent' => $userAgent,
			'disallow' => $disallow,
			'allow' => $allow,
			'crawlDelay' => $crawlDelay
		];

		return $this;
	}

	/**
	 * Save robots txt to a file.
	 *
	 * @param  string $path
	 * @return bool
	 */
	public function saveTo(string $path): bool
	{
		return file_put_contents($path, (string) $this) !== false;
	}

	/**
	 * Build robots txt content.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		$out = "";

		foreach ($this->rules as $rule)
		{
			switch ($rule['type'])
			{
				case 'comment':
					$lines = explode("\n", $rule['text']);
					foreach ($lines as $line)
					{
						$out .= "# {$line}\r\n";
					}
					break;

				case 'sitemap':
					$out .= "Sitemap: {$rule['url']}\r\n";
					break;

				case 'rule':

					$out .= "User-agent: {$rule['userAgent']}\r\n";
					foreach ($rule['disallow'] as $path)
					{
						$out .= "Disallow: {$path}\r\n";
					}

					foreach ($rule['allow'] as $path)
					{
						$out .= "Allow: {$path}\r\n";
					}

					if ($rule['crawlDelay'] !== null) {
						$out .= "Crawl-delay: {$rule['crawlDelay']}\r\n";
					}

					$out .= "\r\n";
					break;
			}
		}

		return $out;
	}
}
