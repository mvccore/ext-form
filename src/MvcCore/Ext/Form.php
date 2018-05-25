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

class Form extends \MvcCore\Controller implements \MvcCore\Ext\Forms\IForm
{
	use \MvcCore\Ext\Form\InternalProps;
	use \MvcCore\Ext\Form\ConfigProps;
	use \MvcCore\Ext\Form\GetMethods;
	use \MvcCore\Ext\Form\SetMethods;
	use \MvcCore\Ext\Form\AddMethods;
	use \MvcCore\Ext\Form\FieldMethods;
	use \MvcCore\Ext\Form\Session;
	use \MvcCore\Ext\Form\Csrf;
	use \MvcCore\Ext\Form\Rendering;
	use \MvcCore\Ext\Form\Submitting;


	/**
	 * Create \MvcCore\Ext\Form instance.
	 * Please don't forget to configure at least $form->Id, $form->Action,
	 * any control to work with and finaly any button:submit/input:submit
	 * to submit the form to any url defined in $form->Action.
	 * @param \MvcCore\Controller|\MvcCore\Interfaces\IController|NULL $controller
	 */
	public function __construct (\MvcCore\Interfaces\IController & $controller = NULL) {
		/** @var $controller \MvcCore\Controller */
		if ($controller === NULL) {
			$controller = & \MvcCore\Ext\Form::GetCallerControllerInstance();
			if ($controller === NULL) 
				$controller = & \MvcCore\Application::GetInstance()->GetController();
			if ($controller === NULL) throw new \InvalidArgumentException(
				'['.__CLASS__.'] There was not possible to determinate caller controller, '
				.'where is form instance create. Provide `$controller` instance explicitly '
				.'by first `\MvcCore\Ext\Form::__construct($controller);` argument.'
			);
		}
		$controller->AddChildController($this, $this->id);
		$baseAssetsPath = str_replace('\\', '/', __DIR__) . '/Forms/assets';
		if ($this->jsSupportFilesRootDir === NULL)
			$this->jsSupportFilesRootDir = $baseAssetsPath;
		if ($this->cssSupportFilesRootDir === NULL)
			$this->cssSupportFilesRootDir = $baseAssetsPath;
	}

	public static function & GetCallerControllerInstance () {
		$result = NULL;
		$backtraceItems = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
		if (count($backtraceItems) < 3) return $result;
		foreach ($backtraceItems as $backtraceItem) {
			if (!isset($backtraceItem['object']) || !$backtraceItem['object']) continue;
			$object = & $backtraceItem['object'];
			if (
				$object instanceof \MvcCore\Interfaces\IController &&
				!$object instanceof \MvcCore\Ext\Forms\IForm
			) {
				$result = & $object;
				break;
			}
		}
		return $result;
	}

	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & ClearSession () {
		$this->values = array();
		$this->errors = array();
		$session = & $this->getSession();
		$session->values = array();
		$session->csrf = array();
		$session->errors = array();
		return $this;
	}

	/**
	 * Initialize the form, check if form is initialized or not and do it only once.
	 * Check if any form id exists and initialize translation boolean for better field initializations.
	 * This is template method. To define any fields in custom `\MvcCore\Ext\Form` class extension,
	 * do it in `Init()` method and call `parent::Init();` as first line inside your custom `Init()` method.
	 * @throws \RuntimeException No form id property defined.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function Init () {
		if ($this->dispatchState > 0) return $this;
		parent::Init();
		$this->dispatchState = 1;
		if (!$this->id)
			throw new \RuntimeException("No form `id` property defined in `".get_class($this)."`.");
		if (isset(self::$allFormIds[$this->id])) {
			throw new \RuntimeException("Form id `".$this->id."` already defined.");
		} else {
			self::$allFormIds[$this->id] = TRUE;
		}
		$this->translate = $this->translator !== NULL && is_callable($this->translator);
		return $this;
	}
	/**
	 * Prepare form and it's fields for rendering.
	 * This function is called automaticly by rendering process if necessary.
	 * But if you need to operate with fields in your controller before rendering
	 * with real session values and initialized session errors, you can call this
	 * method anytime to prepare form for rendering and operate with anything inside.
	 * @return void
	 */
	public function PreDispatch () {
		return $this->preDispatchIfNecessary();
	}
}
