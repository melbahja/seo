<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\Robots;

class RobotsTest extends TestCase
{
	public function testBasicRule()
	{
		$robots = new Robots();
		$robots->addRule('*', disallow: ['/admin'], allow: ['/public']);

		$expected = str_replace("\r\n", PHP_EOL, "User-agent: *\r\nDisallow: /admin\r\nAllow: /public\r\n\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testMultipleRules()
	{
		$robots = new Robots();
		$robots->addRule(userAgent: '*', disallow: ['/private', '/admin'], allow: ['/', '/public'], crawlDelay: 5);
		$robots->addRule('googlebot', [], [], 1);

		$expected = str_replace("\r\n", PHP_EOL, "User-agent: *\r\nDisallow: /private\r\nDisallow: /admin\r\nAllow: /\r\nAllow: /public\r\nCrawl-delay: 5\r\n\r\nUser-agent: googlebot\r\nCrawl-delay: 1\r\n\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testSitemap()
	{
		$robots = new Robots();
		$robots->addSitemap('https://example.com/sitemap.xml');

		$expected = str_replace("\r\n", PHP_EOL, "Sitemap: https://example.com/sitemap.xml\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testComment()
	{
		$robots = new Robots();
		$robots->addComment('Custom robots.txt');

		$expected = str_replace("\r\n", PHP_EOL, "# Custom robots.txt\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testMixedOrder()
	{
		$robots = new Robots();
		$robots->addComment('Website robots.txt');
		$robots->addSitemap('https://example.com/sitemap.xml');
		$robots->addRule('*', ['/admin']);
		$robots->addComment('Block bad bots');
		$robots->addRule('BadBot', ['/']);

		$expected = str_replace("\r\n", PHP_EOL, "# Website robots.txt\r\nSitemap: https://example.com/sitemap.xml\r\nUser-agent: *\r\nDisallow: /admin\r\n\r\n# Block bad bots\r\nUser-agent: BadBot\r\nDisallow: /\r\n\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testMultipleSitemaps()
	{
		$robots = new Robots();
		$robots->addSitemap('https://example.com/sitemap.xml');
		$robots->addSitemap('https://example.com/sitemap-news.xml');

		$expected = str_replace("\r\n", PHP_EOL, "Sitemap: https://example.com/sitemap.xml\r\nSitemap: https://example.com/sitemap-news.xml\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testMultilineComment()
	{
		$robots = new Robots();
		$robots->addComment("Line 1\nLine 2");

		$expected = str_replace("\r\n", PHP_EOL, "# Line 1\r\n# Line 2\r\n");
		$this->assertEquals($expected, (string) $robots);
	}

	public function testStringable()
	{
		$robots = new Robots();
		$robots->addRule('*', ['/admin']);

		$this->assertIsString((string) $robots);
	}

	public function testSaveTo()
	{
		$robots = new Robots();
		$robots->addRule('*', ['/admin']);

		$path = sys_get_temp_dir() . '/test_robots.txt';
		$result = $robots->saveTo($path);

		$this->assertTrue($result);
		$this->assertFileExists($path);
		$this->assertEquals((string) $robots, file_get_contents($path));

		unlink($path);
	}
}