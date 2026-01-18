<?php
namespace Tests\Melbahja\Seo\Indexing;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\Utils\HttpClient;
use Melbahja\Seo\Exceptions\SeoException;
use Melbahja\Seo\Indexing\{
	GoogleIndexer,
	IndexNowIndexer,
	IndexNowEngine,
	URLIndexingType
};

class IndexingTest extends TestCase
{

	public function testGoogleSubmitUrlSuccess()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('{"status":"ok"}');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new GoogleIndexer('valid_token', $mockClient);
		$result = $indexer->submitUrl('https://example.com');

		$this->assertTrue($result);
	}

	public function testGoogleSubmitUrlFailure()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn(null);
		$mockClient->method('getStatusCode')->willReturn(401);

		$indexer = new GoogleIndexer('invalid_token', $mockClient);
		$result = $indexer->submitUrl('https://example.com');

		$this->assertFalse($result);
	}

	public function testGoogleSubmitUrls()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('{"status":"ok"}');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new GoogleIndexer('valid_token', $mockClient);
		$urls = ['https://example.com/page1', 'https://example.com/page2'];

		$results = $indexer->submitUrls($urls);

		$this->assertIsArray($results);
		$this->assertCount(2, $results);
		$this->assertTrue($results['https://example.com/page1']);
		$this->assertTrue($results['https://example.com/page2']);
	}

	public function testGoogleSubmitUrlWithDeleteType()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('{"status":"ok"}');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new GoogleIndexer('valid_token', $mockClient);
		$result = $indexer->submitUrl('https://example.com', URLIndexingType::DELETE);

		$this->assertTrue($result);
	}

	public function testGoogleEmptyAccessToken()
	{
		$this->expectException(SeoException::class);
		$this->expectExceptionMessage('Access token cannot be empty');

		new GoogleIndexer('');
	}

	public function testGoogleServeKeyFileThrowsException()
	{
		$indexer = new GoogleIndexer('test_token');

		$this->expectException(SeoException::class);
		$this->expectExceptionMessage('Google Indexing API does not use key.txt verification');

		$indexer->serveKeyFile();
	}

	public function testGoogleFromEnvironment()
	{
		$_ENV['GOOGLE_INDEXING_ACCESS_TOKEN'] = 'test_token_from_env';

		$indexer = GoogleIndexer::fromEnvironment();

		$this->assertInstanceOf(GoogleIndexer::class, $indexer);

		unset($_ENV['GOOGLE_INDEXING_ACCESS_TOKEN']);
	}

	public function testGoogleFromEnvironmentThrowsException()
	{
		$this->expectException(SeoException::class);
		$this->expectExceptionMessage('Google Indexing API access token not found in env var: CUSTOM_VAR');

		GoogleIndexer::fromEnvironment('CUSTOM_VAR');
	}

	public function testIndexNowSubmitUrlSuccess()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new IndexNowIndexer('valid_key', $mockClient);
		$result = $indexer->submitUrl('https://example.com');

		$this->assertTrue($result);
	}

	public function testIndexNowSubmitUrlFailure()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn(null);
		$mockClient->method('getStatusCode')->willReturn(403);

		$indexer = new IndexNowIndexer('invalid_key', $mockClient);
		$result = $indexer->submitUrl('https://example.com');

		$this->assertFalse($result);
	}

	public function testIndexNowSubmitUrls()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new IndexNowIndexer('valid_key', $mockClient);
		$urls = ['https://example.com/page1', 'https://example.com/page2', 'https://example.com/page3'];

		$results = $indexer->submitUrls($urls);

		$this->assertIsArray($results);
		$this->assertCount(3, $results);
		$this->assertTrue($results['https://example.com/page1']);
		$this->assertTrue($results['https://example.com/page2']);
		$this->assertTrue($results['https://example.com/page3']);
	}

	public function testIndexNowSubmitUrlWithDifferentEngines()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new IndexNowIndexer('valid_key', $mockClient);

		$resultBing = $indexer->submitUrl('https://example.com', IndexNowEngine::BING);
		$resultYandex = $indexer->submitUrl('https://example.com', IndexNowEngine::YANDEX);

		$this->assertTrue($resultBing);
		$this->assertTrue($resultYandex);
	}

	public function testIndexNowSubmitUrlWithDeleteType()
	{
		$mockClient = $this->createMock(HttpClient::class);
		$mockClient->method('request')->willReturn('');
		$mockClient->method('getStatusCode')->willReturn(200);

		$indexer = new IndexNowIndexer('valid_key', $mockClient);
		$result = $indexer->submitUrl('https://example.com', IndexNowEngine::INDEXNOW, URLIndexingType::DELETE);

		$this->assertTrue($result);
	}

	public function testIndexNowEmptyApiKey()
	{
		$this->expectException(SeoException::class);
		$this->expectExceptionMessage('API key cannot be empty');

		new IndexNowIndexer('');
	}

	public function testIndexNowFromEnvironment()
	{
		$_ENV['INDEXNOW_API_KEY'] = 'test_key_from_env';

		$indexer = IndexNowIndexer::fromEnvironment();

		$this->assertInstanceOf(IndexNowIndexer::class, $indexer);

		unset($_ENV['INDEXNOW_API_KEY']);
	}

	public function testIndexNowEngineToUrl()
	{
		$url = IndexNowEngine::INDEXNOW->toUrl('https://example.com/page', 'mykey123');

		$this->assertStringContainsString('api.indexnow.org', $url);
		$this->assertStringContainsString('url=', $url);
		$this->assertStringContainsString('key=', $url);
		$this->assertStringContainsString('mykey123', $url);
	}

	public function testIndexNowAllEngines()
	{
		$engines = [
			IndexNowEngine::INDEXNOW,
			IndexNowEngine::BING,
			IndexNowEngine::YANDEX,
			IndexNowEngine::AMAZON,
			IndexNowEngine::NAVER,
			IndexNowEngine::SEZNAM,
			IndexNowEngine::YEP
		];

		foreach ($engines as $engine) {
			$url = $engine->toUrl('https://example.com', 'key');
			$this->assertIsString($url);
			$this->assertStringContainsString('https://', $url);
		}
	}

	public function testURLIndexingTypeValues()
	{
		$this->assertEquals('update', URLIndexingType::UPDATE->value);
		$this->assertEquals('delete', URLIndexingType::DELETE->value);
	}
}
