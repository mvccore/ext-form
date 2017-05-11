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

namespace MvcCore\Ext\Form\Core;

require_once('Configuration.php');
require_once('Field.php');

abstract class Validator
{
	/** @var \MvcCore\Ext\Form|\MvcCore\Ext\Form\Core\Base */
	protected $Form = NULL;

	/** @var \MvcCore\Controller|mixed */
	protected $Controller = NULL;

	/** @var bool */
	protected $Translate = FALSE;
	
	/** @var callable */
	protected $Translator = NULL;

	/** @var string */
	protected static $validatorsClassNameTemplate = '\MvcCore\Ext\Form\Validators\{ValidatorName}';

	/** @var \MvcCore\Ext\Form\Core\Validator[]|mixed */
	protected static $instances = array();

	/**
	 * Create new validator instance by validator class name end if necessary,
	 * if validator instance for this name exists, previous instance is returned.
	 * @param \MvcCore\Ext\Form $form submitting simple form instance
	 * @param string $validatorName validator class name end
	 * @throws Exception
	 * @return \MvcCore\Ext\Form\Core\Validator[]|mixed
	 */
	public static function Create (\MvcCore\Ext\Form\Core\Configuration & $form, $validatorName = '') {
		if (!isset(static::$instances[$validatorName])) {
			$localValidatorClassName = strpos($validatorName, '_') === FALSE && strpos($validatorName, '\\') === FALSE;
			if ($localValidatorClassName) {
				// if not any full class name - it's built in validator
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
	 * @param \MvcCore\Ext\Form $form 
	 */
	public function __construct (\MvcCore\Ext\Form\Core\Configuration & $form) {
		$this->Form = & $form;
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
	 * @param string|array					$submitValue 
	 * @param string						$fieldName 
	 * @param \MvcCore\Ext\Form\Core\Field	$field
	 * @return string|array					safe submitted value
	 */
	public function Validate ($submitValue, $fieldName, \MvcCore\Ext\Form\Core\Field & $field) {
		return $submitValue;
	}

	protected function addError (\MvcCore\Ext\Form\Core\Field & $field, $msg = '', callable $replaceCall = NULL) {
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
