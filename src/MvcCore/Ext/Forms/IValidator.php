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

namespace MvcCore\Ext\Forms;

interface IValidator {

	/**
	 * Create every time new validator instance with configured form instance. No singleton.
	 * @param array $constructorConfig Configuration arguments for constructor, 
	 *                                 validator's constructor first param.
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public static function CreateInstance (array $constructorConfig = []);
	
	/**
	 * Return predefined validator custom error message strings (not translated) 
	 * with replacements for field names and more specific info 
	 * to tell the user what happened or what to do more.
	 * @param  int $errorMsgIndex Integer index for `static::$errorMessages` array.
	 * @return string
	 */
	public static function GetErrorMessage ($errorMsgIndex);

	/**
	 * Set up form instance, where is validator created during submit.
	 * @internal
	 * @template
	 * @param  \MvcCore\Ext\Form $form 
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public function SetForm (\MvcCore\Ext\IForm $form);

	/**
	 * Set up field instance, where is validated value by this 
	 * validator during submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @internal
	 * @template
	 * @param  \MvcCore\Ext\Forms\Field $field 
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field);

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and 
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);` 
	 * with not translated error message. Return safe submitted value as result or `NULL` if there 
	 * is not possible to return safe valid value.
	 * @param  string|array      $rawSubmittedValue Raw submitted value, string or array of strings.
	 * @return string|array|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue);
}
