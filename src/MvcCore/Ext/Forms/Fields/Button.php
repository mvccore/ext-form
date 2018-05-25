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

namespace MvcCore\Ext\Forms\Fields;

//require_once('Core/View.php');
//require_once('Core/Exception.php');

class Button extends \MvcCore\Ext\Forms\Field
{
	public $Type = 'button'; // submit | reset | button
	public $Value = 'OK';
	public $RenderMode = \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL;
	public $Accesskey = null;
	public static $Templates = array(
		'control'	=> '<button id="{id}" name="{name}" type="{type}"{attrs}>{value}</button>',
	);
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$Templates = (object) array_merge((array)parent::$Templates, (array)self::$Templates);
	}
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
