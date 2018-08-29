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

class MinMaxLength 
	extends		\MvcCore\Ext\Forms\Validator 
	implements	\MvcCore\Ext\Forms\Fields\IMinMaxLength
{
	use \MvcCore\Ext\Forms\Field\Props\MinMaxLength;
	
	/**
	 * Valid email address error message index.
	 * @var int
	 */
	const ERROR_MIN_LENGTH = 0;
	const ERROR_MAX_LENGTH = 1;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_MIN_LENGTH	=> "Field '{0}' requires at least {1} characters.",
		self::ERROR_MAX_LENGTH	=> "Field '{0}' requires no more than {1} characters.",
	];

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField $field 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		parent::SetField($field);
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMinMaxLength) 
			$this->throwNewInvalidArgumentException(
				"Field doesn't implement interface `\\MvcCore\\Ext\\Forms\\Fields\\IMinMaxLength`."
			);
		
		if ($this->minLength === NULL && $field->GetMinLength() !== NULL) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMinLength($this->minLength);
		} else if ($this->minLength === NULL && $field->GetMinLength() !== NULL) {
			// if validator is added as string - get min property from field:
			$this->minLength = $field->GetMinLength();
		}

		if ($this->maxLength === NULL && $field->GetMaxLength() !== NULL) {
			// if this validator is added into field as instance - check field if it has max attribute defined:
			$field->SetMaxLength($this->maxLength);
		} else if ($this->maxLength === NULL && $field->GetMaxLength() !== NULL) {
			// if validator is added as string - get max property from field:
			$this->maxLength = $field->GetMaxLength();
		}

		return $this;
	}

	/**
	 * Validate raw user input with maximum string length check.
	 * @param string|array $submitValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = trim((string) $rawSubmittedValue);
		$resultLength = mb_strlen($result);
		if (
			$this->minLength !== NULL && 
			$this->minLength > 0 && 
			$resultLength < $this->minLength
		) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_MIN_LENGTH)
			);
		}
		if (
			$this->maxLength !== NULL && 
			$this->maxLength > 0 &&
			$resultLength > $this->maxLength
		) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_MAX_LENGTH)
			);
		}
		return $result;
	}
}
