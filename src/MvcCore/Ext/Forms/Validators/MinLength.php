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

class MinLength extends \MvcCore\Ext\Forms\Validator
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxLength;
	
	/**
	 * Valid email address error message index.
	 * @var int
	 */
	const ERROR_MIN_LENGTH = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_MIN_LENGTH	=> "Field '{0}' requires at least {1} characters.",
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
		$fieldImplementsMinMax = $field instanceof \MvcCore\Ext\Forms\Field\Attrs\MinMaxLength;
		if ($this->minLength == NULL && $field->GetMinLength() !== NULL && $fieldImplementsMinMax) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMinLength($this->minLength);
		}
		if (!$this->minLength && $fieldImplementsMinMax && $field->GetMinLength() !== NULL) {
			// if validator is added as string - get min property from field:
			$this->minLength = $field->GetMinLength();
		}
		return $this;
	}

	/**
	 * Validate raw user input with minimal string length check.
  * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = trim((string) $rawSubmittedValue);
		if (
			$this->minLength !== NULL && 
			$this->minLength > 0 && 
			mb_strlen($rawSubmittedValue) < $this->minLength
		) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_MIN_LENGTH)
			);
		}
		return $result;
	}
}
