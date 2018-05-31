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

class MaxLength extends \MvcCore\Ext\Forms\Validator
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxLength;

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
		if ($this->maxLength == NULL && $field->GetMaxLength() !== NULL && $fieldImplementsMinMax) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMaxLength($this->maxLength);
		}
		if (!$this->maxLength && $fieldImplementsMinMax && $field->GetMaxLength() !== NULL) {
			// if validator is added as string - get min property from field:
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
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		if ($this->maxLength !== NULL && $this->maxLength > 0) {
			$result = mb_substr($rawSubmittedValue, 0, $this->maxLength);
		} else {
			$result = $rawSubmittedValue;
		}
		if (mb_strlen($result) !== mb_strlen($rawSubmittedValue))
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::MIN_LENGTH)
			);
		return $result;
	}
}
