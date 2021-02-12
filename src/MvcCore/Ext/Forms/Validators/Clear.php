<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Validators;

/**
 * Responsibility: Attribute class to clear field default validators.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Clear extends \MvcCore\Ext\Forms\Validator {

	/**
	 * Set up field instance, where is validated value by this 
	 * validator during submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param \MvcCore\Ext\Forms\Field $field 
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field) {
		$field->SetValidators([]);
		parent::SetField($field);
		return $this;
	}

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and 
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);` 
	 * with not translated error message. Return safe submitted value as result or `NULL` if there 
	 * is not possible to return safe valid value.
	 * @param string|array			$rawSubmittedValue	Raw submitted value, string or array of strings.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		return $rawSubmittedValue;
	}
}