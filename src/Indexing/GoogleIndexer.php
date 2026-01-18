<?php
namespace Melbahja\Seo\Indexing;


use Melbahja\Seo\{
	Utils\HttpClient,
	Interfaces\SeoInterface,
	Exceptions\SeoException
};

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class GoogleIndexer implements SeoInterface
{
	private string $accessToken;

	private HttpClient $httpClient;

	private const API_URL = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

	public function __construct(string $accessToken, ?HttpClient $httpClient = null)
	{
		if (empty($accessToken)) {
			throw new SeoException('Access token cannot be empty');
		}

		$this->accessToken = $accessToken;
		$this->httpClient  = $httpClient ?? new HttpClient(headers: [
			'Authorization' => "Bearer {$this->accessToken}",
			'Content-Type' => 'application/json'
		]);
	}

	/**
	 * Submit multiple URLs to google indexing API
	 *
	 * @todo  Nice to support BATCH operation later.
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
	 * @throws SeoException Always throws as Google doesn't use this verification method
	 */
	public function serveKeyFile(): never
	{
		throw new SeoException('Google Indexing API does not use key.txt verification');
	}

	/**
	 * Create an GoogleIndexer instance from environment variable
	 *
	 * @param string $envVar The name of the env var of the API key, GOOGLE_INDEXING_ACCESS_TOKEN by default.
	 * @return self New GoogleIndexer instance
	 * @throws SeoException If the environment variable is not set or empty
	 */
	public static function fromEnvironment(string $envVar = 'GOOGLE_INDEXING_ACCESS_TOKEN'): self
	{
		if (!($token = $_ENV[$envVar] ?? getenv($envVar))) {
			throw new SeoException("Google Indexing API access token not found in env var: {$envVar}");
		}

		return new self($token);
	}
}
