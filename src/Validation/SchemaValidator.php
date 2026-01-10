<?php
namespace Melbahja\Seo\Validation;

use Melbahja\Seo\Schema;
use Melbahja\Seo\Schema\Thing;

/**
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class SchemaValidator
{

	public static function validate(Schema|Thing|array $schema): ?array
	{
		if ($schema instanceof Thing) {
			$schema = $schema->jsonSerialize();
		} elseif ($schema instanceof Schema) {
			$graph = [];
			foreach ($schema->all() as $thing) {
				$graph[] = $thing->jsonSerialize();
			}
			$schema = ['@graph' => $graph];
		}

		if (is_array($schema)) {

			// single entity
			if (isset($schema['@type'])) {
				return static::validateType($schema['@type'], $schema);
			}

			// @graph structure
			if (isset($schema['@graph'])) {

				$errors = [];
				if (!is_array($schema['@graph'])) {
					return ['@graph must be an array'];
				}

				$idRefs = [];
				foreach ($schema['@graph'] as $k => $node)
				{
					if ($node instanceof Thing) {
						$node = $node->jsonSerialize();
						$schema['@graph'][$k] = $node;
					}

					// sub types refs
					if (isset($node['@id']) && is_string($node['@id']) && count($node) > 1) {
						$idRefs[$node['@id']] = $node;
					}
				}

				foreach ($schema['@graph'] as $index => $item)
				{
					if (!is_array($item)) {
						$errors[] = "@graph[{$index}] must be an array";
						continue;
					}

					// handle @id refs, allowing some that refs ids that are not defined like page refs.
					if (isset($item['@id']) && count($item) === 1) {
						// if (!isset($idRefs[$item['@id']])) {
						// 	$errors[] = "@graph[{$index}].@id references an unknown node: {$item['@id']}";
						// }
						continue;
					}

					// merge props if @id ref exists
					if (isset($item['@id']) && isset($idRefs[$item['@id']])) {
						$item = array_merge($idRefs[$item['@id']], $item);
					}

					if (!isset($item['@type'])) {
						$errors[] = "@graph[{$index}] missing @type property";
						continue;
					}

					if (($itemErrors = static::validateType($item['@type'], $item)) !== null) {

						foreach ($itemErrors as $error)
						{
							$errors[] = "@graph[{$index}].{$error}";
						}
					}
				}

				return empty($errors) ? null : $errors;
			}

			// array without @type or @graph
			return ['Schema array must have @type property or @graph structure'];
		}

		return ['Input must be a Schema, Thing, or array'];
	}

	public static function validateType(string|array $schemaType, array $data): ?array
	{
		// handle one/multiple types like: ['Article', 'NewsArticle']
		$types = is_array($schemaType) ? $schemaType : [$schemaType];

		$rules = [];
		foreach ($types as $type)
		{
			$typeRules = self::loadRules($type);
			$rules = array_merge($rules, $typeRules);
		}

		$errors = [];
		// validate existing rules.
		foreach ($rules as $prop => $rule)
		{
			$value = $data[$prop] ?? null;
			$rule  = is_string($rule) ? ['type' => $rule] : $rule;

			if (!empty($rule['required']) && self::isEmpty($value)) {
				$errors[] = "{$prop} is required";
				continue;
			}

			// skip if not required and empty
			if (empty($rule['required']) && self::isEmpty($value)) {
				continue;
			}

			// validate prop
			if (isset($rule['type']) && ($propErrors = self::validateProp($prop, $value, $rule['type'], $rule)) !== null ) {
				$errors = array_merge($errors, $propErrors);
			}
		}

		// validate nested props/objects that not in rules
		foreach ($data as $prop => $value)
		{

			if (in_array($prop, ['@type', '@context', '@id']) || isset($rules[$prop])) {
				continue;
			}

			// skip @id only refs
			if (is_array($value) && isset($value['@id']) && count($value) === 1) {
				continue;
			}

			// recursively validate
			if (is_array($value) && isset($value['@type'])) {

				if (($nErrors = self::validateType($value['@type'], $value)) !== null) {
					foreach ($nErrors as $error) {
						$errors[] = "{$prop}.{$error}";
					}
				}

			} elseif (is_array($value)) {

				// in case of array of objects
				foreach ($value as $index => $item)
				{
					if (is_array($item) && isset($item['@type'])) {

						if ( ($nErrors = self::validateType($item['@type'], $item)) !== null) {
							foreach ($nErrors as $error)
							{
								$errors[] = "{$prop}[{$index}].{$error}";
							}
						}
					}
				}
			}
		}

		return empty($errors) ? null : $errors;
	}

	private static function validateProp(string $prop, $value, string $type, array $rule): ?array
	{
		$errors = [];

		// union types (string|array|type thing)
		if (str_contains($type, '|')) {

			$typeMatched = false;
			$matchedType = null;
			foreach (explode('|', $type) as $singleType)
			{
				if (self::checkType($value, trim($singleType), $rule) === null) {
					$typeMatched = true;
					$matchedType = trim($singleType);
					break;
				}
			}

			if ($typeMatched === false) {
				return ["{$prop} must be one of: {$type}"];
			}

			// validate single item type from array
			if ($matchedType === 'array' && isset($rule['item_type']) && is_array($value)) {

				foreach ($value as $index => $item)
				{
					if ( ($nErrors = self::checkType($item, $rule['item_type'], [])) !== null) {
						foreach ($nErrors as $error)
						{
							$errors[] = "{$prop}[{$index}] {$error}";
						}
					}
				}
			}

			return empty($errors) ? null : $errors;
		}

		// if no union it's a single type!
		if ( ($nErrors = self::checkType($value, $type, $rule)) !== null) {

			foreach ($nErrors as $error)
			{
				$errors[] = "{$prop}: {$error}";
			}

			return $errors;
		}

		// array items if type is array
		if ($type === 'array' && isset($rule['item_type']) && is_array($value)) {

			foreach ($value as $index => $item)
			{
				if ( ($nErrors = self::checkType($item, $rule['item_type'], [])) !== null) {
					foreach ($nErrors as $error)
					{
						$errors[] = "{$prop}[{$index}] {$error}";
					}
				}
			}
		}

		return empty($errors) ? null : $errors;
	}

	private static function checkType($value, string $type, array $rule): ?array
	{
		$errors = [];

		// @ prefixed types, rule file references
		if (str_starts_with($type, '@')) {

			$ruleName  = substr($type, 1);
			$baseRules = self::loadRules($ruleName);

			// merge rules with inline rules, inline rules > file rules.
			if (isset($rule['rules']) && is_array($rule['rules'])) {
				$baseRules = array_merge($baseRules, $rule['rules']);
			}

			// TODO: maybe here we need just to return null when no rules are defined?
			if (empty($baseRules)) {
				return ["no rules found for @{$ruleName}"];
			}

			// value must be an array or Thing instance
			if ($value instanceof Thing) {

				$value = $value->jsonSerialize();
				if ($value['@type'] !== $ruleName && $value['@type'] !== "Thing") {
					return ["expected @type '{$ruleName}', got '{$value['@type']}'"];
				}

			} elseif (!is_array($value)) {

				return ["must be an array or Thing instance"];
			}

			// skip @id only references
			if (isset($value['@id']) && count($value) === 1) {
				return null;
			}

			// check @type matches
			if (isset($value['@type']) && $value['@type'] !== $ruleName && $ruleName !== 'Thing') {
				return ["expected @type '{$ruleName}', got '{$value['@type']}'"];
			}

			// recursive rules validation
			$errors = array_merge($errors, self::validateType($ruleName, $value) ?? []);

			return empty($errors) ? null : $errors;
		}

		// Built-in types
		switch ($type)
		{
			case 'string':
				if (!is_string($value)) {
					$errors[] = "must be a string";
				}
				break;

			case 'int':
			case 'integer':
				if (!is_int($value) && !is_numeric($value)) {
					$errors[] = "must be an integer";
				}
				break;

			case 'float':
				if (!is_float($value) && !is_int($value) && !is_numeric($value)) {
					$errors[] = "must be a float";
				}
				break;

			case 'bool':
			case 'boolean':
				if (!is_bool($value) && $value != 'true' && $value != 'false') {
					$errors[] = "must be a boolean";
				}
				break;

			case 'array':
				if (!is_array($value)) {
					$errors[] = "must be an array";
				}
				break;

			case 'iso_date':
				if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
					$errors[] = "must be a valid ISO date (YYYY-MM-DD)";
				}
				break;

			case 'url':
				if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
					$errors[] = "must be a valid URL";
				}
				break;

			case 'email':
				if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$errors[] = "must be a valid email";
				}
				break;

			default:

				// class type
				if (!class_exists($type)) {
					$errors[] = "class {$type} does not exist";
					break;
				}

				// handle Thing instances
				if ($value instanceof Thing) {
					$value = $value->jsonSerialize();
				}

				// If value is array, validate against class rules
				if (is_array($value)) {

					if (isset($value['@id']) && count($value) === 1) {
						break;
					}

					$typeName = self::getClassNameFromType($type);

					// check if @type matches, only if it it's not a generic Thing
					if (isset($value['@type']) && $value['@type'] !== $typeName && $value['@type'] !== 'Thing' && $typeName !== 'Thing') {
						$errors[] = "expected @type '{$typeName}', got '{$value['@type']}'";
						break;
					}

					// recursive type validation
					$errors = array_merge($errors, self::validateType($value['@type'] ?? $typeName, $value) ?? []);
					break;
				}

				$errors[] = "must be an instance of {$type} or array representing {$type}";
				break;
		}

		return empty($errors) ? null : $errors;
	}

	private static function getClassNameFromType(string|Thing $type): string
	{
		if (is_object($type)) {
			return static::getClassNameFromType($type::class);
		}

		$parts = explode('\\', $type);
		return end($parts);
	}

	private static function loadRules(string $schemaType): array
	{
		if (!file_exists($ruleFile = __DIR__ . "/SchemaRules/{$schemaType}.php")) {
			return [];
		}

		return include $ruleFile;
	}

	private static function isEmpty($value): bool
	{

		if (is_array($value)) {
			return empty($value);
		} else if (is_string($value)) {
			return trim($value) === '';
		}

		return $value === null;
	}
}
