<?php
namespace Tests\Melbahja\Seo;

use Melbahja\Seo\Factory;

class MetaTagsTest extends TestCase
{

	public function testMetaTags()
	{
		$metatags = Factory::metaTags(
		[
			'title' => 'My new article',
			'description' => 'My new article about how php is awesome',
			'keywords' => 'php, programming',
			'robots' => 'index, nofollow',
			'author' => 'Mohamed Elbahja'
		]);

		$this->assertEquals('<meta name="title" content="My new article" /><meta name="description" content="My new article about how php is awesome" /><meta name="keywords" content="php, programming" /><meta name="robots" content="index, nofollow" /><meta name="author" content="Mohamed Elbahja" /><meta property="twitter:title" content="My new article" /><meta property="twitter:description" content="My new article about how php is awesome" /><meta property="og:title" content="My new article" /><meta property="og:description" content="My new article about how php is awesome" />', 
			str_replace("\n", '', (string) $metatags)
		);

		$metatags = Factory::metaTags();

		$metatags->meta('author', 'Mohamed Elabhja')
				->meta('title', 'PHP SEO')
				->meta('description', 'This is my description')
				->image('https://avatars3.githubusercontent.com/u/8259014')
				->mobile('https://m.example.com')
				->url('https://examplde.com')
				->shortlink('https://git.io/phpseo')
				->amp('https://apm.example.com');

		$this->assertNotEmpty((string) $metatags);
		
		$this->assertEquals('<meta name="author" content="Mohamed Elabhja" /><meta name="title" content="PHP SEO" /><meta name="description" content="This is my description" /><link rel="alternate" media="only screen and (max-width: 640px)" href="https://m.example.com" /><link rel="canonical" href="https://examplde.com" /><link rel="shortlink" href="https://git.io/phpseo" /><link rel="amphtml" href="https://apm.example.com" /><meta property="twitter:title" content="PHP SEO" /><meta property="twitter:description" content="This is my description" /><meta property="twitter:card" content="summary_large_image" /><meta property="twitter:image" content="https://avatars3.githubusercontent.com/u/8259014" /><meta property="twitter:url" content="https://examplde.com" /><meta property="og:title" content="PHP SEO" /><meta property="og:description" content="This is my description" /><meta property="og:image" content="https://avatars3.githubusercontent.com/u/8259014" /><meta property="og:url" content="https://examplde.com" />', 
			str_replace("\n", '', (string)$metatags)
		);

	}
}
