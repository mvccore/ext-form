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

class Number extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Validate numeric raw user input. Parse numeric value by locale conventions
	 * and check minimum, maximum, step and pattern if necessary.
	 * @param string|array			$submitValue Raw user input.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = $this->getNumericValue($rawSubmittedValue);
		if ($result === NULL) {
			$this->field->AddValidationError(
				$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::NUMBER)	
			);
			return NULL;
		}
		if ($this->field instanceof \MvcCore\Ext\Forms\Field\Attrs\MinMaxStep) {
			$min = $this->field->GetMin();
			$max = $this->field->GetMax();
			$step = $this->field->GetStep();
			if ($min !== NULL && $max !== 0 && ($result < $min || $result > $max)) {
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::RANGE),
					array($min, $max)
				);
			} else if ($min !== NULL && $result < $min) {
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::GREATER),
					array($min)
				);
			} else if ($max !== NULL && $result > $max) {
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::LOWER),
					array($max)
				);
			}
			if ($step !== NULL && $step !== 0) {
				$dividingResultFloat = floatval($result) / $step;
				$dividingResultInt = floatval(intval($dividingResultFloat));
				if ($dividingResultFloat !== $dividingResultInt) 
					$this->field->AddValidationError(
						$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::DIVISIBLE),
						array($step)
					);
			}
		}
		if ($this->field instanceof \MvcCore\Ext\Forms\Field\Attrs\Pattern) {
			$pattern = $this->field->GetPattern();
			if ($pattern && !$this->field->HasValidator('Pattern')) {
				$patternValidator = $this->form->GetValidator('Pattern');
				$patternValidator->SetField($this->field);
				$patternResult = $patternValidator->Validate($rawSubmittedValue);
				if ($patternResult === NULL) $result = NULL;
			}
		}
		return $result;
	}

	/**
	 * @param string|array $rawSubmittedValue
	 * @return Integer|float|NULL
	 */
	protected function getNumericValue ($rawSubmittedValue) {
		$result = NULL;
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		$submittedValueToParse = preg_replace("#[^0-9,\.]#", '', $rawSubmittedValue);
		$noSeparatorsValue = str_replace(array(',', '.'), '', $submittedValueToParse);
		if (strlen($noSeparatorsValue) === 0) return NULL;
		$dot = strpos($submittedValueToParse, '.') !== FALSE;
		$comma = strpos($submittedValueToParse, ',') !== FALSE;
		$lc = (object) localeconv();
		$thousandsSeparator = $lc->thousands_sep;
		$decimalPoint = $lc->decimal_point;
		if ($dot && !$comma) {
			if ($thousandsSeparator == '.') {
				$result = intval(str_replace('.','',$submittedValueToParse));
			} else if ($decimalPoint == '.') {
				$result = floatval($submittedValueToParse);
			}
		} else if (!$dot && $comma) {
			if ($thousandsSeparator == ',') {
				$result = intval(str_replace(',','',$submittedValueToParse));
			} else if ($decimalPoint == ',') {
				$result = floatval(str_replace(',','.',$submittedValueToParse));
			}
		} else if ($dot && $comma) {
			if ($thousandsSeparator == ',' && $decimalPoint == '.') {
				$result = floatval(str_replace(',','',$submittedValueToParse));
			} else if ($thousandsSeparator == '.' && $decimalPoint == ',') {
				$result = floatval(str_replace(array('.',','), array('','.'),$submittedValueToParse));
			} else if ($thousandsSeparator == '.' && $decimalPoint == '.') {
				$lastDotPos = strrpos($submittedValueToParse, '.');
				$result = floatval(str_replace('.','',substr($submittedValueToParse,0,$lastDotPos)).'.'.substr($submittedValueToParse,$lastDotPos+1));
			} else if ($thousandsSeparator == ',' && $decimalPoint == ',') {
				$lastCommaPos = strrpos($submittedValueToParse, ',');
				$result = floatval(str_replace('.','',substr($submittedValueToParse,0,$lastCommaPos)).'.'.substr($submittedValueToParse,$lastCommaPos+1));
			}
		} else if (!$dot && !$comma) {
			$result = intval($submittedValueToParse);
		}
		$firstRawChar = mb_substr($rawSubmittedValue, 0, 1);
		if ($firstRawChar === '-' || $firstRawChar === $lc->negative_sign)
			$result *= -1;
		return $result;
	}
}
