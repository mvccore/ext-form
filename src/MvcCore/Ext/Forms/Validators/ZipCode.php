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
 * Responsibility: Validate ZIP code by specific locale rules.
 * @see https://github.com/zendframework/zend-i18n
 * @see https://github.com/zendframework/zend-i18n/blob/master/src/Validator/PostCode.php
 */
class ZipCode extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_NOT_SUPPORTED = 0;
	const ERROR_VALIDATOR_WRONG_FORMAT = 1;
	const ERROR_WRONG_PATTERN = 2;
	const ERROR_INVALID_ZIP = 3;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_NOT_SUPPORTED			=> "Field '{0}' has not supported validation for ZIP code in locale: `{1}`.",
		self::ERROR_VALIDATOR_WRONG_FORMAT	=> "Field '{0}' has ZIP code validator in locale `{1}` in wrong format.",
		self::ERROR_WRONG_PATTERN			=> "Field '{0}' has wrong ZIP code validation pattern: `{1}`.",
		self::ERROR_INVALID_ZIP				=> "Field '{0}' requires a valid zip code.",
	];

	/**
	 * All configured validators as key/value array.
	 * Keys are locale codes and values are regexp match pattern strings or `callable`s.
	 * @var array
	 */
	protected static $validators = [];

	/**
	 * Set specific locale validator for ZIP code.
	 * It could be `regexp match pattern string` without border characters (`#^$/`)
	 * or `callable` accepting first argument to be raw submitted value and 
	 * returning array with success and with safe ZIP code value.
	 * @param string $localeCode Locale code, automaticly converted to upper case.
	 * @param string|callable $regExpMatchOrCallable `Regexp match pattern string` without border characters (`#^$/`) or `callable`.
	 * @return string|callable
	 */
	public static function SetValidator ($localeCode, $regExpMatchOrCallable) {
		if (!static::$validators) static::GetValidators();
		return static::$validators[strtoupper($localeCode)] = $regExpMatchOrCallable;
	}

	/**
	 * Get all preconfigured validators as key/value array.
	 * Keys are locale codes and values are `regexp match pattern string`s or `callable`s.
	 * @return array
	 */
	public static function & GetValidators () {
		if (static::$validators) return static::$validators;
		$a = '\d{3}'; // 3 digits pattern
		$b = '\d{4}'; // 4 digits pattern
		$c = '\d{5}'; // 5 digits pattern
		$d = '\d{6}'; // 6 digits pattern
		static::$validators = [
			'GB' => 'GIR[ ]?0AA|^((AB|AL|B|BA|BB|BD|BH|BL|BN|BR|BS|BT|CA|CB|CF|CH|CM|CO|CR|CT|CV|CW|DA|DD|DE|DG|DH|DL|DN|DT|DY|E|EC|EH|EN|EX|FK|FY|G|GL|GY|GU|HA|HD|HG|HP|HR|HS|HU|HX|IG|IM|IP|IV|JE|KA|KT|KW|KY|L|LA|LD|LE|LL|LN|LS|LU|M|ME|MK|ML|N|NE|NG|NN|NP|NR|NW|OL|OX|PA|PE|PH|PL|PO|PR|RG|RH|RM|S|SA|SE|SG|SK|SL|SM|SN|SO|SP|SR|SS|ST|SW|SY|TA|TD|TF|TN|TQ|TR|TS|TW|UB|W|WA|WC|WD|WF|WN|WR|WS|WV|YO|ZE)(\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}))$|^BFPO[ ]?\d{1,4}',
			'JE' => 'JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'GG' => 'GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'IM' => 'IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'US' => '\d{5}([ \-]\d{4})?',
			'CA' => '[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d',
			'DE' => $c,
			'JP' => '\d{3}-\d{4}',
			'FR' => '(?!(0{2})|(9(6|9))[ ]?\d{3})(\d{2}[ ]?\d{3})',
			'AU' => $b,
			'IT' => $c,
			'CH' => $b,
			'AT' => $b,
			'ES' => $c,
			'NL' => '\d{4}[ ]?[A-Z]{2}',
			'BE' => $b,
			'DK' => $b,
			'SE' => '\d{3}[ ]?\d{2}',
			'NO' => '(?!0000)\d{4}',
			'BR' => '\d{5}[\-]?\d{3}',
			'PT' => '\d{4}([\-]\d{3})?',
			'FI' => $c,
			'AX' => '22\d{3}',
			'KR' => '\d{3}[\-]\d{3}',
			'CN' => $d,
			'TW' => '\d{3}(\d{2})?',
			'SG' => $d,
			'DZ' => $c,
			'AD' => 'AD\d{3}',
			'AR' => '([A-HJ-NP-Z])?\d{4}([A-Z]{3})?',
			'AM' => '(37)?\d{4}',
			'AZ' => $b,
			'BH' => '((1[0-2]|[2-9])\d{2})?',
			'BD' => $b,
			'BB' => '(BB\d{5})?',
			'BY' => $d,
			'BM' => '[A-Z]{2}[ ]?[A-Z0-9]{2}',
			'BA' => $c,
			'IO' => 'BBND 1ZZ',
			'BN' => '[A-Z]{2}[ ]?\d{4}',
			'BG' => $b,
			'KH' => $c,
			'CV' => $b,
			'CL' => '\d{7}',
			'CR' => '\d{4,5}|\d{3}-\d{4}',
			'HR' => $c,
			'CY' => $b,
			'CZ' => '\d{3}[ ]?\d{2}',
			'DO' => $c,
			'EC' => '([A-Z]\d{4}[A-Z]|(?:[A-Z]{2})?\d{6})?',
			'EG' => $c,
			'EE' => $c,
			'FO' => $a,
			'GE' => $b,
			'GR' => '\d{3}[ ]?\d{2}',
			'GL' => '39\d{2}',
			'GT' => $c,
			'HT' => $b,
			'HN' => '(?:\d{5})?',
			'HU' => $b,
			'IS' => $a,
			'IN' => $d,
			'ID' => $c,
			'IE' => '[\dA-Z]{3} ?[\dA-Z]{4}',
			'IL' => $c,
			'JO' => $c,
			'KZ' => $d,
			'KE' => $c,
			'KW' => $c,
			'LA' => $c,
			'LV' => '(LV-)?\d{4}',
			'LB' => '(\d{4}([ ]?\d{4})?)?',
			'LI' => '(948[5-9])|(949[0-7])',
			'LT' => $c,
			'LU' => $b,
			'MK' => $b,
			'MY' => $c,
			'MV' => $c,
			'MT' => '[A-Z]{3}[ ]?\d{2,4}',
			'MU' => $c,
			'MX' => $c,
			'MD' => $b,
			'MC' => '980\d{2}',
			'MA' => $c,
			'NP' => $c,
			'NZ' => $b,
			'NI' => '((\d{4}-)?\d{3}-\d{3}(-\d{1})?)?',
			'NG' => '(\d{6})?',
			'OM' => '(PC )?\d{3}',
			'PK' => $c,
			'PY' => $b,
			'PH' => $b,
			'PL' => '\d{2}-\d{3}',
			'PR' => '00[679]\d{2}([ \-]\d{4})?',
			'RO' => $d,
			'RU' => $d,
			'SM' => '4789\d',
			'SA' => $c,
			'SN' => $c,
			'SK' => '\d{3}[ ]?\d{2}',
			'SI' => $b,
			'ZA' => $b,
			'LK' => $c,
			'TJ' => $d,
			'TH' => $c,
			'TN' => $b,
			'TR' => $c,
			'TM' => $d,
			'UA' => $c,
			'UY' => $c,
			'UZ' => $d,
			'VA' => '00120',
			'VE' => $b,
			'ZM' => $c,
			'AS' => '96799',
			'CC' => '6799',
			'CK' => $b,
			'RS' => $c,
			'ME' => '8\d{4}',
			'CS' => $c,
			'YU' => $c,
			'CX' => '6798',
			'ET' => $b,
			'FK' => 'FIQQ 1ZZ',
			'NF' => '2899',
			'FM' => '(9694[1-4])([ \-]\d{4})?',
			'GF' => '9[78]3\d{2}',
			'GN' => $a,
			'GP' => '9[78][01]\d{2}',
			'GS' => 'SIQQ 1ZZ',
			'GU' => '969[123]\d([ \-]\d{4})?',
			'GW' => $b,
			'HM' => $b,
			'IQ' => $c,
			'KG' => $d,
			'LR' => $b,
			'LS' => $a,
			'MG' => $a,
			'MH' => '969[67]\d([ \-]\d{4})?',
			'MN' => $d,
			'MP' => '9695[012]([ \-]\d{4})?',
			'MQ' => '9[78]2\d{2}',
			'NC' => '988\d{2}',
			'NE' => $b,
			'VI' => '008(([0-4]\d)|(5[01]))([ \-]\d{4})?',
			'PF' => '987\d{2}',
			'PG' => $a,
			'PM' => '9[78]5\d{2}',
			'PN' => 'PCRN 1ZZ',
			'PW' => '96940',
			'RE' => '9[78]4\d{2}',
			'SH' => '(ASCN|STHL) 1ZZ',
			'SJ' => $b,
			'SO' => $c,
			'SZ' => '[HLMS]\d{3}',
			'TC' => 'TKCA 1ZZ',
			'WF' => '986\d{2}',
			'YT' => '976\d{2}',
			'VN' => $d,
		];
		return static::$validators;
	}

	/**
	 * Validate ZIP code by form internal localization property `$form->GetLocale()`.
  * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedValue = trim((string) $rawSubmittedValue);
		// remove all chars except: 'A-Z', '0-9', spaces and '-'
		$notCheckedValue = preg_replace("#[^0-9A-Z\- ]#", '', strtoupper($rawSubmittedValue));
		$formLocale = $this->form->GetLocale();
		$result = NULL;
		$matched = FALSE;
		if (!$formLocale) {
			return $this->throwNewInvalidArgumentException(
				'Unable to validate ZIP code without configured '
				.'form `locale` property. Use `$form->SetLocale(\'[A-Z]{2}\');` '
				.'to internaly create proper ZIP code validator.'
			);
		} else {
			$validators = static::GetValidators();
			if (!isset($validators[$formLocale])) {
				$this->field->AddValidationError(
					static::GetErrorMessage(self::ERROR_NOT_SUPPORTED),
					[$formLocale]
				);
			} else {
				$validator = $validators[$formLocale];
				if (is_callable($validator)) {
					list($matched, $result) = call_user_func($validator, $notCheckedValue);
				} else if (is_string($validator)) {
					list($matched, $result) = $this->validateZipByRegExp($notCheckedValue, $validator, $formLocale);
				} else {
					$this->field->AddValidationError(
						static::GetErrorMessage(self::ERROR_VALIDATOR_WRONG_FORMAT),
						[$formLocale]
					);
				}
			}
		}
		if (!$matched || $result === NULL)
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_INVALID_ZIP)
			);
		return $result;
	}

	/**
	 * Validate ZIP code by regexp match pattern. Return if matches and safe ZIP code value.
	 * @param string $zip Raw submitted value.
	 * @param string $regExpMatch Regep match pattern without border characters (`#^$/`).
	 * @param string $localeCode Form locale uppercase code.
	 * @return array Array with success and safe ZIP code value.
	 */
	protected function validateZipByRegExp ($zip, $regExpMatch, $localeCode) {
		$matched = @preg_match('#^' . $regExpMatch . '$#', $zip, $matches);
		if ($matched === FALSE) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_WRONG_PATTERN),
				[$regExpMatch]
			);
		}
		if ($matched) return [$matched, $zip];
		return [FALSE, NULL];
	}
}
