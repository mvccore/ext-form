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

require_once(__DIR__.'/../../Form.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/Exception.php');
require_once(__DIR__.'/../Core/View.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class CompanyId extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Error message key
	 * @var string
	 */
	protected static $errorMessageKey = \MvcCore\Ext\Form::TAX_ID;
	/**
	 * EU validators
	 * Array of regexp bases to check company id.
	 * If item is array of regexp bases, company id is
	 * checked in or behaviour. If item is closure function,
	 * company id is checked by calling closure function
	 * with fist param to be company id. Closure function has
	 * to return boolean about company id right form.
	 * Add any other custom validator by info bellow:
	 * @see https://en.wikipedia.org/wiki/VAT_identification_number
	 * @var array
	 */
	public static $Validators = array();
	/**
	 * Static initialization for this class to init validators.
	 * @return void
	 */
	public function StaticInit () {
		self::$Validators = array(
			'AT'=> 'U(\d{8})',						// Austria
			'BE'=> '(0?\d{9})',					// Belgium
			'BG'=> '(\d{9,10})',					// Bulgaria
			'CHE'=>'(\d{9})(MWST)?',				// Switzerland
			'CY'=> '([0-5|9]\d{7}[A-Z])',			// Cyprus
			'CZ'=> function ($id = '') {			// Czech republic
				$id = preg_replace('#\s+#', '', $id);
				if (!preg_match('#^\d{8}$#', $id)) return FALSE;
				$a = 0;
				for ($i = 0; $i < 7; $i++) $a += $id[$i] * (8 - $i);
				$a = $a % 11;
				if ($a === 0) $c = 1;
				elseif ($a === 10) $c = 1;
				elseif ($a === 1) $c = 0;
				else $c = 11 - $a;
				return (int) $id[7] === $c;
			},
			'DE'=> '([1-9]\d{8})',					// Germany
			'DK'=> '(\d{8})',						// Denmark
			'EL'=> '(10\d{7})',						// Estonia
			'ES'=> array(							// Spain
				'([A-Z]\d{8})))))',
				'([A-H|N-S|W]\d{7}[A-J])',
				'([0-9|Y|Z]\d{7}[A-Z])',
				'([K|L|M|X]\d{7}[A-Z])',
			),
			'EU'=> '(\d{9})',						// EU type
			'FI'=> '(\d{8})',						// Finland
			'FR'=> array(							// France
				'(\d{11})',
				'([(A-H)|(J-N)|(P-Z)]\d{10})',
				'(\d[(A-H)|(J-N)|(P-Z)]\d{9})',
				'([(A-H)|(J-N)|(P-Z)]{2}\d{9})',
			),
			'GB'=> array(							// Great Britain
				'?(\d{9})',
				'?(\d{12})',
				'?(GD\d{3})',
				'?(HA\d{3})',
			),
			'GR'=> '(\d{8,9})',						// Greece
			'HR'=> '(\d{11})',						// Croatia
			'HU'=> '(\d{8})',						// Hungary
			'IE'=> array(							// Ireland
				'(\d{7}[A-W])',
				'([7-9][A-Z\*\+)]\d{5}[A-W])',
				'(\d{7}[A-W][AH])',
			),
			'IT'=> '(\d{11})',						// Italy
			'LV'=> '(\d{11})',						// Latvia
			'LT'=> '(\d{9}|\d{12})',				// Lithunia
			'LU'=> '(\d{8})',						// Luxembourg
			'MT'=> '([1-9]\d{7})',					// Malta
			'NL'=> '(\d{9})B\d{2}',					// Netherland
			'NO'=> '(\d{9})',						// Norway
			'PL'=> '(\d{10})',						// Poland
			'PT'=> '(\d{9})',						// Portugal
			'RO'=> '([1-9]\d{1,9})',				// Romania
			'RS'=> '(\d{9})',						// Serbia
			'SI'=> '([1-9]\d{7})',					// Slovenia
			'SK'=> '([1-9]\d[(2-4)|(6-9)]\d{7})',	// Slovak republic
			'SE'=> '(\d{10}01)',					// Sweden
		);
	}
	/**
	 * Validate company ID by regular expression(s) or by closure function.
	 * @param string				$submitValue	raw submitted value
	 * @param string				$fieldName		form field name
	 * @param \MvcCore\Ext\Form\Core\Field $field			form field for company id
	 * @throws \MvcCore\Ext\Form\Core\Exception
	 * @return mixed
	 */
	public function Validate ($rawSubmittedValue) {
		$submitValue = trim($submitValue);
		$safeValue = preg_replace("#[^0-9A-Z\*\+]#", '', strtoupper($submitValue));
		$formLocale = strtoupper($this->Form->Locale);
		$result = FALSE;
		if (!$formLocale) {
			throw new Core\Exception(
				"[".__CLASS__."] Unable to validate company ID without configured form 'Locale' property. "
				. "Use \$form->SetLocale('[A-Z]{2}'); to create proper company ID validator."
			);
		} else {
			$formLocale = strtoupper($formLocale);
			if (!isset(static::$Validators[$formLocale])) {
				throw new Core\Exception(
					"[".__CLASS__."] Unable to create company ID validator for locale '$formLocale'. "
					. "Function to check company ID for locale '$formLocale' is not implemented yet. "
					. "Use different localization or put custom closure function to validate this field."
				);
			} else {
				$validator = self::$Validators[$formLocale];
				if (is_callable($validator)) {
					$result = call_user_func($validator, $safeValue);
				} else if (is_array($validator)) {
					foreach ($validator as $validatorItem) {
						if ($this->checkCompanyIdByRegExpBase($validatorItem, $formLocale, $safeValue)) {
							$result = TRUE;
							break;
						}
					}
				} else if (is_string($validator)) {
					$result = $this->checkCompanyIdByRegExpBase($validator, $formLocale, $safeValue);
				}
			}
		}
		if ((strlen($safeValue) > 0 && !$result) || strlen($safeValue) !== strlen($submitValue)) {
			$this->addError($field, Form::$DefaultMessages[static::$errorMessageKey], function ($msg, $args) {
				return Core\View::Format($msg, $args);
			});
		}
		return $safeValue;
	}
	/**
	 * Check company ID by regular expression base.
	 * Return true if company ID matches.
	 * @param string $regExpBase
	 * @param string $locale
	 * @param string $id
	 * @return bool
	 */
	protected function checkCompanyIdByRegExpBase ($regExpBase, $locale, $id) {
		preg_match("#^$regExpBase$#", $id, $matches);
		return count($matches) > 0;
	}
}
