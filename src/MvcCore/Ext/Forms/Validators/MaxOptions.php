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

class MaxOptions extends ValueInOptions
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
		if ($this->maxOptions == NULL && $field->GetMaxOptions() !== NULL && $fieldImplementsMinMax) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMaxOptions($this->maxOptions);
		}
		if (!$this->maxOptions && $fieldImplementsMinMax && $field->GetMaxOptions() !== NULL) {
			// if validator is added as string - get min property from field:
			$this->maxOptions = $field->GetMaxOptions();
		}
		return $this;
	}
	
	/**
	 * Validate raw user input with maximum options count check.
	 * @param string|array $submitValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedArr = array();
		if (is_array($rawSubmittedValue)) {
			$rawSubmittedArr = $rawSubmittedValue;
		} else if (is_string($rawSubmittedValue) && mb_strlen($rawSubmittedValue) > 0) {
			$rawSubmittedArr = array($rawSubmittedValue);
		}
		$submittedArrCount = count($rawSubmittedArr);
		// check if there is not more options checked
		if ($this->maxOptions !== NULL && $this->maxOptions > 0 && $submittedArrCount > $this->maxOptions) {
			$rawSubmittedArr = array_slice($rawSubmittedArr, 0, $this->maxOptions);
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::CHOOSE_MAX_OPTS)
			);
		}
		return $rawSubmittedArr;
	}
}
