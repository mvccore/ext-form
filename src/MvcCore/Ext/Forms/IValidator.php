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

namespace MvcCore\Ext\Forms;

interface IValidator
{
	/**
	 * Create every time new validator instance with configured form instance. No singleton.
	 * @param \MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\IValidator
	 */
	public static function & CreateInstance ();

	/**
	 * Set up form instance, where is validator created durring submit.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form);
	
	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * @param \MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field);

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and 
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);` 
	 * with not translated error message. Return safe submitted value as result or `NULL` if there 
	 * is not possible to return safe valid value.
	 * @param string|array|NULL		$submitValue
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue);
}
