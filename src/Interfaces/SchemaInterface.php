<?php
namespace Melbahja\Seo\Interfaces;

/**
 * @package Melbahja\Seo
 * @since v1.0
 * @see https://git.io/phpseo 
 * @license MIT
 * @copyright 2019 Mohamed Elabhja 
 */
interface SchemaInterface extends SeoInterface, \JsonSerializable
{

	public function __construct(
		string $type, array $data = [], ?SchemaInterface $parent = null, ?SchemaInterface $root = null
	);

	public function set(string $param, $value): SchemaInterface;

	public function addChild(string $name, array $data = []): SchemaInterface;

	public function toArray(): array;

	public function getParent(): ?SchemaInterface;

	public function getRoot(): ?SchemaInterface;

	public function __toString(): string;

	public function __get(string $name): SchemaInterface;
}
