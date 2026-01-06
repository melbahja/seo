<?php
namespace Melbahja\Seo\Validation;

/**
 * @package Melbahja\Seo
 * @since v2.0
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elabhja
 */
class SchemaValidator
{
	public static function validate(string $schemaType, array $data): ?array
	{
		$rules = self::loadRules($schemaType);
		$errors = [];

		foreach ($rules as $prop => $rule)
		{
			$value = $data[$prop] ?? null;

			if (is_string($rule)) {
				$rule = ['type' => $rule];
			}

			// Check required
			if (!empty($rule['required']) && self::isEmpty($value)) {
				$errors[] = "{$prop} is required";
				continue;
			}

			// Skip if not required and empty
			if (empty($rule['required']) && self::isEmpty($value)) {
				continue;
			}

			// Check type
			if (isset($rule['type'])) {
				$typeErrors = self::validateTypeValue($prop, $value, $rule['type'], $rule);
				if ($typeErrors !== null) {
					$errors = array_merge($errors, $typeErrors);
				}
			}
		}

		return empty($errors) ? null : $errors;
	}

	private static function validateTypeValue(string $prop, $value, string $type, array $rule): ?array
	{
		$errors = [];

		// Handle union types (string|array)
		if (strpos($type, '|') !== false)
		{
			$typeMatched = false;
			foreach (explode('|', $type) as $singleType)
			{
				$singleType = trim($singleType);
				$typeErrors = self::checkType($value, $singleType, $rule);

				if ($typeErrors === null) {
					$typeMatched = true;
					break;
				}
			}

			if (!$typeMatched) {
				return ["{$prop} must be one of: {$type}"];
			}

			return null;
		}

		// Single type check
		$typeErrors = self::checkType($value, $type, $rule);
		if ($typeErrors !== null) {

			foreach ($typeErrors as $error)
			{
				// For nested errors, prepend the property name
				if (strpos($error, '.') !== false) {
					$errors[] = "{$prop}.{$error}";
				} else {
					$errors[] = "{$prop}: {$error}";
				}
			}

			return $errors;
		}

		return null;
	}

	private static function checkType($value, string $type, array $rule): ?array
	{
		$errors = [];

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
				if (!is_int($value)) {
					$errors[] = "must be an integer";
				}
				break;

			case 'float':

				if (!is_float($value)) {
					$errors[] = "must be a float";
				}
				break;

			case 'bool':
			case 'boolean':

				if (!is_bool($value)) {
					$errors[] = "must be a boolean";
				}
				break;

			case 'array':

				if (!is_array($value)) {
					$errors[] = "must be an array";
				} elseif (isset($rule['array_item_type'])) {
					// Check array items if specified
					foreach ($value as $index => $item) {
						$itemErrors = self::checkType($item, $rule['array_item_type'], []);
						if ($itemErrors !== null) {
							foreach ($itemErrors as $error) {
								$errors[] = "[{$index}] {$error}";
							}
						}
					}
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
				// Class type
				if (!class_exists($type)) {
					$errors[] = "class {$type} does not exist";
					break;
				}

				// If value is instance of class
				if ($value instanceof $type) {
					// Check if class has a validate method
					if (method_exists($value, 'validate')) {
						$nestedErrors = $value->validate();
						if ($nestedErrors !== null) {
							$errors = array_merge($errors, $nestedErrors);
						}
					}
					break;
				}

				// If value is array, load and validate against class rules
				if (is_array($value)) {
					$className = self::getClassNameFromType($type);
					$classRules = self::loadRules($className);

					// If no rules for class, fallback to instance check
					if (empty($classRules)) {
						$errors[] = "must be an instance of {$type}";
						break;
					}

					// Recursively validate array against class rules
					$nestedErrors = self::validate($className, $value);
					if ($nestedErrors !== null) {
						$errors = array_merge($errors, $nestedErrors);
					}
					break;
				}

				$errors[] = "must be an instance of {$type} or array representing {$type}";
				break;
		}

		return empty($errors) ? null : $errors;
	}

	private static function getClassNameFromType(string $type): string
	{
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
