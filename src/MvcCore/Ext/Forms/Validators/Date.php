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

class Date extends \MvcCore\Ext\Forms\Validator
{
	use \MvcCore\Ext\Forms\Field\Attrs\Format;

	protected static $errorMessagesFormatReplacements = [
		'd' => 'dd',
		'j' => 'd',
		'D' => 'Mon-Sun',
		'l' => 'Monday-Sunday',
		'm' => 'mm',
		'n' => 'm',
		'M' => 'Jan-Dec',
		'F' => 'January-December',
		'Y' => 'yyyy',
		'y' => 'yy',
		'a' => 'am',
		'A' => 'pm',
		'g' => '1-12',
		'h' => '01-12',
		'G' => '01-12',
		'H' => '00-23',
		'i' => '00-59',
		's' => '00-59',
		'u' => '0-999999',
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
		$fieldImplementsFormat = $field instanceof \MvcCore\Ext\Forms\Field\Attrs\Format;
		if ($this->format && $fieldImplementsFormat && !$field->GetFormat()) {
			// if this validator is added into field as instance - check field if it has format attribute defined:
			$field->SetPattern($this->pattern);
		} else if (!$this->format && $fieldImplementsFormat && $field->GetFormat()) {
			// if validator is added as string - get format property from field:
			$this->format = $field->GetFormat();
		} else {
			$this->throwNewInvalidArgumentException(
				'No `format` property defined in current validator or in field.'	
			);
		}
		return $this;
	}


	public function Validate ($rawSubmittedValue) {
		$rawSubmittedValue = trim($rawSubmittedValue);
		$safeValue = preg_replace('#[^a-zA-Z0-9\:\.\-\,/ ]#', '', $rawSubmittedValue);

		$dateObj = @\DateTime::createFromFormat($this->format, $safeValue);

		if ($dateObj === FALSE || mb_strlen($safeValue) !== mb_strlen($rawSubmittedValue)) {
			$this->addError($field, Form::$DefaultMessages[Form::DATE], function ($msg, $args) use (& $field) {
				$format = $args->Format;
				foreach (static::$errorMessagesFormatReplacements as $key => $value) {
					$format = str_replace($key, $value, $format);
				}
				$args[] = $format;
				return Core\View::Format($msg, $args);
			});
		} else {
			$this->checkMinMax($field, $safeValue, Form::DATE_TO_LOW, Form::DATE_TO_HIGH);
		}
		return $safeValue;
	}

	protected function checkMinMax (\MvcCore\Ext\Form\Core\Field & $field, $safeValue, $minErrorMsgKey, $maxErrorMsgKey) {
		$minSet = !is_null($field->Min);
		$maxSet = !is_null($field->Max);
		if ($minSet || $maxSet) {
			$date = \DateTime::createFromFormat($field->Format, $safeValue);
			if ($minSet) {
				$minDate = \DateTime::createFromFormat($field->Format, $field->Min);
				if ($date < $minDate) {
					$this->addError(
						$field,
						Form::$DefaultMessages[$minErrorMsgKey],
						function ($msg, $args) use (& $field) {
							$args[] = $field->Min;
							return Core\View::Format($msg, $args);
						}
					);
				}
			}
			if ($maxSet) {
				$maxDate = \DateTime::createFromFormat($field->Format, $field->Max);
				if ($date > $maxDate) {
					$this->addError(
						$field,
						Form::$DefaultMessages[$maxErrorMsgKey],
						function ($msg, $args) use (& $field) {
							$args[] = $field->Max;
							return Core\View::Format($msg, $args);
						}
					);
				}
			}
		}
	}
}
