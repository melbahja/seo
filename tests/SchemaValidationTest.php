<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\Validation\SchemaValidator;
use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;
use Melbahja\Seo\Schema\Intangible;
use Melbahja\Seo\Schema\CreativeWork;
use Melbahja\Seo\Schema\Organization;
use Melbahja\Seo\Schema\Place\LocalBusiness;

class SchemaValidationTest extends TestCase
{
	public function testLocalBusinessValidatorWithValidData()
	{
		// Test with valid LocalBusiness data
		$biz = new LocalBusiness([
			'name' => 'Casablanca Cafe',
			'address' => [
				'@type' => 'PostalAddress',
				'streetAddress' => '123 Avenue Mohammed V',
				'addressLocality' => 'Casablanca',
				'addressRegion' => 'Casablanca-Settat',
				'postalCode' => '20000',
				'addressCountry' => 'MA'
			],
			'url' => 'https://example.com',
			'telephone' => '+212524111111',
			'priceRange' => '$$',
			'servesCuisine' => ['Moroccan', 'Mediterranean']
		]);


		$errors = SchemaValidator::validate($biz);
		$this->assertNull($errors, 'LocalBusiness validation should pass with valid data');
	}

	public function testSchemaWithMultipleTypes()
	{
		$data = [
			'@type' => ['Article', 'NewsArticle'],
			'headline' => 'Breaking News',
			'datePublished' => '2024-01-15'
		];

		$errors = SchemaValidator::validateType(['Article', 'NewsArticle'], $data);
		$this->assertNull($errors);

		$schema = new CreativeWork(type: ['Article', 'NewsArticle'], props: [
			'headline' => 'Breaking News',
			'datePublished' => '2024-01-15'
		]);

		$errors = SchemaValidator::validate($schema);
		$this->assertNull($errors);
	}

	public function testThingWithWrongType()
	{
		$data = [
			'name' => 'Test',
			'address' => [
				'@type' => 'Rating', // Wrong! Should be PostalAddress
				'streetAddress' => '123 Main St'
			],
			'url' => 'https://example.com',
			'telephone' => '+212524111111'
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString("expected @type 'PostalAddress'", implode(' ', $errors));
	}

	public function testLocalBusinessWithInvalidOpeningHours()
	{
		$data = [
			'name' => 'Test Restaurant',
			'address' => [
				'@type' => 'PostalAddress',
				'streetAddress' => '123 Main St',
				'addressLocality' => 'Casablanca',
				'addressRegion' => 'Casablanca-Settat',
				'postalCode' => '20000',
				'addressCountry' => 'MA'
			],
			'url' => 'https://example.com',
			'telephone' => '+212524111111',
			'openingHoursSpecification' => [
				[
					'@type' => 'OpeningHoursSpecification',
					'dayOfWeek' => 'Monday',
					'opens' => '09:00',
					'closes' => '18:00'
				],
				[
					'@type' => 'OpeningHoursSpecification',
					'dayOfWeek' => 123, // Wrong! Should be string
					'opens' => 900, // Wrong! Should be string
					'closes' => true // Wrong! Should be string
				],
				'not an array' // Wrong! Should be array/object
			]
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString('openingHoursSpecification', implode(' ', $errors));
	}

	public function testThingObjectWithCorrectType()
	{
		$rating = new Thing(type: 'Rating', props: ['ratingValue' => 4.5]);

		$review = [
			'@type' => 'Review',
			'reviewRating' => $rating, // Thing object instead of Rating class
			'reviewBody' => 'Great!',
			'author' => ['@id' => 'https://example.com/authors/mohamed'], // ref only
		];

		// Should pass since Thing has correct @type
		$errors = SchemaValidator::validate($review);
		$this->assertNull($errors);
	}

	public function testNumericTypeFlexibility()
	{
		$rating1 = new Thing(type: 'Rating', props: ['ratingValue' => 5]);      // int
		$rating2 = new Thing(type: 'Rating', props: ['ratingValue' => 5.0]);    // float
		$rating3 = new Thing(type: 'Rating', props: ['ratingValue' => '5']);    // string

		$this->assertNull(SchemaValidator::validate($rating1));
		$this->assertNull(SchemaValidator::validate($rating2));
		$this->assertNull(SchemaValidator::validate($rating3));
	}

	public function testArrayItemsValidation()
	{
		$data = [
			'name' => 'Test Restaurant',
			'url' => 'https://example.com',
			'sameAs' => ['https://reddit.com/example', 123, 'https://github.com/example'], // 123 is invalid
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString('must be a valid URL', implode(' ', $errors));
	}

	public function testDeeplyNestedValidation()
	{
		$data = [
			'@type' => 'Review',
			'author' => [
				'@type' => 'Person',
				'name' => 'John',
				'address' => [
					'@type' => 'PostalAddress',
					'streetAddress' => 123 // Invalid: should be string
				]
			],
			'reviewBody' => 'Great!'
		];

		$errors = SchemaValidator::validate($data);
		$this->assertIsArray($errors);

		$thing = new Thing([
			'@type' => 'Review',
			'author' => [
				'@type' => 'Person',
				'name' => 'John',
				'address' => [
					'@type' => 'PostalAddress',
					'streetAddress' => 123 // Invalid: should be string
				]
			],
			'reviewBody' => 'Great!'
		]);

		$errors = SchemaValidator::validate($thing);
		$this->assertIsArray($errors);
	}

	public function testEmptyStringValues()
	{
		$data = [
			'name' => '   ', // Empty after trim
			'url' => 'https://example.com'
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertContains('name is required', $errors);
	}

	public function testInvalidSchemaStructure()
	{
		$data = ['name' => 'Test']; // No @type, no @graph

		$errors = SchemaValidator::validate($data);
		$this->assertIsArray($errors);
		$this->assertContains('Schema array must have @type property or @graph structure', $errors);
	}

	public function testGraphWithInvalidStaff()
	{
		$data = ['@graph' => 'not an array'];

		$errors = SchemaValidator::validate($data);
		$this->assertIsArray($errors);
		$this->assertContains('@graph must be an array', $errors);

		$data = [
			'@graph' => [
				['name' => 'Test'] // Missing @type
			]
		];

		$errors = SchemaValidator::validate($data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString('missing @type property', implode(' ', $errors));

		 $data = [
			'@graph' => [
				'not an assoc array'
			]
		];

		$errors = SchemaValidator::validate($data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString('must be an array', implode(' ', $errors));


		$data = [
			'name' => 'Test',
			'url' => 'https://example.com',
			'image' => 123 // Should be string|array
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertStringContainsString('must be one of: url|@ImageObject', implode(' ', $errors));
	}

	public function testSchemaValidatorWithIdReferences()
	{
		// Create Thing with @id
		$restaurant = new LocalBusiness([
			'@id' => 'https://example.com/#restaurant',
			'name' => 'Marrakech Palace',
			'address' => [
				'@type' => 'PostalAddress',
				'streetAddress' => '456 Rue de la Koutoubia',
				'addressLocality' => 'Marrakech',
				'addressRegion' => 'Marrakech-Safi',
				'postalCode' => '40000',
				'addressCountry' => 'MA'
			],
			'telephone' => '+212524445566',
			'url' => 'https://example.com/restaurant'
		]);

		// Create Organization with reference to restaurant
		$organization = new Organization([
			'@id' => 'https://example.com/#organization',
			'name' => 'Marrakech Hospitality Group',
			'owns' => ['@id' => 'https://example.com/#restaurant']
		]);

		// Create Review that references the restaurant
		$review = new Thing(type: 'Review', props: [
			'author' => new Thing(type: 'Person', props: ['name' => 'Ahmed Benali']),
			'reviewRating' => new Intangible(type: 'Rating', props: ['ratingValue' => 5]),
			'itemReviewed' => ['@id' => 'https://example.com/#restaurant'],
			'reviewBody' => 'Amazing traditional food!',
			'datePublished' => '2024-01-15'
		]);

		// Create Schema with all three things
		$schema = new Schema($restaurant, $organization, $review);

		// Validate the schema
		$errors = SchemaValidator::validate($schema);
		$this->assertNull($errors, 'Schema with @id references should pass validation');

		// Also test with array format
		$schemaArray = $schema->jsonSerialize();
		$errors = SchemaValidator::validate($schemaArray);
		$this->assertNull($errors, 'Schema array with @graph should pass validation');

		// Verify the structure contains @id references
		$this->assertArrayHasKey('@graph', $schemaArray);
		$this->assertCount(3, $schemaArray['@graph']);
	}

	public function testSchemaValidatorWithGraphStructure()
	{
		// Simulate schema with @graph structure
		$graphData = [
			'@graph' => [
				[
					'@type' => 'LocalBusiness',
					'name' => 'Restaurant',
					'address' => [
						'streetAddress' => '789 Boulevard Pasteur',
						'addressLocality' => 'Tangier',
						'addressRegion' => 'Tanger-Tétouan-Al Hoceïma',
						'postalCode' => '90000',
						'addressCountry' => 'MA'
					],
					'url' => 'https://example.com',
					'telephone' => '+212511111111'
				],
				[
					'@type' => 'Organization',
					'name' => 'Restaurant Group',
					'url' => 'https://example.com/group',
					'sameAs' => ['https://facebook.com/example']
				],
				[
					'@type' => 'WebPage',
					'name' => 'Home Page',
					'url' => 'https://example.com',
					'description' => 'Welcome to our restaurant'
				]
			]
		];

		// Note: The validator doesn't handle @graph structure directly
		// We would validate each entity separately
		$errors = [];

		// Validate each item in @graph
		foreach ($graphData['@graph'] as $entity)
		{
			$type = $entity['@type'];
			$entityErrors = SchemaValidator::validateType($type, $entity);
			if ($entityErrors !== null) {
				foreach ($entityErrors as $error) {
					$errors[] = "{$type}: {$error}";
				}
			}
		}

		$this->assertEmpty($errors, 'All entities in @graph should pass validation');
		$this->assertNull(SchemaValidator::validateType('LocalBusiness', $graphData['@graph'][0]));
		$this->assertNull(SchemaValidator::validateType('Organization', $graphData['@graph'][1]));
		$this->assertNull(SchemaValidator::validateType('WebPage', $graphData['@graph'][2]));
	}

	public function testLocalBusinessValidatorWithMissingRequiredFields()
	{
		// Test with missing required fields
		$data = [
			'url' => 'https://example.com',
			'telephone' => '+212524111111'
			// Missing required 'name' and 'address'
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
		$this->assertContains('name is required', $errors);
		$this->assertContains('address is required', $errors);
	}

	public function testLocalBusinessValidatorWithInvalidTypes()
	{
		// Test with invalid data types
		$data = [
			'name' => 123, // Should be string
			'address' => 'not an array', // Should be array or PostalAddress
			'url' => 'invalid-url', // Invalid URL
			'telephone' => 212524111111, // Should be string
			'priceRange' => 100, // Should be string
			'servesCuisine' => 123, // Should be string or array
			'acceptsReservations' => 'yes' // Should be bool
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertIsArray($errors);
	}

	public function testLocalBusinessValidatorWithValidNestedAddress()
	{
		// Test with valid nested PostalAddress as array
		$data = [
			'name' => 'Marrakech Restaurant',
			'address' => [
				'streetAddress' => '456 Rue de la Koutoubia',
				'addressLocality' => 'Marrakech',
				'addressRegion' => 'Marrakech-Safi',
				'postalCode' => '40000',
				'addressCountry' => 'MA'
			],
			'url' => 'https://example.com',
			'telephone' => '+212524111111'
		];

		$errors = SchemaValidator::validateType('LocalBusiness', $data);
		$this->assertNull($errors, 'Should pass with valid nested address array');
	}
}
