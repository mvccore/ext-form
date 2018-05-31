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

abstract class Field implements \MvcCore\Ext\Forms\IField
{
	use \MvcCore\Ext\Forms\Field\Props;
	use \MvcCore\Ext\Forms\Field\Getters;
	use \MvcCore\Ext\Forms\Field\Setters;
	use \MvcCore\Ext\Forms\Field\Rendering;
	
    /**
     * Create new form control instance.
     * @param array $cfg config array with camel case
	 *					 public properties and its values which you want to configure.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Field
     */
    public function __construct ($cfg = array()) {
		static::$templates = (object) static::$templates;
		foreach ($cfg as $propertyName => $propertyValue) {
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				$this->throwNewInvalidArgumentException(
					'Property `'.$propertyName.'` is not possible '
					.'to configure by constructor `$config` param.'
				);
			} else if ($propertyName == 'validators') {
				$this->validators = array();
				if (is_string($propertyValue)) {
					$this->AddValidators($propertyValue);
				} else {
					call_user_func_array(array($this, 'AddValidators'), $propertyValue);
				}
			} else {
				$this->{$propertyName} = $propertyValue;
			}
		}
	}

	/**
	 * Sets any custom property `"propertyName"` by `\MvcCore\Ext\Forms\Field::SetPropertyName("value");`,
	 * which is not necessary to define previously or gets previously defined
	 * property `"propertyName"` by `\MvcCore\Ext\Forms\Field::GetPropertyName();`.
	 * Throws exception if no property defined by get call or if virtual call
	 * begins with anything different from `Set` or `Get`.
	 * This method returns custom value for get and `\MvcCore\Request` instance for set.
	 * @param string $name
	 * @param array  $arguments
	 * @throws \InvalidArgumentException
	 * @return mixed|\MvcCore\Ext\Forms\Field
	 */
	public function __call ($name, $arguments = array()) {
		$nameBegin = strtolower(substr($name, 0, 3));
		$prop = lcfirst(substr($name, 3));
		if ($nameBegin == 'get' && isset($this->$prop)) {
			return $this->$prop;
		} else if ($nameBegin == 'set') {
			$this->$prop = isset($arguments[0]) ? $arguments[0] : NULL;
			return $this;
		} else {
			return $this->throwNewInvalidArgumentException("No property with name '$prop' defined.");
		}
	}

	/**
	 * Universal getter, if property not defined, `NULL` is returned.
	 * @param string $name
	 * @return mixed
	 */
	public function __get ($name) {
		return isset($this->$name) ? $this->$name : NULL ;
	}

	/**
	 * Universal setter, if property not defined, it's automaticly declarated.
	 * @param string $name
	 * @param mixed	 $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function __set ($name, $value) {
		$this->$name = $value;
		return $this;
	}

	/**
	 * This method  is called internaly from \MvcCore\Ext\Form after field
	 * is added into form by $form->AddField(); method. Do not use it
	 * if you are only user of this library.
	 * - check if field has any name, which is required
	 * - set up form and field id attribute by form id and field name
	 * - set up required
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		if (!$this->name) $this->throwNewInvalidArgumentException(
			'No `name` property defined.'
		);
		$this->form = & $form;
		if ($this->id === NULL)
			$this->id = implode(\MvcCore\Ext\Forms\IForm::HTML_IDS_DELIMITER, array(
				$form->GetId(),
				$this->name
			));
		// if there is no specific required boolean - set required boolean by form
		$this->required = $this->required === NULL 
			? $form->GetDefaultRequired()
			: $this->required ;
		$this->translate = $form->GetTranslate();
		return $this;
	}

	/**
	 * Set up field properties before rendering process.
	 * - set up field render mode
	 * - set up translation boolean
	 * - translate label if any
	 * @return void
	 */
	public function PreDispatch () {
		$form = & $this->form;
		// if there is no specific render mode - set render mode by form
		if ($this->renderMode === NULL)
			$this->renderMode = $form->GetDefaultFieldsRenderMode();
		if ($this->translate && $this->label)
			$this->label = $form->Translate($this->label);
	}
	
	/**
	 * Throw new `\InvalidArgumentException` with given
	 * error message and append automaticly current class name,
	 * current form id, form class type and current field class type.
	 * @param string $errorMsg 
	 * @throws \InvalidArgumentException 
	 */
	protected function throwNewInvalidArgumentException ($errorMsg) {
		$str = '['.__CLASS__.'] ' . $errorMsg . ' (';
		if ($this->form) {
			$str .= 'form id: `'.$this->form->GetId() . '`, '
				. 'form type: `'.get_class($this->form).'`, ';
		}
		$str .= 'field type: `'.get_class($this) . '`)';
		throw new \InvalidArgumentException($str);
	}

	/**
	 * Submit field value - process raw request value with all
	 * configured validators and add errors into form if necesary.
	 * Then return safe value processed by all from validators or `NULL`.
	 * @param array $rawRequestParams 
	 * @return string|int|array|NULL
	 */
	public function Submit (array & $rawRequestParams = array()) {
		$result = NULL;
		$fieldName = $this->name;
		if ($this->readOnly || $this->disabled) {
			// get value previously assigned from session or by developer when called: 
			// `$form->SetValues(array(/* some predefined values from DB...*/))`
			$result = $this->value;
		} else {
			$result = NULL;
			if (isset($rawRequestParams[$fieldName]))
				$result = $rawRequestParams[$fieldName];
			$processValidators = TRUE;
			if ($result === NULL) {
				$result = $this->value;
				$processValidators = FALSE;
			}
			if ($processValidators && $this->validators) {
				foreach ($this->validators as $validatorName => $validatorNameOrInstance) {
					// set safe value as field submit result value
					$validator = NULL;
					if (is_string($validatorNameOrInstance)) {
						$validator = $this->form->GetValidator($validatorName);
					} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
						$validator = $validatorNameOrInstance->SetForm($this->form)->SetField($this);
					} else {
						return $this->throwNewInvalidArgumentException(
							'Unknown validator type configured: `' . $validatorNameOrInstance 
							. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
						);
					}
					$result = $validator->SetField($this)->Validate($result);	
				}
				// add required error message if necessary
				if ($this->required) {
					$safeSubmittedValueType = gettype($result);
					if (
						!$processValidators || $result === NULL ||
						($safeSubmittedValueType == 'string' && mb_strlen($result) === 0) ||
						($safeSubmittedValueType == 'array'  && count($result) === 0)
					) $this->AddValidationError(
						$this->form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::REQUIRED)
					);
				}
			}
		}
		return $result;
	}

	/**
	 * Add form error with given error message containing 
	 * possible replacements for array values. 
	 * 
	 * If there is necessary to translate form elements 
	 * (form has configured translator `callable`)
	 * than given error message is translated first before replacing.
	 * 
	 * Before error message processing for replacements,
	 * there is automaticly assigned into first position into `$errorMsgArgs`
	 * array (translated) field label or field name and than 
	 * error message is processed for replacements.
	 * 
	 * If there is given some custom `$replacingCallable` param,
	 * error message is processed for replacements by custom `$replacingCallable`.
	 * 
	 * If there is not given any custom `$replacingCallable` param,
	 * error message is processed for replacements by static `Format()`
	 * method by configured form view class.
	 * 
	 * @param string $errorMsg 
	 * @param array $errorMsgArgs 
	 * @param callable $replacingCallable 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddValidationError (
		$errorMsg = '', array 
		$errorMsgArgs = array(), 
		callable $replacingCallable = NULL
	) {
		$customReplacing = $replacingCallable !== NULL;
		$fieldLabelOrName = '';
		if ($this->translate) {
			$errorMsg = $this->form->Translate($errorMsg);
			if ($customReplacing)
				$fieldLabelOrName = $this->label
					? $this->form->Translate($this->label) 
					: $this->name;
		} else {
			$fieldLabelOrName = $this->label
				? $this->label 
				: $this->name;
		}
		array_unshift($errorMsgArgs, $fieldLabelOrName);
		$formViewClass = $this->form->GetViewClass();
		if ($customReplacing) {
			$errorMsg = call_user_func(
				$replacingCallable, 
				$errorMsg, $errorMsgArgs, $formViewClass
			);
		} else if (strpos($errorMsg, '{0}') !== FALSE) {
			$errorMsg = $formViewClass::Format($errorMsg, $errorMsgArgs);
		}
		$this->form->AddError($errorMsg, $this->name);
		return $this;
	}
}
