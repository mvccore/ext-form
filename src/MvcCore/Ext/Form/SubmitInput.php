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

namespace MvcCore\Ext\Form;

require_once(__DIR__.'/../Form.php');
require_once('Core/Field.php');
//require_once('Core/View.php');

class SubmitInput extends Core\Field
{
	public $Type = 'submit';
	public $Value = 'Submit';
	public $RenderMode = \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL;
	public $Accesskey = null;
	public $Validators = array();
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
		if ($this->Translate && $this->Value) {
			$this->Value = call_user_func($this->Form->Translator, $this->Value, $this->Form->Lang);
		}
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Accesskey',)
		);
		include_once('Core/View.php');
		return Core\View::Format(static::$Templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
}