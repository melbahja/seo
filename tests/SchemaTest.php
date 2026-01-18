<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;

use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;
use Melbahja\Seo\Schema\Product;
use Melbahja\Seo\Schema\CreativeWork\WebPage;


class SchemaTest extends TestCase
{

	public function testSchemaResults()
	{
		$schema = new Schema(
			new Thing(type: 'Organization', props: [
				'url'          => 'https://example.com',
				'logo'         => 'https://example.com/logo.png',
				'name'         => 'Example Org',
				'contactPoint' => new Thing(type: 'ContactPoint', props: [
					'telephone' => '+1-000-555-1212',
					'contactType' => 'customer service'
				])
			])
		);

		$this->assertEquals('{"@type":"Organization","@context":"https:\/\/schema.org","url":"https:\/\/example.com","logo":"https:\/\/example.com\/logo.png","name":"Example Org","contactPoint":{"@type":"ContactPoint","@context":"https:\/\/schema.org","telephone":"+1-000-555-1212","contactType":"customer service"}}', json_encode($schema));

		$product = new Product();
		$product->name  = "Foo Bar";
		$product->sku   = "sk12";

		// in addition to __set() y can use __call()
		$product->image("/image.jpeg")
				->description("testing");

		$product->offers(new Thing(type: 'Offer', props:
			[
				'availability' => 'https://schema.org/InStock',
				'priceCurrency' => 'USD',
				"price" => "119.99",
				'url' => 'https://gool.com',
			])
		);

		$webpage = new WebPage([
			'@id' => "https://example.com/product/#webpage",
			'url' => "https://example.com/product",
			'name' => 'Foo Bar',
		]);


		$schema = new Schema(
			$product,
			$webpage
		);


		$this->assertEquals('<script type="application/ld+json">{"@context":"https:\/\/schema.org","@graph":[{"@type":"Product","@context":"https:\/\/schema.org","name":"Foo Bar","sku":"sk12","image":"\/image.jpeg","description":"testing","offers":{"@type":"Offer","@context":"https:\/\/schema.org","availability":"https:\/\/schema.org\/InStock","priceCurrency":"USD","price":"119.99","url":"https:\/\/gool.com"}},{"@type":"WebPage","@context":"https:\/\/schema.org","@id":"https:\/\/example.com\/product\/#webpage","url":"https:\/\/example.com\/product","name":"Foo Bar"}]}</script>', (string) $schema);

	}
}
