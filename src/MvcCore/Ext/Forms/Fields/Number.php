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

namespace MvcCore\Ext\Forms\Fields;

class Number 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IAccessKey, 
				\MvcCore\Ext\Forms\Fields\ITabIndex,
				\MvcCore\Ext\Forms\Fields\INumber,
				\MvcCore\Ext\Forms\Fields\IMinMaxStep,
				\MvcCore\Ext\Forms\Fields\IPattern,
				\MvcCore\Ext\Forms\Fields\IDataList
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;
	use \MvcCore\Ext\Forms\Field\Attrs\TabIndex;
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStepNumbers;
	use \MvcCore\Ext\Forms\Field\Attrs\Pattern;
	use \MvcCore\Ext\Forms\Field\Attrs\DataList;
	use \MvcCore\Ext\Forms\Field\Attrs\AutoComplete;
	use \MvcCore\Ext\Forms\Field\Attrs\PlaceHolder;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	protected $type = 'number';
	
	protected $validators = ['Number' /*,'Pattern'*/];

	/**
	 * Numeric field value is always  stored as float value.
	 * @var float|NULL
	 */
	protected $value = NULL;

	/**
	 * Boolean flag to prefer `Intl` extension parsing if `Intl` installed.
	 * Default is `FALSE`.
	 * @var bool
	 */
	protected $preferIntlParsing = FALSE;

	/**
	 * Get numeric field value as `float`.
	 * @return float|NULL
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * Set numeric field value as `float`.
	 * @param float|NULL $value 
	 * @return \MvcCore\Ext\Forms\Validators\Number
	 */
	public function & SetValue ($value) {
		$this->value = $value;
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
	 * Set `TRUE` to prefer `Intl` extension parsing if `Intl` installed.
	 * @param bool $preferIntlParsing 
	 * @return \MvcCore\Ext\Forms\Validators\Number
	 */
	public function & SetPreferIntlParsing ($preferIntlParsing = TRUE) {
		$this->preferIntlParsing = $preferIntlParsing;
		return $this;
	}

	/**
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		$this->setFormPattern();
		return $this;
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accessKey', 
			'tabIndex',
			'pattern',
			'min', 'max', 'step',
			'list',
			'autoComplete',
			'placeHolder',
		]);
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
		return $this->renderControlWrapper($result);
	}

	/**
	 * Try to parse floating point number from raw user input string.
	 * If `Intl` extension installed and if `Intl` extension parsing prefered, 
	 * try to parse by `Intl` extension integer first, than floating point number.
	 * If not prefered or not installed, try to determinate floating point in 
	 * user input string automaticly and use PHP `floatval()` to parse the result.
	 * If parsing by floatval returns `NULL` and `Intl` extension is installed
	 * but not prefered, try to parse user input by `Intl` extension after it.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	public function ParseFloat ($rawInput) {
		if (!(is_scalar($rawInput) && !is_bool($rawInput))) 
			return NULL;
		if (is_float($rawInput) || is_int($rawInput))
			return floatval($rawInput);
		$intlExtLoaded = extension_loaded('intl');
		$result = NULL;
		if ($this->preferIntlParsing && $intlExtLoaded) {
			if ($intlExtLoaded) 
				$result = $this->parseByIntl($rawInput);
			if ($result !== NULL) return $result;
			return $this->parseByFloatVal($rawInput);
		} else {
			$result = $this->parseByFloatVal($rawInput);
			if ($result !== NULL) return $result;
			if ($intlExtLoaded) 
				$result = $this->parseByIntl($rawInput);
			return $result;
		}
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int` or `float`.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseByIntl ($rawInput) {
		list($formLang, $formLocale) = [$this->form->GetLang(), $this->form->GetLocale()];
		// set default english int parsing behaviour if not configured
		$langAndLocale = $formLang && $formLocale
			? $formLang.'_'.$formLocale
			: 'en_US';
		$intVal = $this->parseIntegerByIntl($rawInput, $langAndLocale);
		if ($intVal !== NULL) 
			return floatval($intVal);
		$floatVal = $this->parseFloatByIntl($rawInput, $langAndLocale);
		if ($floatVal !== NULL) 
			return $floatVal;
		return NULL;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int`.
	 * @param string $rawInput 
	 * @return int|NULL
	 */
	protected function parseIntegerByIntl ($rawInput, $langAndLocale) {
		$formatter = NULL;
		try {
			$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		try {
			$parsedInt = $formatter->parse($rawInput, \NumberFormatter::TYPE_INT64);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawInput);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if (strval($parsedInt) !== $valueFiltered) return NULL;
		return $parsedInt;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `float`.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseFloatByIntl ($rawInput, $langAndLocale) {
		// Need to check if this is scientific formatted string. If not, switch to decimal.
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::SCIENTIFIC);
		try {
			$parsedScient = $formatter->parse($rawInput, \NumberFormatter::TYPE_DOUBLE);
			if (intl_is_failure($formatter->getErrorCode())) 
				$parsedScient = NULL;
		} catch (\IntlException $intlException) {
			$parsedScient = NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawInput);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if ($parsedScient !== NULL && $valueFiltered == strval($parsedScient)) 
			return $parsedScient;
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
		try {
			$parsedDecimal = $formatter->parse($rawInput, \NumberFormatter::TYPE_DOUBLE);
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
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseByFloatVal ($rawInput) {
		$result = NULL;
		$rawInput = trim((string) $rawInput);
		$valueToParse = preg_replace("#[^Ee0-9,\.\-]#", '', $rawInput);
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
