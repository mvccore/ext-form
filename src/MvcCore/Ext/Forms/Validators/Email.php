<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Validators;

class Email extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_EMAIL = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_EMAIL	=> "Field '{0}' requires a valid email address.",
	];

	/**
	 * Validate URI string by PHP `filter_var($rawSubmittedValue, FILTER_VALIDATE_URL);`.
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = NULL;
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		if ($rawSubmittedValue === '') 
			return NULL;
		$safeValue = filter_var($rawSubmittedValue, FILTER_VALIDATE_EMAIL);
		if ($safeValue !== FALSE) {
			$result = $safeValue;
		} else {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_EMAIL)
			);
		}
		return $result;
	}
}
