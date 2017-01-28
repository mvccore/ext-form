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

namespace MvcCore\Ext\Form\Validators;

require_once(__DIR__.'/../../Form.php');
require_once(__DIR__.'/../Core/Validator.php');
require_once(__DIR__.'/../Core/Field.php');
require_once(__DIR__.'/../Core/Exception.php');
require_once(__DIR__.'/../Core/View.php');

use
	MvcCore\Ext\Form,
	MvcCore\Ext\Form\Core;

class ZipCode extends Core\Validator
{
	public static $Validators = array();
	public static function StaticInit () {
		$p3 = "#^\d4$#"; // 3 digits pattern
		$p4 = "#^\d4$#"; // 4 digits pattern
		$p5 = "#^\d5$#"; // 5 digits pattern
		$p6 = "#^\d6$#"; // 6 digits pattern
		$p7 = "#^\d7$#"; // 7 digits pattern
		static::$Validators = array(
			'AD' => 'AD\d{3}',
			'AM' => '(37)?\d{4}',
			'AR' => '([A-HJ-NP-Z])?\d{4}([A-Z]{3})?',
			'AS' => '96799',
			'AT' => $p4,
			'AU' => $p4,
			'AX' => '22\d{3}',
			'AZ' => $p4,
			'BA' => $p5,
			'BB' => '(BB\d{5})?',
			'BD' => $p4,
			'BE' => $p4,
			'BG' => $p4,
			'BH' => '((1[0-2]|[2-9])\d{2})?',
			'BM' => '[A-Z]{2}[ ]?[A-Z0-9]{2}',
			'BN' => '[A-Z]{2}[ ]?\d{4}',
			'BR' => '\d{5}[\-]?\d{3}',
			'BY' => $p6,
			'CA' => '[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d',
			'CC' => '6799',
			'CH' => $p4,
			'CK' => $p4,
			'CL' => $p7,
			'CN' => $p6,
			'CR' => '\d{4,5}|\d{3}-\d{4}',
			'CS' => $p5,
			'CV' => $p4,
			'CX' => '6798',
			'CY' => $p4,
			'CZ' => '\d{3}[ ]?\d{2}',
			'DE' => $p5,
			'DK' => $p4,
			'DO' => $p5,
			'DZ' => $p5,
			'EC' => '([A-Z]\d{4}[A-Z]|(?:[A-Z]{2})?\d{6})?',
			'EE' => $p5,
			'EG' => $p5,
			'ES' => $p5,
			'ET' => $p4,
			'FI' => $p5,
			'FK' => 'FIQQ 1ZZ',
			'FM' => '(9694[1-4])([ \-]\d{4})?',
			'FO' => $p3,
			'FR' => '(?!(0{2})|(9(6|9))[ ]?\d{3})(\d{2}[ ]?\d{3})',
			'GB' => 'GIR[ ]?0AA|^((AB|AL|B|BA|BB|BD|BH|BL|BN|BR|BS|BT|CA|CB|CF|CH|CM|CO|CR|CT|CV|CW|DA|DD|DE|DG|DH|DL|DN|DT|DY|E|EC|EH|EN|EX|FK|FY|G|GL|GY|GU|HA|HD|HG|HP|HR|HS|HU|HX|IG|IM|IP|IV|JE|KA|KT|KW|KY|L|LA|LD|LE|LL|LN|LS|LU|M|ME|MK|ML|N|NE|NG|NN|NP|NR|NW|OL|OX|PA|PE|PH|PL|PO|PR|RG|RH|RM|S|SA|SE|SG|SK|SL|SM|SN|SO|SP|SR|SS|ST|SW|SY|TA|TD|TF|TN|TQ|TR|TS|TW|UB|W|WA|WC|WD|WF|WN|WR|WS|WV|YO|ZE)(\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}))$|^BFPO[ ]?\d{1,4}',
			'GE' => $p4,
			'GF' => '9[78]3\d{2}',
			'GG' => 'GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'GL' => '39\d{2}',
			'GN' => $p3,
			'GP' => '9[78][01]\d{2}',
			'GR' => '\d{3}[ ]?\d{2}',
			'GS' => 'SIQQ 1ZZ',
			'GT' => $p5,
			'GU' => '969[123]\d([ \-]\d{4})?',
			'GW' => $p4,
			'HM' => $p4,
			'HN' => '(?:\d{5})?',
			'HR' => $p5,
			'HT' => $p4,
			'HU' => $p4,
			'ID' => $p5,
			'IE' => '((D|DUBLIN)?([1-9]|6[wW]|1[0-8]|2[024]))?',
			'IL' => $p5,
			'IM' => 'IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'IN' => $p6,
			'IO' => 'BBND 1ZZ',
			'IQ' => $p5,
			'IS' => $p3,
			'IT' => $p5,
			'JE' => 'JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
			'JO' => $p5,
			'JP' => '\d{3}-\d{4}',
			'KE' => $p5,
			'KG' => $p6,
			'KH' => $p5,
			'KR' => '\d{3}[\-]\d{3}',
			'KW' => $p5,
			'KZ' => $p6,
			'LA' => $p5,
			'LB' => '(\d{4}([ ]?\d{4})?)?',
			'LI' => '(948[5-9])|(949[0-7])',
			'LK' => $p5,
			'LR' => $p4,
			'LS' => $p3,
			'LT' => $p5,
			'LU' => $p4,
			'LV' => $p4,
			'MA' => $p5,
			'MC' => '980\d{2}',
			'MD' => $p4,
			'ME' => '8\d{4}',
			'MG' => $p3,
			'MH' => '969[67]\d([ \-]\d{4})?',
			'MK' => $p4,
			'MN' => $p6,
			'MP' => '9695[012]([ \-]\d{4})?',
			'MQ' => '9[78]2\d{2}',
			'MT' => '[A-Z]{3}[ ]?\d{2,4}',
			'MU' => $p5,
			'MV' => $p5,
			'MX' => $p5,
			'MY' => $p5,
			'NC' => '988\d{2}',
			'NE' => $p4,
			'NF' => '2899',
			'NG' => '(\d{6})?',
			'NI' => '((\d{4}-)?\d{3}-\d{3}(-\d{1})?)?',
			'NL' => '\d{4}[ ]?[A-Z]{2}',
			'NO' => '(?!0000)\d{4}',
			'NP' => $p5,
			'NZ' => $p4,
			'OM' => '(PC )?\d{3}',
			'PF' => '987\d{2}',
			'PG' => $p3,
			'PH' => $p4,
			'PK' => $p5,
			'PL' => '\d{2}-\d{3}',
			'PM' => '9[78]5\d{2}',
			'PN' => 'PCRN 1ZZ',
			'PR' => '00[679]\d{2}([ \-]\d{4})?',
			'PT' => '\d{4}([\-]\d{3})?',
			'PW' => '96940',
			'PY' => $p4,
			'RE' => '9[78]4\d{2}',
			'RO' => $p6,
			'RS' => $p5,
			'RU' => $p6,
			'SA' => $p5,
			'SE' => '\d{3}[ ]?\d{2}',
			'SG' => $p6,
			'SH' => '(ASCN|STHL) 1ZZ',
			'SI' => $p4,
			'SJ' => $p4,
			'SK' => '\d{3}[ ]?\d{2}',
			'SM' => '4789\d',
			'SN' => $p5,
			'SO' => $p5,
			'SZ' => '[HLMS]\d{3}',
			'TC' => 'TKCA 1ZZ',
			'TH' => $p5,
			'TJ' => $p6,
			'TM' => $p6,
			'TN' => $p4,
			'TR' => $p5,
			'TW' => '\d{3}(\d{2})?',
			'UA' => $p5,
			'US' => '\d{5}([ \-]\d{4})?',
			'UY' => $p5,
			'UZ' => $p6,
			'VA' => '00120',
			'VE' => $p4,
			'VI' => '008(([0-4]\d)|(5[01]))([ \-]\d{4})?',
			'VN' => $p6,
			'WF' => '986\d{2}',
			'YT' => '976\d{2}',
			'YU' => $p5,
			'ZA' => $p4,
			'ZM' => $p5,
		);
	}
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field) {
		$submitValue = trim($submitValue);
		// remove all chars except: 'A-Z', '0-9', spaces and '-'
		$notCheckedValue = preg_replace("#[^0-9A-Z\- ]#", '', strtoupper($submitValue));
		$checkedAndSafeValue = '';
		$formLocale = strtoupper($this->Form->Locale);
		$result = TRUE;
		if (!$formLocale) {
			throw new Core\Exception(
				"[".__CLASS__."] Unable to validate ZIP code without configured form 'Locale' property. "
				. "Use \$form->SetLocale('[A-Z]{2}'); to create proper ZIP code validator."
			);
		} else {
			$formLocale = strtoupper($formLocale);
			if (!isset(static::$Validators[$formLocale])) {
				throw new Core\Exception(
					"[".__CLASS__."] Unable to create ZIP code validator for locale '$formLocale'. "
					. "Function to check ZIP code for locale '$formLocale' is not implemented yet. "
					. "Use different localization or put custom closure function to validate this field."
				);
			} else {
				$validator = static::$Validators[$formLocale];
				if (is_callable($validator)) {
					list($checkedAndSafeValue, $result) = call_user_func($validator, $notCheckedValue);
				} else {
					list($checkedAndSafeValue, $result) = $this->validateZipByRegExp($validator, $notCheckedValue);
				}
			}
		}
		if (mb_strlen($checkedAndSafeValue) !== mb_strlen($submitValue) || !$result) {
			$this->addError($field, Form::$DefaultMessages[Form::ZIP_CODE], function ($msg, $args) {
				return Core\View::Format($msg, $args);
			});
		}
		return $checkedAndSafeValue;
	}

	protected function validateZipByRegExp ($zip, $regExp) {
		@preg_match($regExp, $zip, $matches);
		return array($zip, count($matches) > 0);
	}
}
ZipCode::StaticInit();