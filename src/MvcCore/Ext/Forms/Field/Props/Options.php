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
 *    - `\MvcCore\Ext\Forms\Fields\LocalizationSelect`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 * @mixin \MvcCore\Ext\Forms\Field
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
	 *   $field->options = [
	 *       'f' => 'Female',
	 *       'm' => 'Male',
	 *   ];
	 *   
	 *   // Or you can use more advanced configuration with css class names 
	 *   // and html element attributes, let's consider html code like this:
	 *   //   <label for="gender-f" class="female">Female:</label>
	 *   //   <input id="gender-f" type="radio" name="gender" value="f" class="female" data-any="something-for-females" />
	 *   //   <label for="gender-m" class="male">Male:</label>
	 *   //   <input id="gender-m" type="radio" name="gender" value="m" class="male" data-any="something-for-males" />
	 *   // For that use this configuration:
	 *   $field->name = 'gender';
	 *   $field->options = [
	 *       'f' => [
	 *           'text'  => 'Female',	// text key will be also automatically translated
	 *           'class' => 'female',
	 *           'attrs' => ['data-any' => 'something-for-females'],
	 *       ],
	 *       'm' => [
	 *           'text'  => 'Male', // text key will be also automatically translated
	 *           'class' => 'male',
	 *           'attrs' => ['data-any' => 'something-for-males'],
	 *       ],
	 *   ];
	 * ```
	 * @requires
	 * @var array|NULL
	 */
	protected $options = NULL;

	/**
	 * Temp flatten key/value array to cache flatten options for submit checking.
	 * @var array|NULL
	 */
	protected $flattenOptions = NULL;

	/**
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @var bool|NULL
	 */
	protected $translateOptions = NULL;

	/**
	 * Callable or dynamic callable definition to load control options.
	 * Value could be:
	 * - Standard PHP callable or `\Closure` function.
	 * - Dynamic callable definition by array with first item to define context
	 *   definition int flag, where the method (second array item) is located, 
	 *   you can use constants:
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 *   Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * @var callable|\Closure|array|string|NULL
	 */
	protected $optionsLoader = NULL;
	
	/**
	 * This array contains options loader execution info.
	 * - First item is bool, `TRUE` to invoke options loader by PHP reflection,
	 *   `FALSE` to invoke options loader by `cal_user_func()`.
	 * - Second item could be standard PHP `callable` or `\Closure` for
	 *   invoking by `cal_user_func()` or array with reflection object and
	 *   reflection method to invoke.
	 * - Third item is array with optional invoke arguments.
	 * @var array `[bool, callable|\Closure|[\MvcCore\Ext\Form|\MvcCore\Controller|\MvcCore\Ext\ModelForms\Model, \ReflectionMethod], array]`
	 */
	protected $optionsLoaderExecution = [];

	/**
	 * @inheritDoc
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptions (array $options = []) {
		$this->options = $options;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @param  array $options
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddOptions (array $options = []) {
		$this->options = array_merge($this->options, $options);
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function & GetOptions () {
		return $this->options;
	}

	/**
	 * @inheritDoc
	 * @param  callable|\Closure|array|string $optionsLoader
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetOptionsLoader ($optionsLoader) {
		if (is_array($optionsLoader) || is_string($optionsLoader) || $optionsLoader instanceof \Closure) {
			$this->optionsLoader = $optionsLoader;
		} else {
			throw new \InvalidArgumentException(
				"Options loader for field `".get_class($this)."` ".
				"has to be PHP callable or array with method ".
				"context definition int and method name string."
			);
		}
		return $this;
	}
	
	/**
	 * @inheritDoc
	 * @return callable|\Closure|array|string|NULL
	 */
	public function GetOptionsLoader () {
		return $this->optionsLoader;
	}

	/**
	 * @inheritDoc
	 * @param  bool|NULL $translateOptions 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTranslateOptions ($translateOptions = TRUE) {
		$this->translateOptions = $translateOptions;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return bool|NULL
	 */
	public function GetTranslateOptions () {
		return $this->translateOptions;
	}

	/**
	 * @inheritDoc
	 * @param  array|NULL $fieldOptions
	 * @return array
	 */
	public function & GetFlattenOptions (array $fieldOptions = NULL) {
		if ($fieldOptions === NULL && $this->flattenOptions !== NULL)
			return $this->flattenOptions;
		$this->flattenOptions = [];
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
		if (!isset($cfg['optionsLoader'])) return;
		$this->SetOptionsLoader($cfg['optionsLoader']);
	}

	/**
	 * Load options by `$this->optionsLoader` definition if any.
	 * @throws \RuntimeException 
	 * @return void
	 */
	protected function setFormLoadOptions () {
		if ($this->options !== NULL || $this->optionsLoader === NULL) 
			return;
		list ($reflectionInvoke, $callable, $args) = $this->getOptionsLoaderExecution();
		if ($reflectionInvoke) {
			list ($reflectionInvokeObject, $reflectionMethod) = $callable;
			$options = $reflectionMethod->invokeArgs(
				$reflectionInvokeObject, $args
			);
		} else {
			$options = call_user_func_array($callable, $args);
		}
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
	 * Initialize (if necessary) and return options loader execution info.
	 * - First item is bool, `TRUE` to invoke options loader by PHP reflection,
	 *   `FALSE` to invoke options loader by `cal_user_func()`.
	 * - Second item could be standard PHP `callable` or `\Closure` for
	 *   invoking by `cal_user_func()` or array with reflection object and
	 *   reflection method to invoke.
	 * - Third item is array with optional invoke arguments.
	 * @throws \InvalidArgumentException 
	 * @return array `[bool, callable|\Closure|[\MvcCore\Ext\Form|\MvcCore\Controller|\MvcCore\Ext\ModelForms\Model, \ReflectionMethod], array]`
	 */
	protected function getOptionsLoaderExecution () {
		if (count($this->optionsLoaderExecution) > 0) 
			return $this->optionsLoaderExecution;

		$reflectionInvoke = FALSE;
		$callable = $this->optionsLoader;
		$args = [];

		if (is_array($callable)) {
			$argsBegin = strpos($callable[0], '::') !== FALSE ? 1 : 2;
			if (count($callable) > $argsBegin) {
				$args = array_slice($callable, $argsBegin);
				$callable = array_slice($callable, 0, $argsBegin);
			}
			/**
			 * Those values of `$callable` could be invoked by `call_user_func()`:
			 * `\Full\ClassName::Method`
			 * `function_name`
			 * `['\Full\ClassName', 'methodName']`
			 * `[$instance, 'methodName']`
			 * `['\Child\ClassName', 'parent::methodName']`
			 * `[$childInstance, 'parent::methodName']`
			 * `\Closure` function instance
			 */
			if (is_int($callable[0])) {
				/**
				 * But this value has to be resolved by PHP reflection:
				 * `[int, 'methodName']`
				 */
				$reflectionInvoke = TRUE;
				$reflectionInvokeObject = NULL;
				$reflectionMethod = NULL;
				list ($context, $methodName) = $callable;

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
					/** @var \MvcCore\Ext\ModelForms\Form $modelForm */
					$modelForm = $this->form;
					if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_MODEL) != 0) {
						$reflectionInvokeObject = $modelForm->GetModelInstance();
						if ($reflectionInvokeObject === NULL) {
							$modelFormType = new \ReflectionClass($modelForm->GetModelClassFullName());
							$reflectionInvokeObject = $modelFormType->newInstance();
						}
						$reflectionMethod = new \ReflectionMethod($reflectionInvokeObject, $methodName);
				
					} else if (($context & \MvcCore\Ext\Forms\IField::VALIDATOR_CONTEXT_MODEL_STATIC) != 0) {
						$reflectionMethod = new \ReflectionMethod($modelForm->GetModelClassFullName(), $methodName);

					} else {
						throw new \InvalidArgumentException(
							"Unknown local load options method context flag: `{$context}` for method `{$methodName}`."
						);
					}
				}
		
				$reflectionMethod->setAccessible(TRUE);
				$callable = [$reflectionInvokeObject, $reflectionMethod];
			}
		}

		$this->optionsLoaderExecution = [$reflectionInvoke, $callable, $args];
		
		return $this->optionsLoaderExecution;
	}

	/**
	 * Translate all texts in property `$this->options`.
	 * @param  bool $useOptGroups `TRUE` to use option groups, `TRUE` by default.
	 * @return void
	 */
	protected function preDispatchOptions ($useOptGroups = TRUE) {
		$form = $this->form;
		if ($useOptGroups) {
			foreach ($this->options as $key => $value) {
				if (is_scalar($value)) { // scalar is string|int|float|bool, scalar is not null|resource
					$valueStr = (string) $value;
					// most simple key/value array options configuration
					if (mb_strlen($valueStr) > 0)
						$this->options[$key] = $form->Translate($valueStr);
				} else if (is_array($value)) {
					if (isset($value['options']) && is_array($value['options'])) {
						// `<optgroup>` options configuration
						$this->preDispatchTranslateOptionOptGroup($value);
						$this->options[$key] = $value;
					} else {
						// advanced configuration with key, text, css class, and any other attributes for single option tag
						$textStr = isset($value['text'])
							? (string) $value['text']
							: (string) $key;
						if (mb_strlen($textStr) > 0)
							$this->options[$key]['text'] = $form->Translate($textStr);
					}
				}
			}
		} else {
			foreach ($this->options as $key => $value) {
				if (is_scalar($value)) { // scalar is string|int|float|bool, scalar is not null|resource
					$valueStr = (string) $value;
					// most simple key/value array options configuration
					if (mb_strlen($valueStr) > 0)
						$this->options[$key] = $form->Translate($valueStr);
				} else if (is_array($value)) {
					// advanced configuration with key, text, css class, and any other attributes for single option tag
					$textStr = isset($value['text'])
						? (string) $value['text']
						: (string) $key;
					if (mb_strlen($textStr) > 0)
						$this->options[$key]['text'] = $form->Translate($textStr);
				}
			}
		}
	}

}
