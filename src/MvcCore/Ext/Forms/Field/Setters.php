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
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait Setters {

	/**
	 * @inheritDoc
	 * @param  string $id
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetId ($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @requires
	 * @param  string $name
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetName ($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $type
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetType ($type) {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetFieldsetName ($fieldsetName) {
		$this->fieldsetName = $fieldsetName;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  int $fieldOrder
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetFieldOrder ($fieldOrder) {
		$this->fieldOrder = $fieldOrder;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string|array|int|float|NULL $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetValue ($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssClasses ($cssClasses) {
		$cssClassesArr = is_array($cssClasses)
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = $cssClassesArr;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string|NULL $title
	 * @param  bool|NULL   $translateTitle
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTitle ($title, $translateTitle = NULL) {
		$this->title = $title;
		if ($translateTitle !== NULL)
			$this->translateTitle = $translateTitle;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddCssClasses ($cssClasses) {
		$cssClassesArr = is_array($cssClasses)
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttrs (array $attrs = []) {
		$this->controlAttrs = $attrs;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $name
	 * @param  mixed  $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttr ($name, $value) {
		$this->controlAttrs[$name] = $value;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddControlAttrs (array $attrs = []) {
		$this->controlAttrs = array_merge($this->controlAttrs, $attrs);
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  \string[]|\MvcCore\Ext\Forms\Validator[] $validatorsNamesOrInstances
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetValidators (array $validatorsNamesOrInstances = []) {
		$this->validators = [];
		return $this->AddValidators($validatorsNamesOrInstances);
	}

	/**
	 * @inheritDoc
	 * @param  \string[]|\MvcCore\Ext\Forms\Validator[] $validatorsNamesOrInstances,...
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddValidators ($validatorsNamesOrInstances = []) {
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
	 * @inheritDoc
	 * @param  string $methodName String method name to return options for `$field->SetOptions()` method.
	 * @param  int    $context    Context definition, where the method is located.
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
	 * @inheritDoc
	 * @param  string $methodName String method name to return options for `$field->SetOptions()` method.
	 * @param  int    $context    Context definition, where the method is located.
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
	 * @inheritDoc
	 * @param  \string[]|\MvcCore\Ext\Forms\Validator[] $validatorNameOrInstance,...
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function RemoveValidator ($validatorNameOrInstance) {
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
	 * @inheritDoc
	 * @param  bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $jsClass
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsClassName ($jsClassName) {
		$this->jsClassName = $jsClassName;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $jsFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsSupportingFile ($jsSupportingFilePath) {
		$this->jsSupportingFile = $jsSupportingFilePath;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $cssFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssSupportingFile ($cssSupportingFilePath) {
		$this->cssSupportingFile = $cssSupportingFilePath;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  bool $translate
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslate ($translate) {
		$this->translate = $translate;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $errorMsg
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddError ($errorMsg) {
		$this->errors[] = $errorMsg;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  string $templateName Template name in array `static::$templates`.
	 * @param  string $templateCode Template HTML code with prepared replacements.
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
	 * @inheritDoc
	 * @param  array|\stdClass $templates
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
