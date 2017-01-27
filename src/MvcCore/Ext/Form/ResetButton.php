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

require_once(__DIR__.'/../Form.php');
require_once('Button.php');
//require_once('Core/Exception.php');

namespace MvcCore\Ext\Form;

class ResetButton extends Button
{
	public $Type = 'reset';
	public $Value = 'Reset';
	public $Validators = array();
	public $JsClass = 'MvcCoreForm.Reset';
	public $Js = '__MVCCORE_FORM_DIR__/fields/reset.js';
	
	public function SetAccesskey ($accesskey) {
		$this->Accesskey = $accesskey;
		return $this;
	}

	public function OnAdded (\MvcCore\Ext\Form & $form) {
		parent::OnAdded($form);
		if (!$this->Value) {
			$clsName = get_class($this);
			include_once('Core/Exception.php');
			throw new Core\Exception("No 'Value' defined for form field: '$clsName'.");
		}
	}
	
	public function SetUp () {
		parent::SetUp();
		$this->Form->AddJs($this->Js, $this->JsClass, array($this->Name));
		if ($this->Translate && $this->Value) {
			$this->Value = call_user_func($this->Form->Translator, $this->Value, $this->Form->Lang);
		}
	}
}
