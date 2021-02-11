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
	 * Remove predefined validator by given class ending name or by given validator instance.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to
	 * implement interface  `\MvcCore\Ext\Forms\IValidator` or it could be extended
	 * from base  abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
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
	 * Set boolean `TRUE` or string with template relative path
	 * without `.phtml` or `.php` extension, if you want to render
	 * field by any custom template.
	 *
	 * If `TRUE` given, path to template
	 * is completed by configured `\MvcCore\Ext\Forms\view::SetFieldsDir(...);`
	 * value, which is `/App/Views/Forms/Fields` by default.
	 *
	 * If any string with relative path given, path must be relative from configured
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, which is again
	 * `/App/Views/Forms/Fields` by default.
	 *
	 * To render field naturally, set `FALSE`, empty string or `NULL` (`NULL` is default).
	 *
	 * Example:
	 * ```
	 * // To render field template prepared in:
	 * // '/App/Views/Forms/Fields/my-specials/my-field-type.phtml':
	 *
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields'); // by default
	 * $field->SetViewScript('my-specials/my-field-type');
	 *
	 * // Or you can do the same by:
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields/my-specials');
	 * $field->SetType('my-field-type');
	 * ```
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetViewScript ($boolOrViewScriptPath = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * Set supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->SetJsSupportingFile(...)` to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $jsClass
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsClassName ($jsClassName) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->jsClassName = $jsClassName;
		return $this;
	}

	/**
	 * Set field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped
	 * class) for any additional purposes for your custom field, you need to
	 * define path to your javascript file relatively from configured
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value.
	 * Than you have to add supporting javascript file path into field form
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $jsFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetJsSupportingFile ($jsSupportingFilePath) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->jsSupportingFile = $jsSupportingFilePath;
		return $this;
	}

	/**
	 * Set field supporting css file relative path.
	 * If you want to use any custom supporting css file
	 * for any additional purposes for your custom field, you need to
	 * define path to your css file relatively from configured
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value.
	 * Than you have to add supporting css file path into field form
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $cssFullFile
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssSupportingFile ($cssSupportingFilePath) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->cssSupportingFile = $cssSupportingFilePath;
		return $this;
	}

	/**
	 * Set boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();`
	 * flag in `$field->Init();` method.
	 * @param bool $translate
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslate ($translate) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->translate = $translate;
		return $this;
	}

	/**
	 * Add field error message text to render it in rendering process.
	 * This method is only for rendering purposes, not to add errors
	 * into session. It's always called internally from `\MvcCore\Ext\Form`
	 * in render preparing process. To add form error properly,
	 * use `$field->form->AddError($errorMsg, $fieldNames);` method instead.
	 * @param string $errorMsg
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddError ($errorMsg) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->errors[] = $errorMsg;
		return $this;
	}

	/**
	 * Set field (or label) default template for natural
	 * (not customized with `*.phtml` view) field rendering.
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
	 * Set fields (and labels) default templates for natural
	 * (not customized with `*.phtml` view) field rendering.
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
