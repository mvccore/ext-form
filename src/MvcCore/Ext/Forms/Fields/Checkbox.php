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
	implements	\MvcCore\Ext\Forms\Fields\IAccessKey, 
				\MvcCore\Ext\Forms\Fields\ITabIndex
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;
	use \MvcCore\Ext\Forms\Field\Attrs\TabIndex;

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
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accessKey', 
			'tabIndex',
		]);
		$viewClass = $this->form->GetViewClass();
		if ($this->value) {
			$value = htmlspecialchars($this->value, ENT_QUOTES) . '" checked="checked';
		} else {
			$value = 'true';
		}
		return $viewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}
}
