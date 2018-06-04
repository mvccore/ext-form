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

class MinOptions extends ValueInOptions
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxOptions;

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
		if ($this->minOptions == NULL && $field->GetMinOptions() !== NULL && $fieldImplementsMinMax) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMinOptions($this->minOptions);
		}
		if (!$this->minOptions && $fieldImplementsMinMax && $field->GetMinOptions() !== NULL) {
			// if validator is added as string - get min property from field:
			$this->minOptions = $field->GetMinOptions();
		}
		return $this;
	}
	
	/**
	 * Validate raw user input with minimal options count check.
  * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedArr = [];
		if (is_array($rawSubmittedValue)) {
			$rawSubmittedArr = $rawSubmittedValue;
		} else if (is_string($rawSubmittedValue) && mb_strlen($rawSubmittedValue) > 0) {
			$rawSubmittedArr = [$rawSubmittedValue];
		}
		$submittedArrCount = count($rawSubmittedArr);
		// check if there is enough options checked
		if ($this->minOptions !== NULL && $this->minOptions > 0 && $submittedArrCount < $this->minOptions) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::CHOOSE_MIN_OPTS)
			);
		}
		return $rawSubmittedArr;
	}
}
