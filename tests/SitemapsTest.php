<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\{
	Sitemap,
	Utils\Utils,
	Sitemap\LinksBuilder,
	Sitemap\IndexBuilder,
	Sitemap\NewsBuilder,
	Sitemap\SitemapUrl,
	Sitemap\OutputMode,
	Exceptions\SitemapException
};

class SitemapsTest extends TestCase
{
	private string $testDir;

	protected function setUp(): void
	{
		$this->testDir = sys_get_temp_dir() . '/sitemap_seo_tests';

		if (!is_dir($this->testDir)) {
			mkdir($this->testDir, 0755, true);
		}
	}

	protected function tearDown(): void
	{
		if (is_dir($this->testDir)) {
			$this->removeDirectory($this->testDir);
		}
	}

	private function removeDirectory(string $dir): void
	{
		if (!is_dir($dir)) {
			return;
		}

		$items = array_diff(scandir($dir), ['.', '..']);

		foreach ($items as $item)
		{
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			is_dir($path) ? $this->removeDirectory($path) : unlink($path);
		}

		rmdir($dir);
	}

	public function testSitemapBuilderBasic()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'posts.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/posts/12')->priority(0.9);
			$builder->loc('/posts/13')->priority(0.9)->lastMod(date('c'));
		});

		$this->assertTrue($sitemap->render());
		$this->assertFileExists($this->testDir . '/posts.xml');
		$this->assertFileExists($this->testDir . '/sitemap.xml');
	}

	public function testSitemapMemoryMode()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			mode: OutputMode::MEMORY
		);

		$sitemap->links('blog.xml', function(LinksBuilder $builder)
		{
			//
			// by design the memory mode in this func will never be called
			// for heavy db queries and rare use cases that you may want
			// to serve only the sitemap index.
			//
			$builder->loc('/blog/post-1')->priority(0.8);
			$builder->loc('/blog/post-2')->priority(0.7);

			exit("This function/generator MUST not be called");
		});

		$xml = $sitemap->render();

		$this->assertIsString($xml);
		$this->assertStringContainsString('<sitemapindex', $xml);
		$this->assertStringContainsString('blog.xml', $xml);
	}

	public function testSitemapSaveWithPriorities()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'blog.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/blog/this_is_php')->priority(0.9);
			$builder->loc('/blog/nx')->priority(0.0);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/blog.xml');

		$this->assertStringContainsString('<loc>https://example.com/blog/this_is_php</loc>', $content);
		$this->assertStringContainsString('<priority>0.9</priority>', $content);
		$this->assertStringContainsString('<loc>https://example.com/blog/nx</loc>', $content);
		$this->assertStringContainsString('<priority>0.0</priority>', $content);
	}

	public function testSitemapWithImages()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'images.xml', 'images' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/php')
				->image('http://php.net/images/logos/php-logo.png', ['title' => 'PHP logo'])
				->image('https://pear.php.net/gifs/pearsmall.gif', ['caption' => 'php pear']);

			$builder->loc('/the-place')
				->image('/uploads/image.jpeg', ['geo_location' => '40.7590,-73.9845']);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/images.xml');

		$this->assertStringContainsString('xmlns:image=', $content);
		$this->assertStringContainsString('<image:title>PHP logo</image:title>', $content);
		$this->assertStringContainsString('<image:caption>php pear</image:caption>', $content);
		$this->assertStringContainsString('<image:geo_location>40.7590,-73.9845</image:geo_location>', $content);
	}

	public function testEscapedUrls()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'escaped.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/ümlat/test?item=12&desc=vacation_hawaii');
			$builder->loc('اهلا-بالعالم');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/escaped.xml');

		$this->assertStringContainsString('%C3%BCmlat/test?item=12&amp;desc=vacation_hawaii', $content);
		$this->assertStringContainsString('%D8%A7%D9%87%D9%84%D8%A7-%D8%A8%D8%A7%D9%84%D8%B9%D8%A7%D9%84%D9%85', $content);
	}

	public function testVideoRequiredOptionsException()
	{
		$this->expectException(SitemapException::class);

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'videos.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/videos/12')->video('Watch my new video', [
				'description' => 'Test'
			]);
		});

		$sitemap->render();
	}

	public function testVideoNoContentOrPlayerLocException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('Raw video url content_loc or player_loc embed is required');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'videos.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/videos/12')->video('My new video', [
				'thumbnail' => 'https://example.com/th.jpeg',
				'description' => 'My descriptions'
			]);
		});

		$sitemap->render();
	}

	public function testBuildedSitemapWithVideos()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'videos.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/blog/12')->changeFreq('weekly')->priority(0.7);
			$builder->loc('/blog/13')->changeFreq('monthly')->priority(0.8)->video('My new video', [
				'thumbnail' => 'https://example.com/th.jpeg',
				'description' => 'My descriptions',
				'content_loc' => 'https://example.com/video.mp4'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/videos.xml');

		$this->assertStringContainsString('xmlns:video=', $content);
		$this->assertStringContainsString('<video:title>My new video</video:title>', $content);
		$this->assertStringContainsString('<video:description>My descriptions</video:description>', $content);
		$this->assertStringContainsString('<video:content_loc>https://example.com/video.mp4</video:content_loc>', $content);
		$this->assertStringContainsString('<video:thumbnail_loc>https://example.com/th.jpeg</video:thumbnail_loc>', $content);
	}

	public function testBuildedSitemapWithLocalizedUrls()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'localized.xml', 'localized' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/blog/12')->changeFreq('weekly')->priority(0.7);
			$builder->loc('/blog/13')->changeFreq('monthly')->priority(0.8)
				->alternate('/ar/blog/13', 'ar')
				->alternate('/de/blog/13', 'de');
			$builder->loc('/blog/14')
				->alternate('/ar/blog/14', 'ar')
				->alternate('/de/blog/14', 'de');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/localized.xml');

		$this->assertStringContainsString('xmlns:xhtml=', $content);
		$this->assertStringContainsString('hreflang="ar"', $content);
		$this->assertStringContainsString('hreflang="de"', $content);
		$this->assertStringContainsString('rel="alternate"', $content);
	}

	public function testSitemapIndexGeneration()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'posts.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/post/1')->priority(0.8);
		});

		$sitemap->links(['name' => 'pages.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/about')->priority(0.5);
		});

		$this->assertTrue($sitemap->render());

		$this->assertFileExists($this->testDir . '/sitemap.xml');
		$this->assertFileExists($this->testDir . '/posts.xml');
		$this->assertFileExists($this->testDir . '/pages.xml');

		$indexContent = file_get_contents($this->testDir . '/sitemap.xml');

		$this->assertStringContainsString('<sitemapindex', $indexContent);
		$this->assertStringContainsString('posts.xml', $indexContent);
		$this->assertStringContainsString('pages.xml', $indexContent);
	}

	public function testCustomIndexName()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			indexName: 'custom_index.xml',
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'blog.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/blog')->priority(0.5);
		});

		$this->assertTrue($sitemap->render());
		$this->assertFileExists($this->testDir . '/custom_index.xml');
	}

	public function testCustomSitemapBaseUrl()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			sitemapBaseUrl: 'https://cdn.example.com/sitemaps',
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'blog.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/blog')->priority(0.5);
		});

		$this->assertTrue($sitemap->render());

		$indexContent = file_get_contents($this->testDir . '/sitemap.xml');

		$this->assertStringContainsString('https://cdn.example.com/sitemaps/blog.xml', $indexContent);
	}

	public function testMaxSitemapUrls()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('The maximum urls has been exhausted');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'max.xml'], function(LinksBuilder $builder)
		{
			$builder->maxUrls = 10;

			for ($i = 0; $i < 20; $i++)
			{
				$builder->loc("/post/{$i}");
			}
		});

		$sitemap->render();
	}

	public function testNewsSitemap()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->news(['name' => 'news.xml'], function(NewsBuilder $builder)
		{
			$builder->loc('/news/12')->news([
				'name' => 'DogNews',
				'language' => 'en',
				'publication_date' => '1997-07-16T19:20:30+01:00',
				'title' => 'Breaking Cat Flying A Plane'
			]);

			$builder->loc('/news/13')->news([
				'name' => 'DogNews',
				'language' => 'en',
				'publication_date' => '2000-07-16T19:22:30+01:00',
				'title' => 'Breaking Cat Flying A Private Jet'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/news.xml');

		$this->assertStringContainsString('xmlns:news=', $content);
		$this->assertStringContainsString('<news:name>DogNews</news:name>', $content);
		$this->assertStringContainsString('<news:language>en</news:language>', $content);
		$this->assertStringContainsString('<news:title>Breaking Cat Flying A Plane</news:title>', $content);
	}

	public function testSitemapUrlObject()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'objects.xml', 'images' => true], function(LinksBuilder $builder)
		{

			$url = new SitemapUrl(
				url: '/test-page',
				lastmod: time(),
				priority: 0.9,
				changefreq: 'weekly'
			);

			$url->image('/image.jpg', ['title' => 'Test Image']);

			$builder->addItem($url);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/objects.xml');

		$this->assertStringContainsString('<loc>https://example.com/test-page</loc>', $content);
		$this->assertStringContainsString('<priority>0.9</priority>', $content);
		$this->assertStringContainsString('<changefreq>weekly</changefreq>', $content);
		$this->assertStringContainsString('<image:title>Test Image</image:title>', $content);
	}

	public function testCDataAutoDetection()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'cdata.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/video')->video('Title with <special> & chars',
			[
				'thumbnail' => 'https://example.com/thumb.jpg',
				'description' => 'Description with <tags> & ampersands',
				'content_loc' => 'https://example.com/video.mp4'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/cdata.xml');

		$this->assertStringContainsString('<![CDATA[Title with <special> & chars]]>', $content);
		$this->assertStringContainsString('<![CDATA[Description with <tags> & ampersands]]>', $content);
	}

	public function testIndentation()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'indented.xml', 'indent' => "\t"], function(LinksBuilder $builder)
		{
			$builder->loc('/page-1')->priority(0.8);
			$builder->loc('/page-2')->priority(0.7);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/indented.xml');

		$this->assertStringContainsString("\t<url>", $content);
		$this->assertStringContainsString("\t\t<loc>", $content);
	}

	public function testVideoWithAttributes()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'video_attrs.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/video')->video('Test Video', [
				'thumbnail' => 'https://example.com/thumb.jpg',
				'description' => 'Test description',
				'player_loc' => [
					'value' => 'https://example.com/player',
					'attrs' => ['allow_embed' => 'yes', 'autoplay' => 'ap=1']
				]
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/video_attrs.xml');

		$this->assertStringContainsString('allow_embed="yes"', $content);
		$this->assertStringContainsString('autoplay="ap=1"', $content);
	}

	public function testMultipleVideosPerUrl()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'multi_videos.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/page')
				->video('First Video', [
					'thumbnail' => 'https://example.com/thumb1.jpg',
					'description' => 'First description',
					'content_loc' => 'https://example.com/video1.mp4'
				])
				->video('Second Video', [
					'thumbnail' => 'https://example.com/thumb2.jpg',
					'description' => 'Second description',
					'content_loc' => 'https://example.com/video2.mp4'
				]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/multi_videos.xml');

		$this->assertStringContainsString('<video:title>First Video</video:title>', $content);
		$this->assertStringContainsString('<video:title>Second Video</video:title>', $content);
	}

	public function testArabicContent()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'arabic.xml', 'videos' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/arabic-page')->video('عنوان الفيديو', [
				'thumbnail' => 'https://cdn.example.com/thumb.jpg',
				'description' => 'وصف الفيديو بالعربية',
				'content_loc' => 'https://example.com/video.mp4'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/arabic.xml');

		$this->assertStringContainsString('https://cdn.example.com/thumb.jpg', $content);
		$this->assertStringContainsString('عنوان الفيديو', $content);
		$this->assertStringContainsString('وصف الفيديو بالعربية', $content);
	}

	public function testLargeNumberOfUrls()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'large.xml'], function(LinksBuilder $builder)
		{
			for ($i = 0; $i < 10000; $i++)
			{
				$builder->loc("/page-{$i}")->priority(0.5);
			}
		});

		$this->assertTrue($sitemap->render());
		$this->assertFileExists($this->testDir . '/large.xml');

		$content = file_get_contents($this->testDir . '/large.xml');
		$this->assertStringContainsString('/page-0', $content);
		$this->assertStringContainsString('/page-9999', $content);
	}

	public function testGeneratorDataSource()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		// y can do this, but u can use lazy Utils::generator() for heavy db queries for eg!
		$generator = function() {
			for ($i = 0; $i < 100; $i++)
			{
				yield new SitemapUrl(
					url: "/post-{$i}",
					lastmod: time(),
					priority: 0.8,
					changefreq: 'daily'
				);
			}
		};

		$sitemap->links(['name' => 'generator.xml'], $generator());

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/generator.xml');
		$this->assertStringContainsString('/post-0', $content);
		$this->assertStringContainsString('/post-99', $content);
		$this->assertStringContainsString('<changefreq>daily</changefreq>', $content);
	}

	public function testArrayDataSource()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$urls = ['/page-1', '/page-2', '/page-3'];

		$sitemap->links(['name' => 'array.xml'], $urls);

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/array.xml');
		$this->assertStringContainsString('/page-1', $content);
		$this->assertStringContainsString('/page-2', $content);
		$this->assertStringContainsString('/page-3', $content);
	}

	public function testMixedSitemapUrlObjects()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'objects.xml'], [
			new SitemapUrl(url: '/page-1', lastmod: time(), priority: 0.9, changefreq: 'weekly'),
			new SitemapUrl(url: '/page-2', lastmod: time(), priority: 0.7, changefreq: 'monthly'),
			"/test-url", // [1]
		]);

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/objects.xml');
		$this->assertStringContainsString('<priority>0.9</priority>', $content);
		$this->assertStringContainsString('<changefreq>weekly</changefreq>', $content);
		$this->assertStringContainsString('/page-2', $content);
		$this->assertStringContainsString('<loc>https://example.com/test-url</loc>', $content); // [1] str
	}

	public function testInvalidPriorityException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('Priority must be between 0.0 and 1.0');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'invalid.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->priority(1.5);
		});

		$sitemap->render();
	}

	public function testInvalidChangeFreqException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('changefreq value not valid');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'invalid.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->changeFreq('sometimes');
		});

		$sitemap->render();
	}

	public function testInvalidDateException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('Invalid date format');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'invalid.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->lastMod('not-a-date');
		});

		$sitemap->render();
	}

	public function testImageWithoutEnablingException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('enable images option');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'test.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->image('/image.jpg');
		});

		$sitemap->render();
	}

	public function testVideoWithoutEnablingException()
	{
		$this->expectException(SitemapException::class);
		$this->expectExceptionMessage('enable videos option');

		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'test.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->video('Test',
			[
				'thumbnail' => 'thumb.jpg',
				'description' => 'desc',
				'content_loc' => 'video.mp4'
			]);
		});

		$sitemap->render();
	}

	public function testComplexMixedSitemap()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'complex.xml', 'images' => true, 'videos' => true, 'localized' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/product/123')
				->priority(0.9)
				->changeFreq('daily')
				->lastMod(time())
				->image('/product-main.jpg', ['title' => 'Product Main Image'])
				->image('/product-thumb.jpg', ['caption' => 'Thumbnail'])
				->video('Product Demo', [
					'thumbnail' => '/video-thumb.jpg',
					'description' => 'Watch our product demo',
					'content_loc' => '/videos/demo.mp4',
					'duration' => 120
				])
				->alternate('/fr/product/123', 'fr')
				->alternate('https://de.example.com/product/123', 'de'); // y can do another host not based on default base url.
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/complex.xml');

		$this->assertStringContainsString('xmlns:image=', $content);
		$this->assertStringContainsString('xmlns:video=', $content);
		$this->assertStringContainsString('xmlns:xhtml=', $content);
		$this->assertStringContainsString('<image:title>Product Main Image</image:title>', $content);
		$this->assertStringContainsString('<video:title>Product Demo</video:title>', $content);
		$this->assertStringContainsString('hreflang="fr"', $content);
		$this->assertStringContainsString('href="https://de.example.com/product/123"', $content);
	}

	public function testMultipleSitemapsWithDifferentOptions()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'posts.xml', 'indent' => '  '], function(LinksBuilder $builder)
		{
			$builder->loc('/blog/post-1')->priority(0.8);
		});

		$sitemap->links(['name' => 'videos.xml', 'videos' => true, 'indent' => "\t"], function(LinksBuilder $builder)
		{
			$builder->loc('/videos/1')->video('Video 1', [
				'thumbnail' => 'thumb.jpg',
				'description' => 'desc',
				'content_loc' => 'video.mp4'
			]);
		});

		$sitemap->links(['name' => 'gallery.xml', 'images' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/gallery/1')->image('image.jpg');
		});

		$this->assertTrue($sitemap->render());

		$this->assertFileExists($this->testDir . '/posts.xml');
		$this->assertFileExists($this->testDir . '/videos.xml');
		$this->assertFileExists($this->testDir . '/gallery.xml');
		$this->assertFileExists($this->testDir . '/sitemap.xml');

		$indexContent = file_get_contents($this->testDir . '/sitemap.xml');
		$this->assertStringContainsString('posts.xml', $indexContent);
		$this->assertStringContainsString('videos.xml', $indexContent);
		$this->assertStringContainsString('gallery.xml', $indexContent);
	}

	public function testNewsWithAllFields()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->news(['name' => 'news-full.xml'], function(NewsBuilder $builder)
		{
			$builder->loc('/news/breaking')->news([
				'name' => 'Example News',
				'language' => 'en',
				'publication_date' => '2024-01-15T10:00:00+00:00',
				'title' => 'Breaking News Title',
				'keywords' => 'breaking, news, important',
				'stock_tickers' => 'NASDAQ:ACOM'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/news-full.xml');

		$this->assertStringContainsString('<news:title>Breaking News Title</news:title>', $content);
		$this->assertStringContainsString('<news:keywords>breaking, news, important</news:keywords>', $content);
		$this->assertStringContainsString('<news:stock_tickers>NASDAQ:ACOM</news:stock_tickers>', $content);
	}

	public function testRelativeAndAbsoluteUrls()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'urls.xml', 'images' => true], function(LinksBuilder $builder)
		{
			$builder->loc('/relative-url')->image('/relative-image.jpg');
			$builder->loc('https://example.com/absolute-url')->image('https://cdn.example.com/absolute-image.jpg');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/urls.xml');

		$this->assertStringContainsString('https://example.com/relative-url', $content);
		$this->assertStringContainsString('https://example.com/relative-image.jpg', $content);
		$this->assertStringContainsString('https://example.com/absolute-url', $content);
		$this->assertStringContainsString('https://cdn.example.com/absolute-image.jpg', $content);
	}

	public function testSpecialCharactersInUrls()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'special.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/path?param1=value&param2=value2');
			$builder->loc('/café/naïve');
			$builder->loc('/中文/路径');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/special.xml');

		$this->assertStringContainsString('&amp;', $content);
		$this->assertStringContainsString('%C3%A9', $content); // café encoded
		$this->assertStringContainsString('%C3%AF', $content); // naïve encoded
	}

	public function testEmptySitemapStillGeneratesIndex()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'empty.xml'], function(LinksBuilder $builder)
		{
			// No URLs added
		});

		$this->assertTrue($sitemap->render());

		$this->assertFileExists($this->testDir . '/empty.xml');
		$this->assertFileExists($this->testDir . '/sitemap.xml');
	}

	public function testManualCDataFields()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'cdata-manual.xml', 'videos' => true, 'cdata' => ['video:content_loc']], function(LinksBuilder $builder)
		{
			$builder->loc('/video')->video('Normal Title', [
				'thumbnail' => 'thumb.jpg',
				'description' => 'Normal description',
				'content_loc' => 'This will be wrapped in CDATA'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/cdata-manual.xml');

		// content_loc should be in CDATA because we specified it
		$this->assertStringContainsString('<![CDATA[This will be wrapped in CDATA]]>', $content);
	}

	public function testTempModeCleanup()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::TEMP
		);

		$sitemap->links(['name' => 'temp.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page')->priority(0.5);
		});

		$this->assertTrue($sitemap->render());
		$this->assertFileExists($this->testDir . '/temp.xml');

		// tmp file should not exist after cleaned up
		$tempFiles = glob(sys_get_temp_dir() . '/*.xml');
		$this->assertEmpty(
			array_filter($tempFiles, fn($f) => str_contains(basename($f), md5('')))
		);
	}

	public function testMemoryModeToFile()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			mode: OutputMode::MEMORY
		);

		$sitemap->links('mem:2', function(LinksBuilder $builder)
		{
			$builder->loc('/page-1')->priority(0.8);
			$builder->loc('/page-2')->priority(0.6);
		});

		$success = $sitemap->render($this->testDir . '/output.xml');

		$this->assertTrue($success);
		$this->assertFileExists($this->testDir . '/output.xml');

		$content = file_get_contents($this->testDir . '/output.xml');
		$this->assertStringContainsString('<sitemapindex', $content);
	}

	public function testChangeFreqValues()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$validFreqs = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

		$sitemap->links(['name' => 'freqs.xml'], function(LinksBuilder $builder) use ($validFreqs)
		{
			foreach ($validFreqs as $freq)
			{
				$builder->loc("/page-{$freq}")->changeFreq($freq);
			}
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/freqs.xml');

		foreach ($validFreqs as $freq)
		{
			$this->assertStringContainsString("<changefreq>{$freq}</changefreq>", $content);
		}
	}

	public function testDateFormats()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'dates.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page-1')->lastMod(time());
			$builder->loc('/page-2')->lastMod('2024-01-15');
			$builder->loc('/page-3')->lastMod('2024-01-15 10:30:00');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/dates.xml');

		$this->assertStringContainsString('<lastmod>', $content);

		// ISO 8601 format
		$this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $content);
	}

	public function testVideoThumbnailAlias()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'video-alias.xml', 'videos' => true], function($builder)
		{
			$builder->loc('/video')->video('Test Video', [
				'thumbnail' => 'thumb.jpg',  // Using alias
				'description' => 'Test',
				'content_loc' => 'video.mp4'
			]);
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/video-alias.xml');

		$this->assertStringContainsString('<video:thumbnail_loc>https://example.com/thumb.jpg</video:thumbnail_loc>', $content);
	}

	public function testPriorityFormatting()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->links(['name' => 'priority.xml'], function(LinksBuilder $builder)
		{
			$builder->loc('/page-1')->priority(1.0);
			$builder->loc('/page-2')->priority(0.5);
			$builder->loc('/page-3')->priority(0);
			$builder->loc('/page-4')->priority('0.7');
		});

		$this->assertTrue($sitemap->render());

		$content = file_get_contents($this->testDir . '/priority.xml');

		// float format check
		$this->assertStringContainsString('<priority>1.0</priority>', $content);
		$this->assertStringContainsString('<priority>0.5</priority>', $content);
		$this->assertStringContainsString('<priority>0.0</priority>', $content);
		$this->assertStringContainsString('<priority>0.7</priority>', $content);
	}


	public function testManualBuildersWithoutSitemapHelper()
	{
		// manual LinksBuilder
		$linksBuilder = new LinksBuilder(
			baseUrl: 'https://example.com',
			filePath: $this->testDir . '/manual-links.xml',
			mode: OutputMode::FILE,
			options: ['indent' => "\t", 'images' => true]
		);

		$linksBuilder
			->loc('/page-1')->priority(0.9)->lastMod(time())
			->loc('/page-2')->priority(0.7)->changeFreq('weekly')
			->loc('/gallery')->image('/img1.jpg', ['title' => 'Image 1'])
				->image('/img2.jpg', ['caption' => 'Image 2']);

		$this->assertTrue($linksBuilder->render());
		$this->assertFileExists($this->testDir . '/manual-links.xml');

		// manual IndexBuilder
		$indexBuilder = new IndexBuilder(
			baseUrl: 'https://example.com',
			filePath: $this->testDir . '/manual-index.xml',
			mode: OutputMode::FILE,
			options: ['indent' => '  ']
		);

		$indexBuilder
			->addSitemap('https://example.com/manual-links.xml')->lastMod(time())
			->addSitemap('https://example.com/sitemap-posts.xml')->lastMod('2024-01-15');

		$this->assertTrue($indexBuilder->render());
		$this->assertFileExists($this->testDir . '/manual-index.xml');

		$indexContent = file_get_contents($this->testDir . '/manual-index.xml');

		$this->assertStringContainsString('<sitemapindex', $indexContent);
		$this->assertStringContainsString('  <sitemap', $indexContent);
		$this->assertStringContainsString('manual-links.xml', $indexContent);
		$this->assertStringContainsString('sitemap-posts.xml', $indexContent);
		$this->assertStringContainsString('<lastmod>', $indexContent);

		$linksContent = file_get_contents($this->testDir . '/manual-links.xml');

		$this->assertStringContainsString("<urlset", $linksContent);
		$this->assertStringContainsString("\t<loc>", $linksContent);
		$this->assertStringContainsString('xmlns:image=', $linksContent);
		$this->assertStringContainsString('<image:title>Image 1</image:title>', $linksContent);
	}


	public function testStreamOutputMode()
	{
		$streamFile = $this->testDir . '/stream-output.xml';
		$stream = fopen($streamFile, 'w');

		$this->assertIsResource($stream);

		$builder = new LinksBuilder(
			baseUrl: 'https://example.com',
			filePath: null,
			mode: OutputMode::STREAM,
			stream: $stream,
			options: ['indent' => "\t"]
		);

		$builder
			->loc('/stream-page-1')->priority(0.8)
			->loc('/stream-page-2')->priority(0.6)->changeFreq('daily')
			->loc('/stream-page-3')->lastMod(time());

		$this->assertTrue($builder->render());

		fclose($stream);

		$this->assertFileExists($streamFile);

		$content = file_get_contents($streamFile);

		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
		$this->assertStringContainsString('<urlset', $content);
		$this->assertStringContainsString('</urlset>', $content);
		$this->assertStringContainsString('/stream-page-1', $content);
		$this->assertStringContainsString('/stream-page-2', $content);
		$this->assertStringContainsString('<changefreq>daily</changefreq>', $content);
		$this->assertStringContainsString('<priority>0.8</priority>', $content);
	}


	public function testTraversableWithUtilsGenerator()
	{
		$sitemap = new Sitemap(
			baseUrl: 'https://example.com',
			saveDir: $this->testDir,
			mode: OutputMode::FILE
		);

		$sitemap->news(['name' => 'traversable-news.xml', 'videos' => true, 'localized' => true], Utils::generator(function()
		{
				for ($i = 0; $i < 50; $i++)
				{
					$item = new SitemapUrl(
						url: "/article-موتسيبي-{$i}?param=value&lang=ar",
						lastmod: time(),
						priority: 0.9,
						changefreq: 'daily',
						news: [
							'title' => "News Title موتسيبي > Article {$i}",
							'genres' => 'PressRelease',
							'name' => 'ExampleNews',
							'language' => 'ar',
							'publication_date' => date('c'),
							'keywords' => 'keyword1, keyword2'
						]
					);

					yield $item->alternate("/fr/article-موتسيبي-{$i}?param=value&lang=fr", "fr")
						->alternate("/en/article-موتسيبي-{$i}?param=value&lang=en", "en")
						->video("Video Title {$i}", [
							'thumbnail' => "/thumb-{$i}.jpg?size=large&quality=high",
							'description' => "Video description with special chars <>&",
							'content_loc' => "/videos/video-{$i}.mp4?quality=hd"
						])
						->video("Second Video {$i}", [
							'thumbnail' => "/thumb2-موتسيبي-{$i}.jpg",
							'description' => 'Second video description',
							'player_loc' => [
								'value' => "/player/{$i}",
								'attrs' => ['allow_embed' => 'yes', 'autoplay' => 'ap=1']
							],
							'view_count' => 1000 + $i,
							'restriction' => [
								'attrs' => ['relationship' => 'allow'],
								'value' => 'US CA GB'
							]
						]);
				}
			})
		);

		$sitemap->links(['name' => 'traversable-links.xml', 'images' => true], Utils::generator(function()
		{
				for ($i = 0; $i < 30; $i++)
				{
					yield (new SitemapUrl(
						url: "/page-{$i}",
						lastmod: time() - ($i * 86400),
						priority: 0.5 + ($i * 0.01),
						changefreq: 'weekly'
					))->image("/images/img-{$i}.jpg", ['title' => "Image {$i}", 'caption' => "Caption {$i}"]);
				}

				for ($i=0; $i < 10; $i++)
				{
					yield "str-page-{$i}";
				}
			})
		);

		$this->assertTrue($sitemap->render());

		// news sitemap
		$this->assertFileExists($this->testDir . '/traversable-news.xml');
		$newsContent = file_get_contents($this->testDir . '/traversable-news.xml');

		$this->assertStringContainsString('xmlns:news=', $newsContent);
		$this->assertStringContainsString('xmlns:video=', $newsContent);
		$this->assertStringContainsString('xmlns:xhtml=', $newsContent);
		$this->assertStringContainsString('/article-%D9%85%D9%88%D8%AA%D8%B3%D9%8A%D8%A8%D9%8A-0', $newsContent);
		$this->assertStringContainsString('https://example.com/article-%D9%85%D9%88%D8%AA%D8%B3%D9%8A%D8%A8%D9%8A-49', $newsContent);
		$this->assertStringContainsString('<news:title>', $newsContent);
		$this->assertStringContainsString('موتسيبي', $newsContent);
		$this->assertStringContainsString('<video:title>Video Title', $newsContent);
		$this->assertStringContainsString('<video:title>Second Video', $newsContent);
		$this->assertStringContainsString('allow_embed="yes"', $newsContent);
		$this->assertStringContainsString('hreflang="fr"', $newsContent);
		$this->assertStringContainsString('hreflang="en"', $newsContent);
		$this->assertStringContainsString('<![CDATA[Video description with special chars <>&]]>', $newsContent);



		// links sitemap
		$this->assertFileExists($this->testDir . '/traversable-links.xml');
		$linksContent = file_get_contents($this->testDir . '/traversable-links.xml');

		$this->assertStringContainsString('xmlns:image=', $linksContent);
		$this->assertStringContainsString('/page-0', $linksContent);
		$this->assertStringContainsString('/page-29', $linksContent);
		$this->assertStringContainsString('<image:title>Image', $linksContent);
		$this->assertStringContainsString('<image:caption>Caption', $linksContent);

		// yield "str-page-{$i}";
		$this->assertStringContainsString('/str-page', $linksContent);
		$this->assertStringContainsString('https://example.com/str-page-9', $linksContent);

		// verify index
		$this->assertFileExists($this->testDir . '/sitemap.xml');
		$indexContent = file_get_contents($this->testDir . '/sitemap.xml');

		$this->assertStringContainsString('traversable-news.xml', $indexContent);
		$this->assertStringContainsString('traversable-links.xml', $indexContent);
	}
}
