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

/**
 * Responsibility: init, predispatch and render `<input>` HTML element 
 *				   with types `text` and types `email`, `password`, 
 *				   `search`, `tel` and `url` in extended class. Text 
 *				   field and it's extended fields have their own 
 *				   validator(s) to check submitted value for 
 *				   min length/max length/pattern and dangerous characters 
 *				   in submitted text value(s). But it don't prevent SQL
 *				   inject attacks.
 */
class Text 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IPattern, 
				\MvcCore\Ext\Forms\Fields\IMinMaxLength,
				\MvcCore\Ext\Forms\Fields\IDataList
{
	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\Pattern;
	use \MvcCore\Ext\Forms\Field\Props\MinMaxLength;
	use \MvcCore\Ext\Forms\Field\Props\DataList;
	use \MvcCore\Ext\Forms\Field\Props\AutoComplete;
	use \MvcCore\Ext\Forms\Field\Props\PlaceHolder;
	use \MvcCore\Ext\Forms\Field\Props\Size;
	use \MvcCore\Ext\Forms\Field\Props\SpellCheck;
	use \MvcCore\Ext\Forms\Field\Props\InputMode;

	/**
	 * Possible values: `text` and `email`, `password`, `search`, `tel` and `url` in extended class.
	 * @var string
	 */
	protected $type = 'text';

	protected $validators = ['SafeString'/*, 'MinLength', 'MaxLength', 'Pattern'*/];

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		$this->setFormPattern();
		$this->setFormMinMaxLength();
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translate && $this->placeholder)
			$this->placeholder = $this->form->Translate($this->placeholder);
		$this->preDispatchInputMode();
		$this->preDispatchTabIndex();
	}

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'pattern',
			'minLength', 'maxLength',
			'list',
			'autoComplete',
			'placeHolder',
			'size',
			'spellCheck',
			'inputMode',
		]);
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
