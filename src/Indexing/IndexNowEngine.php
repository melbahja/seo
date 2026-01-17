<?php
namespace Melbahja\Seo\Indexing;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
enum IndexNowEngine: string
{
	// this only endpoint can inform all supported engines.
	case INDEXNOW = 'https://api.indexnow.org/indexnow';

	// Current supported engines by indexnow.org.
	case BING     = 'https://www.bing.com/indexnow';
	case YANDEX   = 'https://yandex.com/indexnow';
	case AMAZON   = 'https://indexnow.amazonbot.amazon/indexnow';
	case NAVER    = 'https://searchadvisor.naver.com/indexnow';
	case SEZNAM   = 'https://search.seznam.cz/indexnow';
	case YEP      = 'https://indexnow.yep.com/indexnow';

	/**
	 * Convert engine endpoint to a complete IndexNow submission URL.
	 */
	public function toUrl(string $url, string $key): string
	{
		return sprintf('%s?url=%s&key=%s', $this->value, urlencode($url), urlencode($key));
	}
}
