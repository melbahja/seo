<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;

use Melbahja\Seo\Robots;
use Melbahja\Seo\Validation\RobotsValidator;


class RobotsValidationTest extends TestCase
{
	public function testValidRobots()
	{
		$valid = "User-agent: *\r\nDisallow: /admin\r\nAllow: /public\r\nCrawl-delay: 5\r\n\r\nSitemap: https://example.com/sitemap.xml";

		$errors = RobotsValidator::validate($valid);

		$this->assertNull($errors);

		// Empty file/rules
		$this->assertNull(RobotsValidator::validate(''));
	}

	public function testValidWithRobotsObject()
	{
		$robots = new Robots();
		$robots->addRule('*', ['/admin'], ['/public'], 5);
		$robots->addSitemap('https://example.com/sitemap.xml');

		$errors = RobotsValidator::validate($robots);

		$this->assertNull($errors);
	}


	public function testInvalidRobots()
	{
		// Missing User-agent before Disallow
		$invalid = "Disallow: /admin\r\n";

		$errors = RobotsValidator::validate($invalid);

		$this->assertIsArray($errors);
		$this->assertNotEmpty($errors);
	}

	public function testMultipleErrors()
	{
		$invalid = "User-agent: \r\nDisallow: admin\r\nCrawl-delay: -5\r\nSitemap: not-a-url";

		$errors = RobotsValidator::validate($invalid);

		$this->assertIsArray($errors);
		$this->assertCount(4, $errors);
	}

	public function testInvalidFormat()
	{
		$invalid = "User-agent *\r\nSomething without colon";

		$errors = RobotsValidator::validate($invalid);

		$this->assertIsArray($errors);
		$this->assertStringContainsString('missing colon', $errors[0]);
	}
}
