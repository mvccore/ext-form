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

require_once(__DIR__ . '/../Form.php');
require_once('Core/Field.php');
//require_once('Core/View.php');

namespace MvcCore\Ext\Form;

class Textarea extends Core\Field
{
	public $Type = 'textarea';
	public $Rows = null;
	public $Cols = null;
	public $Maxlength = null;
	public $Validators = array('SafeString'/*, 'Maxlength', 'Pattern'*/);
	protected static $templates = array(
		'control'	=> '<textarea id="{id}" name="{name}"{attrs}>{value}</textarea>',
	);
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge((array)parent::$templates, (array)self::$templates);
	}
	public function SetRows ($rows) {
		$this->Rows = $rows;
		return $this;
	}
	public function SetCols ($cols) {
		$this->Cols = $cols;
		return $this;
	}
	public function SetMaxlength ($maxlength) {
		$this->Maxlength = $maxlength;
		return $this;
	}
	public function OnAdded (\MvcCore\Ext\Form & $form) {
		parent::OnAdded($form);
		if ($this->Maxlength && !in_array('Maxlength', $this->Validators)) {
			$this->Validators[] = 'Maxlength';
		}
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Maxlength', 'Rows', 'Cols')
		);
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
}
