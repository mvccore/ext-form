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
class Text extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\AutoComplete;
	use \MvcCore\Ext\Forms\Field\Attrs\PlaceHolder;
	use \MvcCore\Ext\Forms\Field\Attrs\Pattern;
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxLength;

	protected $type = 'text';

	protected $validators = array('SafeString'/*, 'MinLength', 'MaxLength', 'Pattern'*/);

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		$this->checkValidatorsPattern();
		$this->checkValidatorsMinMaxLength();
		return $this;
	}
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translate && $this->placeholder)
			$this->placeholder = $this->form->Translate($this->placeholder);
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('minLength', 'maxLength', 'size', 'placeHolder', 'pattern', 'autoComplete')
		);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
}
