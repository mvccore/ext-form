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

require_once('Core/Field.php');
//require_once('Core/View.php');

class Checkbox extends Core\Field
{
	public $Type = 'checkbox';
	public $LabelSide = 'right';
	public $Validators = array('SafeString');
	public static $Templates = array(
		'control'			=> '<input id="{id}" name="{name}" type="checkbox" value="true"{value}{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	);
	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$Templates = (object) array_merge((array)parent::$Templates, (array)self::$Templates);
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		include_once('Core/View.php');
		return Core\View::Format(static::$Templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'value'		=> $this->Value ? ' checked="checked"' : '',
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
	}
}
