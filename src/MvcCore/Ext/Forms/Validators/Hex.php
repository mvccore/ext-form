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

/**
 * Responsibility: Validate if submitted characters representing a hexadecimal digit.
 * @see http://php.net/manual/en/function.ctype-xdigit.php
 */
class Hex extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_HEX = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_HEX	=> "Field '{0}' requires hexadecimal value ([a-fA-F0-9]).",
	];

	/**
	 * Validate raw user input for character(s) representing a hexadecimal digit.
	 * @see http://php.net/manual/en/function.ctype-xdigit.php
	 * @param string|array			$rawSubmittedValue
	 * @return string|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedValue = (string) $rawSubmittedValue;
		if (!ctype_xdigit($rawSubmittedValue)) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_HEX)
			);
			return NULL;
		}
		return $rawSubmittedValue;
	}
}
