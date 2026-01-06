<?php
namespace Melbahja\Seo\Schema;

use Melbahja\Seo\Interfaces\SchemaInterface;


/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @see https://schema.org/Thing
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class Thing implements SchemaInterface
{

	protected string|array $type;
	protected array        $props   = [];
	public    ?string      $context = null;


	public function __construct(string $type, array $props = [])
	{
		$this->type  = $type;
		$this->props = $props;
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
		$data = [
			'@type'    => $this->type,
			'@context' => $this->context ?? "https://schema.org",
		];

		return array_merge($this->props, $data);
	}

	public function __toString(): string
	{
		return '<script type="application/ld+json">'. json_encode($this) . '</script>';
	}
}
