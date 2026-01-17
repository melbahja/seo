<?php
namespace Melbahja\Seo\Indexing;

use \RuntimeException;
use \InvalidArgumentException;
use Melbahja\Utils\HttpClient;
use Melbahja\Seo\Interfaces\SeoInterface;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class IndexNowIndexer
{
	private string $apiKey;
	private HttpClient $httpClient;

	public function __construct(string $apiKey, ?HttpClient $httpClient = null)
	{
		if (empty($apiKey)) {
			throw new InvalidArgumentException('API key cannot be empty');
		}

		$this->apiKey     = $apiKey;
		$this->httpClient = $httpClient ?? new HttpClient();
	}

	/**
	 * Submit a single URL to $engine for indexing
	 *
	 * @param string $url The URL to submit for indexing
	 * @param IndexNowEngine|null $engine The search engine to notify defaults to indexnow all supported engines
	 * @param URLIndexingType $type The type of indexing operation not needed now y can just send new or 404 urls
	 * @return bool true on successful, false on failure.
	 */
	public function submitUrl(string $url, ?IndexNowEngine $engine = null, URLIndexingType $type = URLIndexingType::UPDATE): bool
	{
		$engine = $engine ?? IndexNowEngine::INDEXNOW;

		return $this->sendRequest($engine->toUrl($url, $this->apiKey));
	}

	/**
	 * Submit multiple URLs to $engine for indexing
	 *
	 * @param array $urls Array of URLs to submit for indexing
	 * @param IndexNowEngine|null $engine The search engine to notify defaults to indexnow all supported engines
	 * @param URLIndexingType $type The type of indexing operation not needed now y can just send new or 404 urls
	 * @return array associative, URLs as keys and values as bool success state
	 */
	public function submitUrls(array $urls, ?IndexNowEngine $engine = null, URLIndexingType $type = URLIndexingType::UPDATE): array
	{
		$results = [];
		foreach ($urls as $url)
		{
			$results[$url] = $this->submitUrl($url, $engine, $type);
		}

		return $results;
	}

	/**
	 * Serve the IndexNow verification key file
	 *
	 * This method handles requests to /{api-key}.txt and returns the key for domain verification.
	 * Returns 404 if the requested path doesn't match the expected key file path.
	 *
	 * @return never This method always exits and terminates runtime.
	 */
	public function serveKeyFile(): never
	{
		$uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
		$exp = '/' . $this->apiKey . '.txt';

		// Ignore/404 invalid attempts or revoked keys.
		if ($uri !== $exp) {

			http_response_code(404);
			header('Content-Type: text/plain');
			echo 'Key file not found';
			exit;
		}

		header('Content-Type: text/plain');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		echo $this->apiKey;
		exit;
	}

	/**
	 * Send the request to indexnow API
	 *
	 * @param string $url The API URL to send the request
	 * @return bool assumes sucess if status is less than 400, false otherwise
	 */
	private function sendRequest(string $url): bool
	{
		$this->httpClient->request('GET', $url);
		return $this->httpClient->getStatusCode() < 400;
	}

	/**
	 * Create an IndexNowIndexer instance from environment variable
	 *
	 * @param string $envVar The name of the env var of the API key, INDEXNOW_API_KEY by default.
	 * @return self New IndexNowIndexer instance
	 * @throws RuntimeException If the environment variable is not set or empty
	 */
	public static function fromEnvironment(string $envVar = 'INDEXNOW_API_KEY'): self
	{
		if (!($key = $_ENV[$envVar] ?? getenv($envVar))) {
			throw new RuntimeException("IndexNow API key not found in env var: {$envVar}");
		}

		return new self($key);
	}
}
