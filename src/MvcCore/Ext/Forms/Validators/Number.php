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
class Number extends \MvcCore\Ext\Forms\Validator
{
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
	 * Boolean flag to prefer `Intl` extension parsing if `Intl` installed.
	 * Default is `FALSE`.
	 * @var bool
	 */
	protected $preferIntlParsing = FALSE;

	/**
	 * Set `TRUE` to prefer `Intl` extension parsing if `Intl` installed.
	 * @param bool $preferIntlParsing 
	 * @return \MvcCore\Ext\Forms\Validators\Number
	 */
	public function & SetPreferIntlParsing ($preferIntlParsing = TRUE) {
		$this->preferIntlParsing = $preferIntlParsing;
		return $this;
	}

	/**
	 * Get boolean flag about to prefer `Intl` extension parsing if `Intl` installed.
	 * Default is `FALSE`.
	 * @return bool
	 */
	public function GetPreferIntlParsing () {
		return $this->preferIntlParsing;
	}
	
	/**
	 * Validate raw user input. Parse float value if possible by `Intl` extension 
	 * or try to determinate floating point automaticly and return `float` or `NULL`.
	 * @param string|array			$submitValue Raw user input.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = $this->parseFloat((string)$rawSubmittedValue);
		if ($result === NULL) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_NUMBER)	
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
					[$min, $max]
				);
			} else if ($min !== NULL && $result < $min) {
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::GREATER),
					[$min]
				);
			} else if ($max !== NULL && $result > $max) {
				$this->field->AddValidationError(
					$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::LOWER),
					[$max]
				);
			}
			if ($step !== NULL && $step !== 0) {
				$dividingResultFloat = floatval($result) / $step;
				$dividingResultInt = floatval(intval($dividingResultFloat));
				if ($dividingResultFloat !== $dividingResultInt) 
					$this->field->AddValidationError(
						$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::DIVISIBLE),
						[$step]
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
	 * Try to parse floating point number from raw user input string.
	 * If `Intl` extension installed and if `Intl` extension parsing prefered, 
	 * try to parse by `Intl` extension integer first, than floating point number.
	 * If not prefered or not installed, try to determinate floating point in 
	 * user input string automaticly and use PHP `floatval()` to parse the result.
	 * If parsing by floatval returns `NULL` and `Intl` extension is installed
	 * but not prefered, try to parse user input by `Intl` extension after it.
	 * @param string $rawSubmittedValue 
	 * @return float|NULL
	 */
	protected function parseFloat ($rawSubmittedValue) {
		if (!(is_scalar($rawSubmittedValue) && !is_bool($rawSubmittedValue))) 
			return NULL;
		if (is_float($rawSubmittedValue) || is_int($rawSubmittedValue))
			return floatval($rawSubmittedValue);
		$intlExtLoaded = extension_loaded('intl');
		$result = NULL;
		if ($this->preferIntlParsing && $intlExtLoaded) {
			if ($intlExtLoaded) 
				$result = $this->parseByIntl($rawSubmittedValue);
			if ($result !== NULL) return $result;
			return $this->parseByFloatVal($rawSubmittedValue);
		} else {
			$result = $this->parseByFloatVal($rawSubmittedValue);
			if ($result !== NULL) return $result;
			if ($intlExtLoaded) 
				$result = $this->parseByIntl($rawSubmittedValue);
			return $result;
		}
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int` or `float`.
	 * @param string $rawSubmittedValue 
	 * @return float|NULL
	 */
	protected function parseByIntl ($rawSubmittedValue) {
		list($formLang, $formLocale) = [$this->form->GetLang(), $this->form->GetLocale()];
		// set default english int parsing behaviour if not configured
		$langAndLocale = $formLang && $formLocale
			? $formLang.'_'.$formLocale
			: 'en_US';
		$intVal = $this->parseIntegerByIntl($rawSubmittedValue, $langAndLocale);
		if ($intVal !== NULL) 
			return floatval($intVal);
		$floatVal = $this->parseFloatByIntl($rawSubmittedValue, $langAndLocale);
		if ($floatVal !== NULL) 
			return $floatVal;
		return NULL;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int`.
	 * @param string $rawSubmittedValue 
	 * @return int|NULL
	 */
	protected function parseIntegerByIntl ($rawSubmittedValue, $langAndLocale) {
		$formatter = NULL;
		try {
			$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		try {
			$parsedInt = $formatter->parse($rawSubmittedValue, \NumberFormatter::TYPE_INT64);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawSubmittedValue);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if (strval($parsedInt) !== $valueFiltered) return NULL;
		return $parsedInt;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `float`.
	 * @param string $rawSubmittedValue 
	 * @return float|NULL
	 */
	protected function parseFloatByIntl ($rawSubmittedValue, $langAndLocale) {
		// Need to check if this is scientific formatted string. If not, switch to decimal.
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::SCIENTIFIC);
		try {
			$parsedScient = $formatter->parse($rawSubmittedValue, \NumberFormatter::TYPE_DOUBLE);
			if (intl_is_failure($formatter->getErrorCode())) 
				$parsedScient = NULL;
		} catch (\IntlException $intlException) {
			$parsedScient = NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawSubmittedValue);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if ($parsedScient !== NULL && $valueFiltered == strval($parsedScient)) 
			return $parsedScient;
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
		try {
			$parsedDecimal = $formatter->parse($rawSubmittedValue, \NumberFormatter::TYPE_DOUBLE);
			if (intl_is_failure($formatter->getErrorCode())) 
				$parsedDecimal = NULL;
		} catch (\IntlException $intlException) {
			$parsedDecimal = NULL;
		}
		return $parsedDecimal;
	}

	/**
	 * Try to determinate floating point separator if any automaticly
	 * and try to parse user input by `floatval()` PHP function.
	 * @param string $rawSubmittedValue 
	 * @return float|NULL
	 */
	protected function parseByFloatVal ($rawSubmittedValue) {
		$result = NULL;
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		$valueToParse = preg_replace("#[^Ee0-9,\.\-]#", '', $rawSubmittedValue);
		if (strlen($valueToParse) === 0) return NULL;
		$dot = strpos($valueToParse, '.') !== FALSE;
		$comma = strpos($valueToParse, ',') !== FALSE;
		if ($dot && !$comma) {
			$cnt = substr_count($valueToParse, '.');
			if ($cnt == 1) {
				$result = floatval($valueToParse);
			} else {
				$result = floatval(str_replace('.','',$valueToParse));
			}
		} else if (!$dot && $comma) {
			$cnt = substr_count($valueToParse, ',');
			if ($cnt == 1) {
				$result = floatval(str_replace(',','.',$valueToParse));
			} else {
				$result = floatval(str_replace(',','',$valueToParse));
			}
		} else if ($dot && $comma) {
			$dotLastPos = mb_strrpos($valueToParse, '.');
			$commaLastPos = mb_strrpos($valueToParse, ',');
			$dotCount = substr_count($valueToParse, '.');
			$commaCount = substr_count($valueToParse, ',');
			if ($dotLastPos > $commaLastPos && $dotCount == 1) {
				// dot is decimal point separator
				$result = floatval(str_replace(',','',$valueToParse));
			} else if ($commaLastPos > $dotLastPos && $commaCount == 1) {
				// comma is decimal point separator
				$result = floatval(str_replace(['.',','],['','.'],$valueToParse));
			}
		} else if (!$dot && !$comma) {
			$result = floatval($valueToParse);
		}
		return $result;
	}
}
