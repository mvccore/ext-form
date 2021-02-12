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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\DataList`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait Options {

	/**
	 * Form group control options to render
	 * more sub-control attributes for specified
	 * submitted values (array keys).
	 * This property configuration is required.
	 * 
	 * Example:
	 * ```
	 *   // To configure for example radio buttons named: `gender` for `Female` and `Male`:
	 *   //   <label for="gender-f">Female:</label>
	 *   //   <input id="gender-f" type="radio" name="gender" value="f" />
	 *   //   <label for="gender-m">Male:</label>
	 *   //   <input id="gender-m" type="radio" name="gender" value="m" />
	 *   // use this configuration:
	 *   $field->name = 'gender';
	 *   $field->options = array(
	 *       'f' => 'Female',
	 *       'm' => 'Male',
	 *   );
	 *   
	 *   // Or you can use more advanced configuration with css class names 
	 *   // and html element attributes, let's consider html code like this:
	 *   //   <label for="gender-f" class="female">Female:</label>
	 *   //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 *   //   <label for="gender-m" class="male">Male:</label>
	 *   //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 *   // For that use this configuration:
	 *   $field->name = 'gender';
	 *   $field->options = array(
	 *       'f' => array(
	 *           'text'  => 'Female',	// text key will be also automatically translated
	 *           'class' => 'female',
	 *           'attrs' => array('data-any' => 'something-for-females'),
	 *       ),
	 *       'm' => array(
	 *           'text'  => 'Male', // text key will be also automatically translated
	 *           'class' => 'male',
	 *           'attrs' => array('data-any' => 'something-for-males'),
	 *       ),
	 *   ));
	 * ```
	 * @requires
	 * @var array
	 */
	protected $options = [];

	/**
	 * Temp flatten key/value array to cache flatten options for submit checking.
	 * @var array|NULL
	 */
	protected $flattenOptions = NULL;

	/**
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @var bool
	 */
	protected $translateOptions = TRUE;

	/**
	 * Definition for method name and context to resolve options loading for complex cases.
	 * First item is string method name, which has to return options for `$field->SetOptions()` method.
	 * Second item is context definition int flag, where the method is located, you can use constants:
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 * Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * @var array
	 */
	protected $optionsLoader = [];
	
	/**
	 * Resolved reflection method and invoke object to load options.
	 * @var array `[\ReflectionMethod, \MvcCore\Ext\Form|\MvcCore\Controller|\MvcCore\Ext\ModelForms\Model|NULL]`
	 */
	protected $optionsLoaderReflection = [];

	/**
	 * @inheritDocs
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptions (array $options = []) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		$this->options = $options;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddOptions (array $options = []) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		$this->options = array_merge($this->options, $options);
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return array
	 */
	public function & GetOptions () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		return $this->options;
	}

	/**
	 * @inheritDocs
	 * @param  string $methodName String method name to return options for `$field->SetOptions()` method.
	 * @param  int    $context    Context where method is located.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptionsLoader ($methodName, $context = \MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		$this->optionsLoader = [$methodName, $context];
		return $this;
	}
	
	/**
	 * @inheritDocs
	 * @throws \InvalidArgumentException 
	 * @return array `[string $methodName, int $context]`
	 */
	public function GetOptionsLoader () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		return $this->optionsLoader;
	}

	/**
	 * @inheritDocs
	 * @param  bool $translateOptions 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslateOptions ($translateOptions = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		$this->translateOptions = $translateOptions;
		return $this;
	}

	/**
	 * @inheritDocs
	 * @return bool
	 */
	public function GetTranslateOptions () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		return $this->translateOptions;
	}

	/**
	 * @inheritDocs
	 * @param  array|NULL $fieldOptions
	 * @return array
	 */
	public function & GetFlattenOptions (array $fieldOptions = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		if ($fieldOptions === NULL && $this->flattenOptions !== NULL)
			return $this->flattenOptions;
		$this->flattenOptions = [];
		/** @var $this \MvcCore\Ext\Forms\Fields\IOptions */
		$fieldOptions = $fieldOptions === NULL
			? $this->options
			: $fieldOptions;
		foreach ($fieldOptions as $key1 => $value1) {
			if (is_scalar($value1)) {
				// most simple key/value array options configuration
				$this->flattenOptions[$key1] = $value1;
			} else if (is_array($value1)) {
				if (array_key_exists('options', $value1) && is_array($value1['options'])) {
					// `<optgroup>` options configuration
					$subOptions = $value1['options'];
					foreach ($subOptions as $key2 => $value2) {
						if (is_scalar($value2)) {
							// most simple key/value array options configuration
							$this->flattenOptions[$key2] = $value2;
						} else if (is_array($value2)) {
							// advanced configuration with key, text, cs class, 
							// and any other attributes for single option tag
							$value = array_key_exists('value', $value2) 
								? $value2['value'] 
								: $key2;
							$text = array_key_exists('text', $value2) 
								? $value2['text'] 
								: $key2;
							$this->flattenOptions[$value] = $text;
						}
					}
				} else {
					// advanced configuration with `value`, `text`, `attrs` or css class, 
					// and any other attributes for single option tag:
					$value = array_key_exists('value', $value1) 
						? $value1['value'] 
						: $key1;
					$text = array_key_exists('text', $value1) 
						? $value1['text'] 
						: $key1;
					$this->flattenOptions[$value] = $text;
				}
			}
		}
		return $this->flattenOptions;
	}

	/**
	 * Check options loader configuration and call it if necessary.
	 * @param  array $cfg 
	 * @throws \InvalidArgumentException 
	 * @return void
	 */
	protected function ctorOptions (array & $cfg = []) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		if (!isset($cfg['optionsLoader'])) return;
		$optionsLoader = $cfg['optionsLoader'];
		if (is_string($optionsLoader)) {
			$optionsLoader = [$optionsLoader];
		} else if (!is_array($optionsLoader)) {
			throw new \InvalidArgumentException(
				"Options loader for field `".get_class($this)."` ".
				"has to be array with method name and optional context definition."
			);
		}
		call_user_func_array([$this, 'SetOptionsLoader'], $optionsLoader);
	}

	/**
	 * Load options by `$this->optionsLoader` definition if any.
	 * @throws \RuntimeException 
	 * @return void
	 */
	protected function setFormLoadOptions () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		if (count($this->optionsLoader) === 0) 
			return;
		list (
			$reflectionMethod, 
			$reflectionInvokeObject
		) = $this->getOptionsLoaderReflection();
		$options = $reflectionMethod->invokeArgs(
			$reflectionInvokeObject, []
		);
		if (is_array($options)) {
			$this->options = $options;
		} else {
			throw new \RuntimeException(
				"Options loader method `{$this->optionsLoader[0]}` in field ".
				"`{$this->name}` doesn't return array for options."
			);
		}
	}

	/**
	 * Initialize (if necessary) and return reflection invoke method and object.
	 * @throws \InvalidArgumentException 
	 * @return array [`\ReflectionMethod`, `\MvcCore\Ext\Form|\MvcCore\Controller|\MvcCore\Ext\ModelForms\Model|NULL`]
	 */
	protected function getOptionsLoaderReflection () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Options */
		if (count($this->optionsLoaderReflection) > 0) 
			return $this->optionsLoaderReflection;

		$reflectionMethod = NULL;
		$reflectionInvokeObject = NULL;
		list ($methodName, $context) = $this->optionsLoader;

		if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_FORM) != 0) {
			$reflectionInvokeObject = $this->form;
			$reflectionMethod = new \ReflectionMethod($reflectionInvokeObject, $methodName);

		} else if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_FORM_STATIC) != 0) {
			$reflectionMethod = new \ReflectionMethod(get_class($this->form), $methodName);

		} else if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_CTRL) != 0) {
			$reflectionInvokeObject = $this->form->GetController();
			$reflectionMethod = new \ReflectionMethod($reflectionInvokeObject, $methodName);

		} else if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_CTRL_STATIC) != 0) {
			$reflectionMethod = new \ReflectionMethod(get_class($this->form->GetController()), $methodName);

		} else {
			$modelFormInterface = 'MvcCore\\Ext\\ModelForms\\IForm';
			if (!interface_exists($modelFormInterface)) throw new \InvalidArgumentException(
				"For model context options loader, you have to install extension `mvccore/ext-model-form`."
			);
			$toolClass = $this->form->GetController()->GetApplication()->GetToolClass();
			$formImplementsInterface = $toolClass::CheckClassInterface(
				get_class($this->form), $modelFormInterface, TRUE, FALSE
			);
			if (!$formImplementsInterface) throw new \InvalidArgumentException(
				"For model context options loader, you have to implement form interface `{$modelFormInterface}`."
			);
			/** @var $modelForm \MvcCore\Ext\ModelForms\Form */
			$modelForm = $this->form;
			if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_MODEL) != 0) {
				$reflectionInvokeObject = $modelForm->GetModelInstance();
				$reflectionMethod = new \ReflectionMethod($reflectionInvokeObject, $methodName);
				
			} else if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_MODEL_STATIC) != 0) {
				$reflectionMethod = new \ReflectionMethod(get_class($modelForm->GetModelInstance()), $methodName);

			} else {
				throw new \InvalidArgumentException(
					"Unknown local load options method context flag: `{$context}` for method `{$methodName}`."
				);
			}
		}
		
		$reflectionMethod->setAccessible(TRUE);

		$this->optionsLoaderReflection = [$reflectionMethod, $reflectionInvokeObject];

		return $this->optionsLoaderReflection;
	}

}
