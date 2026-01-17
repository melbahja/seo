<?php
namespace Melbahja\Seo;

use Melbahja\Seo\Interfaces\SchemaInterface;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class Schema implements SchemaInterface
{

	protected $things = [];

	/**
	 * @param string               $type
	 * @param array                $data
	 * @param SchemaInterface|null $parent
	 * @param SchemaInterface|null $root
	 */
	public function __construct(SchemaInterface ...$things)
	{
		$this->things = $things;
	}


	/**
	 * Add schema item to the graph.
	 *
	 * @param Thing $thing
	 */
	public function add(Thing $thing): SchemaInterface
	{
		$this->things[] = $thing;
		return $this;
	}

	/**
	 * Get schema items
	 *
	 * @return Thing[]
	 */
	public function all(): array
	{
		return $this->things;
	}

	/**
	 * Get data as array
	 *
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		if (count($this->things) === 1) {
			return $this->things[0]->jsonSerialize();
		}

		return [
			'@context' => 'https://schema.org',
			'@graph'   => $this->things,
		];
	}


	/**
	 * Serialize root schema
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return '<script type="application/ld+json">'. json_encode($this->jsonSerialize()) .'</script>';
	}

}
