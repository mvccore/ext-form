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

class		Email 
extends		\MvcCore\Ext\Forms\Validator
implements	\MvcCore\Ext\Forms\Fields\IMultiple
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
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * Check if given field implements `\MvcCore\Ext\Forms\Fields\IAccept`
	 * and `\MvcCore\Ext\Forms\Fields\IMultiple`.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMultiple) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `Email` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IMultiple`.'
			);
		
		if ($this->multiple !== NULL && $field->GetMultiple() === NULL) {
			// if this validator is added into field as instance - check field if it has multiple attribute defined:
			$field->SetMultiple($this->multiple);
		} else if ($this->multiple === NULL && $field->GetMultiple() !== NULL) {
			// if validator is added as string - get multiple property from field:
			$this->multiple = $field->GetMultiple();
		}
		
		return parent::SetField($field);
	}

	/**
	 * Validate URI string by PHP `filter_var($rawSubmittedValue, FILTER_VALIDATE_URL);`.
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|\string[]|NULL Safe submitted string value or array of string for `multiple` attribute defined or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		if ($rawSubmittedValue === '') 
			return NULL;
		if ($this->multiple) {
			$result = [];
			$rawValues = explode(',', $rawSubmittedValue);
		} else {
			$result = NULL;
			$rawValues = [$rawSubmittedValue];
		}
		$errorReported = FALSE;
		foreach ($rawValues as $rawValue) {
			$safeValue = filter_var($rawValue, FILTER_VALIDATE_EMAIL);
			if ($safeValue !== FALSE) {
				if ($this->multiple) {
					$result[] = $safeValue;
				} else {
					$result = $safeValue;
				}
			} else {
				if (!$errorReported) {
					$errorReported = TRUE;
					$this->field->AddValidationError(
						static::GetErrorMessage(self::ERROR_EMAIL)
					);
				}
			}
		}
		return $result;
	}
}
