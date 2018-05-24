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

require_once('Form/Core/Configuration.php');
//require_once('Form/Core/Exception.php');
require_once('Form/Core/Field.php');
//require_once('Form/Core/Helpers.php');
//require_once('Form/Core/View.php');

class Form extends \MvcCore\Controller implements \MvcCore\Ext\Forms\IForm
{
	use \MvcCore\Ext\Forms\Form\Traits\Form\InternalProps;
	use \MvcCore\Ext\Forms\Core\Traits\Form\ConfigProps;
	use \MvcCore\Ext\Forms\Core\Traits\Form\Getters;
	use \MvcCore\Ext\Forms\Core\Traits\Form\Setters;
	use \MvcCore\Ext\Forms\Core\Traits\Form\Assets;

	/* public methods ************************************************************************/
	/**
	 * Create \MvcCore\Ext\Form instance.
	 * Please don't forget to configure at least $form->Id, $form->Action,
	 * any control to work with and finaly any button:submit/input:submit
	 * to submit the form to any url defined in $form->Action.
	 * @param \MvcCore\Controller|mixed $controller
	 */
	public function __construct (/*\MvcCore\Controller*/ & $controller) {
		$this->application = \MvcCore\Application::GetInstance();
		$this->Controller = $controller;
		$baseLibPath = str_replace('\\', '/', __DIR__ . '/Form');
		if (!$this->jsAssetsRootDir) $this->jsAssetsRootDir = $baseLibPath;
		if (!$this->cssAssetsRootDir) $this->cssAssetsRootDir = $baseLibPath;
	}


	/**
	 * Unset submitted $form->Data records wchid are empty string or empty array.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function UnsetEmptyData () {
		$dataKeys = array_keys($this->Data);
		for ($i = 0, $l = count($dataKeys); $i < $l; $i += 1) {
			$dataKey = $dataKeys[$i];
			$dataValue = $this->Data[$dataKey];
			$dataValueType = gettype($dataValue);
			if ($dataValueType == 'array') {
				if (!$dataValue) unset($this->Data[$dataKey]);
			} else {
				if ($dataValue === '') unset($this->Data[$dataKey]);
			}
		}
		return $this;
	}

	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & ClearSession () {
		$this->Data = array();
		include_once('Form/Core/Helpers.php');
		Form\Core\Helpers::SetSessionData($this->Id, array());
		Form\Core\Helpers::SetSessionCsrf($this->Id, array());
		Form\Core\Helpers::SetSessionErrors($this->Id, array());
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
		if (!$this->Id) {
			$clsName = get_class($this);
			include_once('Form/Core/Exception.php');
			throw new \RuntimeException("No form 'Id' property defined in: '$clsName'.");
		}
		if ((is_null($this->Translate) || $this->Translate === TRUE) && !is_null($this->Translator)) {
			$this->Translate = TRUE;
		} else {
			$this->Translate = FALSE;
		}
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
		parent::PreDispatch(); // code: `if ($this->dispatchState < 1) $this->Init();` is executed by parent
		if ($this->dispatchState < 2) $this->preDispatchIfNecessary();
		return $this;
	}
}
