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
     */
    public function __construct ($cfg = array()) {
		static::$templates = (object) static::$templates;
		foreach ($cfg as $propertyName => $propertyValue) {
			if (in_array($propertyName, static::$declaredProtectedProperties)) {
				throw new \InvalidArgumentException(
					'Property `'.$propertyName.'` is not possible '
					.'to configure by constructor `$config` param. '
					.'(class: `'.get_class($this).'`).'
				);
			} else {
				$this->$propertyName = $propertyValue;
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
			throw new \InvalidArgumentException('['.__CLASS__."] No property with name '$prop' defined.");
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
		if (!$this->name) $this->thrownInvalidArgumentException(
			'No `name` property defined.'
		);
		$this->form = & $form;
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


	protected function thrownInvalidArgumentException ($errorMsg) {
		throw new \InvalidArgumentException(
			$errorMsg . ' ('
				. 'form id: `'.$this->form->GetId() . '`, '
				. 'form type: `'.get_class($this->form).'`, '
				. 'field type: `'.get_class($this).'`'
			.')'
		);
	}

	/**
	 * Submit field value - process raw request value with all
	 * configured validators and add errors into form if necesary.
	 * Then return safe value processed by all from validators.
	 * @param array $rawRequestParams 
	 * @return string|int|array|NULL
	 */
	public function Submit (array & $rawRequestParams = array()) {
		$result = NULL;
		$fieldName = $this->name;
		if ($this->readOnly || $this->disabled) {
			// get value previously assigned from session or 
			// by developer when called: 
			// `$form->SetValues(array(/* some predefined values from DB...*/))`
			$result = $this->value;
		} else {
			if (!$this->validators) {
				$submitValue = isset($rawRequestParams[$fieldName]) 
					? $rawRequestParams[$fieldName] 
					: $this->value;
				$result = $submitValue;
			} else {
				$safeValue = NULL;
				foreach ($this->validators as $validatorKey => $validator) {
					if ($validatorKey > 0) {
						$submitValue = $result; // take previous
					} else {
						// take submitted or default by SetDefault(array()) call in first verification loop
						$submitValue = isset($rawRequestParams[$fieldName]) 
							? $rawRequestParams[$fieldName] 
							: $this->value;
					}
					if ($validator instanceof \Closure) {
						$safeValue = $validator(
							$submitValue, $fieldName, $this, $this->form
						);
					} else /*if (gettype($validator) == 'string')*/ {
						$validatorInstance = \MvcCore\Ext\Forms\Validator::Create($this->form, (string) $validator);
						$safeValue = $validatorInstance->Validate(
							$submitValue, $fieldName, $this
						);
					}
					// set safe value as field submit result value
					$result = $safeValue;
				}
				// add required error message if necessary
				$this->submitAddRequiredErrorIfNecessary($result);
			}
		}
		return $result;
	}

	protected function submitAddRequiredErrorIfNecessary ($safeSubmittedValue) {
		if ($this->required) {
			$safeSubmittedValueType = gettype($safeSubmittedValue);
			if (
				$safeSubmittedValue === NULL ||
				($safeSubmittedValueType == 'string' && mb_strlen($safeSubmittedValue) === 0) ||
				($safeSubmittedValueType == 'array'  && count($safeSubmittedValue) === 0)
			) {
				$form = & $this->form;
				$errorMsg = $form->GetDefaultErrorMsg(\MvcCore\Ext\Forms\IError::REQUIRED);
				if ($this->translate)
					$errorMsg = $form->Translate($errorMsg);
				$viewClass = $this->form->GetViewClass();
				$errorMsg = $viewClass::Format(
					$errorMsg, array($this->label ? $this->label : $this->name)
				);
				$form->AddError(
					$errorMsg, $this->name
				);
			}
		}
		return $this;
	}
}
