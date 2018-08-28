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

class Color 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IDataList
{
	use \MvcCore\Ext\Forms\Field\Attrs\VisibleField;
	use \MvcCore\Ext\Forms\Field\Attrs\Label;
	use \MvcCore\Ext\Forms\Field\Attrs\DataList;
	use \MvcCore\Ext\Forms\Field\Attrs\AutoComplete;

	protected $type = 'color';

	/**
	 * Hexadecimal color value with no transparency including leading hash char `#`.
	 * Default value - a black color - `#000000`.
	 * @var string
	 */
	protected $vale = '#000000';

	protected $validators = ['Color'];

	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'list',
			'autoComplete',
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
