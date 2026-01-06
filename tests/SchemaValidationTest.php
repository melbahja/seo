<?php
namespace Tests\Melbahja\Seo;

use PHPUnit\Framework\TestCase;
use Melbahja\Seo\Validation\SchemaValidator;
use Melbahja\Seo\Schema\Place\LocalBusiness;

class SchemaValidationTest extends TestCase
{
	public function testLocalBusinessValidatorWithValidData()
	{
		// Test with valid LocalBusiness data
		$biz = new LocalBusiness([
			'name' => 'Casablanca Cafe',
			'address' => [
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



		$errors = SchemaValidator::validate('LocalBusiness', $biz->jsonSerialize());
		$this->assertNull($errors, 'LocalBusiness validation should pass with valid data');
	}

	public function testLocalBusinessValidatorWithMissingRequiredFields()
	{
		// Test with missing required fields
		$data = [
			'url' => 'https://example.com',
			'telephone' => '+212524111111'
			// Missing required 'name' and 'address'
		];

		$errors = SchemaValidator::validate('LocalBusiness', $data);
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

		$errors = SchemaValidator::validate('LocalBusiness', $data);
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

		$errors = SchemaValidator::validate('LocalBusiness', $data);
		$this->assertNull($errors, 'Should pass with valid nested address array');
	}
}
