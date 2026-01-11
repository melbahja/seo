<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\MetaTags;
use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;

class MetaTagsTest extends TestCase
{

	public function testMetaTags()
	{
		$metatags = new MetaTags(
		[
			'title' => 'My new article',
			'description' => 'My new article about how php is awesome',
			'keywords' => 'php, programming',
			'robots' => 'index, nofollow',
			'author' => 'Mohamed Elbahja'
		]);

		$this->assertEquals('<title>My new article</title><meta name="title" content="My new article" /><meta name="description" content="My new article about how php is awesome" /><meta name="keywords" content="php, programming" /><meta name="robots" content="index, nofollow" /><meta name="author" content="Mohamed Elbahja" /><meta property="og:title" content="My new article" /><meta property="og:description" content="My new article about how php is awesome" /><meta property="twitter:title" content="My new article" /><meta property="twitter:description" content="My new article about how php is awesome" />',
			str_replace("\n", '', (string) $metatags)
		);

		$metatags = new MetaTags();

		$metatags
				->title('PHP SEO')
				->description('This is my description')
				->meta('author', 'Mohamed Elabhja')
				->image('https://avatars3.githubusercontent.com/u/8259014')
				->mobile('https://m.example.com')
				->canonical('https://example.com')
				->shortlink('https://git.io/phpseo')
				->amp('https://apm.example.com')
				->hreflang('es-es', 'https://example.com/es/');

		$this->assertNotEmpty((string) $metatags);

		$this->assertEquals('<title>PHP SEO</title><meta name="title" content="PHP SEO" /><meta name="description" content="This is my description" /><meta name="author" content="Mohamed Elabhja" /><link href="https://m.example.com" rel="alternate" media="only screen and (max-width: 640px)" /><link rel="canonical" href="https://example.com" /><link rel="shortlink" href="https://git.io/phpseo" /><link rel="amphtml" href="https://apm.example.com" /><link rel="alternate" href="https://example.com/es/" hreflang="es-es" /><meta property="og:title" content="PHP SEO" /><meta property="og:description" content="This is my description" /><meta property="og:image" content="https://avatars3.githubusercontent.com/u/8259014" /><meta property="twitter:title" content="PHP SEO" /><meta property="twitter:description" content="This is my description" /><meta property="twitter:card" content="summary_large_image" /><meta property="twitter:image" content="https://avatars3.githubusercontent.com/u/8259014" />',
			str_replace("\n", '', (string)$metatags)
		);

	}

	public function testConstructorProps()
	{
		$metatags = new MetaTags(
			meta: [
				'title'       => 'Test Page',
				'description' => 'Test description',
				'keywords'    => 'php, test',
				'author'      => 'Mohamed Elbahja',
				'theme-color' => '#ffffff',
				'robots'      => 'index, follow',
				'canonical'   => 'https://example.com',

				// two args methods like verification:
				// methdName(arg, value), in this case verification(google, abc123)
				// verification, robots, feed, hreflang, image...
				'verification' => [
					'google' => 'abc123',
				],

				'link' => [
					['rel' => 'alternate', 'href' => 'https://example.com/fr', 'hreflang' => 'fr'],
					['rel' => 'alternate', 'href' => 'https://example.com/es', 'hreflang' => 'es'],
				],

			],
			og: [
				'type'      => 'article',
				'locale'    => 'en_US',
				'site_name' => 'My Site',
			],
			twitter: [
				'card'   => 'summary_large_image',
				'author' => '@dev0x0',
			],
		);

		$output = (string) $metatags;

		// var_export($output);

		$this->assertStringContainsString('<title>Test Page</title>', $output);
		$this->assertStringContainsString('name="description" content="Test description"', $output);
		$this->assertStringContainsString('name="keywords" content="php, test"', $output);
		$this->assertStringContainsString('name="author" content="Mohamed Elbahja"', $output);
		$this->assertStringContainsString('name="theme-color" content="#ffffff"', $output);
		$this->assertStringContainsString('name="robots" content="index, follow"', $output);
		$this->assertStringContainsString('name="google-site-verification" content="abc123"', $output);
		$this->assertStringContainsString('rel="canonical" href="https://example.com"', $output);
		$this->assertStringContainsString('hreflang="fr"', $output);
		$this->assertStringContainsString('hreflang="es"', $output);
		$this->assertStringContainsString('property="og:type" content="article"', $output);
		$this->assertStringContainsString('property="og:locale" content="en_US"', $output);
		$this->assertStringContainsString('property="og:site_name" content="My Site"', $output);
		$this->assertStringContainsString('property="twitter:card" content="summary_large_image"', $output);
		$this->assertStringContainsString('property="twitter:author" content="@dev0x0"', $output);
	}


	public function testMetaTagsWithSchema()
	{
		$metatags = new MetaTags([
			'title'       => 'Test Page',
			'description' => 'Test description',
			'keywords'    => 'php, test',
			'author'      => 'Mohamed Elbahja',
			'theme-color' => '#ffffff',
			'robots'      => 'index, follow',
			'canonical'   => 'https://example.com',
		]);

		$metatags->schema(new Schema(
			new Thing(type: 'Organization', props: [
				'url'          => 'https://example.com',
				'logo'         => 'https://example.com/logo.png',
				'name'         => 'Example Org',
				'contactPoint' => new Thing(type: 'ContactPoint', props: [
					'telephone' => '+1-000-555-1212',
					'contactType' => 'customer service'
				])
			])
		));

		$output = (string) $metatags;

		$output2 = (string) new MetaTags([
			'title'       => 'Test Page',
			'description' => 'Test description',
			'keywords'    => 'php, test',
			'author'      => 'Mohamed Elbahja',
			'theme-color' => '#ffffff',
			'robots'      => 'index, follow',
			'canonical'   => 'https://example.com',
			'schema'      => new Schema(
				new Thing(type: 'Organization', props: [
					'url'          => 'https://example.com',
					'logo'         => 'https://example.com/logo.png',
					'name'         => 'Example Org',
					'contactPoint' => new Thing(type: 'ContactPoint', props: [
						'telephone' => '+1-000-555-1212',
						'contactType' => 'customer service'
					])
				])
			),
		]);

		$this->assertEquals($output, $output2);
		$this->assertStringContainsString('<title>Test Page</title>', $output);
		$this->assertStringContainsString('name="description" content="Test description"', $output);
		$this->assertStringContainsString('name="keywords" content="php, test"', $output);
		$this->assertStringContainsString('name="author" content="Mohamed Elbahja"', $output);
		$this->assertStringContainsString('name="theme-color" content="#ffffff"', $output);
		$this->assertStringContainsString('name="robots" content="index, follow"', $output);
		$this->assertStringContainsString('rel="canonical" href="https://example.com"', $output);
		$this->assertStringContainsString('"@type":"ContactPoint"', $output);
		$this->assertStringContainsString('<script type="application/ld+json">', $output);
		$this->assertStringContainsString('"name":"Example Org",', $output);
	}
}
