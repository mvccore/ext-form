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

abstract class Validator implements \MvcCore\Ext\Forms\IValidator
{
	/**
	 * Form instance where was validator created.
	 * Every validator instance belongs to only one form isntance.
	 * @var \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected $form = NULL;

	/**
	 * Currently validated form field instance.
	 * Before every `Validate()` method call, there is called
	 * `$validator->SetField($field);` to work with proper field 
	 * instance durring validation.
	 * @var \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	protected $field = NULL;

	/**
	 * Create every time new validator instance with configured form instance. No singleton.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public static function & CreateInstance (\MvcCore\Ext\Forms\IForm & $form) {
		$validator = new static();
		return $validator->SetForm($form);
	}

	/**
	 * Set up form instance, where is validator created durring submit.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		$this->form = & $form;
		return $this;
	}

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		$this->field = & $field;
		return $this;
	}

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and 
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);` 
	 * with not translated error message. Return safe submitted value as result or `NULL` if there 
	 * is not possible to return safe valid value.
	 * @param string|array|NULL		$submitValue
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public abstract function Validate ($rawSubmittedValue);
}
