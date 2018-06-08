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
 * Responsibility - Validate raw user input. Parse float value if possible by `Intl` extension 
					or try to determinate floating point automaticly and return `float` or `NULL`.
 */
class Number 
	extends		\MvcCore\Ext\Forms\Validator
	implements	\MvcCore\Ext\Forms\Fields\IMinMaxStep,
				\MvcCore\Ext\Forms\Fields\IPattern
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStepNumbers;

	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_NUMBER = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_NUMBER	=> "Field '{0}' requires a valid number.",
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
		
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMinMaxStep)
			$this->throwNewInvalidArgumentException(
				"Field `".$field->GetName()."` doesn't implement interface `\\MvcCore\\Ext\\Forms\\Fields\\IMinMaxStep`."
			);
		
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\INumber)
			$this->throwNewInvalidArgumentException(
				"Field `".$field->GetName()."` doesn't implement interface `\\MvcCore\\Ext\\Forms\\Fields\\INumber`."
			);

		if ($this->min !== NULL && $field->GetMin() === NULL) {
			$field->SetMin($this->min);
		} else if ($this->min === NULL && $field->GetMin() !== NULL) {
			$this->min = $field->GetMin();
		}
		if ($this->max !== NULL && $field->GetMax() === NULL) {
			$field->SetMax($this->max);
		} else if ($this->max === NULL && $field->GetMax() !== NULL) {
			$this->max = $field->GetMax();
		}
		if ($this->step !== NULL && $field->GetStep() === NULL) {
			$field->SetStep($this->step);
		} else if ($this->step === NULL && $field->GetStep() !== NULL) {
			$this->step = $field->GetStep();
		}

		return $this;
	}
	
	/**
	 * Validate raw user input. Parse float value if possible by `Intl` extension 
	 * or try to determinate floating point automaticly and return `float` or `NULL`.
	 * @param string|array			$submitValue Raw user input.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = $this->field->ParseFloat((string)$rawSubmittedValue);
		if ($result === NULL) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_NUMBER)	
			);
			return NULL;
		}
		if (
			$this->min !== NULL && $this->max !== NULL &&
			$this->min > 0 && $this->max > 0 &&
			($result < $this->min || $result > $this->max)
		) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::RANGE),
				[$this->min, $this->max]
			);
		} else if ($this->min !== NULL && $this->min > 0 && $result < $this->min) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::GREATER),
				[$this->min]
			);
		} else if ($this->max !== NULL && $this->max > 0 && $result > $this->max) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::LOWER),
				[$this->max]
			);
		}
		if ($this->step !== NULL && $this->step !== 0) {
			$dividingResultFloat = floatval($result) / $this->step;
			$dividingResultInt = floatval(intval($dividingResultFloat));
			if ($dividingResultFloat !== $dividingResultInt) 
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::DIVISIBLE),
					[$this->step]
				);
		}
		return $result;
	}

}
