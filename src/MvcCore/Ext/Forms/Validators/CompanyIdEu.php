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
 * Responsibility - Validate company ID for EU states by regular expression(s) or by closure function(s).
 * - DO NOT USE ANY ADVANCED CONSTRUCTIONS for validations, because there are lot of checking exceptions.
 * - Return from `Validate()` function safe submitted value or `NULL` if there is not possible to return safe value.
 * @see https://en.wikipedia.org/wiki/VAT_identification_number
 * @see http://studylib.net/doc/7254793/vat-number-construction-rules
 * @see http://85.81.229.78/systems/DKVIES/-%20Arkiv/Algoritme%E6ndringer/VIES-VAT%20Validation%20Routines-v15.0.doc
 */
class CompanyIdEu extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_SUBJECT_ID = 0;
	const ERROR_COMPANY_ID = 1;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		IError::ERROR_SUBJECT_ID				=> "Field '{0}' requires a valid subject ID.",
		IError::ERROR_COMPANY_ID				=> "Field '{0}' requires a valid company ID.",
	];

	/**
	 * EU company IDs validators.
	 * Array of regexp match patterns to check company ID.
	 * Keys are locale code strings and values are regexp `match 
	 * pattern strings`, `array` with regexp `match pattern strings`, 
	 * always without border characters (`#^$/`) or `callable`.
	 * If item is array of regexp match patterns, company ID is
	 * checked continuously until moment, when any regexp pattern
	 * finaly match company ID. If item is `callable`,
	 * company ID is checked by calling the function
	 * with fist param to be company ID. Closure function has
	 * to return array with success and with safe company ID value.
	 * Add any other custom validator by info bellow:
	 * @see https://en.wikipedia.org/wiki/VAT_identification_number
	 * @var array
	 */
	protected static $validators = [];

	/**
	 * Set specific locale validator for company ID.
	 * It could be regexp match pattern string without border characters (`#^$/`)
	 * or `callable` accepting first argument to be raw submitted value and 
	 * returning array with success and with safe company ID value.
	 * @param string $localeCode Locale code, automaticly converted to upper case.
	 * @param string|callable $regExpMatchOrCallable Reg exp match pattern string with or without border characters or `callable`.
	 * @return string|callable
	 */
	public static function SetValidator ($localeCode, $regExpMatchOrCallable) {
		if (!static::$validators) static::GetValidators();
		return static::$validators[strtoupper($localeCode)] = $regExpMatchOrCallable;
	}

	/**
	 * Get all preconfigured validators as key/value array.
	 * Keys are locale codes and values are regexp match 
	 * pattern strings or array with regexp match 
	 * pattern strings or `callable`s.
	 * @return array
	 */
	public static function & GetValidators () {
		self::$validators = [
			'AT'=> 'U(\d{8})',						// Austria
			'BE'=> function ($id) {					// Belgium
				$id = trim(preg_replace('#[^A-Z0-9]#', '', $id));
				$success = preg_match('#^(0|1)(?=[\d]{9})$#', $id);
				return [(bool) $success, $id];
			},
			'BG'=> '\d{9,10}',						// Bulgaria
			'CH'=>'(\d{9})(MWST)?',					// Switzerland
			'CY'=> function ($id) {					// Cyprus
				$success = preg_match('#^[0-5|9]\d{7}[A-Z]$#', $id);
				if (substr($id, 0, 2) == '12') $success = 0;
				return [(bool) $success, $id];
			},
			'CZ'=> '\d{8,10}',						// Czech Republic
			'DE'=> '[1-9]\d{8}',					// Germany
			'DK'=> '\d{8}',							// Denmark
			'EL'=> '10\d{7}',						// Estonia
			'ES'=> [								// Spain
				'[A-Z]\d{8}))))',
				'[A-H|N-S|W]\d{7}[A-J]',
				'[0-9|Y|Z]\d{7}[A-Z]',
				'[K|L|M|X]\d{7}[A-Z]',
			],
			'EU'=> '\d{9}',							// EU type
			'FI'=> '\d{8}',							// Finland
			'FR'=> [								// France
				'\d{11}',
				'[(A-H)|(J-N)|(P-Z)]\d{10}',
				'\d[(A-H)|(J-N)|(P-Z)]\d{9}',
				'[(A-H)|(J-N)|(P-Z)]{2}\d{9}',
			],
			'GB'=> [								// Great Britain
				'\d{9}',
				'\d{12}',
				'GD\d{3}',
				'HA\d{3}',
			],
			'GR'=> '\d{8,9}',						// Greece
			'HR'=> '\d{11}',						// Croatia
			'HU'=> '\d{8}',							// Hungary
			'IE'=> [								// Ireland
				'\d{7}[A-W]',
				'[7-9][A-Z\*\+\)]\d{5}[A-W]',
				'\d{7}[A-W][AH]',
			],
			'IT'=> '\d{11}',						// Italy
			'LV'=> '\d{11}',						// Latvia
			'LT'=> '(\d{9}|\d{12})',				// Lithunia
			'LU'=> '\d{8}',							// Luxembourg
			'MT'=> '[1-9]\d{7}',					// Malta
			'NL'=> '(\d{9})B\d{2}',					// Netherland
			'NO'=> '\d{9}',							// Norway
			'PL'=> '\d{10}',						// Poland
			'PT'=> '\d{9}',							// Portugal
			'RO'=> '[1-9]\d{1,9}',					// Romania
			'RS'=> '\d{9}',							// Serbia
			'SI'=> '[1-9]\d{7}',					// Slovenia
			'SK'=> '([1-9]\d[(2-4)|(6-9)]\d{7})',	// Slovak republic
			'SE'=> '\d{10}01',						// Sweden
		];
	}

	/**
	 * Validate company ID by regular expression(s) or by closure function.
	 * @param string|array $rawSubmittedValueraw submitted value
	 * @return mixed
	 */

	/**
	 * Validate company ID for EU states by regular expression(s) or by closure function.
	 * Do not use any advanced constructions for validations, because there are lot of checking exceptions.
	 * Return safe submitted value as result or `NULL` if there is not possible to return safe valid value.
	 * @param string|array			$submitValue	Raw submitted value, string or array of strings.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = NULL;
		$matched = FALSE;
		$rawSubmittedValue = trim($rawSubmittedValue);
		$saferValue = preg_replace("#[^A-Z0-9\.\-\*\+ ]#", '', strtoupper($rawSubmittedValue));
		$formLocale = strtoupper($this->Form->Locale);
		if (!$formLocale) {
			return $this->throwNewInvalidArgumentException(
				'Unable to validate company ID without configured '
				.'form `locale` property. Use `$form->SetLocale(\'[A-Z]{2}\');` '
				.'to internaly create proper company ID validator.'
			);
		} else {
			$formLocale = strtoupper($formLocale);
			$validators = static::GetValidators();
			if (!isset($validators[$formLocale])) {
				$this->field->AddValidationError(
					'Company ID validation not supported (field `{0}`, locale: `{1}`).',
					[$formLocale]
				);
			} else {
				$validator = $validators[$formLocale];
				if (is_callable($validator)) {
					list($matched, $safeValue) = call_user_func($validator, $saferValue);
					if ($matched) $result = $safeValue;
				} else if (is_array($validator)) {
					foreach ($validator as $validatorRegExp) {
						list($matched, $safeValue) = $this->validateCompanyIdByRegExp(
							$saferValue, $formLocale, $validatorRegExp
						);
						if ($matched) {
							$result = $safeValue;
							break;
						}
					}
				} else if (is_string($validator)) {
					list($matched, $safeValue) = $this->validateCompanyIdByRegExp(
						$saferValue, $formLocale, $validator
					);
					if ($matched) $result = $safeValue;
				}
			}
		}
		if (!$matched || $result === NULL)
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_COMPANY_ID)
			);
		return $result;
	}

	/**
	 * Validate company ID by regexp match pattern. Return if matches and safe company id value.
	 * @param string $submittedValue Raw submitted value.
	 * @param string $regExpMatch Regep match pattern without border characters (`#^$/`).
	 * @param string $localeCode Form locale uppercase code.
	 * @return array Array with success and safe company id value.
	 */
	protected function validateCompanyIdByRegExp ($submittedValue, $regExpMatch, $localeCode) {
		$matched = @preg_match('#^' . $regExpMatch . '$#', $submittedValue, $matches);
		if ($matched === FALSE) {
			$this->field->AddValidationError(
				'Company ID validation pattern for field `{0}` is wrong: `{1}`.',
				[$regExpMatch]
			);
		}
		if ($matched) 
			return [$matched, $submittedValue];
		return [FALSE, NULL];
	}
}
