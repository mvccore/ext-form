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

class Checkbox extends \MvcCore\Ext\Forms\Field
{
	protected $type = 'checkbox';
	
	protected $labelSide = 'right';

	protected $validators = array('SafeString');
	
	protected static $templates = array(
		'control'			=> '<input id="{id}" name="{name}" type="checkbox" value="true"{value}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	);
	
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		$viewClass = $this->form->GetViewClass();
		return $viewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $this->value ? ' checked="checked"' : '',
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
}
