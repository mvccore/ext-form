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

class Button 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IAccessKey, 
				\MvcCore\Ext\Forms\Fields\ITabIndex
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;
	use \MvcCore\Ext\Forms\Field\Attrs\TabIndex;

	protected $type = 'button'; // submit | reset | button

	protected $value = 'OK';

	protected $renderMode = \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL;

	public static $templates = [
		'control'	=> '<button id="{id}" name="{name}" type="{type}"{attrs}>{value}</button>',
	];

	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->value) $this->throwNewInvalidArgumentException(
			'No button `value` defined.'
		);
		return $this;
	}
	
	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translate && $this->value)
			$this->value = $this->form->Translate($this->value);
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accessKey', 
			'tabIndex',
		]);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}
}
