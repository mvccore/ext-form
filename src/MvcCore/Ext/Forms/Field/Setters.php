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

namespace MvcCore\Ext\Forms\Field;

/**
 * Trait for class `\MvcCore\Ext\Forms\Field` containing field (mostly
 * configurable) properties setter methods.
 */
trait Setters {

	/**
	 * @inheritDocs
	 * @param string $id
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetId ($id = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->id = $id;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @requires
	 * @param string $name
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetName ($name = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->name = $name;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $type
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetType ($type = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->type = $type;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string|array|int|float|NULL $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetValue ($value) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->value = $value;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssClasses ($cssClasses) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = $cssClassesArr;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string|NULL  $title
	 * @param boolean|NULL $translateTitle
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTitle ($title, $translateTitle = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->title = $title;
		if ($translateTitle !== NULL)
			$this->translateTitle = $translateTitle;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddCssClasses ($cssClasses) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttrs (array $attrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->controlAttrs = $attrs;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $name
	 * @param mixed $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttr ($name, $value) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->controlAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddControlAttrs (array $attrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->controlAttrs = array_merge($this->controlAttrs, $attrs);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param \string[]|\MvcCore\Ext\Forms\Validator[] $validatorsNamesOrInstances
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetValidators (array $validatorsNamesOrInstances = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->validators = [];
		return $this->AddValidators($validatorsNamesOrInstances);
	}

	/**
	 * @inheritDocs
	 * @param \string[]|\MvcCore\Ext\Forms\Validator[] $validatorsNamesOrInstances,...
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddValidators ($validatorsNamesOrInstances = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$validatorsNamesOrInstances = func_get_args();
		if (count($validatorsNamesOrInstances) === 1 && is_array($validatorsNamesOrInstances[0]))
			$validatorsNamesOrInstances = $validatorsNamesOrInstances[0];
		foreach ($validatorsNamesOrInstances as $validatorNameOrInstance) {
			$instanceType = FALSE;
			if (is_string($validatorNameOrInstance)) {
				$validatorClassName = $validatorNameOrInstance;
			} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
				$instanceType = TRUE;
				$validatorClassName = get_class($validatorNameOrInstance);
			} else  {
				return $this->throwNewInvalidArgumentException(
					$validatorNameOrInstance instanceof \Closure
						? 'Unknown validator type given.'
						: 'Unknown validator type given: `' . $validatorNameOrInstance
						  .'`, type: `' . gettype($validatorNameOrInstance) . '`.'
				);
			}
			$slashPos = strrpos($validatorClassName, '\\');
			$validatorName = $slashPos !== FALSE
				? substr($validatorClassName, $slashPos + 1)
				: $validatorClassName;
			$this->validators[$validatorName] = $validatorNameOrInstance;
			if ($instanceType) 
				$validatorNameOrInstance->SetField($this);
		}
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $methodName String method name to return options for `$field->SetOptions()` method.
	 * @param int $context Context definition, where the method is located.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddValidatorLocal ($methodName, $context = \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_FORM) {
		$validator = (new \MvcCore\Ext\Forms\Validators\Local)
			->SetMethod($methodName)
			->SetContext($context);
		$this->validators[$methodName] = $validator;
		$validator->SetField($this);
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @param string $methodName String method name to return options for `$field->SetOptions()` method.
	 * @param int $context Context definition, where the method is located.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetValidatorLocal ($methodName, $context = \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_FORM) {
		$validator = (new \MvcCore\Ext\Forms\Validators\Local)
			->SetMethod($methodName)
			->SetContext($context);
		$this->validators = [];
		$this->validators[$methodName] = $validator;
		$validator->SetField($this);
		return $this;
	}
	

	/**
	 * @inheritDocs
	 * @param \string[]|\MvcCore\Ext\Forms\Validator[] $validatorNameOrInstance,...
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function RemoveValidator ($validatorNameOrInstance) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		if (is_string($validatorNameOrInstance)) {
			$validatorClassName = $validatorNameOrInstance;
		} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
			$validatorClassName = get_class($validatorNameOrInstance);
		} else {
			return $this->throwNewInvalidArgumentException(
				'Unknown validator type given: `' . $validatorNameOrInstance
				. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
			);
		}
		$slashPos = strrpos($validatorClassName, '\\');
		$validatorName = $slashPos !== FALSE
			? substr($validatorClassName, $slashPos + 1)
			: $validatorClassName;
		if (isset($this->validators[$validatorName]))
			unset($this->validators[$validatorName]);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetViewScript ($boolOrViewScriptPath = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $jsClass
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsClassName ($jsClassName) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->jsClassName = $jsClassName;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $jsFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsSupportingFile ($jsSupportingFilePath) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->jsSupportingFile = $jsSupportingFilePath;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $cssFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssSupportingFile ($cssSupportingFilePath) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->cssSupportingFile = $cssSupportingFilePath;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param bool $translate
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslate ($translate) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->translate = $translate;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $errorMsg
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddError ($errorMsg) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->errors[] = $errorMsg;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param string $templateName Template name in array `static::$templates`.
	 * @param string $templateCode Template HTML code with prepared replacements.
	 * @return string Newly configured template value.
	 */
	public static function SetTemplate ($templateName = 'control', $templateCode = '' /* '<input id="{id}" name="{name}" type="{type}" value="{value}"{attrs} />' */) {
		if (gettype(static::$templates) == 'array') {
			static::$templates[$templateName] = $templateCode;
		} else {
			static::$templates->{$templateName} = $templateCode;
		}
		return $templateCode;
	}

	/**
	 * @inheritDocs
	 * @param array|\stdClass $templates
	 * @return array
	 */
	public static function SetTemplates ($templates = []) {
		if (gettype(static::$templates) == 'array') {
			static::$templates = (array) $templates;
		} else {
			static::$templates = (object) $templates;
		}
		return static::$templates;
	}
}
