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
 * Responsibility: init, predispatch and render submit button  
 *				   based on `<input>` HTML element with type `image`.
 */
class Image 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField,
				\MvcCore\Ext\Forms\Fields\ISubmit
{
	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Submit;
	use \MvcCore\Ext\Forms\Field\Props\FormAttrs;
	use \MvcCore\Ext\Forms\Field\Props\WidthHeight;

	/**
	 * Possible values: `image` (this type has for all browsers the same behaviour as type `submit`).
	 * @var string
	 */
	protected $type = 'image';

	/**
	 * Displayed image as button background, relative or absolute path.
	 * @requires
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @var string|NULL
	 */
	protected $src = NULL;

	/**
	 * Alternative button text in `alt` attribute, default value is: `Submit`.
	 * @var string
	 */
	protected $alt = 'Submit';

	/**
	 * Standard field control natural template string.
	 * @var string
	 */
	protected static $templates = [
		'control'	=> '<input type="image" id="{id}" name="{name}" src="{src}"{attrs} />',
	];

	/**
	 * Get displayed image as button background, relative or absolute path.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @return string|NULL
	 */
	public function GetSrc () {
		return $this->src;
	}

	/**
	 * Set displayed image as button background, relative or absolute path.
	 * @requires
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-src
	 * @param string $src 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetSrc ($src) {
		$this->src = $src;
		return $this;
	}

	/**
	 * Get alternative button text for `alt` attribute.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-alt
	 * @return string|NULL
	 */
	public function GetAlt () {
		return $this->alt;
	}

	/**
	 * Set alternative button text for `alt` attribute.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-alt
	 * @param string $alt
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetAlt ($alt) {
		$this->alt = $alt;
		return $this;
	}

	/**
	 * Create new form `<input type="image" />` control instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Image|\MvcCore\Ext\Forms\IField
	 */
	public function __construct (array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) self::$templates;
	}
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form instance by `$form->AddField();` method. Do not 
	 * use this method even if you don't develop any form field.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Set up translate boolean property.
	 * - Check if there is defined any value for `src` attribute.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Image|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->src) $this->throwNewInvalidArgumentException(
			'No input:image `src` attribute defined.'
		);
		return $this;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * - Set up tabindex if necessary.
	 * - Translate `alt` attribute text if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
		if (!$this->translate) return;
		$form = & $this->form;
		if ($this->alt !== NULL && $this->alt !== '')
			$this->alt = $form->translate($this->alt);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors,
	 * including all select `<option>` tags or `<optgroup>` tags
	 * if there are options configured for.
	 * @return string
	 */
	public function RenderControl () {
		if ($this->customResultState !== NULL) 
			$this->SetControlAttr('data-result', $this->customResultState);
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
