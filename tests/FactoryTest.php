<?php
namespace Tests\Melbahja\Seo;

use Melbahja\Seo\{
	Factory,
	Exceptions\SeoException,
	Exceptions\SitemapException,
	Interfaces\SeoInterface,
	Interfaces\SchemaInterface,
	Interfaces\MetaTagsInterface,
	Interfaces\SitemapInterface,
	Interfaces\SitemapIndexInterface,
	Interfaces\SitemapBuilderInterface
};

class FactoryTest extends TestCase
{

	public function testFactoryBuildExceptions()
	{
		$this->expectException(SeoException::class);

		Factory::notValid('test');
	}

	public function testFactoryBuildSitemap()
	{
		$this->assertInstanceOf(SeoInterface::class, Factory::sitemap('https://example.com'));
		
		$this->assertInstanceOf(SitemapInterface::class, Factory::sitemap('https://example.com'));

		$this->assertInstanceOf(SitemapIndexInterface::class, Factory::sitemap('https://example.com'));
	}


	public function testFactoryBuildMetaTags()
	{
		$this->assertInstanceOf(SeoInterface::class, Factory::metaTags());

		$this->assertInstanceOf(MetaTagsInterface::class, Factory::metaTags());
	}


	public function testFactorySchema()
	{
		$this->assertInstanceOf(SeoInterface::class, Factory::schema('organization'));

		$this->assertInstanceOf(SchemaInterface::class, Factory::schema('article'));
	}


	public function testFactoryBuildPing()
	{
		$this->assertInstanceOf(SeoInterface::class, Factory::ping());
	}

}
