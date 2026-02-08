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
				->meta('author', 'Mohamed Elbahja')
				->image('https://avatars3.githubusercontent.com/u/8259014')
				->mobile('https://m.example.com')
				->canonical('https://example.com')
				->shortlink('https://git.io/phpseo')
				->amp('https://apm.example.com')
				->hreflang('es-es', 'https://example.com/es/');

		$this->assertNotEmpty((string) $metatags);

		$this->assertEquals('<title>PHP SEO</title><meta name="title" content="PHP SEO" /><meta name="description" content="This is my description" /><meta name="author" content="Mohamed Elbahja" /><link href="https://m.example.com" rel="alternate" media="only screen and (max-width: 640px)" /><link rel="canonical" href="https://example.com" /><link rel="shortlink" href="https://git.io/phpseo" /><link rel="amphtml" href="https://apm.example.com" /><link rel="alternate" href="https://example.com/es/" hreflang="es-es" /><meta property="og:title" content="PHP SEO" /><meta property="og:description" content="This is my description" /><meta property="og:image" content="https://avatars3.githubusercontent.com/u/8259014" /><meta property="twitter:title" content="PHP SEO" /><meta property="twitter:description" content="This is my description" /><meta property="twitter:card" content="summary_large_image" /><meta property="twitter:image" content="https://avatars3.githubusercontent.com/u/8259014" />',
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

				// Set multi tags
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


		$this->assertEquals($output, (string) new MetaTags([
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
		]));


		$this->assertStringContainsString('<title>Test Page</title>', $output);
		$this->assertStringContainsString('name="description" content="Test description"', $output);
		$this->assertStringContainsString('name="keywords" content="php, test"', $output);
		$this->assertStringContainsString('name="author" content="Mohamed Elbahja"', $output);
		$this->assertStringContainsString('name="theme-color" content="#ffffff"', $output);
		$this->assertStringContainsString('name="robots" content="index, follow"', $output);
		$this->assertStringContainsString('rel="canonical" href="https://example.com"', $output);
		$this->assertStringContainsString('"@type":"ContactPoint"', $output);
		$this->assertStringContainsString('"@type":"Organization"', $output);
		$this->assertStringContainsString('"@context":"https:\/\/schema.org"', $output);
		$this->assertStringContainsString('<script type="application/ld+json">', $output);
		$this->assertStringContainsString('"name":"Example Org",', $output);
	}

	public function testRobotsWithArrayOptions()
	{
		$metatags = new MetaTags();
		$metatags->robots(
			options: ['index', 'follow', 'max-snippet' => -1]
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('name="robots" content="index, follow, max-snippet:-1"', $output);
	}

	public function testRobotsWithBotName()
	{
		$metatags = new MetaTags();
		$metatags->robots(
			options: ['index', 'nofollow'],
			botName: 'bingbot'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('name="bingbot" content="index, nofollow"', $output);
	}

	public function testFeedWithTitle()
	{
		$metatags = new MetaTags();
		$metatags->feed(
			url: 'https://example.com/feed.rss',
			type: 'application/rss+xml',
			title: 'My Feed'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="alternate"', $output);
		$this->assertStringContainsString('type="application/rss+xml"', $output);
		$this->assertStringContainsString('title="My Feed"', $output);
		$this->assertStringContainsString('href="https://example.com/feed.rss"', $output);
	}

	public function testUrlMethod()
	{
		$metatags = new MetaTags();
		$metatags->url('https://example.com/page');

		$output = (string) $metatags;
		$this->assertStringContainsString('property="og:url" content="https://example.com/page"', $output);
		$this->assertStringContainsString('property="twitter:url" content="https://example.com/page"', $output);
	}

	public function testHreflangsWithDefault()
	{
		$metatags = new MetaTags();
		$metatags->hreflangs(
			langUrls: [
				'en' => 'https://example.com/en/',
				'fr' => 'https://example.com/fr/',
				'es' => 'https://example.com/es/'
			],
			defaultUrl: 'https://example.com/'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('hreflang="en"', $output);
		$this->assertStringContainsString('hreflang="fr"', $output);
		$this->assertStringContainsString('hreflang="es"', $output);
		$this->assertStringContainsString('hreflang="x-default"', $output);
	}

	public function testArticleMeta()
	{
		$metatags = new MetaTags();
		$metatags->articleMeta(
			published: '2019-03-02T13:18:30.000Z',
			modified: '2019-03-02T14:14:30.000Z',
			author: 'Mohamed Elbahja'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('property="article:published_time" content="2019-03-02T13:18:30.000Z"', $output);
		$this->assertStringContainsString('property="article:modified_time" content="2019-03-02T14:14:30.000Z"', $output);
		$this->assertStringContainsString('property="article:author" content="Mohamed Elbahja"', $output);
	}

	public function testArticleMetaWithoutOptional()
	{
		$metatags = new MetaTags();
		$metatags->articleMeta(published: '2019-03-02T13:18:30.000Z');

		$output = (string) $metatags;
		$this->assertStringContainsString('property="article:published_time"', $output);
		$this->assertStringNotContainsString('article:modified_time', $output);
		$this->assertStringNotContainsString('article:author', $output);
	}

	public function testPagination()
	{
		$metatags = new MetaTags();
		$metatags->pagination(
			prev: 'https://example.com/page/1',
			next: 'https://example.com/page/3',
			first: 'https://example.com/page/1',
			last: 'https://example.com/page/10'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="prev" href="https://example.com/page/1"', $output);
		$this->assertStringContainsString('rel="next" href="https://example.com/page/3"', $output);
		$this->assertStringContainsString('rel="first" href="https://example.com/page/1"', $output);
		$this->assertStringContainsString('rel="last" href="https://example.com/page/10"', $output);
	}

	public function testPaginationPartial()
	{
		$metatags = new MetaTags();
		$metatags->pagination(next: 'https://example.com/page/2');

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="next"', $output);
		$this->assertStringNotContainsString('rel="prev"', $output);
	}

	public function testImageWithCustomCard()
	{
		$metatags = new MetaTags();
		$metatags->image(
			url: 'https://example.com/image.jpg',
			card: 'summary'
		);

		$output = (string) $metatags;
		$this->assertStringContainsString('property="og:image"', $output);
		$this->assertStringContainsString('property="twitter:card" content="summary"', $output);
		$this->assertStringContainsString('property="twitter:image"', $output);
	}

	public function testEmptyMetaValueSkipped()
	{
		$metatags = new MetaTags();
		$metatags->push('meta', ['name' => 'test', 'content' => '']);

		$output = (string) $metatags;
		$this->assertStringNotContainsString('content=""', $output);
	}

	public function testXssPrevention()
	{
		$metatags = new MetaTags();
		$metatags->meta(
			name: 'description" onload="alert(1)',
			value: 'test"><script>alert(1)</script>'
		);

		$output = (string) $metatags;
		$this->assertStringNotContainsString('onload&quot;', $output);
		$this->assertStringNotContainsString('<script>', $output);
		$this->assertStringContainsString('name="description&quot; onload=&quot;alert(1)"', $output);
		$this->assertStringContainsString('content="test&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;" />', $output);
	}

	public function testSchemaViaConstructor()
	{
		$schema = new Schema(
			new Thing(type: 'Organization', props: [
				'name' => 'Test Org',
				'url' => 'https://example.com'
			])
		);

		$metatags = new MetaTags(schema: $schema);

		$output = (string) $metatags;
		$this->assertStringContainsString('<script type="application/ld+json">', $output);
		$this->assertStringContainsString('"@type":"Organization"', $output);
	}

	public function testBuildMethodSorting()
	{
		$metatags = new MetaTags();
		$metatags->og('title', 'OG Title');
		$metatags->twitter('title', 'Twitter Title');
		$metatags->meta('description', 'Description');
		$metatags->canonical('https://example.com');

		$output = (string) $metatags;

		// Check order: meta tags first, then links, then og, then twitter
		$metaPos = strpos($output, 'name="description"');
		$linkPos = strpos($output, 'rel="canonical"');
		$ogPos = strpos($output, 'property="og:title"');
		$twitterPos = strpos($output, 'property="twitter:title"');

		$this->assertLessThan($linkPos, $metaPos);
		$this->assertLessThan($ogPos, $linkPos);
		$this->assertLessThan($twitterPos, $ogPos);
	}

	public function testEmptyObjectOutput()
	{
		$metatags = new MetaTags();
		$output = (string) $metatags;
		$this->assertEquals('', $output);
	}

	public function testMultipleVerificationEngines()
	{
		$metatags = new MetaTags();
		$metatags->verification('google', 'abc123');
		$metatags->verification('bing', 'abc456');
		$metatags->verification('yandex', 'abc789');

		$output = (string) $metatags;
		$this->assertStringContainsString('name="google-site-verification" content="abc123"', $output);
		$this->assertStringContainsString('name="bing-site-verification" content="abc456"', $output);
		$this->assertStringContainsString('name="yandex-site-verification" content="abc789"', $output);
	}

	public function testAmpMethod()
	{
		$metatags = new MetaTags();
		$metatags->amp('https://amp.example.com/page');

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="amphtml" href="https://amp.example.com/page"', $output);
	}

	public function testMobileMethod()
	{
		$metatags = new MetaTags();
		$metatags->mobile('https://m.example.com/page');

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="alternate"', $output);
		$this->assertStringContainsString('media="only screen and (max-width: 640px)"', $output);
		$this->assertStringContainsString('href="https://m.example.com/page"', $output);
	}

	public function testShortlinkMethod()
	{
		$metatags = new MetaTags();
		$metatags->shortlink('https://git.io/mohamed');

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="shortlink" href="https://git.io/mohamed"', $output);
	}

	public function testMetaMethodChaining()
	{
		$metatags = new MetaTags();
		$result = $metatags
			->meta('author', 'Mohamed')
			->meta('keywords', 'php, seo')
			->meta('theme-color', '#fff');

		$this->assertInstanceOf(MetaTags::class, $result);

		$output = (string) $metatags;
		$this->assertStringContainsString('name="author" content="Mohamed"', $output);
		$this->assertStringContainsString('name="keywords" content="php, seo"', $output);
		$this->assertStringContainsString('name="theme-color" content="#fff"', $output);
	}

	public function testOgMethodChaining()
	{
		$metatags = new MetaTags();
		$result = $metatags
			->og('type', 'article')
			->og('locale', 'en_US')
			->og('site_name', 'My Site');

		$this->assertInstanceOf(MetaTags::class, $result);

		$output = (string) $metatags;
		$this->assertStringContainsString('property="og:type" content="article"', $output);
		$this->assertStringContainsString('property="og:locale" content="en_US"', $output);
		$this->assertStringContainsString('property="og:site_name" content="My Site"', $output);
	}

	public function testTwitterMethodChaining()
	{
		$metatags = new MetaTags();
		$result = $metatags
			->twitter('card', 'summary')
			->twitter('author', '@dev0x0')
			->twitter('site', '@site');

		$this->assertInstanceOf(MetaTags::class, $result);

		$output = (string) $metatags;
		$this->assertStringContainsString('property="twitter:card" content="summary"', $output);
		$this->assertStringContainsString('property="twitter:author" content="@dev0x0"', $output);
		$this->assertStringContainsString('property="twitter:site" content="@site"', $output);
	}

	public function testPushMethod()
	{
		$metatags = new MetaTags();
		$result = $metatags->push('link', [
			'rel' => 'manifest',
			'href' => '/manifest.json'
		]);

		$this->assertInstanceOf(MetaTags::class, $result);

		$output = (string) $metatags;
		$this->assertStringContainsString('rel="manifest" href="/manifest.json"', $output);
	}

	public function testComplexRealWorldScenario()
	{
		$schema = new Schema(
			new Thing(type: 'Article', props: [
				'headline' => 'Test Article',
				'author' => new Thing(type: 'Person', props: ['name' => 'Mohamed Elbahja']),
				'datePublished' => '2024-01-15'
			])
		);

		$metatags = new MetaTags(
			meta: [
				'title' => 'Test Article',
				'description' => 'Article description',
				'robots' => 'index, follow',
				'canonical' => 'https://example.com/article',
				'verification' => ['google' => 'abc123']
			],
			og: [
				'type' => 'article',
				'locale' => 'en_US'
			],
			twitter: [
				'card' => 'summary_large_image',
				'creator' => '@dev0x0'
			]
		);

		$metatags->schema($schema);

		$output = (string) $metatags;

		$this->assertStringContainsString('<title>Test Article</title>', $output);
		$this->assertStringContainsString('name="description"', $output);
		$this->assertStringContainsString('rel="canonical"', $output);
		$this->assertStringContainsString('name="google-site-verification"', $output);
		$this->assertStringContainsString('property="og:type" content="article"', $output);
		$this->assertStringContainsString('property="twitter:card"', $output);
		$this->assertStringContainsString('"@type":"Article"', $output);
		$this->assertStringContainsString('"headline":"Test Article"', $output);
	}
}
