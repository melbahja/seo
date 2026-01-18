<?php
namespace Melbahja\Seo;

use Melbahja\Seo\Interfaces\SeoInterface;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 *
 */
class Robots implements SeoInterface, \Stringable
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
	 * @return self
	 */
	public function addComment(string $text): self
	{
		$this->rules[] = ['type' => 'comment', 'text' => $text];
		return $this;
	}

	/**
	 * Add a sitemap URL.
	 *
	 * @param  string $url Sitemap URL
	 * @return self
	 */
	public function addSitemap(string $url): self
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
	 * @return self
	 */
	public function addRule(string $userAgent = '*', array $disallow = [], array $allow = [], ?int $crawlDelay = null): self
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

					foreach (explode("\n", $rule['text']) as $line)
					{
						$out .= "# {$line}". PHP_EOL;
					}
					break;

				case 'sitemap':

					$out .= "Sitemap: {$rule['url']}". PHP_EOL;
					break;

				case 'rule':

					$out .= "User-agent: {$rule['userAgent']}". PHP_EOL;
					foreach ($rule['disallow'] as $path)
					{
						$out .= "Disallow: {$path}". PHP_EOL;
					}

					foreach ($rule['allow'] as $path)
					{
						$out .= "Allow: {$path}". PHP_EOL;
					}

					if ($rule['crawlDelay'] !== null) {
						$out .= "Crawl-delay: {$rule['crawlDelay']}". PHP_EOL;
					}

					$out .= PHP_EOL;
					break;
			}
		}

		return $out;
	}
}
