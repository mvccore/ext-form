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

class Tel extends \MvcCore\Ext\Forms\Validator
{
	/**
	 * Validate phone number only by removing all other characters than digits and plus.
	 * To validate phone number realy deeply - use zend validator instead:
	 * @see https://github.com/zendframework/zend-i18n
	 * @see https://github.com/zendframework/zend-i18n/blob/master/src/Validator/PhoneNumber.php
	 * @see https://olegkrivtsov.github.io/using-zend-framework-3-book/html/en/Checking_Input_Data_with_Validators/Validator_Usage_Examples.html#Example
	 * @param string|array			$submitValue
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		// remove spaces
		$rawSubmittedValue = str_replace(' ', '', (string) $rawSubmittedValue);
		// eliminate every char except 0-9 and +
		$result = preg_replace("#[^0-9\+]#", '', $rawSubmittedValue);
		$resultLength = mb_strlen($result);
		if (!$resultLength) $result = NULL;
		// add error if result is an emptry string or if there was any other characters than numbers and plus.
		if (!$resultLength || ($resultLength && $resultLength !== mb_strlen($rawSubmittedValue))) {
			$this->field->AddValidationError(
				 $this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::PHONE)	
			);
		}
		return $result;
	}
}
