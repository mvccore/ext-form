<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once('Configuration.php');
require_once('Field.php');

abstract class SimpleForm_Core_Validator
{
	/** @var SimpleForm */
	protected $Form = NULL;

	/** @var MvcCore_Controller|mixed */
	protected $Controller = NULL;

	/** @var bool */
	protected $Translate = FALSE;
	
	/** @var callable */
	protected $Translator = NULL;

	/** @var string */
	protected static $validatorsClassNameTemplate = 'SimpleForm_Validators_{ValidatorName}';

	/** @var array */
	protected static $instances = array();

	/**
	 * Create new validator instance by validator class name end if necessary,
	 * if validator instance for this name exists, previous instance is returned.
	 * @param string $validatorName validator class name end
	 * @param SimpleForm $form submitting simple form instance
	 * @throws Exception 
	 * @return string|array
	 */
	public static function Create ($validatorName = '', SimpleForm_Core_Configuration & $form) {
		if (!isset(static::$instances[$validatorName])) {
			if (strpos($validatorName, '_') === FALSE) { // if not any full class name - it's built in validator
				$className = str_replace('{ValidatorName}', $validatorName, static::$validatorsClassNameTemplate);
			} else {
				$className = $validatorName;
			}
			static::$instances[$validatorName] = new $className($form);
		}
		return static::$instances[$validatorName];
	}
	/**
	 * Create new validator instance.
	 * @param SimpleForm $form 
	 */
	public function __construct (SimpleForm_Core_Configuration & $form) {
		$this->Form = $form;
		$this->Controller = & $form->Controller;
		$this->Translate = $form->Translate;
		if ($this->Translate) $this->Translator = & $form->Translator;
	}
	/**
	 * Validation template method.
	 * In your validator implementation, check submitted value 
	 * by validator specific rules and if there is any error, call
	 * $form->AddError with translated or not translated error message.
	 * Return safe submitted value as result.
	 * @param string|array			$submitValue 
	 * @param string				$fieldName 
	 * @param SimpleForm_Core_Field	$field
	 * @return string|array			safe submitted value
	 */
	public function Validate ($submitValue, $fieldName, SimpleForm_Core_Field & $field) {
		return $submitValue;
	}

	protected function addError (SimpleForm_Core_Field & $field, $msg = '', callable $replaceCall = NULL) {
		$replacing = !is_null($replaceCall);
		$label = '';
		if ($replacing) $label = $field->Label ? $field->Label : $field->Name;
		if ($this->Translate) {
			$msg = call_user_func($this->Translator, $msg);
			if ($replacing) {
				$label = $field->Label ? call_user_func($this->Translator, $field->Label) : $field->Name;
			}
		}
		if ($replacing) {
			$msg = call_user_func($replaceCall, $msg, array($label));
		}
		$this->Form->AddError(
			$msg, $field->Name
		);
	}
}
