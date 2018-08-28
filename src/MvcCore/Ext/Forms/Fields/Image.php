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

class Image 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField,
				\MvcCore\Ext\Forms\Fields\ISubmit
{
	use \MvcCore\Ext\Forms\Field\Attrs\VisibleField;
	use \MvcCore\Ext\Forms\Field\Attrs\CustomResultState;
	use \MvcCore\Ext\Forms\Field\Attrs\FormAttrs;
	use \MvcCore\Ext\Forms\Field\Attrs\WidthHeight;

	protected $type = 'image';

	protected $alt = 'Submit';

	protected static $templates = [
		'control'	=> '<input type="image" id="{id}" name="{name}" src="{src}"{attrs} />',
	];

	/**
	 * @requires
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @var string|NULL
	 */
	protected $src = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @return string|NULL
	 */
	public function GetSrc () {
		return $this->src;
	}

	/**
	 * @requires
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @param string $src 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetSrc ($src) {
		$this->src = $src;
		return $this;
	}

	public function __construct (array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) self::$templates;
	}
	
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->src) $this->throwNewInvalidArgumentException(
			'No button:image `src` defined.'
		);
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'formAction', 'formEnctype', 'formMethod', 'formNoValidate', 'formTarget',
			'width', 'height',
		]);
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'src'		=> htmlspecialchars($this->src, ENT_QUOTES),
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
