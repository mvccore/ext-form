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
	 * Every validator instance belongs to only one form instance.
	 * @var \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected $form = NULL;

	/**
	 * Currently validated form field instance.
	 * Before every `Validate()` method call, there is called
	 * `$validator->SetField($field);` to work with proper field 
	 * instance during validation.
	 * @var \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	protected $field = NULL;

	/**
	 * Validator custom error message strings (not translated) 
	 * with replacements for field names and more specific info 
	 * to tell the user what happend or what to do more.
	 * @var \string[]
	 */
	protected static $errorMessages = [];

	/**
	 * Remembered value from `\MvcCore\Application::GetInstance()->GetToolClass();`
	 * @var string
	 */
	protected static $toolClass = '';

	/**
	 * Create every time new validator instance with configured form instance. No singleton.
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public static function & CreateInstance () {
		$validator = new static();
		$validator::$toolClass = \MvcCore\Application::GetInstance()->GetToolClass();
		return $validator;
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
	 * validator during submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField $field 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		$this->field = & $field;
		return $this;
	}
	
	/**
	 * Return predefined validator custom error message strings (not translated) 
	 * with replacements for field names and more specific info 
	 * to tell the user what happend or what to do more.
	 * @param int $errorMsgIndex Integer index for `static::$errorMessages` array.
	 * @return string
	 */
	public static function GetErrorMessage ($errorMsgIndex) {
		return static::$errorMessages[$errorMsgIndex];;
	}

	/**
	 * Validation method.
	 * Check submitted value by validator specific rules and 
	 * if there is any error, call: `$this->field->AddValidationError($errorMsg, $errorMsgArgs, $replacingCallable);` 
	 * with not translated error message. Return safe submitted value as result or `NULL` if there 
	 * is not possible to return safe valid value.
	 * @param string|array			$submitValue	Raw submitted value, string or array of strings.
	 * @return string|array|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public abstract function Validate ($rawSubmittedValue);

	/**
	 * Throw new `\InvalidArgumentException` with given
	 * error message and append automaticly current class, 
	 * field name, form id, field class name and form class name.
	 * @param string $errorMsg 
	 * @throws \InvalidArgumentException 
	 */
	protected function throwNewInvalidArgumentException ($errorMsg) {
		if ($this->field) 
			$msgs[] = 'Field name: `'.$this->field->GetName() . '`, Field type: `'.get_class($this->field).'`';
		if ($this->form) 
			$msgs[] = 'Form id: `'.$this->form->GetId() . '`, Form type: `'.get_class($this->form).'`';
		
		throw new \InvalidArgumentException(
			'['.__CLASS__.'] ' . $errorMsg . ($msgs ? ' '.implode(', ', $msgs) : '')
		);
	}


}
