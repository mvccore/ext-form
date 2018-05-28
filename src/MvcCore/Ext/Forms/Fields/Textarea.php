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

class Textarea extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxLength;
	use \MvcCore\Ext\Forms\Field\Attrs\RowsCols;

	protected $type = 'textarea';

	protected $validators = array('SafeString'/*, 'MinLength', 'MaxLength', 'Pattern'*/);

	protected static $templates = array(
		'control'	=> '<textarea id="{id}" name="{name}"{attrs}>{value}</textarea>',
	);

	public function __construct (array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		$this->checkValidatorsMinMaxLength();
		return $this;
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('MinLength', 'MaxLength', 'Rows', 'Cols')
		);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
}
