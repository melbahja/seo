<?php
namespace Melbahja\Seo\Validation;

use \Stringable;

/**
 * Simple robots.txt rules validator
 *
 * @package Melbahja\Seo
 * @see https://git.io/phpseo
 * @license MIT
 * @copyright Mohamed Elbahja
 */
class RobotsValidator
{
	/**
	 * Validate robots.txt content
	 *
	 * @param string|Stringable $rules robots.txt file content
	 * @return array|null Array of errors or null if valid
	 */
	public static function validate(string|Stringable $rules): ?array
	{
		if ($rules instanceof Stringable) {
			$rules = (string) $rules;
		}

		// Empty it's okey search engines don't consider this
		// as a error or a issue, means they can do whatever they want
		if (empty(trim($rules))) {
			return null;
		}

		$errors = [];
		$lines  = explode(PHP_EOL, str_replace(["\r\n", "\n", "\n\r"], PHP_EOL, $rules));
		$currentAgent = null;
		$hasUserAgent = false;

		foreach ($lines as $lineNum => $line)
		{
			$line = trim($line);
			$realLine = $lineNum + 1;

			// Skip empty lines and comments
			if ($line === '' || str_starts_with($line, '#')) {
				continue;
			}

			// Split by first colon
			$parts = explode(':', $line, 2);

			if (count($parts) !== 2) {
				$errors[] = "Line $realLine: Invalid format, missing colon";
				continue;
			}

			$direc = trim(strtolower($parts[0]));
			$value = trim($parts[1]);

			switch ($direc)
			{
				case 'user-agent':
					if (empty($value)) {
						$errors[] = "Line $realLine: User-agent cannot be empty";
					}

					$currentAgent = $value;
					$hasUserAgent = true;
					break;

				case 'disallow':
				case 'allow':
					if ($currentAgent === null) {
						$errors[] = "Line $realLine: $direc must come after User-agent";
					}
					if ($value !== '' && !str_starts_with($value, '/')) {
						$errors[] = "Line $realLine: Path must start with / or be empty";
					}
					break;

				case 'crawl-delay':
					if ($currentAgent === null) {
						$errors[] = "Line $realLine: Crawl-delay must come after User-agent";
					}
					if (!is_numeric($value) || $value < 0) {
						$errors[] = "Line $realLine: Crawl-delay must be non-negative number";
					}
					break;

				case 'sitemap':
					if (!filter_var($value, FILTER_VALIDATE_URL)) {
						$errors[] = "Line $realLine: Invalid sitemap URL";
					}
					break;

				default:
					$errors[] = "Line $realLine: Unknown directive '$direc'";
					break;
			}
		}

		if (!$hasUserAgent && !empty($errors)) {
			$errors[] = "No User-agent directive found";
		}

		return empty($errors) ? null : $errors;
	}
}
