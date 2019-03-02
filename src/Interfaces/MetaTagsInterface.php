<?php
namespace Melbahja\Seo\Interfaces;

/**
 * @package Melbahja\Seo
 * @since v1.0
 * @see https://git.io/phpseo 
 * @license MIT
 * @copyright 2019 Mohamed Elabhja 
 */
interface MetaTagsInterface extends SeoInterface
{
	public function __construct(array $tags = []);

	public function meta(string $name, string $value): MetaTagsInterface;

	public function push(string $name, array $attrs): MetaTagsInterface;

	public function build(array $tags): string;

	public function __toString(): string;
}
