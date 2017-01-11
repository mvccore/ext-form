<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view 
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once('/../SimpleForm.php');
require_once('/Core/Field.php');
require_once('/Core/Exception.php');
require_once('/Core/View.php');

class SimpleForm_SubmitInput extends SimpleForm_Core_Field
{
	public $Type = 'submit';
	public $Value = 'Submit';
	public $RenderMode = SimpleForm::FIELD_RENDER_MODE_NO_LABEL;
	public $Accesskey = null;
	public $Validators = array();
	public function SetAccesskey ($accesskey) {
		$this->Accesskey = $accesskey;
		return $this;
	}
	public function OnAdded (SimpleForm & $form) {
		parent::OnAdded($form);
		if (!$this->Value) {
			$clsName = get_class($this);
			throw new SimpleForm_Core_Exception("No 'Value' defined for form field: '$clsName'.");
		}
	}
	public function SetUp () {
		parent::SetUp();
		if ($this->Translate && $this->Value) {
			$translator = $this->Form->Translator;
			$this->Value = $translator($this->Value, $this->Form->Lang);
		}
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Accesskey',)
		);
		return SimpleForm_Core_View::Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
}