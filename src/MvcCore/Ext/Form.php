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

namespace MvcCore\Ext;

/**
 * Responsibility: Main form class with all `<form>` configuration, attributes,
 *				   field instances and it's validators. To create any HTML form,
 *				   you need to instantiate this class, configure an id, action 
 *				   and more.
 */
class		Form 
extends		\MvcCore\Controller
implements	\MvcCore\Ext\Forms\IForm
{
	/**
	 * MvcCore Extension - Form - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';

	use \MvcCore\Ext\Form\InternalProps;
	use \MvcCore\Ext\Form\ConfigProps;
	use \MvcCore\Ext\Form\GetMethods;
	use \MvcCore\Ext\Form\SetMethods;
	use \MvcCore\Ext\Form\AddMethods;
	use \MvcCore\Ext\Form\FieldMethods;
	use \MvcCore\Ext\Form\Session;
	use \MvcCore\Ext\Form\Csrf;
	use \MvcCore\Ext\Form\Rendering;
	use \MvcCore\Ext\Form\Assets;
	use \MvcCore\Ext\Form\Submitting;

	/**
	 * Create \MvcCore\Ext\Form instance.
	 * Please don't forget to configure at least $form->Id, $form->Action,
	 * any control to work with and finally any button:submit/input:submit
	 * to submit the form to any URL defined in $form->Action.
	 * @param \MvcCore\Controller|\MvcCore\IController|NULL $controller
	 * @return void
	 */
	public function __construct (\MvcCore\IController $controller = NULL) {
		/** @var $controller \MvcCore\Controller */
		if ($controller === NULL) {
			$controller = \MvcCore\Ext\Form::GetCallerControllerInstance();
			if ($controller === NULL) 
				$controller = \MvcCore\Application::GetInstance()->GetController();
			if ($controller === NULL) $this->throwNewInvalidArgumentException(
				'There was not possible to determinate caller controller, '
				.'where is form instance create. Provide `$controller` instance explicitly '
				.'by first `\MvcCore\Ext\Form::__construct($controller);` argument.'
			);
		}
		$controller->AddChildController($this, $this->id);
		if (static::$jsSupportFilesRootDir === NULL || static::$cssSupportFilesRootDir === NULL) {
			$baseAssetsPath = str_replace('\\', '/', __DIR__) . '/Forms/assets';
			if (static::$jsSupportFilesRootDir === NULL)
				static::$jsSupportFilesRootDir = $baseAssetsPath;
			if (static::$cssSupportFilesRootDir === NULL)
				static::$cssSupportFilesRootDir = $baseAssetsPath;
		}
		if (self::$sessionClass === NULL)
			self::$sessionClass = $this->application->GetSessionClass();
		if (self::$toolClass === NULL)
			self::$toolClass = $this->application->GetToolClass();
	}

	/**
	 * Throw new `\InvalidArgumentException` with given
	 * error message and append automatically current class name,
	 * current form id and form class type.
	 * @param string $errorMsg 
	 * @throws \InvalidArgumentException 
	 */
	protected function throwNewInvalidArgumentException ($errorMsg) {
		$str = '['.get_class().'] ' . $errorMsg . ' ('
			. 'form id: `'.$this->id . '`, '
			. 'form type: `'.get_class($this->form).'`'
		.')';
		throw new \InvalidArgumentException($str);
	}

	/**
	 * Initialize the form, check if form is initialized or not and do it only once.
	 * Check if any form id exists and exists only once and initialize translation 
	 * boolean for better field initializations. This is template method. To define 
	 * any fields in custom `\MvcCore\Ext\Form` extended class, do it in custom 
	 * extended `Init()` method and call `parent::Init();` as first line inside 
	 * your extended `Init()` method.
	 * @throws \RuntimeException No form id property defined or Form id `...` already defined.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function Init () {
		if ($this->dispatchState > 0) return $this;
		parent::Init();
		$this->dispatchState = 1;
		if (!$this->id)
			throw new \RuntimeException("No form `id` property defined in `".get_class($this)."`.");
		if (isset(self::$instances[$this->id])) {
			$storedInstance = self::$instances[$this->id];
			if ($storedInstance !== $this) 
				throw new \RuntimeException("Form id `".$this->id."` already defined.");
		} else {
			self::$instances[$this->id] = $this;
		}
		$this->translate = $this->translator !== NULL && is_callable($this->translator);
		return $this;
	}

	/**
	 * Prepare form and it's fields for rendering.
	 * 
	 * This function is called automatically by rendering process if necessary.
	 * But if you need to operate with fields in your controller before rendering
	 * with real session values and initialized session errors, you can call this
	 * method anytime to prepare form for rendering and operate with anything inside.
	 * 
	 * - Process all defined fields and call `$field->PreDispatch();`
	 *   to prepare all fields for rendering process.
	 * - Load any possible error from session and set up
	 *   errors into fields and into form object to render them properly.
	 * - Load any possible previously submitted and/or stored values
	 *   from session and set up form fields with them.
	 * - Set initialized state to 2, which means - prepared, pre-dispatched for rendering.
	 * 
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function PreDispatch () {
		if ($this->dispatchState > 1) return $this;
		parent::PreDispatch(); // code: `if ($this->dispatchState < 1) $this->Init();` is executed by parent
		foreach ($this->fields as $field) 
			// translate fields if necessary and do any rendering preparation stuff
			$field->PreDispatch();
		$session = & $this->getSession();
		foreach ($session->errors as $errorMsgAndFieldNames) {
			list($errorMsg, $fieldNames) = array_merge([], $errorMsgAndFieldNames);
			$this->AddError($errorMsg, $fieldNames);
		}
		if ($session->values) {
			foreach ($session->values as $fieldName => $fieldValue) {
				if (!array_key_exists($fieldName, $this->fields)) continue;
				$field = $this->fields[$fieldName];
				$configuredFieldValue = $field->GetValue();
				$multiple = FALSE;
				if ($field instanceof \MvcCore\Ext\Forms\Fields\IMultiple)
					$multiple = $field->GetMultiple() ?: FALSE;
				if (
					$configuredFieldValue === NULL || 
					($multiple && is_array($configuredFieldValue) && count($configuredFieldValue) === 0)
				) {
					$field->SetValue($fieldValue);
				}
			}
		}
		$viewClass = $this->viewClass;
		$this->view = $viewClass::CreateInstance()
			->SetForm($this);
		if ($this->viewScript) 
			$this->view
				->SetController($this->parentController)
				->SetView($this->parentController->GetView());
		$this->dispatchState = 2;
		return $this;
	}

	/**
	 * Translate given string with configured translator and configured language code.
	 * @param string $translationKey 
	 * @return string
	 */
	public function Translate ($translationKey) {
		return call_user_func_array($this->translator, [$translationKey, $this->lang]);
	}
}
