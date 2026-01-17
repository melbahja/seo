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
		protected array $props             = [],
		protected string|array|null $type  = null,
		protected ?string $id              = null,
		protected ?string $context         = null
	) {

		if ($this->id !== null) {
			$this->props['@id']  = $this->id;
		}

		if (empty($this->type)) {
			$parts = explode("\\", static::class);
			$this->type = end($parts);
		}
	}

	public function __get(string $name)
	{
		return $this->props[$name] ?? null;
	}

	public function __set(string $name, $value)
	{
		$this->props[$name] = $value;
	}

	public function jsonSerialize(): array
	{
		return array_merge($this->props,
		[
			'@type'    => $this->type,
			'@context' => $this->context ?? "https://schema.org",
		]);
	}

	public function __toString(): string
	{
		return '<script type="application/ld+json">'. json_encode($this) . '</script>';
	}
}
