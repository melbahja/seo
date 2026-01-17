<?php
namespace Melbahja\Seo\Indexing;

use \RuntimeException;
use \InvalidArgumentException;
use Melbahja\Seo\Utils\HttpClient;
use Melbahja\Seo\Interfaces\SeoInterface;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class GoogleIndexer
{
	private string $accessToken;

	private HttpClient $httpClient;

	private const API_URL = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

	public function __construct(string $accessToken, ?HttpClient $httpClient = null)
	{
		if (empty($accessToken)) {
			throw new InvalidArgumentException('Access token cannot be empty');
		}

		$this->accessToken = $accessToken;
		$this->httpClient  = $httpClient ?? new HttpClient(null,
		[
			'Authorization' => "Bearer {$this->accessToken}",
			'Content-Type' => 'application/json'
		]);
	}

	/**
	 * Submit multiple URLs to google indexing API
	 *
	 * @todo  Nice to support batch operation later.
	 * @param array $urls list of URLs to submit for indexing
	 * @param URLIndexingType $type The type of indexing operation UPDATE or DELETE
	 * @return array associative, URLs as keys and values as bool success state
	 */
	public function submitUrls(array $urls, URLIndexingType $type = URLIndexingType::UPDATE): array
	{
		$results = [];
		foreach ($urls as $url)
		{
			$results[$url] = $this->submitUrl($url, $type);
		}

		return $results;
	}

	/**
	 * Submit a single URL to google indexing API
	 *
	 * @param string $url The URL to submit
	 * @param URLIndexingType $type The type of indexing operation UPDATE or DELETE
	 * @return bool true if HTTP status is 200, false otherwise
	 */
	public function submitUrl(string $url, URLIndexingType $type = URLIndexingType::UPDATE): bool
	{
		$payload = [
			'url' => $url,
			'type' => $type === URLIndexingType::UPDATE ? 'URL_UPDATED' : 'URL_DELETED'
		];

		$this->httpClient->request('POST', self::API_URL, $payload);
		return $this->httpClient->getStatusCode() === 200;
	}

	/**
	 * Not supported - this is just for IndexNow compatibility!
	 *
	 * @return never This method always throws an exception
	 * @throws RuntimeException Always throws as Google doesn't use this verification method
	 */
	public function serveKeyFile(): never
	{
		throw new RuntimeException('Google Indexing API does not use key.txt verification');
	}

	/**
	 * Create an GoogleIndexer instance from environment variable
	 *
	 * @param string $envVar The name of the env var of the API key, INDEXNOW_API_KEY by default.
	 * @return self New GoogleIndexer instance
	 * @throws RuntimeException If the environment variable is not set or empty
	 */
	public static function fromEnvironment(string $envVar = 'GOOGLE_INDEXING_ACCESS_TOKEN'): self
	{
		if (!($token = $_ENV[$envVar] ?? getenv($envVar))) {
			throw new RuntimeException("Google Indexing API access token not found in env var: {$envVar}");
		}

		return new self($token);
	}
}
