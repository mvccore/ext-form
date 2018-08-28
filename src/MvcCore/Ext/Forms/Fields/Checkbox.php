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

class Checkbox 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel, 
				\MvcCore\Ext\Forms\Fields\IChecked
{
	use \MvcCore\Ext\Forms\Field\Attrs\VisibleField;
	use \MvcCore\Ext\Forms\Field\Attrs\Label;
	use \MvcCore\Ext\Forms\Field\Attrs\Checked;

	protected $type = 'checkbox';
	
	protected $labelSide = 'right';

	protected $validators = ['SafeString'];

	protected static $templates = [
		'control'			=> '<input id="{id}" name="{name}" type="checkbox" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];
	
	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$viewClass = $this->form->GetViewClass();
		if ($this->checked === NULL) 
			$this->checked = static::GetCheckedByValue($this->value);
		$valueStr = htmlspecialchars($this->value, ENT_QUOTES);
		if (!$valueStr) 
			$valueStr = 'true';
		if ($this->checked) 
			$valueStr .= '" checked="checked';
		return $viewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $valueStr,
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
