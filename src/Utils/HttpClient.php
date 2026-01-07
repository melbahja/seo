<?php
namespace Melbahja\Utils;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class HttpClient
{
	private int $lastStatus = 0;
	private array $headers  = [];
	private ?string $baseUrl;

	/**
	 * @param string|null $baseUrl Base URL for requests
	 * @param array|null $headers Default headers for all requests
	 */
	public function __construct(?string $baseUrl = null, ?array $headers = null)
	{
		$this->baseUrl = $baseUrl ? rtrim($baseUrl, '/') : null;
		$this->headers = $headers ?? [];
	}

	/**
	 * Execute HTTP request
	 *
	 * @param string $method HTTP method GET, POST...
	 * @param string $url Full URL or path to append to base URL
	 * @param mixed $body Request body if array will auto JSON encoded
	 * @param array $headers Additional headers
	 * @return string|null Response body or null on failure
	 */
	public function request(string $method, string $url, $body = null, array $headers = []): ?string
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->buildUrl($url));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

		$isJson = false;
		if ($body !== null) {
			if (is_array($body)) {
				$body   = json_encode($body);
				$isJson = true;
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}

		$headers = array_merge($this->headers, $headers);
		$headersList = [];
		foreach ($headers as $key => $value)
		{
			// Just skip it!
			if ($isJson && strtolower($key) === 'content-type') {
				continue;
			}
			$headersList[] = "$key: $value";
		}

		if ($isJson) {
			$headersList[] = "content-type: application/json";
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headersList);

		$response = curl_exec($ch);
		$this->lastStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		return $response !== false ? $response : null;
	}

	/**
	 * Get last response status code
	 *
	 * @return int HTTP status code
	 */
	public function getStatusCode(): int
	{
		return $this->lastStatus;
	}

	/**
	 * Build full URL
	 */
	private function buildUrl(string $url): string
	{
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			return $url;
		}

		return $this->baseUrl . '/' . ltrim($url, '/');
	}
}