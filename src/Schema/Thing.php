<?php
namespace Melbahja\Seo\Schema;

use Melbahja\Seo\Interfaces\SchemaInterface;


/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @see https://schema.org/Thing
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class Thing implements SchemaInterface
{

	public function __construct(

		/**
		 * @param array $props Schema properties as key-value pairs
		 */
		protected array $props = [],

		/**
		 * @param string|array|null $type Schema type(s), defaults to class name if null
		 */
		protected string|array|null $type = null,

		/**
		 * @param string|null $id The @id identifier for this object
		 */
		protected ?string $id = null,

		/**
		 * @param string $context The @context URL
		 */
		protected string $context = "https://schema.org"
	) {

		if ($this->id !== null) {
			$this->props['@id']  = $this->id;
		}

		if (empty($this->type)) {
			$parts = explode("\\", static::class);
			$this->type = end($parts);
		}
	}

	/**
	 * Get a prop value by name
	 *
	 * @param string $name
	 * @return mixed The prop value or null if not set
	 */
	public function __get(string $name)
	{
		return $this->props[$name] ?? null;
	}

	/**
	 * Set a prop value by name
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set(string $name, $value)
	{
		$this->props[$name] = $value;
	}

	/**
	 * Dynamically set props via method calls with chainable syntax
	 *
	 * @param string $name
	 * @param array $args single value or array or Thing object
	 * @return self
	 */
	public function __call(string $name, array $args): self
	{
		$this->props[$name] = count($args) === 1 ? $args[0] : $args;
		return $this;
	}

	/**
	 * Serialize to JSON-LD format
	 *
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return array_merge(['@type' => $this->type, '@context' => $this->context], $this->props);
	}

	/**
	 * Render as JSON-LD script tag for HTML output
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return '<script type="application/ld+json">'. json_encode($this) . '</script>';
	}
}
