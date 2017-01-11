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

require_once(__DIR__.'/../../SimpleForm.php');
require_once('Field.php');

abstract class SimpleForm_Core_Validator
{
	/** @var SimpleForm */
	protected $Form = null;

	/** @var MvcCore_Controller|mixed */
	protected $Controller = null;

	/** @var bool */
	protected $Translate = null;
	
	/** @var callable */
	protected $Translator = null;

	/** @var array */
	protected static $validators = array('SafeString');

	/** @var string */
	protected static $validatorsClassNameTemplate = 'SimpleForm_Validators_{ValidatorName}';

	/** @var array */
	protected static $validatorsKeys = array();

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
	public static function Create ($validatorName = '', SimpleForm & $form) {
		if (!self::$validatorsKeys) {
			$exploded = explode(',', self::$validators);
			foreach ($exploded as $value) self::$validatorsKeys[$value] = TRUE;
		}
		if (!isset(self::$validatorsKeys[$validatorName])) {
			if (strpos($validatorName, '_') !== FALSE) { // if not any full class name - go throw exception
				throw new Exception ("[SimpleForm_Core_Validator] Validator: '$validatorName' doesn't exist.");
			}
		}
		if (!isset(self::$instances[$validatorName])) {
			if (strpos($validatorName, '_') === FALSE) { // if not any full class name - it's built in validator
				$className = str_replace('{ValidatorName}', $validatorName, self::$validatorsClassNameTemplate);
			} else {
				$className = $validatorName;
			}
			self::$instances[$validatorName] = new $className($form);
		}
		return self::$instances[$validatorName];
	}
	/**
	 * Create new validator instance.
	 * @param SimpleForm $form 
	 */
	public function __construct (SimpleForm & $form) {
		$this->Form = $form;
		$this->Controller = & $form->Controller;
		$this->Translate = $form->Translate;
		$this->Translator = $form->Translator;
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
}
