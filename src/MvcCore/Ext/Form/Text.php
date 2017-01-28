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

require_once(__DIR__ . '/../Form.php');
require_once('Core/Field.php');
//require_once('Core/View.php');

class Text extends Core\Field
{
	public $Type = 'text';
	public $Placeholder = null;
	public $Size = null;
	public $Maxlength = null;
	public $Pattern = null;
	public $Autocomplete = null;
	public $Validators = array('SafeString'/*, 'Maxlength', 'Pattern'*/);
	public function SetPlaceholder ($placeholder) {
		$this->Placeholder = $placeholder;
		return $this;
	}
	public function SetSize ($size) {
		$this->Size = $size;
		return $this;
	}
	public function SetMaxlength ($maxlength) {
		$this->Maxlength = $maxlength;
		return $this;
	}
	public function SetPattern ($pattern) {
		$this->Pattern = $pattern;
		return $this;
	}
	public function SetAutocomplete ($autocomplete) {
		$this->Autocomplete = $autocomplete;
		return $this;
	}
	public function OnAdded (\MvcCore\Ext\Form & $form) {
		parent::OnAdded($form);
		if ($this->Pattern && !in_array('Pattern', $this->Validators)) {
			$this->Validators[] = 'Pattern';
		}
		if ($this->Maxlength && !in_array('Maxlength', $this->Validators)) {
			$this->Validators[] = 'Maxlength';
		}
	}
	public function SetUp () {
		parent::SetUp();
		$form = $this->Form;
		if ($this->Translate && $this->Placeholder) {
			$this->Placeholder = call_user_func($form->Translator, $this->Placeholder, $form->Lang);
		}
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Maxlength', 'Size', 'Placeholder', 'Pattern', 'Autocomplete')
		);
		include_once('Core/View.php');
		return Core\View::Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
}
