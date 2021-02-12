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

namespace MvcCore\Ext;

/**
 * Responsibility: Main form class with all `<form>` configuration, attributes,
 *                 field instances and it's validators. To create any HTML form,
 *                 you need to instantiate this class, configure an id, action 
 *                 and more.
 */
class		Form 
extends		\MvcCore\Controller
implements	\MvcCore\Ext\IForm {

	/**
	 * MvcCore Extension - Form - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.1.2';

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
	 * @param  \MvcCore\Controller|NULL $controller
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
		$this->controller = $controller;
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
	 * @param  string $errorMsg 
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
	 * @inheritDocs
	 * @param  bool $submit      `TRUE` if form is submitting, `FALSE` otherwise by default.
	 * @throws \RuntimeException No form id property defined or Form id `...` already defined.
	 * @return void
	 */
	public function Init ($submit = FALSE) {
		if ($this->dispatchState > \MvcCore\IController::DISPATCH_STATE_CREATED) 
			return;
		parent::Init();
		$this->dispatchState = \MvcCore\IController::DISPATCH_STATE_INITIALIZED;
		if (!$this->id)
			throw new \RuntimeException("No form `id` property defined in `".get_class($this)."`.");
		if (isset(self::$instances[$this->id])) {
			$anotherForm = self::$instances[$this->id];
			if ($anotherForm !== $this)
				throw new \RuntimeException("Form id `".$this->id."` already defined.");
		} else {
			self::$instances[$this->id] = TRUE;
		}
		$this->translate = $this->translator !== NULL && is_callable($this->translator);
	}

	/**
	 * @inheritDocs
	 * @param  bool $submit `TRUE` if form is submitting, `FALSE` otherwise by default.
	 * @return void
	 */
	public function PreDispatch ($submit = FALSE) {
		if ($this->dispatchState >= \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED) 
			return;
		$this->viewEnabled = !$submit;
		parent::PreDispatch(); // code: `if ($this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED) $this->Init();` is executed by parent
		
		$session = & $this->getSession();
		$this->preDispatchLoadErrors($session);
		$this->preDispatchLoadValues($session);

		if ($submit) {
			$this->dispatchState = \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED;
			return;
		}
		
		foreach ($this->fields as $field) 
			// translate fields if necessary and do any rendering preparation stuff
			$field->PreDispatch();
		
		if ($this->translate && $this->translateTitle && $this->title !== NULL)
			$this->title = $this->Translate($this->title);
		
		$viewClass = $this->viewClass;
		$this->view = $viewClass::CreateInstance()
			->SetForm($this);
		if ($this->viewScript) 
			$this->view
				->SetController($this->parentController)
				->SetView($this->parentController->GetView());
		
		if ($this->csrfEnabled)
			$this->SetUpCsrf();
		
		$this->dispatchState = \MvcCore\IController::DISPATCH_STATE_PRE_DISPATCHED;
	}
	
	/**
	 * Initialize form errors from session for all fields in `PreDispatch()`
	 * lifecycle moment or earlier from method `GetValues()`.
	 * @param  \MvcCore\Session $session 
	 * @return void
	 */
	protected function preDispatchLoadErrors (\MvcCore\ISession $session) {
		if (!$session->errors) return;
		if (
			$this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED
		) $this->Init();
		foreach ($session->errors as $errorMsgAndFieldNames) {
			list($errorMsg, $fieldNames) = array_merge([], $errorMsgAndFieldNames);
			$this->AddError($errorMsg, $fieldNames);
		}
	}

	/**
	 * Initialize values from session for all fields in `PreDispatch()`
	 * lifecycle moment or earlier from method `GetValues()`.
	 * @param  \MvcCore\Session $session 
	 * @return void
	 */
	protected function preDispatchLoadValues (\MvcCore\ISession $session) {
		if (!$session->values) return;
		if (
			$this->dispatchState < \MvcCore\IController::DISPATCH_STATE_INITIALIZED
		) $this->Init();
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
				$this->values[$fieldName] = $fieldValue;
				$field->SetValue($fieldValue);
			}
		}
	}

	/**
	 * @inheritDocs
	 * @param  string $key          A key to translate.
	 * @param  array  $replacements An array of replacements to process in translated result.
	 * @throws \Exception           An exception if translations store is not successful.
	 * @return string               Translated key or key itself if there is no key in translations store.
	 */
	public function Translate ($key, $replacements = []) {
		return call_user_func_array($this->translator, func_get_args());
	}
}